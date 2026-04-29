<div class="wiki-crumbs">
  <a href="/">Codex</a><span class="sep">›</span>
  <a href="/wiki/<?= htmlspecialchars($page['slug']) ?>"><?= htmlspecialchars($page['title']) ?></a>
  <span class="sep">›</span><span class="now">History</span>
</div>
<div style="padding:32px;">
  <h2 style="font-family:var(--f-display);font-size:24px;margin:0 0 20px;">Revision History</h2>
  <ul class="wiki-list">
    <?php foreach ($revs as $rev): ?>
    <li>
      <span><?= htmlspecialchars($rev['display_name'] ?? 'Unknown') ?></span>
      <span class="when"><?= htmlspecialchars(date('j M Y H:i', strtotime($rev['created_at']))) ?></span>
      <?php if ($rev['comment']): ?>
      <span class="desc"><?= htmlspecialchars($rev['comment']) ?></span>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>
  <p style="margin-top:16px;"><a href="/wiki/<?= htmlspecialchars($page['slug']) ?>">← Back to article</a></p>
</div>
