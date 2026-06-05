<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_login();

$db = db();
$isEdit = basename($_SERVER['PHP_SELF']) === 'edit.php';

$item = [
    'id' => '',
    'title' => '',
    'type' => 'Anime',
    'genre' => 'Action',
    'status' => 'Plan to Watch',
    'total_episodes' => 1,
    'watched_episodes' => 0,
    'rating' => 0,
    'notes' => '',
    'poster_url' => '',
    'cover_url' => '',
    'added_date' => date('Y-m-d'),
];

if ($isEdit) {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare("SELECT * FROM watchlist WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();

    if (!$found) {
        flash_set('Data tontonan tidak ditemukan.', 'error');
        redirect_to('/watchlist.php');
    }

    $item = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'type' => $_POST['type'] ?? 'Anime',
        'genre' => trim($_POST['genre'] ?? ''),
        'status' => $_POST['status'] ?? 'Plan to Watch',
        'total_episodes' => max(1, (int)($_POST['total_episodes'] ?? 1)),
        'watched_episodes' => max(0, (int)($_POST['watched_episodes'] ?? 0)),
        'rating' => min(10, max(0, (float)($_POST['rating'] ?? 0))),
        'notes' => trim($_POST['notes'] ?? ''),
        'poster_url' => trim($_POST['poster_url'] ?? ''),
        'cover_url' => trim($_POST['cover_url'] ?? ''),
        'added_date' => $_POST['added_date'] ?? date('Y-m-d'),
    ];

    if ($data['title'] === '' || $data['genre'] === '') {
        flash_set('Judul dan genre wajib diisi.', 'error');
    } else {
        $data['watched_episodes'] = min($data['watched_episodes'], $data['total_episodes']);

        if ($isEdit) {
            $stmt = $db->prepare("
                UPDATE watchlist SET
                    title = ?, type = ?, genre = ?, status = ?, total_episodes = ?,
                    watched_episodes = ?, rating = ?, notes = ?, poster_url = ?, cover_url = ?, added_date = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['title'], $data['type'], $data['genre'], $data['status'],
                $data['total_episodes'], $data['watched_episodes'], $data['rating'],
                $data['notes'], $data['poster_url'], $data['cover_url'], $data['added_date'],
                (int)$item['id']
            ]);
            flash_set('Data tontonan berhasil diperbarui.');
            redirect_to('/detail.php?id=' . (int)$item['id']);
        } else {
            $stmt = $db->prepare("
                INSERT INTO watchlist
                (title, type, genre, status, total_episodes, watched_episodes, rating, notes, poster_url, cover_url, added_date)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['title'], $data['type'], $data['genre'], $data['status'],
                $data['total_episodes'], $data['watched_episodes'], $data['rating'],
                $data['notes'], $data['poster_url'], $data['cover_url'], $data['added_date']
            ]);
            flash_set('Tontonan baru berhasil ditambahkan.');
            redirect_to('/watchlist.php');
        }
    }
}

$title = $isEdit ? 'Edit Tontonan - WatchVault' : 'Tambah Tontonan - WatchVault';
$active = $isEdit ? 'watchlist' : 'tambah';
require __DIR__ . '/../app/partials/header.php';
?>

<main class="form-layout">
  <section class="form-panel">
    <div style="text-align:center; margin-bottom:26px">
      <span class="eyebrow"><?= $isEdit ? 'Edit Koleksi' : 'Tambah Koleksi' ?></span>
      <h1 style="letter-spacing:-.06em; font-size:42px; margin:14px 0 8px"><?= $isEdit ? 'Edit Tontonan' : 'Tambah Tontonan Baru' ?></h1>
      <p style="color:var(--muted)">Isi data film, anime, atau series yang ingin kamu simpan.</p>
    </div>

    <form method="POST">
      <div class="form-grid">
        <label class="full">Judul
          <input type="text" name="title" value="<?= e($item['title']) ?>" placeholder="Contoh: One Piece" required>
        </label>

        <label>Tipe
          <select name="type">
            <?php foreach (['Anime','Movie','Series'] as $option): ?>
              <option value="<?= e($option) ?>" <?= $item['type'] === $option ? 'selected' : '' ?>><?= e($option) ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>Genre
          <input type="text" name="genre" value="<?= e($item['genre']) ?>" placeholder="Action" required>
        </label>

        <label>Status
          <select name="status">
            <?php foreach (['Plan to Watch','Watching','Completed','Dropped'] as $option): ?>
              <option value="<?= e($option) ?>" <?= $item['status'] === $option ? 'selected' : '' ?>><?= e($option) ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>Tanggal Ditambahkan
          <input type="date" name="added_date" value="<?= e($item['added_date']) ?>">
        </label>

        <label>Total Episode
          <input type="number" name="total_episodes" value="<?= e($item['total_episodes']) ?>" min="1">
        </label>

        <label>Episode Ditonton
          <input type="number" name="watched_episodes" value="<?= e($item['watched_episodes']) ?>" min="0">
        </label>

        <label>Rating 0–10
          <input type="number" name="rating" value="<?= e($item['rating']) ?>" min="0" max="10" step="0.1">
        </label>

        <label class="full">Poster URL
          <input type="url" name="poster_url" value="<?= e($item['poster_url']) ?>" placeholder="https://...">
        </label>

        <label class="full">Cover URL
          <input type="url" name="cover_url" value="<?= e($item['cover_url']) ?>" placeholder="https://...">
        </label>

        <label class="full">Catatan
          <textarea name="notes" placeholder="Catatan pribadi..."><?= e($item['notes']) ?></textarea>
        </label>
      </div>

      <div class="form-actions">
        <a class="btn secondary" href="<?= $isEdit ? '/detail.php?id=' . e($item['id']) : '/watchlist.php' ?>">Batal</a>
        <button class="btn primary" type="submit">Simpan</button>
      </div>
    </form>
  </section>
</main>

<?php require __DIR__ . '/../app/partials/footer.php'; ?>
