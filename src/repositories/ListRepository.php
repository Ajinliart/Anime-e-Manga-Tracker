<?php
declare(strict_types=1);

/** Lista personale dell'utente (tabelle user_anime / user_manga). */
class ListRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function get(int $userId, string $type, int $titleId): ?array
    {
        $t = TYPES[$type];
        $stmt = $this->db->prepare("SELECT status, score, progress, updated_at FROM {$t['list']} WHERE user_id = ? AND {$t['fk']} = ?");
        $stmt->execute([$userId, $titleId]);
        return $stmt->fetch() ?: null;
    }

    public function save(int $userId, string $type, int $titleId, string $status, ?int $score, ?int $progress): void
    {
        $t = TYPES[$type];
        $stmt = $this->db->prepare(
            "INSERT INTO {$t['list']} (user_id, {$t['fk']}, status, score, progress)
             VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE status = VALUES(status), score = VALUES(score), progress = VALUES(progress)"
        );
        $stmt->execute([$userId, $titleId, $status, $score, $progress]);
    }

    public function remove(int $userId, string $type, int $titleId): bool
    {
        $t = TYPES[$type];
        $stmt = $this->db->prepare("DELETE FROM {$t['list']} WHERE user_id = ? AND {$t['fk']} = ?");
        $stmt->execute([$userId, $titleId]);
        return $stmt->rowCount() > 0;
    }

    /** Voci della lista con i dati del titolo, filtrabili per stato. */
    public function forUser(int $userId, string $type, ?string $status = null): array
    {
        $t = TYPES[$type];
        $sql = "SELECT l.status, l.score, l.progress, l.updated_at,
                       x.id, x.title, x.year, x.cover_url, x.{$t['total']} AS total
                FROM {$t['list']} l
                JOIN {$t['table']} x ON x.id = l.{$t['fk']}
                WHERE l.user_id = ?";
        $params = [$userId];

        if ($status !== null) {
            $sql .= ' AND l.status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY l.updated_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Conteggi per tipo e stato: ['anime' => ['planned' => 2, ...], 'manga' => [...]] */
    public function counts(int $userId): array
    {
        $counts = [];
        foreach (TYPES as $type => $t) {
            $counts[$type] = array_fill_keys(STATUSES, 0);
            $stmt = $this->db->prepare("SELECT status, COUNT(*) AS n FROM {$t['list']} WHERE user_id = ? GROUP BY status");
            $stmt->execute([$userId]);
            foreach ($stmt->fetchAll() as $row) {
                $counts[$type][$row['status']] = (int) $row['n'];
            }
        }
        return $counts;
    }
}
