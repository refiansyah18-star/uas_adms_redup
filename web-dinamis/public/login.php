<?php
session_start();
require_once __DIR__ . '/../app/config.php';

if (is_logged_in()) {
    redirect_to('/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi.';
    } else {
        $db = db();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            flash_set('Selamat datang, ' . $user['full_name'] . '!', 'success');
            redirect_to('/dashboard.php');
        } else {
            $error = 'Username atau password salah.';
        }
    }
}

$title = 'Login - WatchVault';
require __DIR__ . '/../app/partials/header.php';
?>

<main class="form-layout" style="max-width: 460px; margin-top: 60px;">
  <section class="form-panel">
    <div style="text-align:center; margin-bottom:26px">
      <span class="eyebrow">Autentikasi</span>
      <h1 style="letter-spacing:-.06em; font-size:36px; margin:14px 0 8px">Masuk ke WatchVault</h1>
      <p style="color:var(--muted)">Silakan login untuk mengelola koleksimu.</p>
    </div>

    <?php if ($error): ?>
      <div class="flash error" style="margin-bottom:20px;"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/login.php">
      <div class="form-grid" style="display:flex; flex-direction:column; gap:16px;">
        <label>Username
          <input type="text" name="username" placeholder="Masukkan username" required autofocus>
        </label>
        
        <label>Password
          <input type="password" name="password" placeholder="Masukkan password" required>
        </label>
      </div>
      
      <div class="form-actions" style="margin-top:24px; display:flex; justify-content:center;">
        <button type="submit" class="btn primary" style="width:100%; justify-content:center;">
          <span class="material-symbols-outlined">login</span>
          Login
        </button>
      </div>
    </form>
  </section>
</main>

<?php require __DIR__ . '/../app/partials/footer.php'; ?>
