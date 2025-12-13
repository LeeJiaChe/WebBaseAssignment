<?php
require __DIR__ . '/_auth.php';
require __DIR__ . '/../lib/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: products.php'); exit;
}

// delete product
$stmt = $pdo->prepare('SELECT image_path FROM products WHERE id = :id');
$stmt->execute([':id'=>$id]);
$row = $stmt->fetch();
if ($row) {
    if (!empty($row['image_path']) && file_exists(__DIR__ . '/../' . $row['image_path'])) {
        @unlink(__DIR__ . '/../' . $row['image_path']);
    }
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $stmt->execute([':id'=>$id]);
}

header('Location: products.php'); exit;
