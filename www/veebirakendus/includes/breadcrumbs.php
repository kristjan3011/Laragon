<?php
// Ühine leivapuru: iga element sisaldab label-väärtust ja soovi korral sisemist url-i.
require_once __DIR__ . '/functions.php';
$breadcrumbs = $breadcrumbs ?? [];
?>
<?php if ($breadcrumbs !== []): ?>
    <nav aria-label="Leivapuru" class="mb-4">
        <ol class="breadcrumb mb-0">
            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                <?php $last = $index === array_key_last($breadcrumbs); ?>
                <li class="breadcrumb-item<?= $last ? ' active' : '' ?>"<?= $last ? ' aria-current="page"' : '' ?>>
                    <?php if (!$last && isset($crumb['url'])): ?>
                        <a href="<?= e($crumb['url']) ?>" class="text-decoration-none"><?= e($crumb['label']) ?></a>
                    <?php else: ?>
                        <?= e($crumb['label']) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
<?php endif; ?>
