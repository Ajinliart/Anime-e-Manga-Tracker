<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$data = catalog_query($_GET);
$type = $data['type'];
$filters = $data['filters'];
$genres = $data['repo']->availableGenres();

$pageTitle = 'Catalogo ' . TYPES[$type]['label'];
$activeNav = $type;
require __DIR__ . '/../src/views/header.php';
?>

<div class="page-head">
    <h1>Catalogo <?= e(TYPES[$type]['label']) ?></h1>
    <div class="type-toggle">
        <?php foreach (TYPES as $key => $t): ?>
            <a href="<?= e(url('catalog.php?type=' . $key)) ?>" class="<?= $key === $type ? 'active' : '' ?>"><?= e($t['label']) ?></a>
        <?php endforeach; ?>
    </div>
</div>

<form id="catalog-form" class="filters" method="get" action="<?= e(url('catalog.php')) ?>">
    <input type="hidden" name="type" value="<?= e($type) ?>">

    <input type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Cerca per titolo…" aria-label="Cerca per titolo" autocomplete="off" maxlength="100">

    <select name="genre" aria-label="Genere">
        <option value="">Tutti i generi</option>
        <?php foreach ($genres as $g): ?>
            <option value="<?= e($g['id']) ?>" <?= $filters['genre'] === (int) $g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="sort" aria-label="Ordina per">
        <?php foreach (TitleRepository::SORTS as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= $filters['sort'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn">Cerca</button>
</form>

<div id="results" aria-live="polite">
    <?= render('partials/results', $data) ?>
</div>

<?php require __DIR__ . '/../src/views/footer.php'; ?>
