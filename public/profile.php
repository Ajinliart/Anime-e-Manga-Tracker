<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$type = valid_type($_GET['type'] ?? 'anime') ?? 'anime';
$status = in_array($_GET['status'] ?? null, STATUSES, true) ? $_GET['status'] : null;

$user = require_login('profile.php?type=' . $type);
$userId = (int) $user['id'];

$lists = new ListRepository(db());
$counts = $lists->counts($userId);
$entries = $lists->forUser($userId, $type, $status);
$t = TYPES[$type];

$pageTitle = 'Profilo';
$activeNav = 'profile';
require __DIR__ . '/../src/views/header.php';
?>

<section class="profile-head panel">
    <div class="avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($user['username'], 0, 1))) ?></div>
    <div>
        <h1><?= e($user['username']) ?></h1>
        <p class="muted"><?= e($user['email']) ?> · iscritto dal <?= e(date('d/m/Y', strtotime($user['created_at']))) ?></p>
    </div>
</section>

<section class="stats">
    <?php foreach (TYPES as $key => $info): ?>
        <div class="panel stat-card">
            <h2><?= e($info['label']) ?></h2>
            <ul>
                <?php foreach (STATUSES as $s): ?>
                    <li><span><?= e(status_label($key, $s)) ?></span><strong><?= e($counts[$key][$s]) ?></strong></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</section>

<section>
    <div class="page-head">
        <h2>La mia lista</h2>
        <div class="type-toggle">
            <?php foreach (TYPES as $key => $info): ?>
                <a href="<?= e(url('profile.php?type=' . $key)) ?>" class="<?= $key === $type ? 'active' : '' ?>"><?= e($info['label']) ?> (<?= array_sum($counts[$key]) ?>)</a>
            <?php endforeach; ?>
        </div>
    </div>

    <nav class="status-tabs">
        <a href="<?= e(url('profile.php?type=' . $type)) ?>" class="<?= $status === null ? 'active' : '' ?>">Tutti</a>
        <?php foreach (STATUSES as $s): ?>
            <a href="<?= e(url('profile.php?type=' . $type . '&status=' . $s)) ?>" class="<?= $status === $s ? 'active' : '' ?>">
                <?= e(status_label($type, $s)) ?> (<?= e($counts[$type][$s]) ?>)
            </a>
        <?php endforeach; ?>
    </nav>

    <?php if ($entries): ?>
        <ul class="entry-list">
            <?php foreach ($entries as $entry): ?>
                <li class="entry">
                    <a href="<?= e(url('detail.php?type=' . $type . '&id=' . $entry['id'])) ?>" class="entry-cover">
                        <?= render('partials/cover', ['item' => $entry, 'size' => 'small']) ?>
                    </a>
                    <div class="entry-info">
                        <a href="<?= e(url('detail.php?type=' . $type . '&id=' . $entry['id'])) ?>" class="entry-title"><?= e($entry['title']) ?></a>
                        <div class="entry-meta">
                            <span class="status-pill status-<?= e($entry['status']) ?>"><?= e(status_label($type, $entry['status'])) ?></span>
                            <span>Voto: <strong><?= $entry['score'] !== null ? e($entry['score']) . '/10' : '—' ?></strong></span>
                            <span><?= e(ucfirst($t['unit'])) ?>: <?= e($entry['progress'] ?? 0) ?> / <?= e($entry['total'] ?? '?') ?></span>
                        </div>
                    </div>
                    <form method="post" action="<?= e(url('api/list.php')) ?>" class="entry-actions" data-confirm="Rimuovere «<?= e($entry['title']) ?>» dalla lista?">
                        <?= csrf_field() ?>
                        <input type="hidden" name="type" value="<?= e($type) ?>">
                        <input type="hidden" name="id" value="<?= e($entry['id']) ?>">
                        <input type="hidden" name="return" value="profile">
                        <input type="hidden" name="action" value="remove">
                        <a href="<?= e(url('detail.php?type=' . $type . '&id=' . $entry['id'])) ?>" class="btn btn-ghost btn-small">Modifica</a>
                        <button type="submit" class="btn btn-danger btn-small">Rimuovi</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="empty-state">
            <p>Nessun titolo in questa sezione.</p>
            <a class="btn" href="<?= e(url('catalog.php?type=' . $type)) ?>">Esplora il catalogo</a>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../src/views/footer.php'; ?>
