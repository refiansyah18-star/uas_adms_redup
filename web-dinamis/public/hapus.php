<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_login();

$db = db();
$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $db->prepare("DELETE FROM watchlist WHERE id = ?");
    $stmt->execute([$id]);
    flash_set('Tontonan berhasil dihapus.');
}

redirect_to('/watchlist.php');
