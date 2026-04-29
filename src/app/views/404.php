<?php $tweaks = $tweaks ?? get_tweaks(); ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8" />
<title>Not Found — Codex</title>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<link href="https://fonts.googleapis.com/css2?family=IM+Fell+English+SC&family=EB+Garamond:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="/assets/wiki-styles.css"/>
</head><body>
<div class="wiki" data-theme="<?= htmlspecialchars($tweaks['theme'] ?? 'parchment') ?>"
     data-density="cozy" data-font="serif"
     style="place-items:center;display:flex;flex-direction:column;justify-content:center;height:100vh;text-align:center;">
  <div style="font-family:var(--f-display);font-size:11px;letter-spacing:.3em;text-transform:uppercase;color:var(--accent);margin-bottom:14px;">Page Not Found</div>
  <h1 style="font-family:var(--f-display);font-size:52px;margin:0 0 14px;">Lost in the Codex</h1>
  <p style="font-family:var(--f-serif);font-style:italic;color:var(--ink-mute);max-width:400px;margin:0 0 24px;">
    This page was never written, or was consumed by the Sundering. Perhaps the loremasters will record it in time.
  </p>
  <a href="/" style="color:var(--accent);font-family:var(--f-display);letter-spacing:.14em;text-transform:uppercase;font-size:13px;">Return to the Codex →</a>
  <?php if (Auth::isLoggedIn() && isset($_SERVER['REQUEST_URI'])): ?>
  <?php
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (preg_match('#^/wiki/([^/]+)$#', $uri, $m)):
  ?>
  <p style="margin-top:16px;"><a href="/wiki/<?= htmlspecialchars($m[1]) ?>/create"
     style="color:var(--ink-mute);font-size:13px;">Create this page →</a></p>
  <?php endif; endif; ?>
</div>
</body></html>
