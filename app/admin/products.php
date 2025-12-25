<?php
require __DIR__ . '/_auth.php';
require __DIR__ . '/../_base.php';

global $db;
if (!isset($db)) {
    $db = require __DIR__ . '/../lib/db.php';
}

$_title = 'Admin - Products';
$_bodyClass = 'transparent-header-page';

// get search query and products
$q = trim($_GET['q'] ?? '');
$sql = 'SELECT * FROM products';
$params = [];

if ($q !== '') {
    $sql .= ' WHERE name LIKE ?';
    $params[] = "%$q%";
}
$sql .= ' ORDER BY id DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get product count
$totalCount = count($products);

require __DIR__ . '/head.php';
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
    max-width: 1200px;
    margin: 20px auto 40px;
    padding: 28px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(34,34,34,0.06);
    color: #222;
}

.stat-card {
    padding: 20px;
    background: #fff8f0;
    border-radius: 10px;
    border: 2px solid #ffe8cc;
    margin-bottom: 30px;
    text-align: center;
}

.stat-count {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ff6f00;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
    margin-top: 8px;
    text-transform: uppercase;
    font-weight: 600;
}

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #d97700 0%, #c86400 100%);
}

.search-box {
    background: #f5f7fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 24px;
}

.search-box h3 {
    margin: 0 0 12px 0;
    font-size: 1rem;
    color: #333;
}

form[method="get"] {
    display: flex;
    gap: 12px;
    align-items: center;
}

form[method="get"] input[name="q"] {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 0.95rem;
}

.btn-search {
    padding: 12px 24px;
    background: linear-gradient(135deg, #ff8f00 0%, #ff6f00 100%);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-search:hover {
    background: linear-gradient(135deg, #d97700 0%, #c86400 100%);
}

.btn-clear {
    padding: 12px 24px;
    background: #e8ecf1;
    color: #333;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-clear:hover {
    background: #d8dce5;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
}

thead {
    background: #f5f7fa;
    border-bottom: 2px solid #e8ecf1;
}

thead th {
    text-align: left;
    padding: 14px 12px;
    color: #333;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
}

.actions-header {
    text-align: center;
}

tbody td {
    padding: 14px 12px;
    vertical-align: middle;
}

tbody tr {
    border-bottom: 1px solid #e8ecf1;
}

tbody tr:last-child {
    border-bottom: none;
}

tbody tr:hover {
    background: #f9fafb;
}

.image-thumb {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #f0f4f8;
}

.image-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-name-cell {
    text-align: left;
    vertical-align: middle;
}

.product-name {
    display: inline-block;
    font-weight: 700;
    color: #1a1a1a;
    font-size: 1.05rem;
    line-height: 1.3;
    max-width: 320px;
    word-break: keep-all;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.product-price {
    font-weight: 700;
    color: #ff6f00;
    font-size: 1.05rem;
}

.actions-cell {
    text-align: center;
    vertical-align: middle;
}

.actions {
    display: inline-flex;
    gap: 10px;
    justify-content: center;
    align-items: center;
    text-align: center;
    min-width: 220px;
}

.btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: #f0f4f8;
    color: #0066cc;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-edit:hover {
    background: #e0e8f0;
}

.btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: #fee;
    color: #c00;
    text-decoration: none;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-delete:hover {
    background: #fdd;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 12px;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .top-bar {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }
    
    form[method="get"] {
        flex-direction: column;
    }
    
    form[method="get"] input[name="q"] {
        width: 100%;
    }
    
    table {
        font-size: 0.85rem;
    }
    
    thead {
        display: none;
    }
    
    tbody td {
        display: block;
        text-align: right;
        padding: 8px 12px;
    }

    .product-name-cell {
        text-align: left;
    }

    .product-name {
        white-space: normal;
        text-align: left;
    }
    
    tbody td::before {
        content: attr(data-label);
        float: left;
        font-weight: 600;
    }
    
    tbody tr {
        margin-bottom: 12px;
        display: block;
        border: 1px solid #e8ecf1;
        border-radius: 8px;
    }
}
</style>

<div class="brand-header">
    <h1><i class="fas fa-box" style="margin-right: 12px;"></i>Product Management</h1>
    <p>Manage your product inventory</p>
</div>

<section class="content">
    <!-- Statistics -->
    <div class="stat-card">
        <div class="stat-count"><?= $totalCount ?></div>
        <div class="stat-label">Total Products</div>
    </div>

    <!-- Top Bar -->
    <div class="top-bar">
        <h2 style="margin: 0; font-size: 1.3rem;">All Products</h2>
        <a href="product_edit.php" class="btn-primary">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>

    <!-- Search -->
    <div class="search-box">
        <h3><i class="fas fa-search" style="margin-right: 8px;"></i>Search Products</h3>
        <form method="get" action="">
            <input name="q" value="<?= htmlspecialchars($q, ENT_QUOTES) ?>" placeholder="Search by name...">
            <button class="btn-search" type="submit">
                <i class="fas fa-search"></i> Search
            </button>
            <?php if ($q !== ''): ?>
                <a href="products.php" class="btn-clear">
                    <i class="fas fa-times"></i> Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Products Table -->
    <?php if (count($products) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th class="actions-header">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td data-label="Image">
                        <?php if (!empty($p['image_path'])):
                            $imagePath = $p['image_path'];
                            $isAbsolute = stripos($imagePath, 'http') === 0 || ($imagePath !== '' && $imagePath[0] === '/');
                            if (!$isAbsolute) {
                                $imagePath = '../' . ltrim($imagePath, '/');
                            }
                        ?>
                            <div class="image-thumb">
                                <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                            </div>
                        <?php else: ?>
                            <div class="image-thumb" style="background: #f0f4f8; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="color: #999;"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td data-label="Name" class="product-name-cell">
                        <span class="product-name"><?= htmlspecialchars($p['name']) ?></span>
                    </td>
                    <td data-label="Price" class="product-price">RM <?= number_format($p['price'], 2) ?></td>
                    <td data-label="Stock"><?= (int)($p['stock'] ?? 0) ?></td>
                    <td data-label="Actions" class="actions-cell">
                        <div class="actions">
                        <a href="product_edit.php?id=<?= urlencode($p['id']) ?>" class="btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a class="btn-delete" href="product_delete.php?id=<?= urlencode($p['id']) ?>" onclick="return confirm('Are you sure you want to delete this product?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <p><?= $q !== '' ? 'No products found matching your search.' : 'No products available. Add your first product!' ?></p>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/foot.php'; ?>
