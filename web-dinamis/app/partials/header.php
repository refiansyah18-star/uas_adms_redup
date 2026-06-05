<?php $active = $active ?? ''; ?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'WatchVault') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="/index.php">
    <span class="brand-icon material-symbols-outlined">movie</span>
    <span>WatchVault</span>
  </a>

  <?php if (is_logged_in()): ?>
    <nav class="nav">
      <a class="<?= $active === 'dashboard' ? 'active' : '' ?>" href="/dashboard.php">Dashboard</a>
      <a class="<?= $active === 'watchlist' ? 'active' : '' ?>" href="/watchlist.php">Watchlist</a>
      <a class="<?= $active === 'tambah' ? 'active' : '' ?>" href="/tambah.php">Koleksi Baru</a>
      <a class="<?= $active === 'tentang' ? 'active' : '' ?>" href="/tentang.php">Tentang</a>
    </nav>
  <?php else: ?>
    <nav class="nav">
      <a class="<?= $active === 'tentang' ? 'active' : '' ?>" href="/tentang.php">Tentang</a>
    </nav>
  <?php endif; ?>

  <?php if (is_logged_in()): ?>
    <div style="display:flex;align-items:center;gap:1.5rem;justify-self:end;">
      <span style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);font-weight:800;">
        <span class="material-symbols-outlined" style="font-size:18px;">person</span>
        <?= e(current_user()['full_name'] ?: current_user()['username']) ?>
      </span>
      <a class="top-cta" href="/logout.php" style="background:rgba(255,180,171,.08);color:var(--red);border:1px solid rgba(255,180,171,.18);">
        <span class="material-symbols-outlined" style="font-size:18px;">logout</span>
        Keluar
      </a>
    </div>
  <?php else: ?>
    <a class="top-cta" href="/login.php" style="background:var(--surface-2);color:var(--text);border:1px solid var(--line);">
      <span class="material-symbols-outlined" style="font-size:18px;">login</span>
      Login
    </a>
  <?php endif; ?>
</header>

<?php if ($flash = flash_get()): ?>
  <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>