<?php
$isNew = ($page === null);
$pageTags = !$isNew ? Db::all('SELECT tag FROM tags WHERE page_id = ? ORDER BY tag', [$page['id']]) : [];
// $slug is set by the controller before layout.php (and sidebar.php) runs
$saveUrl  = '/wiki/' . $slug . '/save';
$previewUrl = '/wiki/' . $slug . '/preview';
?>
<div class="wiki-crumbs">
  <a href="/">Codex</a><span class="sep">›</span>
  <?php if (!$isNew): ?>
  <a href="/wiki/<?= htmlspecialchars($slug) ?>"><?= htmlspecialchars($page['title']) ?></a>
  <span class="sep">›</span>
  <?php endif; ?>
  <span class="now"><?= $isNew ? 'New Page' : 'Edit' ?></span>
</div>

<div style="padding:24px 32px;max-width:1200px;">
  <form action="<?= $saveUrl ?>" method="post" id="edit-form">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
      <div>
        <label style="font-family:var(--f-display);font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--ink-mute);">Title</label>
        <input type="text" name="title" required
               value="<?= htmlspecialchars($page['title'] ?? '') ?>"
               style="width:100%;margin-top:4px;padding:8px 10px;background:var(--paper-2);border:1px solid var(--rule);font-family:var(--f-display);font-size:18px;color:var(--ink);outline:none;box-sizing:border-box;" />
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;align-items:end;">
        <div>
          <label style="font-family:var(--f-display);font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--ink-mute);">Category</label>
          <select name="category"
                  style="width:100%;margin-top:4px;padding:8px 10px;background:var(--paper-2);border:1px solid var(--rule);font-family:var(--f-serif);color:var(--ink);">
            <?php $allCategories = Db::all("SELECT DISTINCT category FROM pages WHERE category <> '' ORDER BY category"); ?>
            <?php foreach ($allCategories as $cat): $c = $cat['category']; ?>
            <option value="<?= htmlspecialchars($c) ?>" <?= ($page['category'] ?? '') === $c ? 'selected' : '' ?>>
              <?= htmlspecialchars(ucwords(str_replace(['-', '_'], ' ', $c))) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label style="font-family:var(--f-display);font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--ink-mute);">Tags</label>
          <input type="text" name="tags" placeholder="tag one, tag two" value="<?= htmlspecialchars(implode(', ', array_map(fn($t) => $t['tag'], $pageTags ?? []))) ?>"
                 style="width:100%;margin-top:4px;padding:8px 10px;background:var(--paper-2);border:1px solid var(--rule);font-family:var(--f-serif);color:var(--ink);" />
        </div>
        <?php if (Auth::isGm()): ?>
        <div>
          <label style="font-family:var(--f-display);font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--gm-edge);">Visibility</label>
          <select name="visibility"
                  style="width:100%;margin-top:4px;padding:8px 10px;background:var(--paper-2);border:1px solid var(--gm-edge);font-family:var(--f-serif);color:var(--ink);">
            <option value="public" <?= ($page['visibility'] ?? 'public') === 'public' ? 'selected' : '' ?>>Public</option>
            <option value="gm"     <?= ($page['visibility'] ?? '') === 'gm'     ? 'selected' : '' ?>>GM Only</option>
          </select>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Toolbar -->
    <div id="editor-toolbar" style="display:flex;flex-wrap:wrap;gap:4px;padding:6px 8px;background:var(--paper-2);border:1px solid var(--rule);border-bottom:none;">
      <button type="button" data-action="bold"       title="Bold"       class="wiki-iconbtn" style="width:auto;padding:3px 8px;font-weight:bold;font-family:var(--f-serif);">B</button>
      <button type="button" data-action="italic"     title="Italic"     class="wiki-iconbtn" style="width:auto;padding:3px 8px;font-style:italic;font-family:var(--f-serif);">I</button>
      <button type="button" data-action="h2"         title="Heading 2"  class="wiki-iconbtn" style="width:auto;padding:3px 8px;font-family:var(--f-display);">H2</button>
      <button type="button" data-action="h3"         title="Heading 3"  class="wiki-iconbtn" style="width:auto;padding:3px 8px;font-family:var(--f-display);">H3</button>
      <button type="button" data-action="link"       title="Wiki Link"  class="wiki-iconbtn" style="width:auto;padding:3px 8px;">[[Link]]</button>
      <button type="button" data-action="blockquote" title="Blockquote" class="wiki-iconbtn" style="width:auto;padding:3px 6px;">&ldquo;&rdquo;</button>
      <button type="button" data-action="ul"         title="List"       class="wiki-iconbtn" style="width:auto;padding:3px 8px;">— List</button>
      <span style="width:1px;background:var(--rule);margin:2px 4px;"></span>
      <button type="button" data-action="statblock"  title="Insert statblock" class="wiki-iconbtn" style="width:auto;padding:3px 8px;color:var(--accent);">Statblock</button>
      <button type="button" data-action="map"        title="Insert map"       class="wiki-iconbtn" style="width:auto;padding:3px 8px;color:var(--accent);">Map</button>
      <button type="button" data-action="gm"         title="Insert GM block"  class="wiki-iconbtn" style="width:auto;padding:3px 8px;color:var(--gm-edge);">GM</button>
      <span style="width:1px;background:var(--rule);margin:2px 4px;"></span>
      <label class="wiki-iconbtn" style="width:auto;padding:3px 8px;cursor:pointer;color:var(--ink-mute);">
        ↑ Image
        <input type="file" id="img-upload" accept="image/*" style="display:none;" />
      </label>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;border:1px solid var(--rule);">
      <textarea id="editor-body" name="body"
                hx-post="<?= $previewUrl ?>"
                hx-trigger="keyup changed delay:500ms"
                hx-target="#preview-pane"
                hx-swap="innerHTML"
                style="width:100%;height:500px;padding:16px;background:var(--paper);border:none;border-right:1px solid var(--rule-soft);font-family:var(--f-mono);font-size:13px;color:var(--ink);line-height:1.6;resize:vertical;outline:none;box-sizing:border-box;"
                ><?= htmlspecialchars($page['body_md'] ?? '') ?></textarea>
      <div id="preview-pane" class="wiki-prose"
           style="padding:16px;overflow-y:auto;background:var(--paper);min-height:500px;font-size:15px;">
        <p style="color:var(--ink-mute);font-style:italic;">Preview appears here as you type…</p>
      </div>
    </div>

    <div style="display:flex;align-items:center;gap:12px;margin-top:12px;">
      <input type="text" name="comment" placeholder="Edit summary (optional)"
             style="flex:1;padding:7px 10px;background:var(--paper-2);border:1px solid var(--rule);font-family:var(--f-serif);font-size:13px;color:var(--ink);outline:none;" />
      <button type="submit"
              style="padding:8px 22px;background:var(--accent);color:var(--paper);border:none;font-family:var(--f-display);font-size:13px;letter-spacing:.12em;text-transform:uppercase;cursor:pointer;">
        Save Page
      </button>
      <?php if (!$isNew): ?>
      <button type="submit" formaction="/wiki/<?= htmlspecialchars($slug) ?>/delete" formmethod="post" onclick="return confirm('Delete this page?')"
              style="padding:8px 16px;border:1px solid #a33;color:#a33;background:transparent;font-family:var(--f-display);font-size:13px;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;">Delete</button>
      <a href="/wiki/<?= htmlspecialchars($slug) ?>"
         style="padding:8px 16px;border:1px solid var(--rule);font-family:var(--f-display);font-size:13px;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;color:var(--ink-mute);">
        Cancel
      </a>
      <?php endif; ?>
    </div>
  </form>
</div>

<script src="/assets/editor.js" defer></script>
