<?php
define('DB_HOST', 'sql210.infinityfree.com');
define('DB_NAME', 'if0_41799430_portfolio_by_shinouee');
define('DB_USER', 'if0_41799430');
define('DB_PASS', 'r3sr0wSZ21va');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}