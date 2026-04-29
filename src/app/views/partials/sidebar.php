<?php
$currentSlug = $page['slug'] ?? '';
$categories = ['regions' => 'Regions', 'bestiary' => 'Bestiary', 'npcs' => 'NPCs', 'lore' => 'Lore', 'sessions' => 'Sessions'];
$gmWhere = Auth::isGm() ? '' : 'AND visibility = "public"';

$glyphs = [
    'home'   => '<path d="M2 7 L8 2 L14 7 V14 H2 Z"/>',
    'region' => '<path d="M2 12 L5 4 L9 8 L13 3 L14 14 Z"/>',
    'skull'  => '<path d="M3 7 C3 4 5 2 8 2 C11 2 13 4 13 7 V10 H10 V13 H6 V10 H3 Z"/><circle cx="6" cy="7" r="1" fill="currentColor" stroke="none"/><circle cx="10" cy="7" r="1" fill="currentColor" stroke="none"/>',
    'scroll' => '<path d="M2 4 H12 V12 H4 Q2 12 2 10 Z"/><path d="M12 4 Q14 4 14 6 V12"/><path d="M5 7 H10 M5 9 H9"/>',
    'crown'  => '<path d="M2 12 L3 5 L6 8 L8 4 L10 8 L13 5 L14 12 Z"/>',
    'flame'  => '<path d="M8 2 C5 6 4 8 4 11 C4 13 6 14 8 14 C10 14 12 13 12 11 C12 8 11 7 9 5 C9 7 8 8 7 8 C7 6 8 4 8 2 Z"/>',
    'star'   => '<path d="M8 2 L10 6 L14 7 L11 10 L12 14 L8 12 L4 14 L5 10 L2 7 L6 6 Z"/>',
    'pin'    => '<path d="M8 2 C5 2 3 4 3 7 C3 11 8 14 8 14 C8 14 13 11 13 7 C13 4 11 2 8 2 Z"/><circle cx="8" cy="7" r="1.6" fill="var(--paper-2)" stroke="none"/>',
];

function nav_svg(string $glyph, string $paths): string {
    return '<svg class="nav-glyph" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.1">' . $paths . '</svg>';
}

$catGlyphs = [
    'regions'  => 'region',
    'bestiary' => 'skull',
    'npcs'     => 'crown',
    'lore'     => 'scroll',
    'sessions' => 'flame',
];
?>
<nav class="wiki-side">
  <div class="wiki-side-section">
    <h4>The Codex</h4>
    <ul>
      <li><a href="/" <?= $currentSlug===''?'class="is-active"':'' ?>>
        <?= nav_svg('home', $glyphs['home']) ?> Front Page</a></li>
      <li><a href="/search"><?= nav_svg('scroll', $glyphs['scroll']) ?> Recent Changes</a></li>
    </ul>
  </div>

  <?php foreach ($categories as $catKey => $catLabel):
    $glyphKey = $catGlyphs[$catKey] ?? 'star';
    $pages = Db::all("SELECT slug, title FROM pages WHERE category = ? $gmWhere ORDER BY title LIMIT 20", [$catKey]);
    if (!$pages) continue;
  ?>
  <div class="wiki-side-section">
    <h4><?= htmlspecialchars($catLabel) ?></h4>
    <ul>
      <?php foreach ($pages as $navPage): ?>
      <li><a href="/wiki/<?= htmlspecialchars($navPage['slug']) ?>"
             <?= $navPage['slug']===$currentSlug?'class="is-active"':'' ?>>
        <?= nav_svg($glyphKey, $glyphs[$glyphKey]) ?>
        <?= htmlspecialchars($navPage['title']) ?>
      </a></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endforeach; ?>

  <?php if (Auth::isLoggedIn()): ?>
  <div class="wiki-side-foot">
    Logged in as <b><?= htmlspecialchars(Auth::user()['display_name'] ?? '') ?></b>
    · <?= htmlspecialchars(strtoupper(Auth::role())) ?>
  </div>
  <?php endif; ?>
</nav>
