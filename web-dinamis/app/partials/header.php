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
    <div style="display:flex;align-items:center;gap:.75rem">
      <span style="font-size:.85rem;color:var(--on-surface-variant);font-weight:500;">
        <span class="material-symbols-outlined" style="font-size:1.1rem;vertical-align:-3px;margin-right:2px;">person</span>
        <?= e(current_user()['full_name'] ?: current_user()['username']) ?>
      </span>
      <a class="top-cta" href="/logout.php" style="background:rgba(255,180,171,.1);color:var(--error);border:1px solid rgba(255,180,171,.2);">
        <span class="material-symbols-outlined" style="font-size:1rem;">logout</span>
        Keluar
      </a>
    </div>
  <?php else: ?>
    <a class="top-cta" href="/login.php" style="background:var(--primary);color:var(--on-primary);">
      <span class="material-symbols-outlined" style="font-size:1.1rem;">login</span>
      Login
    </a>
  <?php endif; ?>
</header>

<?php if ($flash = flash_get()): ?>
  <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>