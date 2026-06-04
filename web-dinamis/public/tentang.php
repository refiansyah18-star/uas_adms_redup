<?php
session_start();
require_once __DIR__ . '/../app/config.php';

$title = 'Tentang - WatchVault';
$active = 'tentang';
require __DIR__ . '/../app/partials/header.php';
?>

<main class="container">
  <section class="page-title about-hero">
    <span class="eyebrow">Tentang WatchVault</span>
    <h1>Ruang pribadi untuk merapikan semua tontonan favoritmu.</h1>
    <p>
      WatchVault membantu kamu menyimpan film, anime, dan series dalam satu tempat.
      Atur status menonton, pantau progress episode, beri rating, dan simpan catatan setelah selesai menonton.
    </p>
  </section>

  <section class="tech-grid">
    <article class="tech-card">
      <span class="material-symbols-outlined">collections_bookmark</span>
      <h3>Katalog Pribadi</h3>
      <p>Simpan semua judul yang ingin kamu tonton, sedang ditonton, atau sudah selesai dalam koleksi yang rapi.</p>
    </article>

    <article class="tech-card">
      <span class="material-symbols-outlined">play_circle</span>
      <h3>Progress Menonton</h3>
      <p>Pantau episode yang sudah ditonton dan lanjutkan tontonan tanpa lupa posisi terakhir.</p>
    </article>

    <article class="tech-card">
      <span class="material-symbols-outlined">star</span>
      <h3>Rating & Catatan</h3>
      <p>Beri nilai untuk setiap tontonan dan tulis catatan singkat sebagai arsip pribadi.</p>
    </article>
  </section>

  <section class="dash-grid" style="margin-top:28px">
    <div class="panel">
      <h2>Cara Kerja WatchVault</h2>
      <div class="info-list">
        <div>
          <span>Tambah koleksi</span>
          <strong>Masukkan judul, tipe, genre, dan gambar poster</strong>
        </div>
        <div>
          <span>Atur status</span>
          <strong>Plan to Watch, Watching, Completed, atau Dropped</strong>
        </div>
        <div>
          <span>Pantau progress</span>
          <strong>Lihat episode yang sudah ditonton lewat progress bar</strong>
        </div>
        <div>
          <span>Kurasi tontonan</span>
          <strong>Gunakan rating dan catatan untuk menilai koleksi</strong>
        </div>
      </div>
    </div>

    <div class="panel highlight-panel">
      <span class="material-symbols-outlined highlight-icon">auto_awesome</span>
      <h2>Dibuat untuk koleksi yang terus bertambah.</h2>
      <p>
        WatchVault cocok dipakai untuk mencatat anime musiman, film favorit, series panjang,
        sampai daftar tontonan yang ingin diselesaikan nanti.
      </p>

      <div class="mini-feature-grid">
        <div>
          <strong>Filter cepat</strong>
          <span>Cari berdasarkan tipe, status, dan genre.</span>
        </div>
        <div>
          <strong>Visual rapi</strong>
          <span>Poster, badge status, dan progress tampil dalam card.</span>
        </div>
        <div>
          <strong>Dashboard koleksi</strong>
          <span>Lihat ringkasan watchlist secara visual.</span>
        </div>
      </div>
    </div>
  </section>

  <section class="about-cta">
    <h2>Siap merapikan watchlist kamu?</h2>
    <p>Mulai tambahkan tontonan baru dan buat koleksi film, anime, serta series jadi lebih teratur.</p>
    <div class="hero-actions">
      <a class="btn primary" href="/tambah.php">
        <span class="material-symbols-outlined">add</span>
        Tambah Koleksi
      </a>
      <a class="btn secondary" href="/watchlist.php">Lihat Watchlist</a>
    </div>
    </section>

  <section class="watchlist-bottom">
    <div>
      <span class="eyebrow">Kurasi berikutnya</span>
      <h2>Masih ada tontonan yang belum masuk daftar?</h2>
      <p>
        Tambahkan anime, film, atau series baru supaya koleksi WatchVault tetap lengkap dan mudah dilacak.
      </p>
    </div>

    <div class="watchlist-bottom-actions">
      <a class="btn primary" href="/tambah.php">
        <span class="material-symbols-outlined">add</span>
        Tambah Koleksi
      </a>
      <a class="btn secondary" href="/dashboard.php">Kembali ke Dashboard</a>
    </div>
  </section>
</main>

<?php require __DIR__ . '/../app/partials/footer.php'; ?>