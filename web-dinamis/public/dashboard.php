<?php
session_start();
require_once __DIR__ . '/../app/config.php';

$db = db();

$total = count_by($db, "SELECT COUNT(*) FROM watchlist");
$watching = count_by($db, "SELECT COUNT(*) FROM watchlist WHERE status = 'Watching'");
$completed = count_by($db, "SELECT COUNT(*) FROM watchlist WHERE status = 'Completed'");
$avg = avg_rating($db);

$statusRows = $db->query("SELECT status, COUNT(*) AS total FROM watchlist GROUP BY status")->fetchAll();
$typeRows = $db->query("SELECT type, COUNT(*) AS total FROM watchlist GROUP BY type")->fetchAll();
$genreRows = $db->query("SELECT genre, COUNT(*) AS total FROM watchlist GROUP BY genre ORDER BY total DESC LIMIT 5")->fetchAll();
$continue = $db->query("SELECT * FROM watchlist WHERE status = 'Watching' ORDER BY watched_episodes DESC LIMIT 2")->fetchAll();
$top = $db->query("SELECT * FROM watchlist ORDER BY rating DESC, id ASC LIMIT 5")->fetchAll();
$recent = $db->query("SELECT * FROM watchlist ORDER BY added_date DESC, id DESC LIMIT 6")->fetchAll();

$title = 'Dashboard - WatchVault';
$active = 'dashboard';
require __DIR__ . '/../app/partials/header.php';
?>

<main class="container">
  <section class="dashboard-head">
    <div class="page-title">
      <span class="eyebrow">Dashboard</span>
      <h1>Halo, Redup</h1>
      <p>Ringkasan koleksi film dan anime yang tersimpan di WatchVault.</p>
    </div>
    <a class="btn primary" href="/tambah.php">
      <span class="material-symbols-outlined">add</span>
      Tambah Tontonan
    </a>
  </section>

  <section class="stats-grid">
    <article class="stat-card"><span>Total Judul</span><strong><?= e($total) ?></strong><small>Semua koleksi</small></article>
    <article class="stat-card"><span>Sedang Ditonton</span><strong><?= e($watching) ?></strong><small>Progress aktif</small></article>
    <article class="stat-card"><span>Selesai Ditonton</span><strong><?= e($completed) ?></strong><small>Koleksi selesai</small></article>
    <article class="stat-card"><span>Rata-rata Rating</span><strong><?= e($avg) ?></strong><small>Dari rating aktif</small></article>
  </section>

  <section class="dash-grid">
    <div>
      <div class="panel">
        <h2>Status Menonton</h2>
        <p>Distribusi koleksi berdasarkan status watchlist.</p>
        <div class="chart-wrap"><canvas id="statusChart"></canvas></div>
      </div>

            <div class="panel">
        <h2>Genre Populer</h2>
        <p>Genre yang paling banyak muncul di koleksi WatchVault.</p>

        <div class="genre-list">
          <?php foreach ($genreRows as $genre): ?>
            <div class="genre-row">
              <span><?= e($genre['genre']) ?></span>
              <strong><?= e($genre['total']) ?> judul</strong>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="panel collection-goal">
        <div>
          <span class="eyebrow">Target Koleksi</span>
          <h2>Selesaikan lebih banyak tontonan bulan ini.</h2>
          <p>
            Saat ini ada <?= e($completed) ?> judul selesai dari total <?= e($total) ?> koleksi.
            Lanjutkan tontonan aktif supaya progress koleksi makin rapi.
          </p>
        </div>

        <a class="btn secondary" href="/watchlist.php">Kelola Watchlist</a>
      </div>
    </div>

    <div>
      <div class="panel">
        <h2>Lanjutkan Menonton</h2>
        <div class="media-row">
          <?php foreach ($continue as $item): ?>
            <a class="landscape-card" href="/detail.php?id=<?= e($item['id']) ?>">
              <img src="<?= e($item['cover_url'] ?: $item['poster_url'] ?: cover_fallback($item['title'])) ?>" alt="<?= e($item['title']) ?>">
              <div class="card-overlay">
                <small class="badge <?= status_class($item['status']) ?>"><?= e($item['status']) ?></small>
                <strong><?= e($item['title']) ?></strong>
                <span><?= e($item['watched_episodes']) ?>/<?= e($item['total_episodes']) ?> episode</span>
                <div class="progress"><span style="width: <?= progress_percent($item) ?>%"></span></div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="panel">
        <h2>Aktivitas Tontonan</h2>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Judul</th><th>Tipe</th><th>Status</th><th>Rating</th></tr></thead>
            <tbody>
              <?php foreach ($recent as $item): ?>
                <tr>
                  <td><strong><?= e($item['title']) ?></strong><small><?= e($item['genre']) ?></small></td>
                  <td><?= e($item['type']) ?></td>
                  <td><span class="badge <?= status_class($item['status']) ?>"><?= e($item['status']) ?></span></td>
                  <td><?= rating_text($item['rating']) ?>/10</td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="panel">
        <h2>Top Rated</h2>
        <?php foreach ($top as $item): ?>
          <div class="info-list" style="margin-bottom:10px">
            <div>
              <span><?= e($item['title']) ?></span>
              <strong><?= rating_text($item['rating']) ?>/10</strong>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const statusLabels = <?= json_encode(array_column($statusRows, 'status')) ?>;
const statusValues = <?= json_encode(array_map('intval', array_column($statusRows, 'total'))) ?>;
const typeLabels = <?= json_encode(array_column($typeRows, 'type')) ?>;
const typeValues = <?= json_encode(array_map('intval', array_column($typeRows, 'total'))) ?>;

const chartText = '#E6E1E5';
const grid = 'rgba(255,255,255,.08)';

new Chart(document.getElementById('statusChart'), {
  type: 'doughnut',
  data: {
    labels: statusLabels,
    datasets: [{
      data: statusValues,
      backgroundColor: ['#4CD7F6', '#4EDEA3', '#D0BCFF', '#FFB4AB'],
      borderColor: '#111318',
      borderWidth: 4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '68%',
    plugins: { legend: { position: 'bottom', labels: { color: chartText } } }
  }
});

new Chart(document.getElementById('typeChart'), {
  type: 'bar',
  data: {
    labels: typeLabels,
    datasets: [{
      label: 'Jumlah',
      data: typeValues,
      backgroundColor: ['#D0BCFF', '#4CD7F6', '#4EDEA3'],
      borderRadius: 14
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { labels: { color: chartText } } },
    scales: {
      x: { ticks: { color: chartText }, grid: { color: grid } },
      y: { ticks: { color: chartText }, grid: { color: grid } }
    }
  }
});
</script>

<?php require __DIR__ . '/../app/partials/footer.php'; ?>
