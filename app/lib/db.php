<?php
// lib/db.php
// Simple PDO factory — returns $pdo variable
$config = require __DIR__ . '/config.php';
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $config['host'], $config['dbname'], $config['charset']);

try {
    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // In production you might log this instead of echoing
    echo 'Database connection failed: ' . htmlspecialchars($e->getMessage());
    exit;
}

return $pdo;
