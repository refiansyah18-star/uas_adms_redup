<?php
session_start();
require_once __DIR__ . '/../app/config.php';

// Kalau sudah login, redirect ke dashboard
if (is_logged_in()) {
    redirect_to('/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $db = db();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            flash_set('Selamat datang, ' . $user['full_name'] . '!');
            redirect_to('/dashboard.php');
        } else {
            $error = 'Username atau password salah.';
        }
    }
}

$title  = 'Login - WatchVault';
$active = '';
require __DIR__ . '/../app/partials/header.php';
?>

<main class="container" style="max-width:420px;margin-top:80px">
  <div class="panel" style="padding:2rem 2rem 2.5rem">
    <div style="text-align:center;margin-bottom:1.8rem">
      <span class="brand-icon material-symbols-outlined" style="font-size:2.5rem;color:var(--primary)">movie</span>
      <h1 style="margin:0.4rem 0 0.25rem;font-size:1.5rem">WatchVault</h1>
      <p style="color:var(--on-surface-variant);font-size:.9rem;margin:0">Masuk ke akun kamu</p>
    </div>

    <?php if ($error): ?>
      <div class="flash error" style="margin-bottom:1rem"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/login.php" style="display:flex;flex-direction:column;gap:1rem">
      <div>
        <label style="display:block;font-size:.85rem;font-weight:600;margin-bottom:.4rem;color:var(--on-surface-variant)">Username</label>
        <input
          type="text"
          name="username"
          value="<?= e($_POST['username'] ?? '') ?>"
          autocomplete="username"
          placeholder="Masukkan username"
          style="width:100%;padding:.65rem .9rem;background:var(--surface-variant);border:1px solid rgba(255,255,255,.1);border-radius:10px;color:var(--on-surface);font-size:.95rem;outline:none;box-sizing:border-box"
          required
        >
      </div>
      <div>
        <label style="display:block;font-size:.85rem;font-weight:600;margin-bottom:.4rem;color:var(--on-surface-variant)">Password</label>
        <input
          type="password"
          name="password"
          autocomplete="current-password"
          placeholder="Masukkan password"
          style="width:100%;padding:.65rem .9rem;background:var(--surface-variant);border:1px solid rgba(255,255,255,.1);border-radius:10px;color:var(--on-surface);font-size:.95rem;outline:none;box-sizing:border-box"
          required
        >
      </div>
      <button
        type="submit"
        class="btn primary"
        style="width:100%;justify-content:center;margin-top:.4rem;padding:.75rem"
      >
        <span class="material-symbols-outlined">login</span>
        Masuk
      </button>
    </form>
  </div>
</main>

<?php require __DIR__ . '/../app/partials/footer.php'; ?>
