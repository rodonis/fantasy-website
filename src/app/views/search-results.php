<?php if (empty($results)): ?>
<p style="padding:12px 18px;color:var(--ink-mute);font-style:italic;font-size:13px;">No results for <b><?= htmlspecialchars($q) ?></b></p>
<?php else: ?>
<ul class="wiki-list">
  <?php foreach ($results as $r): ?>
  <li>
    <a href="/wiki/<?= htmlspecialchars($r['slug']) ?>"><?= htmlspecialchars($r['title']) ?></a>
    <span class="when"><?= htmlspecialchars(ucfirst($r['category'] ?? '')) ?></span>
  </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
