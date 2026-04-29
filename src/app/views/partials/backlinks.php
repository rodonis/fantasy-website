<?php if (!empty($links)): ?>
<section class="wiki-backlinks">
  <h4>What links here</h4>
  <ul>
    <?php foreach ($links as $l): ?>
    <li>
      <a href="/wiki/<?= htmlspecialchars($l['from_slug']) ?>">
        <?= htmlspecialchars($l['title'] ?? $l['from_slug']) ?>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>
