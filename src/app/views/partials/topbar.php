<?php $user = Auth::user(); ?>
<header class="wiki-top">
  <button class="wiki-menu-btn" id="sidebar-toggle" title="Toggle sidebar" aria-label="Toggle sidebar">
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4">
      <path d="M2 4 H14 M2 8 H14 M2 12 H14"/>
    </svg>
  </button>
  <div class="wiki-mark">
    <div class="wiki-sigil">
      <svg width="38" height="38" viewBox="0 0 38 38" fill="none" stroke="currentColor" stroke-width="1">
        <circle cx="19" cy="19" r="14" />
        <circle cx="19" cy="19" r="10" stroke-dasharray="2 3" />
        <path d="M19 5 v8 M19 25 v8 M5 19 h8 M25 19 h8" />
        <path d="M19 11 L23 19 L19 27 L15 19 Z" fill="currentColor" fill-opacity="0.85" stroke="none" />
        <circle cx="19" cy="19" r="1.4" fill="var(--paper)" stroke="none" />
      </svg>
    </div>
    <a href="/" class="wiki-mark-text" style="text-decoration:none;color:inherit;">
      <b>Codex of the Broken Pantheon</b>
      <small>A Compendium of the Sundered World</small>
    </a>
  </div>

  <form class="wiki-search" action="/search" method="get"
        hx-get="/search" hx-trigger="input delay:300ms" hx-target="#search-results"
        hx-push-url="false" autocomplete="off">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4">
      <circle cx="7" cy="7" r="4.5"/><path d="M10.5 10.5 L14 14"/>
    </svg>
    <input type="search" name="q" placeholder="Search the codex…"
           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
           style="background:transparent;border:none;outline:none;font-family:var(--f-serif);font-style:italic;font-size:13px;color:var(--ink);flex:1;" />
    <kbd>/</kbd>
  </form>
  <div id="search-results" style="position:absolute;top:52px;left:240px;right:0;z-index:10;"></div>

  <div class="wiki-toolbar">
    <a class="wiki-iconbtn" href="/" title="Home">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.2">
        <path d="M2 7 L8 2 L14 7 V14 H2 Z"/>
      </svg>
    </a>
    <a class="wiki-iconbtn" title="Recent changes" href="/?recent=1">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.2">
        <circle cx="8" cy="8" r="6"/><path d="M8 4 V8 L11 10"/>
      </svg>
    </a>
    <?php if (Auth::isLoggedIn()): ?>
    <a class="wiki-iconbtn" title="New page" href="/wiki/new-page/create">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.2">
        <rect x="2" y="2" width="12" height="12" rx="1"/><path d="M8 5 V11 M5 8 H11"/>
      </svg>
    </a>
    <?php endif; ?>
    <?php if (Auth::isGm()): ?>
    <a class="wiki-iconbtn" title="Manage GMs" href="/admin/gms">GM</a>
    <?php endif; ?>
  </div>

  <?php if ($user): ?>
  <div class="wiki-user">
    <div class="wiki-user-avatar"><?= htmlspecialchars(strtoupper($user['display_name'][0] ?? '?')) ?></div>
    <div>
      <div class="wiki-user-name"><?= htmlspecialchars($user['display_name'] ?? $user['username']) ?></div>
      <div class="wiki-user-role"><?= htmlspecialchars(strtoupper($user['role'])) ?></div>
    </div>
    <a href="/logout" class="wiki-iconbtn" title="Logout" style="margin-left:4px;">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.3">
        <path d="M6 8 H14 M11 5 L14 8 L11 11 M10 3 H3 V13 H10"/>
      </svg>
    </a>
  </div>
  <?php else: ?>
  <a href="/login" class="wiki-iconbtn" title="Login" style="border:1px solid var(--rule-soft);padding:4px 10px;border-radius:2px;font-family:var(--f-display);font-size:12px;text-decoration:none;color:var(--ink-2);">Login</a>
  <?php endif; ?>

  <button class="wiki-iconbtn" id="theme-toggle" title="Toggle parchment/candle mode">
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.2">
      <circle cx="8" cy="8" r="3"/>
      <path d="M8 1 V3 M8 13 V15 M1 8 H3 M13 8 H15"/>
    </svg>
  </button>
</header>
<script>
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('theme-toggle');
    var root = document.getElementById('wiki-root');
    if (!btn || !root) return;
    btn.addEventListener('click', function(){
      var current = (root.dataset.theme === 'dark') ? 'candle' : 'parchment';
      var next = current === 'candle' ? 'parchment' : 'candle';
      root.dataset.theme = next === 'candle' ? 'dark' : 'parchment';
      localStorage.setItem('wiki_theme', next);
    });
  });
})();
</script>
