<?php
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_NAME') ?: 'watchvault_db';
    $user = getenv('DB_USER') ?: 'watchvault_user';
    $pass = getenv('DB_PASSWORD') ?: 'watchvault_password';

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect_to(string $path): void
{
    header("Location: {$path}");
    exit;
}

function flash_set(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function flash_get(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function status_class(string $status): string
{
    return match ($status) {
        'Watching' => 'watching',
        'Completed' => 'completed',
        'Plan to Watch' => 'plan',
        'Dropped' => 'dropped',
        default => 'plan',
    };
}

function progress_percent(array $item): int
{
    $total = max(1, (int)$item['total_episodes']);
    $watched = min($total, max(0, (int)$item['watched_episodes']));
    return (int)round(($watched / $total) * 100);
}

function poster_fallback(string $title): string
{
    return 'https://placehold.co/600x900/111318/d0bcff?text=' . urlencode($title);
}

function cover_fallback(string $title): string
{
    return 'https://placehold.co/1400x620/111318/d0bcff?text=' . urlencode($title);
}

function rating_text($rating): string
{
    $num = (float)$rating;
    return $num > 0 ? number_format($num, 1) : '-';
}

function count_by(PDO $db, string $sql, array $params = []): int
{
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function avg_rating(PDO $db): string
{
    $value = $db->query("SELECT AVG(NULLIF(rating, 0)) FROM watchlist")->fetchColumn();
    return $value ? number_format((float)$value, 1) : '0.0';
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }
    static $user = null;
    if ($user === null) {
        $db = db();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash_set('Silakan login terlebih dahulu.', 'error');
        redirect_to('/login.php');
    }
}
