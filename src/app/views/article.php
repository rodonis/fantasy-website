<?php require __DIR__ . '/partials/crumbs.php'; ?>

<article class="wiki-article">
  <div>
    <header class="wiki-article-head">
      <div class="wiki-article-eyebrow">
        <?= htmlspecialchars(ucfirst($page['category'] ?? 'lore')) ?>
        <?php if ($page['visibility'] === 'gm'): ?>
          · <span style="color:var(--gm-edge);">GM Only</span>
        <?php endif; ?>
      </div>
      <h1 class="wiki-article-title"><?= htmlspecialchars($page['title']) ?></h1>

      <?php if ($tags): ?>
      <div class="wiki-tags">
        <?php foreach ($tags as $t): ?>
        <a class="wiki-tag" href="/search?q=<?= urlencode($t['tag']) ?>"><?= htmlspecialchars($t['tag']) ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="wiki-article-meta">
        <span><b>Updated</b> <?= htmlspecialchars(date('j M Y', strtotime($page['updated_at']))) ?></span>
        <?php if ($page['category']): ?>
        <span><b>Category</b> <?= htmlspecialchars(ucfirst($page['category'])) ?></span>
        <?php endif; ?>
      </div>
    </header>

    <div class="wiki-prose">
      <?= $html ?>

      <div hx-get="/wiki/<?= htmlspecialchars($page['slug']) ?>/backlinks"
           hx-trigger="load"
           hx-swap="outerHTML">
        <section class="wiki-backlinks">
          <h4>What links here</h4>
          <p style="color:var(--ink-mute);font-style:italic;font-size:12px;">Loading…</p>
        </section>
      </div>

      <footer class="wiki-article-foot">
        <span>Last updated <?= htmlspecialchars(date('j M Y', strtotime($page['updated_at']))) ?></span>
        <span>
          <?php if (Auth::isLoggedIn()): ?>
          <a href="/wiki/<?= htmlspecialchars($page['slug']) ?>/edit">Edit</a> ·
          <?php endif; ?>
          <a href="/wiki/<?= htmlspecialchars($page['slug']) ?>/history">History</a>
        </span>
      </footer>
    </div>
  </div>

  <?php if ($toc): ?>
  <aside class="wiki-toc">
    <h4>On this page</h4>
    <ol>
      <?php foreach ($toc as $entry): ?>
      <li <?= $entry['level'] === 3 ? 'class="sub"' : '' ?>>
        <a href="#<?= htmlspecialchars($entry['id']) ?>"><?= htmlspecialchars($entry['text']) ?></a>
      </li>
      <?php endforeach; ?>
    </ol>
  </aside>
  <?php endif; ?>
</article>
