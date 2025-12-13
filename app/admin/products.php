<?php
require __DIR__ . '/_auth.php';
require __DIR__ . '/../lib/db.php';
require __DIR__ . '/../_base.php';

$_title = 'Admin - Products';
$_bodyClass = 'transparent-header-page';

require __DIR__ . '/head.php';

// get search query and products
$q = trim($_GET['q'] ?? '');
$params = [];
$sql = 'SELECT * FROM products';
if ($q !== '') {
    $sql .= ' WHERE name LIKE :q OR sku LIKE :q OR description LIKE :q';
    $params[':q'] = "%$q%";
}
$sql .= ' ORDER BY id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
.brand-header {
    background: linear-gradient(135deg, #333333 0%, #8c8c8c 100%);
    color: white;
    padding: 135px 0 50px 0;
    margin: -20px -50px 30px -50px;
    text-align: center;
}
.brand-header h1 { margin: 0; font-size: 2.5rem; }
.brand-header p { margin: 10px 0 0 0; font-size: 1.1rem; opacity: 0.9; }

.content {
    max-width: 1100px;
    margin: 20px auto 40px;
    padding: 18px 22px;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 6px 18px rgba(34,34,34,0.06);
    color: #222;
}
.content h1 { margin: 0 0 10px 0; font-size: 1.6rem; }

/* Search form */
form[method="get"] {
    display: flex;
    gap: 8px;
    margin: 12px 0;
    align-items: center;
}
form[method="get"] input[name="q"] {
    flex: 1;
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 0.95rem;
    background: #fafafa;
}
.btn {
    display: inline-block;
    padding: 8px 12px;
    background: #2b7cff;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-size: 0.95rem;
}
.btn:hover { opacity: 0.95; }

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
    font-size: 0.95rem;
}
thead th {
    text-align: left;
    padding: 10px 8px;
    border-bottom: 1px solid #eee;
    color: #444;
    font-weight: 600;
}
tbody td {
    padding: 10px 8px;
    vertical-align: middle;
    border-bottom: 1px solid #f3f3f3;
    color: #333;
}
tbody tr:hover { background: #fbfbfb; }

.image-thumb img {
    height: 40px;
    width: 40px;
    border-radius: 4px;
    object-fit: cover;
    border: 1px solid #e9e9e9;
}

/* Actions */
.actions {
    text-align: right;
}
.actions a {
    color: #2b7cff;
    text-decoration: none;
    margin-left: 8px;
    font-weight: 600;
}
.actions a.delete {
    color: #b00020;
    margin-left: 12px;
}

/* Responsive tweaks */
@media (max-width: 760px) {
    .content { padding: 12px; margin: 12px; }
    thead { display: none; }
    tbody td { display: block; width: 100%; box-sizing: border-box; }
    tbody tr { margin-bottom: 10px; display: block; border: 1px solid #f0f0f0; border-radius: 6px; padding: 8px; }
    .actions { text-align: left; margin-top: 8px; }
}
</style>

<section class="content">
    <h1>Products</h1>
    <p>
        <a href="product_edit.php">+ Create new product</a>
    </p>

    <form method="get" action="">
        <input name="q" value="<?= htmlspecialchars($q, ENT_QUOTES) ?>" placeholder="Search by name, sku or description">
        <button class="btn" type="submit">Search</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>SKU</th>
                <th>Name</th>
                <th>Price</th>
                <th>Image</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($products) === 0): ?>
                <tr><td colspan="6">No products found.</td></tr>
            <?php endif; ?>

            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['id']) ?></td>
                <td><?= htmlspecialchars($p['sku']) ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['price']) ?></td>
                <td>
                    <?php if (!empty($p['image_path'])): ?>
                        <div class="image-thumb"><img src="<?= htmlspecialchars($p['image_path']) ?>" alt=""></div>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a href="product_edit.php?id=<?= urlencode($p['id']) ?>">Edit</a>
                    <a class="delete" href="product_delete.php?id=<?= urlencode($p['id']) ?>" onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require __DIR__ . '/foot.php'; ?>
