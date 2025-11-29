<?php
// lib/products.php

/**
 * Return featured products (array)
 * @param PDO $pdo
 * @param int $limit
 * @return array
 */
function get_featured_products(PDO $pdo, $limit = 6)
{
    $sql = 'SELECT id, sku, name, description, price, currency, image_path FROM products WHERE featured = 1 ORDER BY created_at DESC LIMIT :limit';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Get single product by id
 */
function get_product(PDO $pdo, $id)
{
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

/**
 * Simple helper to format price
 */
function format_price($price, $currency = 'USD')
{
    return htmlspecialchars($currency . ' ' . number_format($price, 2));
}
