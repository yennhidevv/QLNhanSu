<?php
declare(strict_types=1);
const DB_HOST = '127.0.0.1';
const DB_NAME = 'QLNhanSu';
const DB_USER = 'root';
const DB_PASS = ''; // Nếu MySQL có mật khẩu, sửa ở đây.

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Không kết nối được MySQL. Hãy bật MySQL trong XAMPP và import database.sql, sau đó kiểm tra config.php.');
}

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function redirectWith(string $type, string $message): never {
    header('Location: index.php?' . http_build_query([$type => $message]));
    exit;
}
function csrfToken(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
