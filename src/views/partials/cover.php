<?php
/** @var array $item  (id, title, cover_url) */
/** @var string $size  'small' | 'medium' | 'large' */
$size ??= 'medium';
$initials = '';
foreach (preg_split('/[\s:;\-]+/u', $item['title'], -1, PREG_SPLIT_NO_EMPTY) as $word) {
    $initials .= mb_strtoupper(mb_substr($word, 0, 1));
    if (mb_strlen($initials) >= 2) {
        break;
    }
}
$hue = ((int) $item['id'] * 47) % 360;
?>
<?php if (!empty($item['cover_url'])): ?>
    <img class="cover cover-<?= e($size) ?>" src="<?= e($item['cover_url']) ?>" alt="Copertina di <?= e($item['title']) ?>" loading="lazy">
<?php else: ?>
    <div class="cover cover-<?= e($size) ?> cover-placeholder" style="--hue: <?= $hue ?>" aria-hidden="true">
        <span><?= e($initials) ?></span>
    </div>
<?php endif; ?>
