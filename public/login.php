<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$next = safe_next($_GET['next'] ?? $_POST['next'] ?? null);

if (current_user()) {
    redirect($next);
}

$error = null;
$login = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim((string) ($_POST['login'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!csrf_check()) {
        $error = 'Sessione scaduta, riprova.';
    } else {
        $users = new UserRepository(db());
        $user = $login !== '' ? $users->findForLogin($login) : null;

        if ($user && password_verify($password, $user['password_hash'])) {
            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                $users->updatePasswordHash((int) $user['id'], password_hash($password, PASSWORD_DEFAULT));
            }
            login_user($user);
            flash('success', 'Bentornato, ' . $user['username'] . '!');
            redirect($next);
        }

        // Messaggio generico: non riveliamo se esiste lo username
        $error = 'Credenziali non valide.';
    }
}

$pageTitle = 'Accedi';
$activeNav = 'login';
require __DIR__ . '/../src/views/header.php';
?>

<section class="auth-box">
    <h1>Accedi</h1>

    <?php if ($error): ?>
        <div class="flash flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('login.php')) ?>" class="form">
        <?= csrf_field() ?>
        <input type="hidden" name="next" value="<?= e($next) ?>">

        <label>
            Username o email
            <input type="text" name="login" value="<?= e($login) ?>" required autocomplete="username" autofocus>
        </label>

        <label>
            Password
            <input type="password" name="password" required autocomplete="current-password">
        </label>

        <button type="submit" class="btn btn-block">Accedi</button>
    </form>

    <p class="muted center">Non hai un account? <a href="<?= e(url('register.php')) ?>">Registrati</a></p>
</section>

<?php require __DIR__ . '/../src/views/footer.php'; ?>
