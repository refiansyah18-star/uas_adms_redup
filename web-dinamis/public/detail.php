<?php
session_start();
require_once __DIR__ . '/../app/config.php';

$db = db();
$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM watchlist WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    flash_set('Data tontonan tidak ditemukan.', 'error');
    redirect_to('/watchlist.php');
}

$title = $item['title'] . ' - WatchVault';
$active = 'watchlist';
require __DIR__ . '/../app/partials/header.php';
?>

<section class="detail-hero">
  <div class="cover-image">
    <img src="<?= e($item['cover_url'] ?: $item['poster_url'] ?: cover_fallback($item['title'])) ?>" alt="<?= e($item['title']) ?>">
  </div>
  <div class="detail-content">
    <div class="detail-poster">
      <img src="<?= e($item['poster_url'] ?: poster_fallback($item['title'])) ?>" alt="<?= e($item['title']) ?>">
    </div>
    <div class="detail-title">
      <span class="badge <?= status_class($item['status']) ?>"><?= e($item['status']) ?></span>
      <h1><?= e($item['title']) ?></h1>
      <div class="meta-line">
        <span><?= e($item['type']) ?></span>
        <span>•</span>
        <span><?= e($item['genre']) ?></span>
        <span>•</span>
        <span><?= rating_text($item['rating']) ?>/10</span>
      </div>
      <div class="hero-actions">
        <a class="btn primary" href="/edit.php?id=<?= e($item['id']) ?>">Edit Tontonan</a>
        <a class="btn secondary" href="/watchlist.php">Kembali ke Daftar</a>
      </div>
    </div>
  </div>
</section>

<main class="detail-main">
  <section>
    <article class="detail-card">
      <h2>Progress Tontonan</h2>
      <p><?= e($item['watched_episodes']) ?> dari <?= e($item['total_episodes']) ?> episode sudah ditonton.</p>
      <div class="progress"><span style="width: <?= progress_percent($item) ?>%"></span></div>
    </article>

    <article class="detail-card">
      <h2>Catatan Koleksi</h2>
      <p><?= nl2br(e($item['notes'] ?: 'Belum ada catatan.')) ?></p>
    </article>
  </section>

  <aside class="detail-card">
    <h2>Metadata</h2>
    <div class="info-list">
      <div><span>Tipe</span><strong><?= e($item['type']) ?></strong></div>
      <div><span>Genre</span><strong><?= e($item['genre']) ?></strong></div>
      <div><span>Status</span><strong><?= e($item['status']) ?></strong></div>
      <div><span>Rating</span><strong><?= rating_text($item['rating']) ?>/10</strong></div>
      <div><span>Ditambahkan</span><strong><?= e(date('d M Y', strtotime($item['added_date']))) ?></strong></div>
    </div>
    <div class="hero-actions">
      <a class="btn secondary" href="/edit.php?id=<?= e($item['id']) ?>">Edit</a>
      <a class="btn danger" href="/hapus.php?id=<?= e($item['id']) ?>" onclick="return confirm('Hapus tontonan ini?')">Hapus</a>
    </div>
  </aside>
</main>

<?php require __DIR__ . '/../app/partials/footer.php'; ?>
