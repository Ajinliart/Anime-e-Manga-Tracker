<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$sections = [];
foreach (TYPES as $type => $t) {
    $repo = new TitleRepository(db(), $type);
    $sections[$type] = [
        'label'  => $t['label'],
        'top'    => $repo->top(6),
        'latest' => $repo->latest(6),
    ];
}

$pageTitle = 'Home';
require __DIR__ . '/../src/views/header.php';
?>

<section class="hero">
    <h1>Tieni traccia dei tuoi anime e manga</h1>
    <p>Cerca nel catalogo, segna cosa vuoi guardare o leggere, cosa stai seguendo e cosa hai completato.</p>

    <form class="hero-search" method="get" action="<?= e(url('catalog.php')) ?>">
        <select name="type" aria-label="Tipo">
            <option value="anime">Anime</option>
            <option value="manga">Manga</option>
        </select>
        <input type="search" name="q" placeholder="Cerca un titolo…" aria-label="Cerca un titolo">
        <button type="submit" class="btn">Cerca</button>
    </form>

    <?php if (!current_user()): ?>
        <p class="hero-cta">
            <a href="<?= e(url('register.php')) ?>" class="btn">Crea un account</a>
            <a href="<?= e(url('login.php')) ?>" class="btn btn-ghost">Ho già un account</a>
        </p>
    <?php endif; ?>
</section>

<?php foreach ($sections as $type => $section): ?>
    <section class="home-section">
        <div class="section-head">
            <h2>Top <?= e($section['label']) ?></h2>
            <a href="<?= e(url('catalog.php?type=' . $type . '&sort=score')) ?>">Vedi classifica →</a>
        </div>
        <div class="grid">
            <?php foreach ($section['top'] as $item): ?>
                <?= render('partials/card', ['item' => $item, 'type' => $type]) ?>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="home-section">
        <div class="section-head">
            <h2><?= e($section['label']) ?> più recenti</h2>
            <a href="<?= e(url('catalog.php?type=' . $type . '&sort=year')) ?>">Vedi tutti →</a>
        </div>
        <div class="grid">
            <?php foreach ($section['latest'] as $item): ?>
                <?= render('partials/card', ['item' => $item, 'type' => $type]) ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>

<?php require __DIR__ . '/../src/views/footer.php'; ?>
