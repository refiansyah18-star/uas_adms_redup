<?php
/**
 * Jalankan sekali untuk membuat user default.
 * Dijalankan otomatis oleh docker-compose command sebelum Apache start.
 */

// Path di Docker: /var/www/html/db/seed_users.php
// Config di Docker: /var/www/app/config.php
$configPaths = [
    __DIR__ . '/../../app/config.php',   // /var/www/html/db/../../app = /var/www/app
    __DIR__ . '/../app/config.php',      // fallback
];

$loaded = false;
foreach ($configPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $loaded = true;
        break;
    }
}

if (!$loaded) {
    echo "ERROR: config.php tidak ditemukan.\n";
    echo "Checked paths:\n";
    foreach ($configPaths as $p) {
        echo "  - " . realpath(dirname($p)) . "/" . basename($p) . "\n";
    }
    exit(1);
}

try {
    $db = db();

    $users = [
        ['username' => 'admin', 'full_name' => 'Admin WatchVault', 'password' => 'admin123'],
    ];

    foreach ($users as $u) {
        $hash = password_hash($u['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $db->prepare("INSERT IGNORE INTO users (username, full_name, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$u['username'], $u['full_name'], $hash]);
        echo "User '{$u['username']}' ready.\n";
    }
    echo "Seed done.\n";
} catch (Exception $e) {
    echo "Seed error (will retry on next restart): " . $e->getMessage() . "\n";
    // Jangan exit(1) supaya Apache tetap start
}
