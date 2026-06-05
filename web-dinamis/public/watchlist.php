<?php
session_start();
require_once __DIR__ . '/../app/config.php';

$db = db();
$q = trim($_GET['q'] ?? '');
$type = trim($_GET['type'] ?? '');
$status = trim($_GET['status'] ?? '');
$genre = trim($_GET['genre'] ?? '');

$sql = "SELECT * FROM watchlist WHERE 1=1";
$params = [];

if ($q !== '') {
    $sql .= " AND title LIKE ?";
    $params[] = "%{$q}%";
}
if ($type !== '') {
    $sql .= " AND type = ?";
    $params[] = $type;
}
if ($status !== '') {
    $sql .= " AND status = ?";
    $params[] = $status;
}
if ($genre !== '') {
    $sql .= " AND genre = ?";
    $params[] = $genre;
}

$sql .= " ORDER BY added_date DESC, id DESC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();
$genres = $db->query("SELECT DISTINCT genre FROM watchlist ORDER BY genre")->fetchAll(PDO::FETCH_COLUMN);

$title = 'Watchlist - WatchVault';
$active = 'watchlist';
require __DIR__ . '/../app/partials/header.php';
?>

<main class="container">
  <section class="page-head">
    <div class="page-title">
      <span class="eyebrow">Koleksi</span>
      <h1>Daftar Watchlist</h1>
      <p>Cari, filter, dan kelola semua film atau anime yang tersimpan di database.</p>
    </div>
    <a class="btn primary" href="/tambah.php">Tambah Baru</a>
  </section>

  <section class="glass-panel filter-panel">
    <form class="filter-form" method="GET" action="/watchlist.php">
      <label>Cari judul
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari judul...">
      </label>
      <label>Tipe
        <select name="type">
          <option value="">Semua</option>
          <?php foreach (['Anime','Movie','Series'] as $option): ?>
            <option value="<?= e($option) ?>" <?= $type === $option ? 'selected' : '' ?>><?= e($option) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Status
        <select name="status">
          <option value="">Semua</option>
          <?php foreach (['Plan to Watch','Watching','Completed','Dropped'] as $option): ?>
            <option value="<?= e($option) ?>" <?= $status === $option ? 'selected' : '' ?>><?= e($option) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Genre
        <select name="genre">
          <option value="">Semua</option>
          <?php foreach ($genres as $option): ?>
            <option value="<?= e($option) ?>" <?= $genre === $option ? 'selected' : '' ?>><?= e($option) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button class="btn primary" type="submit">Terapkan</button>
    </form>
  </section>

  <section class="watch-grid">
    <?php foreach ($items as $item): ?>
      <article class="watch-card">
        <a class="poster-card" href="/detail.php?id=<?= e($item['id']) ?>">
          <img src="<?= e($item['poster_url'] ?: poster_fallback($item['title'])) ?>" alt="<?= e($item['title']) ?>">
          <span class="card-badge badge <?= status_class($item['status']) ?>"><?= e($item['type']) ?></span>
        </a>
        <div class="card-body">
          <h3><?= e($item['title']) ?></h3>
          <div class="meta-line">
            <span><?= e($item['genre']) ?></span>
            <span>•</span>
            <span><?= rating_text($item['rating']) ?>/10</span>
          </div>
          <div style="margin:12px 0">
            <span class="badge <?= status_class($item['status']) ?>"><?= e($item['status']) ?></span>
          </div>
          <small><?= e($item['watched_episodes']) ?>/<?= e($item['total_episodes']) ?> episode</small>
          <div class="progress"><span style="width: <?= progress_percent($item) ?>%"></span></div>

          <div class="card-actions">
            <a class="icon-btn" href="/detail.php?id=<?= e($item['id']) ?>">Detail</a>
            <a class="icon-btn" href="/edit.php?id=<?= e($item['id']) ?>">Edit</a>
            <a class="icon-btn" href="/hapus.php?id=<?= e($item['id']) ?>" onclick="return confirm('Hapus tontonan ini?')">
              <span class="material-symbols-outlined">delete</span>
            </a>
          </div>
        </div>
      </article>
    <?php endforeach; ?>

    <?php if (count($items) === 0): ?>
      <div class="panel"><p>Data tidak ditemukan.</p></div>
    <?php endif; ?>
  </section>
</main>

<?php require __DIR__ . '/../app/partials/footer.php'; ?>
