<?php if (!empty($breadcrumbs)): ?>
<div class="wiki-crumbs">
  <?php foreach ($breadcrumbs as $i => $crumb): ?>
    <?php if ($i > 0): ?><span class="sep">›</span><?php endif; ?>
    <?php if ($crumb['href']): ?>
      <a href="<?= htmlspecialchars($crumb['href']) ?>"><?= htmlspecialchars($crumb['label']) ?></a>
    <?php else: ?>
      <span class="now"><?= htmlspecialchars($crumb['label']) ?></span>
    <?php endif; ?>
  <?php endforeach; ?>
</div>
<?php endif; ?>
