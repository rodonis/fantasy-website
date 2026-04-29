<?php
$stats = [
    'entries' => Db::one('SELECT COUNT(*) AS n FROM pages')['n'] ?? 0,
    'maps'    => Db::one("SELECT COUNT(*) AS n FROM pages WHERE body_md LIKE '%::: map%'")['n'] ?? 0,
    'users'   => Db::one('SELECT COUNT(*) AS n FROM users')['n'] ?? 0,
];
?>
<section class="wiki-hero">
  <div class="wiki-eyebrow"><span class="dash"></span>Pathfinder 1e Campaign Wiki</div>
  <h1 class="wiki-h1">Codex of the <em>Broken Pantheon</em></h1>
  <p class="wiki-lede">
    A wounded record of the Sundered World, set down by surviving loremasters
    after the gods turned their faces from us. Within these pages: ruined holds,
    petty tyrants, hungry relics, and the long roads that bind them.
  </p>
  <div class="wiki-hero-meta">
    <span><?= $stats['entries'] ?> entries</span>
    <span><?= $stats['maps'] ?> maps</span>
    <span><?= $stats['users'] ?> contributors</span>
  </div>
</section>

<section class="wiki-grid">
  <div>
    <?php if ($featured): ?>
    <h3 class="wiki-section-title">Featured Entry</h3>
    <article class="wiki-card">
      <div class="meta"><?= htmlspecialchars(ucfirst($featured['category'] ?? '')) ?></div>
      <h3><?= htmlspecialchars($featured['title']) ?></h3>
      <p><?php
        preg_match('/^.{1,300}\b/su', strip_tags((new Markdown(false))->text($featured['body_md'])), $m);
        echo htmlspecialchars(($m[0] ?? '') . '…');
      ?></p>
      <a class="more" href="/wiki/<?= htmlspecialchars($featured['slug']) ?>">Read the entry →</a>
    </article>
    <?php endif; ?>

    <h3 class="wiki-section-title">Recent Changes</h3>
    <ul class="wiki-list">
      <?php foreach ($recent as $r): ?>
      <li>
        <a href="/wiki/<?= htmlspecialchars($r['slug']) ?>"><?= htmlspecialchars($r['title']) ?></a>
        <span class="when"><?= htmlspecialchars(date('j M Y', strtotime($r['updated_at']))) ?></span>
        <span class="desc"><?= htmlspecialchars(ucfirst($r['category'] ?? 'lore')) ?></span>
      </li>
      <?php endforeach; ?>
      <?php if (!$recent): ?>
      <li><span style="color:var(--ink-mute);font-style:italic;">No pages yet. <?php if(Auth::isLoggedIn()):?><a href="/wiki/welcome/create">Create the first page →</a><?php endif;?></span></li>
      <?php endif; ?>
    </ul>
  </div>

  <div>
    <h3 class="wiki-section-title">Pillars of the Codex</h3>
    <div class="wiki-pillars">
      <?php
      $pillars = [
          ['regions',  'Regions',  '<path d="M4 22 L8 8 L14 14 L20 6 L24 22 Z"/>'],
          ['bestiary', 'Bestiary', '<path d="M5 12 C5 6 9 4 14 4 C19 4 23 6 23 12 V20 H17 V24 H11 V20 H5 Z"/><circle cx="11" cy="13" r="1.5" fill="currentColor"/><circle cx="17" cy="13" r="1.5" fill="currentColor"/>'],
          ['lore',     'Lore',     '<path d="M14 3 L17 11 L25 13 L19 19 L21 27 L14 22 L7 27 L9 19 L3 13 L11 11 Z"/>'],
      ];
      foreach ($pillars as [$pillarCat, $pillarLabel, $svgPath]):
          $count = Db::one("SELECT COUNT(*) AS n FROM pages WHERE category = ?", [$pillarCat])['n'] ?? 0;
      ?>
      <div class="wiki-pillar">
        <svg class="glyph" viewBox="0 0 28 28" fill="none" stroke="currentColor" stroke-width="1.1">
          <?= $svgPath ?>
        </svg>
        <b><?= $pillarLabel ?></b><small><?= $count ?> entries</small>
      </div>
      <?php endforeach; ?>
    </div>

    <h3 class="wiki-section-title">Atlas</h3>
    <div class="wiki-map">
      <svg viewBox="0 0 400 225" preserveAspectRatio="none">
        <path d="M50 60 Q100 40 150 70 T260 80 Q320 90 360 60" stroke="rgba(70,50,30,0.35)" stroke-width="0.6" fill="none" stroke-dasharray="3 2"/>
        <path d="M40 160 Q120 140 200 170 T380 150" stroke="rgba(70,50,30,0.35)" stroke-width="0.6" fill="none" stroke-dasharray="3 2"/>
        <?php
        $mapPages = Db::all("SELECT slug, title FROM pages WHERE category IN ('regions','npcs') AND visibility='public' ORDER BY RAND() LIMIT 8");
        $coords = [[100,80],[220,120],[310,170],[160,180],[80,140],[250,60],[340,110],[140,150]];
        foreach ($mapPages as $i => $mp):
            if (!isset($coords[$i])) break;
            [$cx, $cy] = $coords[$i];
        ?>
        <g class="pin">
          <circle class="dot" cx="<?=$cx?>" cy="<?=$cy?>" r="2.5"/>
          <text x="<?=$cx?>" y="<?=$cy-8?>"><?= htmlspecialchars($mp['title']) ?></text>
        </g>
        <?php endforeach; ?>
      </svg>
      <svg class="compass" viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="0.8">
        <circle cx="28" cy="28" r="22"/>
        <path d="M28 8 L31 28 L28 48 L25 28 Z" fill="currentColor" fill-opacity="0.4"/>
        <path d="M8 28 L28 31 L48 28 L28 25 Z"/>
        <text x="28" y="6" text-anchor="middle" font-size="6" fill="currentColor" stroke="none" font-family="serif">N</text>
      </svg>
      <div class="scale"><i></i> 50 leagues</div>
    </div>
    <p class="wiki-figcap">A rough sketching of the known world.</p>
  </div>
</section>
