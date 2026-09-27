<?php
/** @var array $item */
/** @var string $type */
?>
<a class="card" href="<?= e(url('detail.php?type=' . $type . '&id=' . $item['id'])) ?>">
    <?= render('partials/cover', ['item' => $item, 'size' => 'medium']) ?>
    <div class="card-body">
        <h3 class="card-title"><?= e($item['title']) ?></h3>
        <div class="card-meta">
            <span><?= e($item['year'] ?? '—') ?></span>
            <span class="score-badge" title="<?= e($item['votes']) ?> voti">★ <?= e(format_score($item['avg_score'])) ?></span>
        </div>
    </div>
</a>
