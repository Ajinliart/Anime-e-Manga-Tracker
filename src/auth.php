<?php
declare(strict_types=1);

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('amtracker');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function current_user(): ?array
{
    static $user = false;

    if ($user === false) {
        $user = null;
        if (!empty($_SESSION['user_id'])) {
            $user = (new UserRepository(db()))->findById((int) $_SESSION['user_id']);
            if ($user === null) {
                // Utente eliminato: la sessione non è più valida
                unset($_SESSION['user_id']);
            }
        }
    }

    return $user;
}

/** Richiede un utente autenticato: 401 JSON per le API, redirect al login per le pagine. */
function require_login(string $next = 'index.php'): array
{
    $user = current_user();
    if ($user !== null) {
        return $user;
    }

    if (wants_json()) {
        json_response(['ok' => false, 'error' => 'Devi effettuare l\'accesso.'], 401);
    }

    flash('info', 'Accedi per continuare.');
    redirect('login.php?next=' . urlencode($next));
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
}

function logout_user(): void
{
    $_SESSION = [];

    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 3600,
        'path'     => $params['path'],
        'secure'   => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'],
    ]);

    session_destroy();
}
