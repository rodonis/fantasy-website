<form action="/tweaks" method="post" hx-post="/tweaks" hx-swap="none" hx-on::after-request="window.location.reload()"
      style="position:absolute;right:0;top:100%;background:var(--paper-2);border:1px solid var(--rule);padding:16px 18px;min-width:220px;z-index:20;box-shadow:var(--shadow);">

  <div style="font-family:var(--f-display);font-size:10px;letter-spacing:.22em;text-transform:uppercase;color:var(--ink-mute);margin-bottom:10px;">Display</div>

  <fieldset style="border:none;padding:0;margin:0 0 12px;">
    <legend style="font-family:var(--f-display);font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--ink-mute);margin-bottom:6px;">Theme</legend>
    <?php foreach (['parchment'=>'Parchment','dark'=>'Candle','light'=>'Vellum'] as $v=>$l): ?>
    <label style="display:flex;align-items:center;gap:6px;margin-bottom:4px;font-size:13px;cursor:pointer;">
      <input type="radio" name="theme" value="<?= $v ?>" <?= $tweaks['theme']===$v?'checked':'' ?>
             onchange="this.form.requestSubmit()" />
      <?= $l ?>
    </label>
    <?php endforeach; ?>
  </fieldset>

  <fieldset style="border:none;padding:0;margin:0 0 12px;">
    <legend style="font-family:var(--f-display);font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--ink-mute);margin-bottom:6px;">Density</legend>
    <?php foreach (['compact'=>'Compact','cozy'=>'Cozy','comfortable'=>'Airy'] as $v=>$l): ?>
    <label style="display:flex;align-items:center;gap:6px;margin-bottom:4px;font-size:13px;cursor:pointer;">
      <input type="radio" name="density" value="<?= $v ?>" <?= $tweaks['density']===$v?'checked':'' ?>
             onchange="this.form.requestSubmit()" />
      <?= $l ?>
    </label>
    <?php endforeach; ?>
  </fieldset>

  <fieldset style="border:none;padding:0;margin:0 0 12px;">
    <legend style="font-family:var(--f-display);font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--ink-mute);margin-bottom:6px;">Font</legend>
    <?php foreach (['serif'=>'Garamond','uncial'=>'Gothic','modern'=>'Source Serif','humanist'=>'Lora'] as $v=>$l): ?>
    <label style="display:flex;align-items:center;gap:6px;margin-bottom:4px;font-size:13px;cursor:pointer;">
      <input type="radio" name="font" value="<?= $v ?>" <?= $tweaks['font']===$v?'checked':'' ?>
             onchange="this.form.requestSubmit()" />
      <?= $l ?>
    </label>
    <?php endforeach; ?>
  </fieldset>

  <?php if (Auth::isGm()): ?>
  <fieldset style="border:none;padding:0;margin:0;">
    <legend style="font-family:var(--f-display);font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--gm-edge);margin-bottom:6px;">GM Tools</legend>
    <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
      <input type="checkbox" name="gmReveal" value="1"
             <?= $tweaks['gmReveal'] ? 'checked' : '' ?>
             onchange="this.form.requestSubmit()" />
      Reveal GM sections
    </label>
  </fieldset>
  <?php endif; ?>
</form>
