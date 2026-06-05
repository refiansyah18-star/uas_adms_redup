<?php
session_start();
require_once __DIR__ . '/../app/config.php';

$db = db();
$featured = $db->query("SELECT * FROM watchlist ORDER BY rating DESC, id ASC LIMIT 3")->fetchAll();
$total = count_by($db, "SELECT COUNT(*) FROM watchlist");
$watching = count_by($db, "SELECT COUNT(*) FROM watchlist WHERE status = 'Watching'");
$completed = count_by($db, "SELECT COUNT(*) FROM watchlist WHERE status = 'Completed'");

$title = 'WatchVault - Kelola Film dan Anime';
$active = '';
require __DIR__ . '/../app/partials/header.php';
?>

<main class="container">
  <section class="landing-hero">
    <div>
      <div class="hero-actions">
        <a class="btn primary" href="/dashboard.php">
          <span class="material-symbols-outlined">dashboard</span>
          Buka Dashboard
        </a>
        <a class="btn secondary" href="/watchlist.php">
          <span class="material-symbols-outlined">movie_filter</span>
          Lihat Koleksi
        </a>
      </div>
    </div>

    <div class="hero-preview">
      <div class="poster-stack">
        <?php foreach ($featured as $index => $item): ?>
          <article class="<?= $index === 1 ? 'poster-large' : 'poster-mini' ?>">
            <img src="<?= e($item['poster_url'] ?: poster_fallback($item['title'])) ?>" alt="<?= e($item['title']) ?>">
            <div class="poster-info">
              <small class="badge <?= status_class($item['status']) ?>"><?= e($item['status']) ?></small>
              <strong><?= e($item['title']) ?></strong>
              <span><?= e($item['type']) ?> • <?= rating_text($item['rating']) ?>/10</span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="feature-grid">
    <article class="feature-card">
      <span class="material-symbols-outlined">dynamic_feed</span>
      <h3>Watchlist Dinamis</h3>
      <p>Data tersimpan di database dan dapat ditambah, diubah, dicari, serta dihapus kapan saja.</p>
    </article>
    <article class="feature-card">
      <span class="material-symbols-outlined">slow_motion_video</span>
      <h3>Progress Episode</h3>
      <p>Pantau jumlah episode yang sudah ditonton dengan progress bar yang jelas.</p>
    </article>
    <article class="feature-card">
      <span class="material-symbols-outlined">star</span>
      <h3>Rating & Statistik</h3>
      <p>Lihat rata-rata rating, status tontonan, dan distribusi tipe koleksi.</p>
    </article>
    <article class="feature-card">
      <span class="material-symbols-outlined">database</span>
      <h3>Database MariaDB</h3>
      <p>Seluruh koleksi berjalan sebagai aplikasi web dinamis dengan penyimpanan database.</p>
    </article>
  </section>

  <section class="cta-banner">
    <h2>Siap merapikan koleksimu?</h2>
    <p>Total <?= e($total) ?> judul tersimpan, <?= e($watching) ?> sedang ditonton, dan <?= e($completed) ?> selesai ditonton.</p>
    <div class="hero-actions" style="justify-content:center">
      <?php if (is_logged_in()): ?>
        <a class="btn primary" href="/tambah.php">Tambah Tontonan</a>
      <?php else: ?>
        <a class="btn primary" href="/login.php">
          <span class="material-symbols-outlined">login</span>
          Login
        </a>
      <?php endif; ?>
      <a class="btn secondary" href="/tentang.php">Pelajari Platform</a>
    </div>
  </section>
</main>

<?php require __DIR__ . '/../app/partials/footer.php'; ?>
