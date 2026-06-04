<?php
session_start();
require_once __DIR__ . '/../app/config.php';

$title = 'Tentang - WatchVault';
$active = 'tentang';
require __DIR__ . '/../app/partials/header.php';
?>

<main class="container">
  <section class="page-title" style="text-align:center; max-width:760px; margin:28px auto 46px;">
    <span class="eyebrow">Tentang WatchVault</span>
    <h1>Infrastruktur media modern untuk koleksi tontonan pribadi.</h1>
    <p>WatchVault dirancang sebagai ruang digital untuk menyimpan, mengatur, dan memantau film, anime, serta series dalam satu antarmuka yang cepat dan rapi.</p>
  </section>

  <section class="tech-grid">
    <article class="tech-card">
      <span class="material-symbols-outlined">code</span>
      <h3>PHP Application</h3>
      <p>Halaman dan aksi CRUD diproses langsung oleh backend PHP dengan struktur file yang sederhana.</p>
    </article>
    <article class="tech-card">
      <span class="material-symbols-outlined">database</span>
      <h3>MariaDB Storage</h3>
      <p>Semua data watchlist, rating, progress, gambar, dan catatan tersimpan sebagai record database.</p>
    </article>
    <article class="tech-card">
      <span class="material-symbols-outlined">deployed_code</span>
      <h3>Container Ready</h3>
      <p>Aplikasi dikemas dalam container agar mudah dipindahkan dari development ke server cloud.</p>
    </article>
  </section>

  <section class="dash-grid" style="margin-top:28px">
    <div class="panel">
      <h2>Alur Platform</h2>
      <div class="info-list">
        <div><span>Tambah koleksi</span><strong>Input judul, tipe, status, dan gambar</strong></div>
        <div><span>Kelola progress</span><strong>Update episode ditonton</strong></div>
        <div><span>Analisis koleksi</span><strong>Grafik status dan tipe konten</strong></div>
        <div><span>Kurasi pribadi</span><strong>Rating dan catatan tontonan</strong></div>
      </div>
    </div>

    <div class="panel">
      <h2>Konfigurasi Runtime</h2>
      <div class="code-card">
        <div>web: php:8.2-apache</div>
        <div>database: mariadb:11.4</div>
        <div>public_port: 3000</div>
        <div>network: watchvault-network</div>
        <div>storage: persistent volume</div>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/../app/partials/footer.php'; ?>
