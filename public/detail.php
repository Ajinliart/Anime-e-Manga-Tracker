<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$type = valid_type($_GET['type'] ?? null);
$id = int_param($_GET, 'id');
if ($type === null || $id === null) {
    not_found('Titolo non trovato.');
}

$t = TYPES[$type];
$repo = new TitleRepository(db(), $type);
$item = $repo->find($id);
if ($item === null) {
    not_found('Titolo non trovato.');
}

$genres = $repo->genresFor($id);
$rank = $repo->rankOf($id);
$user = current_user();
$entry = $user ? (new ListRepository(db()))->get((int) $user['id'], $type, $id) : null;
$total = $item[$t['total']];

$pageTitle = $item['title'];
$activeNav = $type;
require __DIR__ . '/../src/views/header.php';
?>

<a class="back-link" href="<?= e(url('catalog.php?type=' . $type)) ?>">← Catalogo <?= e($t['label']) ?></a>

<article class="detail">
    <div class="detail-cover">
        <?= render('partials/cover', ['item' => $item, 'size' => 'large']) ?>
    </div>

    <div class="detail-main">
        <span class="type-badge"><?= e($t['label']) ?></span>
        <h1><?= e($item['title']) ?></h1>

        <ul class="genre-list">
            <?php foreach ($genres as $g): ?>
                <li><a href="<?= e(url('catalog.php?type=' . $type . '&genre=' . $g['id'])) ?>"><?= e($g['name']) ?></a></li>
            <?php endforeach; ?>
        </ul>

        <dl class="info-list">
            <div><dt>Anno</dt><dd><?= e($item['year'] ?? '—') ?></dd></div>
            <div><dt><?= e($t['creator_label']) ?></dt><dd><?= e($item[$t['creator']] ?? '—') ?></dd></div>
            <div><dt><?= e(ucfirst($t['unit'])) ?></dt><dd><?= e($total ?? '—') ?></dd></div>
            <?php if ($type === 'manga'): ?>
                <div><dt>Volumi</dt><dd><?= e($item['volumes'] ?? '—') ?></dd></div>
            <?php endif; ?>
            <div><dt>Stato</dt><dd><?= e(RELEASE_LABELS[$item[$t['release']]] ?? $item[$t['release']]) ?></dd></div>
        </dl>

        <?php if (!empty($item['synopsis'])): ?>
            <h2>Trama</h2>
            <p class="synopsis"><?= nl2br(e($item['synopsis'])) ?></p>
        <?php endif; ?>
    </div>

    <aside class="detail-side">
        <div class="panel score-panel">
            <div class="score-big">★ <span id="avg-score"><?= e(format_score($item['avg_score'])) ?></span></div>
            <div class="muted"><span id="votes"><?= e($item['votes']) ?></span> voti</div>
            <div class="rank">Ranking: <strong id="rank"><?= $rank !== null ? '#' . e($rank) : '—' ?></strong></div>
        </div>

        <div class="panel list-panel">
            <h2>La mia lista</h2>

            <?php if ($user): ?>
                <form id="list-form" method="post" action="<?= e(url('api/list.php')) ?>" data-in-list="<?= $entry ? '1' : '0' ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="type" value="<?= e($type) ?>">
                    <input type="hidden" name="id" value="<?= e($id) ?>">
                    <input type="hidden" name="return" value="detail">

                    <label>
                        Stato
                        <select name="status" required>
                            <?php foreach (STATUSES as $status): ?>
                                <option value="<?= e($status) ?>" <?= ($entry['status'] ?? 'planned') === $status ? 'selected' : '' ?>>
                                    <?= e(status_label($type, $status)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>
                        Il mio voto
                        <select name="score">
                            <option value="">—</option>
                            <?php for ($s = 10; $s >= 1; $s--): ?>
                                <option value="<?= $s ?>" <?= isset($entry['score']) && (int) $entry['score'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                            <?php endfor; ?>
                        </select>
                    </label>

                    <label>
                        <?= e(ucfirst($t['unit'])) ?> <?= $type === 'anime' ? 'visti' : 'letti' ?>
                        <input type="number" name="progress" min="0" <?= $total ? 'max="' . e($total) . '"' : '' ?>
                               value="<?= e($entry['progress'] ?? '') ?>" placeholder="<?= $total ? '0 / ' . e($total) : '0' ?>">
                    </label>

                    <div class="form-actions">
                        <button type="submit" name="action" value="save" class="btn" data-label-add="Aggiungi alla lista" data-label-update="Aggiorna">
                            <?= $entry ? 'Aggiorna' : 'Aggiungi alla lista' ?>
                        </button>
                        <button type="submit" name="action" value="remove" class="btn btn-danger" data-confirm="Rimuovere questo titolo dalla tua lista?" <?= $entry ? '' : 'hidden' ?>>
                            Rimuovi
                        </button>
                    </div>
                    <p class="form-status" role="status"></p>
                </form>
            <?php else: ?>
                <p class="muted">Accedi per aggiungere questo titolo alla tua lista.</p>
                <a class="btn" href="<?= e(url('login.php?next=' . urlencode('detail.php?type=' . $type . '&id=' . $id))) ?>">Accedi</a>
            <?php endif; ?>
        </div>
    </aside>
</article>

<?php require __DIR__ . '/../src/views/footer.php'; ?>
