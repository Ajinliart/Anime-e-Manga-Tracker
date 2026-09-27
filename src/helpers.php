<?php
declare(strict_types=1);

/**
 * Configurazione per tipo di titolo. I nomi di tabella usati nelle query
 * arrivano SOLO da qui, mai dall'input dell'utente.
 */
const TYPES = [
    'anime' => [
        'table'   => 'anime',
        'genres'  => 'anime_genres',
        'list'    => 'user_anime',
        'fk'      => 'anime_id',
        'label'   => 'Anime',
        'planned' => 'Voglio guardarlo',
        'unit'    => 'episodi',
        'total'   => 'episodes',
        'creator' => 'studio',
        'creator_label' => 'Studio',
        'release' => 'status_air',
    ],
    'manga' => [
        'table'   => 'manga',
        'genres'  => 'manga_genres',
        'list'    => 'user_manga',
        'fk'      => 'manga_id',
        'label'   => 'Manga',
        'planned' => 'Voglio leggerlo',
        'unit'    => 'capitoli',
        'total'   => 'chapters',
        'creator' => 'author',
        'creator_label' => 'Autore',
        'release' => 'status_pub',
    ],
];

const STATUSES = ['planned', 'in_progress', 'completed'];

const RELEASE_LABELS = [
    'in_corso' => 'In corso',
    'concluso' => 'Concluso',
    'in_pausa' => 'In pausa',
];

function config(string $key): mixed
{
    static $config = null;
    $config ??= require __DIR__ . '/config.php';
    return $config[$key] ?? null;
}

/** Escape HTML per l'output. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return rtrim((string) config('base_url'), '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/** Restituisce il tipo se valido, altrimenti null. */
function valid_type(mixed $type): ?string
{
    return is_string($type) && isset(TYPES[$type]) ? $type : null;
}

function status_label(string $type, string $status): string
{
    return match ($status) {
        'planned'     => TYPES[$type]['planned'],
        'in_progress' => 'In corso',
        'completed'   => 'Completato',
        default       => $status,
    };
}

/** Legge un intero positivo da GET/POST, null se assente o non valido. */
function int_param(array $source, string $key): ?int
{
    $value = filter_var($source[$key] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $value === false ? null : $value;
}

/** Percorso interno sicuro per i redirect post-login (es. "detail.php?type=anime&id=3"). */
function safe_next(mixed $next): string
{
    if (is_string($next) && preg_match('/^[a-z_]+\.php(\?[\w=&%.\-+]*)?$/i', $next)) {
        return $next;
    }
    return 'index.php';
}

function format_score(?float $score): string
{
    return $score === null ? 'N/D' : number_format($score, 2, ',', '');
}

function wants_json(): bool
{
    return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
}

function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Renderizza un template in src/views e restituisce l'HTML. */
function render(string $view, array $vars = []): string
{
    extract($vars, EXTR_SKIP);
    ob_start();
    require __DIR__ . '/views/' . $view . '.php';
    return (string) ob_get_clean();
}

function not_found(string $message = 'Pagina non trovata.'): never
{
    http_response_code(404);
    $pageTitle = 'Non trovato';
    require __DIR__ . '/views/header.php';
    echo '<section class="empty-state"><h1>404</h1><p>' . e($message) . '</p>'
        . '<a class="btn" href="' . e(url('index.php')) . '">Torna alla home</a></section>';
    require __DIR__ . '/views/footer.php';
    exit;
}
