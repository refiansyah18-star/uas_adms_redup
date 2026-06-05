<?php
/**
 * Jalankan sekali untuk membuat user default.
 * php db/seed_users.php
 * Atau bisa diintegrasikan ke init container.
 */
require_once __DIR__ . '/../../app/config.php';

$db = db();

$users = [
    ['username' => 'admin', 'full_name' => 'Admin WatchVault', 'password' => 'admin123'],
];

foreach ($users as $u) {
    $hash = password_hash($u['password'], PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $db->prepare("INSERT IGNORE INTO users (username, full_name, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$u['username'], $u['full_name'], $hash]);
    echo "User '{$u['username']}' created.\n";
}
echo "Done.\n";
