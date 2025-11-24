<?php
require '_base.php';
$_title = 'Insta360 Products';
include '_head.php';

$imagesDir = __DIR__ . '/images/insta360_product';
$files = is_dir($imagesDir) ? array_values(array_filter(scandir($imagesDir), function($f){return !in_array($f,['.','..']);})) : [];
$prices = [999,1199,1299,1499,1599];
?>

<section class="content">
    <h1>Insta360 Products</h1>
    <div class="products-grid">
        <?php foreach ($files as $i => $file):
            $name = pathinfo($file, PATHINFO_FILENAME);
            $displayName = ucwords(str_replace(['_','-'], ' ', $name));
            $price = $prices[$i % count($prices)];
            $src = "/images/insta360_product/" . $file;
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
