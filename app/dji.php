<?php
require '_base.php';
$_title = 'DJI Products';
include '_head.php';

$imagesDir = __DIR__ . '/images/dji_product';
$files = is_dir($imagesDir) ? array_values(array_filter(scandir($imagesDir), function($f){return !in_array($f,['.','..']);})) : [];
$prices = [1599,2499,3299,1999];
?>

<section class="content">
    <h1>DJI Products</h1>
    <div class="products-grid">
        <?php foreach ($files as $i => $file):
            $name = pathinfo($file, PATHINFO_FILENAME);
            $displayName = ucwords(str_replace(['_','-'], ' ', $name));
            $price = $prices[$i % count($prices)];
            $src = "/images/dji_product/" . $file;
        ?>
            <div class="product-card">
                <img src="<?= $src ?>" alt="<?= htmlspecialchars($displayName) ?>">
                <div class="product-info">
                    <div class="product-name"><?= htmlspecialchars($displayName) ?></div>
                    <div class="product-price">RM<?= number_format($price,2) ?></div>
                    <button type="button" class="add-to-cart" data-name="<?= htmlspecialchars($displayName) ?>" data-price="<?= $price ?>" data-image="<?= $src ?>">Add to cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include '_foot.php';
