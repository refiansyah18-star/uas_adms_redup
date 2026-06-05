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

  <nav class="nav">
    <a class="<?= $active === 'dashboard' ? 'active' : '' ?>" href="/dashboard.php">Dashboard</a>
    <a class="<?= $active === 'watchlist' ? 'active' : '' ?>" href="/watchlist.php">Watchlist</a>
    <a class="<?= $active === 'tambah' ? 'active' : '' ?>" href="/tambah.php">Koleksi Baru</a>
    <a class="<?= $active === 'tentang' ? 'active' : '' ?>" href="/tentang.php">Tentang</a>
  </nav>

  <a class="top-cta" href="/tambah.php">
    <span class="material-symbols-outlined">add</span>
    Koleksi Baru
  </a>
</header>

<?php if ($flash = flash_get()): ?>
  <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>