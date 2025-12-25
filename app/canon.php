<?php

// app/canon.php

require '_base.php';



// 1. 确保数据库已连接

global $db;

if (!isset($db)) {

    $db = require __DIR__ . '/lib/db.php';

}



// 2. 从数据库查询 Canon 品牌的产品 (名字以 Canon 开头的)

$stmt = $db->prepare("SELECT id, name, price, image_path, description, category_id FROM products WHERE name LIKE 'Canon%'");

$stmt->execute();

$products = $stmt->fetchAll();



$_title = 'Canon Products - VisionX';

$_bodyClass = 'transparent-header-page';



include '_head.php';

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

.filter-group { display: flex; align-items: center; gap: 10px; }

.filter-group label { font-weight: 500; color: #333; }

.filter-group select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; background: white; }

.products-count { margin-left: auto; color: #666; font-size: 14px; }

</style>



<div class="brand-header">

    <h1>Canon</h1>

    <p>Discover premium Canon cameras and lenses</p>

</div>



<div class="filter-section">

    <div class="filter-group">
        <label for="categoryFilter">Category:</label>
        <select id="categoryFilter">
            <option value="">All Categories</option>
            <option value="1">Mirrorless</option>
            <option value="2">DSLR</option>
        </select>
    </div>

    <div class="filter-group">

        <label for="priceFilter">Price Range:</label>

        <select id="priceFilter">

            <option value="">All Prices</option>

            <option value="0-2999">Under RM 2,999</option>

            <option value="3000-4999">RM 3,000 - RM 4,999</option>

            <option value="5000+">RM 5,000+</option>

        </select>

    </div>

    <div class="products-count">

        Showing <strong id="productCount"><?= count($products) ?></strong> products

    </div>

</div>



<div class="products-grid">

    <?php foreach ($products as $p): ?>

        <?php $id = (int)$p['id']; ?>

        <div class="product-card" data-product-id="<?= $id ?>" 

             data-category="<?= htmlspecialchars($p['category_id'] ?? '') ?>" 

             data-price="<?= $p['price'] ?>"

             data-name="<?= htmlspecialchars($p['name']) ?>">

            

            <img src="<?= htmlspecialchars($p['image_path'] ?? '/images/placeholder.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>" />

            

            <div class="product-info">

                <div>

                    <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>

                    <div class="product-subtitle"><?= htmlspecialchars($p['description'] ?? '') ?></div>

                </div>

                <div class="product-price">RM <?= number_format((float)$p['price'], 2) ?></div>

                <div class="product-footer">

                    <button type="button" class="add-to-cart buy-btn" 

                        data-id="<?= $p['id'] ?>" 

                        data-name="<?= htmlspecialchars($p['name']) ?>" 

                        data-price="<?= $p['price'] ?>" 

                        data-image="<?= htmlspecialchars($p['image_path']) ?>">

                        Add to cart

                    </button>

                    <button type="button" class="buy-now buy-btn" 

                        data-id="<?= $p['id'] ?>" 

                        data-name="<?= htmlspecialchars($p['name']) ?>" 

                        data-price="<?= $p['price'] ?>" 

                        data-image="<?= htmlspecialchars($p['image_path']) ?>">

                        Buy now

                    </button>

                </div>

            </div>

        </div>

    <?php endforeach; ?>

</div>



<script>

// 这里保留你原来的筛选 JS 逻辑即可

document.getElementById('categoryFilter')?.addEventListener('change', filterProducts);

document.getElementById('priceFilter')?.addEventListener('change', filterProducts);



function filterProducts() {

    const categoryFilter = document.getElementById('categoryFilter').value;

    const priceFilter = document.getElementById('priceFilter').value;

    const cards = document.querySelectorAll('.product-card');

    let visibleCount = 0;

    

    cards.forEach(card => {

        const category = card.dataset.category;

        const price = parseInt(card.dataset.price);

        let showCategory = !categoryFilter || category === categoryFilter;

        let showPrice = true;

        if (priceFilter) {

            if (priceFilter === '0-2999') showPrice = price <= 2999;

            else if (priceFilter === '3000-4999') showPrice = price >= 3000 && price <= 4999;

            else if (priceFilter === '5000+') showPrice = price >= 5000;

        }

        if (showCategory && showPrice) { card.style.display = ''; visibleCount++; }

        else { card.style.display = 'none'; }

    });

    document.getElementById('productCount').textContent = visibleCount;

}

</script>



<?php include '_foot.php'; ?>
