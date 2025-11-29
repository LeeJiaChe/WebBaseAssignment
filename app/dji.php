<?php

require '_base.php';

$_title = 'DJI Products - VisionX';

$_bodyClass = 'transparent-header-page';

include '_head.php';



$imagesDir = __DIR__ . '/images/dji_product';

$files = is_dir($imagesDir) ? array_values(array_filter(scandir($imagesDir), function ($f) {return !in_array($f, ['.','..']);})) : [];

$priceRanges = [1599,2499,3299,1999];

$categories = ['Drone', 'Action Camera', 'Gimbal', 'Drone'];



$products = [];

foreach ($files as $i => $file) {

    $name = pathinfo($file, PATHINFO_FILENAME);

    $displayName = ucwords(str_replace(['_','-'], ' ', $name));

    $price = $priceRanges[$i % count($priceRanges)];

    $category = $categories[$i % count($categories)];

    $src = "/images/dji_product/" . $file;



    $products[] = [

        'name' => $displayName,

        'price' => $price,

        'image' => $src,

        'category' => $category,

        'brand' => 'DJI'

    ];

}

?>



<style>

.brand-header {

    background: linear-gradient(135deg, #333333 0%, #8c8c8c 100%);

    color: white;

    padding: 135px 0 50px 0;

    margin: -20px -50px 30px -50px;

    text-align: center;

}



.brand-header h1 {

    margin: 0;

    font-size: 2.5rem;

}



.brand-header p {

    margin: 10px 0 0 0;

    font-size: 1.1rem;

    opacity: 0.9;

}



/* **START OF COPIED CSS FOR FILTERS** */

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



@media (max-width: 768px) {

    .filter-section {

        flex-direction: column;

        align-items: stretch;

    }

    

    .filter-group {

        width: 100%;

    }

    

    .products-count {

        margin-left: 0;

    }

}

/* **END OF COPIED CSS FOR FILTERS** */

</style>



<div class="brand-header">

    <h1>DJI</h1>

    <p>Discover premium DJI drones and cameras</p>

</div>



<div class="filter-section">

    <div class="filter-group">

        <label for="categoryFilter">Category:</label>

        <select id="categoryFilter">

            <option value="">All Categories</option>

            <option value="Drone">Drone</option>

            <option value="Action Camera">Action Camera</option>

            <option value="Gimbal">Gimbal</option>

        </select>

    </div>



    <div class="filter-group">

        <label for="priceFilter">Price Range:</label>

        <select id="priceFilter">

            <option value="">All Prices</option>

            <option value="0-2999">Under RM 2,999</option>

            <option value="3000-4999">RM 3,000 - RM 4,999</option>

            <option value="5000-7999">RM 5,000 - RM 7,999</option>

            <option value="8000+">RM 8,000+</option>

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

        Showing <strong id="productCount"><?= count($products) ?></strong> products

    </div>

</div>

<div class="products-grid">

        <?php foreach ($products as $product): ?>

            <div class="product-card" 

                 data-category="<?= $product['category'] ?>" 

                 data-price="<?= $product['price'] ?>"

                 data-name="<?= htmlspecialchars($product['name']) ?>">

                <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">

                <div class="product-info">

                    <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>

                    <div class="product-price">RM<?= number_format($product['price'], 2) ?></div>

                    <button type="button" class="add-to-cart" data-name="<?= htmlspecialchars($product['name']) ?>" data-price="<?= $product['price'] ?>" data-image="<?= $product['image'] ?>">Add to cart</button>

                </div>

            </div>

        <?php endforeach; ?>

</div>



<script>

document.getElementById('categoryFilter').addEventListener('change', filterProducts);

document.getElementById('priceFilter').addEventListener('change', filterProducts);

document.getElementById('sortFilter').addEventListener('change', sortProducts);



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

            else if (priceFilter === '5000-7999') showPrice = price >= 5000 && price <= 7999;

            else if (priceFilter === '8000+') showPrice = price >= 8000;

        }

        

        if (showCategory && showPrice) {

            card.style.display = '';

            visibleCount++;

        } else {

            card.style.display = 'none';

        }

    });

    

    document.getElementById('productCount').textContent = visibleCount;

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

            return a.dataset.name.localeCompare(b.dataset.name);

        }

        return 0; 

    });

    

    cards.forEach(card => grid.appendChild(card));

}



filterProducts(); 

sortProducts(); 

</script>

<?php include '_foot.php'; ?>
