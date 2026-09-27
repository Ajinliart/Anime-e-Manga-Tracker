<?php
declare(strict_types=1);

/**
 * Accesso al catalogo. La stessa classe gestisce anime e manga:
 * le tabelle vengono scelte dalla whitelist TYPES in base a $type.
 */
class TitleRepository
{
    private array $t;

    public const SORTS = [
        'title' => 'Titolo (A-Z)',
        'score' => 'Voto medio',
        'year'  => 'Più recenti',
    ];

    public function __construct(private PDO $db, private string $type)
    {
        if (!isset(TYPES[$type])) {
            throw new InvalidArgumentException('Tipo non valido: ' . $type);
        }
        $this->t = TYPES[$type];
    }

    /** Sottoquery con voto medio e numero di voti per titolo. */
    private function statsJoin(): string
    {
        return "LEFT JOIN (
                    SELECT {$this->t['fk']} AS title_id, AVG(score) AS avg_score, COUNT(score) AS votes
                    FROM {$this->t['list']}
                    WHERE score IS NOT NULL
                    GROUP BY {$this->t['fk']}
                ) s ON s.title_id = t.id";
    }

    private function orderBy(string $sort): string
    {
        return match ($sort) {
            'score' => 's.avg_score IS NULL, s.avg_score DESC, s.votes DESC, t.title',
            'year'  => 't.year IS NULL, t.year DESC, t.title',
            default => 't.title',
        };
    }

    private static function normalize(array $row): array
    {
        $row['id'] = (int) $row['id'];
        $row['avg_score'] = $row['avg_score'] !== null ? (float) $row['avg_score'] : null;
        $row['votes'] = (int) ($row['votes'] ?? 0);
        return $row;
    }

    /**
     * Ricerca con filtro per titolo e genere, ordinamento e paginazione.
     * @return array{items: array, total: int}
     */
    public function search(string $query, ?int $genreId, string $sort, int $page, int $perPage): array
    {
        $where = [];
        $params = [];

        if ($query !== '') {
            $where[] = 't.title LIKE :q';
            $params[':q'] = '%' . addcslashes($query, '%_\\') . '%';
        }
        if ($genreId !== null) {
            $where[] = "EXISTS (SELECT 1 FROM {$this->t['genres']} g WHERE g.{$this->t['fk']} = t.id AND g.genre_id = :genre)";
            $params[':genre'] = $genreId;
        }
        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->t['table']} t $whereSql");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT t.id, t.title, t.year, t.cover_url, s.avg_score, s.votes
                FROM {$this->t['table']} t
                {$this->statsJoin()}
                $whereSql
                ORDER BY {$this->orderBy($sort)}
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', max(0, ($page - 1) * $perPage), PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items' => array_map(fn(array $row) => self::normalize($row), $stmt->fetchAll()),
            'total' => $total,
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT t.*, s.avg_score, s.votes
             FROM {$this->t['table']} t
             {$this->statsJoin()}
             WHERE t.id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? self::normalize($row) : null;
    }

    public function genresFor(int $id): array
    {
        $stmt = $this->db->prepare(
            "SELECT g.id, g.name
             FROM genres g
             JOIN {$this->t['genres']} tg ON tg.genre_id = g.id
             WHERE tg.{$this->t['fk']} = ?
             ORDER BY g.name"
        );
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /** Generi che hanno almeno un titolo di questo tipo. */
    public function availableGenres(): array
    {
        return $this->db->query(
            "SELECT DISTINCT g.id, g.name
             FROM genres g
             JOIN {$this->t['genres']} tg ON tg.genre_id = g.id
             ORDER BY g.name"
        )->fetchAll();
    }

    /** Posizione in classifica per voto medio, null se il titolo non ha abbastanza voti. */
    public function rankOf(int $id): ?int
    {
        $minVotes = (int) config('min_votes_for_rank');
        $title = $this->find($id);
        if ($title === null || $title['avg_score'] === null || $title['votes'] < $minVotes) {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) + 1 FROM (
                 SELECT AVG(score) AS avg_score
                 FROM {$this->t['list']}
                 WHERE score IS NOT NULL
                 GROUP BY {$this->t['fk']}
                 HAVING COUNT(score) >= :min_votes
             ) ranked
             WHERE ranked.avg_score > :avg"
        );
        $stmt->bindValue(':min_votes', $minVotes, PDO::PARAM_INT);
        $stmt->bindValue(':avg', (string) $title['avg_score'], PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /** Titoli meglio votati (quelli senza voti in fondo). */
    public function top(int $limit): array
    {
        return $this->search('', null, 'score', 1, $limit)['items'];
    }

    public function latest(int $limit): array
    {
        return $this->search('', null, 'year', 1, $limit)['items'];
    }
}
