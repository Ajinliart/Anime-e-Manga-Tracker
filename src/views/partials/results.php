<?php
/**
 * Griglia risultati + paginazione. Usato sia da catalog.php sia da api/search.php.
 * @var string $type
 * @var array  $items
 * @var int    $total
 * @var int    $page
 * @var int    $perPage
 * @var array  $filters  (q, genre, sort) per costruire i link di paginazione
 */
$pages = max(1, (int) ceil($total / $perPage));
?>
<p class="results-count">
    <?= $total === 1 ? '1 risultato' : e($total) . ' risultati' ?>
</p>

<?php if ($items): ?>
    <div class="grid">
        <?php foreach ($items as $item): ?>
            <?= render('partials/card', ['item' => $item, 'type' => $type]) ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <p>Nessun titolo trovato. Prova a cambiare ricerca o filtri.</p>
    </div>
<?php endif; ?>

<?php if ($pages > 1): ?>
    <nav class="pagination" aria-label="Pagine">
        <?php for ($p = 1; $p <= $pages; $p++):
            $query = http_build_query(array_filter([
                'type'  => $type,
                'q'     => $filters['q'] ?? '',
                'genre' => $filters['genre'] ?? null,
                'sort'  => $filters['sort'] ?? '',
                'page'  => $p > 1 ? $p : null,
            ]));
        ?>
            <?php if ($p === $page): ?>
                <span class="page current"><?= $p ?></span>
            <?php else: ?>
                <a class="page" href="<?= e(url('catalog.php?' . $query)) ?>"><?= $p ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </nav>
<?php endif; ?>
