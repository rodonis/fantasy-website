<div style="max-width:380px;margin:60px auto;padding:32px;background:var(--paper-2);border:1px solid var(--rule);position:relative;">
  <div style="position:absolute;inset:4px;border:1px solid var(--rule-soft);pointer-events:none;"></div>
  <h1 style="font-family:var(--f-display);font-size:26px;margin:0 0 22px;text-align:center;">Join the Codex</h1>
  <?php if (isset($error)): ?>
  <p style="background:var(--gm-bg);border:1px solid var(--gm-edge);padding:8px 12px;font-size:13px;color:var(--gm-edge);margin-bottom:16px;">
    <?= htmlspecialchars($error) ?>
  </p>
  <?php endif; ?>
  <form action="/register" method="post">
    <div style="margin-bottom:14px;">
      <label style="font-family:var(--f-display);font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--ink-mute);display:block;margin-bottom:4px;">Username</label>
      <input type="text" name="username" required autofocus minlength="2"
             style="width:100%;padding:8px 10px;background:var(--paper);border:1px solid var(--rule);font-family:var(--f-serif);font-size:14px;color:var(--ink);outline:none;box-sizing:border-box;" />
    </div>
    <div style="margin-bottom:14px;">
      <label style="font-family:var(--f-display);font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--ink-mute);display:block;margin-bottom:4px;">Password</label>
      <input type="password" name="password" required minlength="6"
             style="width:100%;padding:8px 10px;background:var(--paper);border:1px solid var(--rule);font-family:var(--f-serif);font-size:14px;color:var(--ink);outline:none;box-sizing:border-box;" />
    </div>
    <div style="margin-bottom:20px;">
      <label style="font-family:var(--f-display);font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--ink-mute);display:block;margin-bottom:4px;">Confirm Password</label>
      <input type="password" name="confirm" required
             style="width:100%;padding:8px 10px;background:var(--paper);border:1px solid var(--rule);font-family:var(--f-serif);font-size:14px;color:var(--ink);outline:none;box-sizing:border-box;" />
    </div>
    <button type="submit"
            style="width:100%;padding:10px;background:var(--accent);color:var(--paper);border:none;font-family:var(--f-display);font-size:13px;letter-spacing:.16em;text-transform:uppercase;cursor:pointer;">
      Create Account
    </button>
    <p style="text-align:center;margin-top:14px;font-size:12px;color:var(--ink-mute);">
      Already a member? <a href="/login" style="color:var(--accent);">Login</a>
    </p>
  </form>
</div>
