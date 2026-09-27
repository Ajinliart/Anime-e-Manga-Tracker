<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

if (current_user()) {
    redirect('profile.php');
}

$errors = [];
$old = ['username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['password_confirm'] ?? '');
    $old = ['username' => $username, 'email' => $email];

    // Se il token non è valido l'account non viene creato (il controllo "if (!$errors)" più sotto)
    if (!csrf_check()) {
        $errors[] = 'Sessione scaduta, riprova.';
    }

    $users = new UserRepository(db());

    if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $username)) {
        $errors['username'] = 'Lo username deve avere 3-30 caratteri: lettere, numeri, "_", "." o "-".';
    } elseif ($users->usernameExists($username)) {
        $errors['username'] = 'Questo username è già in uso.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
        $errors['email'] = 'Inserisci un indirizzo email valido.';
    } elseif ($users->emailExists($email)) {
        $errors['email'] = 'Esiste già un account con questa email.';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'La password deve avere almeno 8 caratteri.';
    } elseif (strlen($password) > 72) {
        // bcrypt considera solo i primi 72 byte
        $errors['password'] = 'La password può avere al massimo 72 caratteri.';
    } elseif ($password !== $confirm) {
        $errors['password_confirm'] = 'Le password non coincidono.';
    }

    if (!$errors) {
        try {
            $id = $users->create($username, $email, $password);
            login_user(['id' => $id]);
            flash('success', 'Benvenuto, ' . $username . '! Il tuo account è stato creato.');
            redirect('profile.php');
        } catch (PDOException $ex) {
            // Violazione di unicità in caso di registrazioni simultanee
            if ($ex->getCode() === '23000') {
                $errors[] = 'Username o email già in uso.';
            } else {
                throw $ex;
            }
        }
    }
}

$pageTitle = 'Registrati';
$activeNav = 'register';
require __DIR__ . '/../src/views/header.php';
?>

<section class="auth-box">
    <h1>Crea un account</h1>

    <?php if (isset($errors[0])): ?>
        <div class="flash flash-error"><?= e($errors[0]) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('register.php')) ?>" class="form" novalidate>
        <?= csrf_field() ?>

        <label>
            Username
            <input type="text" name="username" value="<?= e($old['username']) ?>" required minlength="3" maxlength="30" pattern="[A-Za-z0-9_.\-]{3,30}" autocomplete="username" autofocus>
            <?php if (isset($errors['username'])): ?><span class="field-error"><?= e($errors['username']) ?></span><?php endif; ?>
        </label>

        <label>
            Email
            <input type="email" name="email" value="<?= e($old['email']) ?>" required maxlength="255" autocomplete="email">
            <?php if (isset($errors['email'])): ?><span class="field-error"><?= e($errors['email']) ?></span><?php endif; ?>
        </label>

        <label>
            Password
            <input type="password" name="password" required minlength="8" maxlength="72" autocomplete="new-password">
            <?php if (isset($errors['password'])): ?><span class="field-error"><?= e($errors['password']) ?></span><?php endif; ?>
        </label>

        <label>
            Conferma password
            <input type="password" name="password_confirm" required autocomplete="new-password">
            <?php if (isset($errors['password_confirm'])): ?><span class="field-error"><?= e($errors['password_confirm']) ?></span><?php endif; ?>
        </label>

        <button type="submit" class="btn btn-block">Registrati</button>
    </form>

    <p class="muted center">Hai già un account? <a href="<?= e(url('login.php')) ?>">Accedi</a></p>
</section>

<?php require __DIR__ . '/../src/views/footer.php'; ?>
