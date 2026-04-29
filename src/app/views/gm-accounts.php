<div style="padding:24px 32px;max-width:900px;">
  <h1 style="margin:0 0 16px;">GM Accounts</h1>
  <?php if ($error): ?><p style="color:#a33;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <?php if ($success): ?><p style="color:#2a7a2a;"><?= htmlspecialchars($success) ?></p><?php endif; ?>

  <form action="/admin/gms" method="post" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:24px;">
    <input type="text" name="username" placeholder="username" required />
    <input type="text" name="display_name" placeholder="display name (optional)" />
    <input type="password" name="password" placeholder="password" required />
    <input type="password" name="confirm" placeholder="confirm password" required />
    <div><button type="submit">Create GM</button></div>
  </form>

  <h2>Existing GMs</h2>
  <ul>
  <?php foreach ($gms as $gm): ?>
    <li><?= htmlspecialchars($gm['display_name'] ?: $gm['username']) ?> (<?= htmlspecialchars($gm['username']) ?>)</li>
  <?php endforeach; ?>
  </ul>
</div>
