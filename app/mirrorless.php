<?php
require '_base.php';
$_title = 'Mirrorless Cameras - VisionX';
include '_head.php';

// Sample products - you can modify this based on your actual products
$products = [
    ['name' => 'Sony Alpha A7 IV', 'price' => 8999, 'image' => '/images/sony_product/sony_a7iv.jpg', 'brand' => 'Sony'],
    ['name' => 'Canon EOS R6 Mark II', 'price' => 9999, 'image' => '/images/canon_product/canon_r6.jpg', 'brand' => 'Canon'],
    ['name' => 'Fujifilm X-T5', 'price' => 6999, 'image' => '/images/fujifilm_product/fuji_xt5.jpg', 'brand' => 'Fujifilm'],
    ['name' => 'Sony A6400', 'price' => 3999, 'image' => '/images/sony_product/sony_a6400.jpg', 'brand' => 'Sony'],
    ['name' => 'Canon EOS R50', 'price' => 3499, 'image' => '/images/canon_product/canon_r50.jpg', 'brand' => 'Canon'],
    ['name' => 'Fujifilm X-S20', 'price' => 4999, 'image' => '/images/fujifilm_product/fuji_xs20.jpg', 'brand' => 'Fujifilm'],
];
?>

<style>
.category-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 0;
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
    <h1>Mirrorless Cameras</h1>
    <p>Discover our collection of high-performance mirrorless cameras</p>
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
        <div class="product-card" data-brand="<?= $product['brand'] ?>" data-price="<?= $product['price'] ?>">
            <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <div class="product-info">
                <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                <div class="product-price">RM<?= number_format($product['price'], 2) ?></div>
                <button type="button" class="add-to-cart" 
                        data-name="<?= htmlspecialchars($product['name']) ?>" 
                        data-price="<?= $product['price'] ?>" 
                        data-image="<?= $product['image'] ?>">
                    Add to cart
                </button>
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
