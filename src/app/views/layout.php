<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title><?= htmlspecialchars($page['title'] ?? 'Codex of the Broken Pantheon') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=IM+Fell+English+SC&family=UnifrakturCook:wght@700&family=Source+Serif+4:ital,wght@0,400;0,500;0,600;1,400&family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="/assets/wiki-styles.css" />
<link rel="stylesheet" href="/assets/wiki-extra.css" />
<script src="/assets/htmx.min.js" defer></script>
<script>
(function(){
  var STORAGE_KEY = 'wiki_sidebar';
  var MOBILE_BP   = 768;

  function isMobile() { return window.innerWidth <= MOBILE_BP; }

  function applyState(wiki, state) {
    wiki.dataset.sidebar = state;
  }

  document.addEventListener('DOMContentLoaded', function(){
    var wiki   = document.getElementById('wiki-root');
    var toggle = document.getElementById('sidebar-toggle');
    var scrim  = document.getElementById('sidebar-scrim');
    if (!wiki || !toggle) return;

    // Initial state: desktop reads saved pref (default open), mobile starts closed
    var saved = localStorage.getItem(STORAGE_KEY) || 'open';
    applyState(wiki, isMobile() ? 'closed' : saved);

    toggle.addEventListener('click', function(){
      var next = wiki.dataset.sidebar === 'open' ? 'closed' : 'open';
      applyState(wiki, next);
      if (!isMobile()) localStorage.setItem(STORAGE_KEY, next);
    });

    // Scrim closes drawer on mobile
    if (scrim) {
      scrim.addEventListener('click', function(){
        applyState(wiki, 'closed');
      });
    }

    // On resize: switch between modes without flicker
    window.addEventListener('resize', function(){
      if (!isMobile()) {
        applyState(wiki, localStorage.getItem(STORAGE_KEY) || 'open');
      } else if (wiki.dataset.sidebar === 'open') {
        // leave open if user opened it on mobile
      }
    });
  });
})();
</script>
</head>
<body>
<div class="wiki"
     data-theme="<?= htmlspecialchars($tweaks['theme']) ?>"
     data-density="<?= htmlspecialchars($tweaks['density']) ?>"
     data-font="<?= htmlspecialchars($tweaks['font']) ?>"
     id="wiki-root"
     hx-headers='{"X-Wiki-Request": "1"}'>
<?php require __DIR__ . '/partials/topbar.php'; ?>
<div id="sidebar-scrim" class="wiki-side-scrim"></div>
<div class="wiki-shell">
<?php require __DIR__ . '/partials/sidebar.php'; ?>
<main class="wiki-main">
