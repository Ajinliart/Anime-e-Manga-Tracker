<?php
/** @var string|null $pageTitle */
/** @var string|null $activeNav */
$user = current_user();
$activeNav ??= '';
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="base-url" content="<?= e(rtrim((string) config('base_url'), '/')) ?>">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' · ' : '' ?>Anime &amp; Manga Tracker</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="<?= e(url('index.php')) ?>">A<span>&amp;</span>M Tracker</a>

        <nav class="main-nav">
            <a href="<?= e(url('catalog.php?type=anime')) ?>" class="<?= $activeNav === 'anime' ? 'active' : '' ?>">Anime</a>
            <a href="<?= e(url('catalog.php?type=manga')) ?>" class="<?= $activeNav === 'manga' ? 'active' : '' ?>">Manga</a>
        </nav>

        <div class="user-nav">
            <?php if ($user): ?>
                <a href="<?= e(url('profile.php')) ?>" class="<?= $activeNav === 'profile' ? 'active' : '' ?>">👤 <?= e($user['username']) ?></a>
                <form method="post" action="<?= e(url('logout.php')) ?>" class="inline-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-ghost btn-small">Esci</button>
                </form>
            <?php else: ?>
                <a href="<?= e(url('login.php')) ?>" class="<?= $activeNav === 'login' ? 'active' : '' ?>">Accedi</a>
                <a href="<?= e(url('register.php')) ?>" class="btn btn-small">Registrati</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="container">
    <?php foreach (take_flashes() as $flash): ?>
        <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endforeach; ?>
