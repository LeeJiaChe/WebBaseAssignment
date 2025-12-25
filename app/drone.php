<?php
require '_base.php';
$_title = 'Drone - VisionX';
$_bodyClass = 'transparent-header-page';
include '_head.php';

$db = require __DIR__ . '/lib/db.php';

$detectBrand = function (string $name = '', ?string $imagePath = null): string {
    $map = [
        'DJI' => ['dji_', 'images/dji_product/'],
        'Sony' => ['sony_', 'images/sony_product/'],
        'Canon' => ['canon_', 'images/canon_product/'],
        'Fujifilm' => ['fujifilm_', 'images/fujifilm_product/'],
        'Insta360' => ['insta360_', 'images/insta360_product/'],
    ];
    foreach ($map as $brand => $patterns) {
        foreach ($patterns as $p) {
            if (stripos($name, $brand) === 0 || (!empty($imagePath) && stripos($imagePath, $p) !== false)) {
                return $brand;
            }
        }
    }
    return 'Other';
};

// Drone classification by image/name containing 'drone' or dji
$stmt = $db->prepare("SELECT id, name, price, currency, image_path, description FROM products WHERE image_path LIKE 'images/dji_product/%' OR name LIKE 'DJI%' OR name LIKE '%drone%' ORDER BY name ASC");
$stmt->execute();
$rows = $stmt->fetchAll();

$products = array_map(function ($r) use ($detectBrand) {
    $brand = $detectBrand($r['name'] ?? '', $r['image_path'] ?? '');
    return [
        'id' => $r['id'],
        'name' => $r['name'],
        'price' => $r['price'],
        'currency' => $r['currency'] ?? 'RM',
        'image' => $r['image_path'] ?: '/images/placeholder.png',
        'description' => $r['description'] ?? '',
        'brand' => $brand,
    ];
}, $rows);
?>

<style>
.category-header {
    background: linear-gradient(135deg, #333333 0%, #8c8c8c 100%);
    color: white;
    padding: 135px 0 50px 0;
    margin: -20px -50px 30px -50px;
    text-align: center;
}

.category-header h1 {
    margin: 0;
    font-size: 2.5rem;
}

.category-header p {
    margin: 10px 0 0 0;
    font-size: 1.1rem;
    opacity: 0.9;
}

.filter-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    align-items: center;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-group label {
    font-weight: 500;
    color: #333;
}

.filter-group select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
}

.products-count {
    margin-left: auto;
    color: #666;
    font-size: 14px;
}
</style>

<div class="category-header">
    <h1>Drone</h1>
    <p>Fly high with our professional drones</p>
</div>

<div class="filter-section">
    <div class="filter-group">
        <label for="brandFilter">Brand:</label>
        <select id="brandFilter">
            <option value="">All Brands</option>
            <option value="Sony">Sony</option>
            <option value="Canon">Canon</option>
            <option value="Fujifilm">Fujifilm</option>
        </select>
    </div>

    <div class="filter-group">
        <label for="priceFilter">Price Range:</label>
        <select id="priceFilter">
            <option value="">All Prices</option>
            <option value="0-3999">Under RM 3,999</option>
            <option value="4000-6999">RM 4,000 - RM 6,999</option>
            <option value="7000-9999">RM 7,000 - RM 9,999</option>
            <option value="10000+">RM 10,000+</option>
        </select>
    </div>

    <div class="filter-group">
        <label for="sortFilter">Sort By:</label>
        <select id="sortFilter">
            <option value="featured">Featured</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="name">Name: A to Z</option>
        </select>
    </div>

    <div class="products-count">
        Showing <strong><?= count($products) ?></strong> products
    </div>
</div>

<div class="products-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card" data-brand="<?= $product['brand'] ?>" data-price="<?= $product['price'] ?>" data-product-id="<?= $product['id'] ?>">
            <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <div class="product-info">
                <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                <div class="product-subtitle"><?= htmlspecialchars($product['description']) ?></div>
                <div class="product-price">RM <?= number_format((float)$product['price'], 2) ?></div>
                <div class="product-footer">
                    <button type="button" class="add-to-cart buy-btn"
                            data-id="<?= $product['id'] ?>"
                            data-name="<?= htmlspecialchars($product['name']) ?>"
                            data-price="<?= $product['price'] ?>"
                            data-image="<?= $product['image'] ?>">
                        Add to cart
                    </button>
                    <button type="button" class="buy-now buy-btn"
                            data-id="<?= $product['id'] ?>"
                            data-name="<?= htmlspecialchars($product['name']) ?>"
                            data-price="<?= $product['price'] ?>"
                            data-image="<?= $product['image'] ?>">
                        Buy now
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
// Simple filtering functionality
document.getElementById('brandFilter').addEventListener('change', filterProducts);
document.getElementById('priceFilter').addEventListener('change', filterProducts);
document.getElementById('sortFilter').addEventListener('change', sortProducts);

function filterProducts() {
    const brandFilter = document.getElementById('brandFilter').value;
    const priceFilter = document.getElementById('priceFilter').value;
    const cards = document.querySelectorAll('.product-card');
    
    let visibleCount = 0;
    
    cards.forEach(card => {
        const brand = card.dataset.brand;
        const price = parseInt(card.dataset.price);
        
        let showBrand = !brandFilter || brand === brandFilter;
        let showPrice = true;
        
        if (priceFilter) {
            if (priceFilter === '0-3999') showPrice = price <= 3999;
            else if (priceFilter === '4000-6999') showPrice = price >= 4000 && price <= 6999;
            else if (priceFilter === '7000-9999') showPrice = price >= 7000 && price <= 9999;
            else if (priceFilter === '10000+') showPrice = price >= 10000;
        }
        
        if (showBrand && showPrice) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    document.querySelector('.products-count strong').textContent = visibleCount;
}

function sortProducts() {
    const sortValue = document.getElementById('sortFilter').value;
    const grid = document.querySelector('.products-grid');
    const cards = Array.from(grid.querySelectorAll('.product-card'));
    
    cards.sort((a, b) => {
        if (sortValue === 'price-low') {
            return parseInt(a.dataset.price) - parseInt(b.dataset.price);
        } else if (sortValue === 'price-high') {
            return parseInt(b.dataset.price) - parseInt(a.dataset.price);
        } else if (sortValue === 'name') {
            return a.querySelector('.product-name').textContent.localeCompare(
                b.querySelector('.product-name').textContent
            );
        }
        return 0;
    });
    
    cards.forEach(card => grid.appendChild(card));
}
</script>

<?php include '_foot.php'; ?>
