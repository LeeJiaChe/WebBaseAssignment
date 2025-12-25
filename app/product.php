<?php
require '_base.php';
$_bodyClass = 'product-page product-static-header';
include '_head.php';

// Get product name and details from query string
$productName = isset($_GET['name']) ? $_GET['name'] : '';
$productPrice = isset($_GET['price']) ? (float)$_GET['price'] : 0;
$productImage = isset($_GET['image']) ? $_GET['image'] : '';
$productCategory = isset($_GET['category']) ? $_GET['category'] : '';
$productBrand = isset($_GET['brand']) ? $_GET['brand'] : '';

// If coming from old ID-based system, try database lookup
if (!$productName && isset($_GET['id'])) {
    global $db;
    if (!isset($db)) {
        $db = require __DIR__ . '/lib/db.php';
    }
    $productId = (int)$_GET['id'];
    $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    $dbProduct = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dbProduct) {
        $productName = $dbProduct['name'];
        $productPrice = $dbProduct['price'];
        $productImage = $dbProduct['image_path'];
        $productCategory = $dbProduct['category_id'];
        $product = $dbProduct;
    }
}

if (!$productName) {
    echo '<p>Product not found.</p>';
    include '_foot.php';
    exit;
}

// Build product array for display
if (!isset($product)) {
    $product = [
        'name' => $productName,
        'price' => $productPrice,
        'image_path' => $productImage,
        'category_id' => $productCategory,
        'brand' => $productBrand,
        'currency' => 'RM',
        'stock' => 50,
        'sku' => strtoupper(str_replace([' ', '-'], '_', $productBrand . '_' . substr($productName, 0, 20))),
        'description' => 'Professional camera equipment with cutting-edge technology and exceptional performance.'
    ];
}

$_title = htmlspecialchars($product['name']) . ' - VisionX';
?>

<style>
    .product-detail {
        max-width: 1200px;
        margin: 40px auto;
        padding: 40px 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }
    
    .product-image {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f5f5;
        border-radius: 8px;
        min-height: 400px;
    }
    
    .product-image img {
        max-width: 100%;
        max-height: 500px;
        object-fit: contain;
    }
    
    .product-details {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .product-details h1 {
        font-size: 2.2rem;
        margin: 0 0 15px 0;
        color: #333;
    }
    
    .product-details .sku {
        color: #888;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }
    
    .product-details .price {
        font-size: 2rem;
        font-weight: bold;
        color: #2c7f3f;
        margin: 20px 0;
    }
    
    .product-details .description {
        font-size: 1.1rem;
        color: #666;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    
    .product-details .specs {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .product-details .specs p {
        margin: 8px 0;
        color: #555;
    }
    
    .product-details .specs strong {
        color: #333;
    }
    
    .specifications {
        background: #f9f9f9;
        padding: 30px;
        border-radius: 8px;
        margin: 30px 0;
    }
    
    .specifications h3 {
        font-size: 1.4rem;
        margin: 20px 0 15px 0;
        color: #333;
        border-bottom: 2px solid #2c7f3f;
        padding-bottom: 10px;
    }
    
    .specifications table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    
    .specifications table td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
    }
    
    .specifications table td:first-child {
        font-weight: bold;
        color: #333;
        width: 40%;
    }
    
    .specifications table td:last-child {
        color: #666;
    }
    
    .specifications ul {
        list-style: none;
        padding: 0;
        margin: 15px 0;
    }
    
    .specifications li {
        padding: 8px 0;
        color: #666;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .specifications li:before {
        content: "✓ ";
        color: #2c7f3f;
        font-weight: bold;
        margin-right: 10px;
    }
    
    .product-details .actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    
    .buy-btn {
        padding: 15px 40px;
        background: #333;
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: bold;
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }
    
    .buy-btn:hover {
        background: #555;
    }
    
    .back-link {
        color: #0066cc;
        text-decoration: none;
        margin-bottom: 20px;
    }
    
    .back-link:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .product-detail {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }
</style>

<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <a href="index.php" class="back-link">← Back to home</a>
</div>

<div class="product-detail">
    <div class="product-image">
        <img src="<?= htmlspecialchars($product['image_path'] ?? '/images/placeholder.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
    </div>
    
    <div class="product-details">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        
        <div class="sku">SKU: <?= htmlspecialchars($product['sku'] ?? 'N/A') ?></div>
        
        <div class="price">
            RM <?= number_format($product['price'], 2) ?>
        </div>
        
        <div class="description">
            <?= htmlspecialchars($product['description'] ?? '') ?>
        </div>
        
        <div class="specs">
            <p><strong>Category:</strong> <?= htmlspecialchars($product['category_id'] ?? 'N/A') ?></p>
            <p><strong>Stock Available:</strong> <?= (int)($product['stock'] ?? 0) ?> units</p>
            <p><strong>Availability:</strong> <?= (int)($product['stock'] ?? 0) > 0 ? '<span style="color: green;">In Stock</span>' : '<span style="color: red;">Out of Stock</span>' ?></p>
        </div>
        
        <div class="actions">
            <?php if ((int)($product['stock'] ?? 0) > 0): ?>
                <button type="button" class="buy-btn add-to-cart" 
                        data-id="<?= $product['id'] ?>"
                        data-name="<?= htmlspecialchars($product['name']) ?>"
                        data-price="<?= $product['price'] ?>" 
                        data-image="<?= htmlspecialchars($product['image_path']) ?>">
                    Add to Cart
                </button>
            <?php else: ?>
                <button class="buy-btn" disabled style="background: #ccc; cursor: not-allowed;">Out of Stock</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Detailed Specifications Section -->
<div class="specifications">
    <h3>📋 Product Specifications</h3>
    
    <?php
    // Product-specific specifications based on product ID or name
    $productName = strtolower($product['name']);

// Canon Products
if (strpos($productName, 'canon eos-1d x mark iii') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>Full-Frame CMOS</td></tr>
            <tr><td>Resolution:</td><td>20.1 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>100-102,400 (Expandable to 819,200)</td></tr>
            <tr><td>Autofocus Points:</td><td>191 AF Points (155 Cross-type)</td></tr>
            <tr><td>Continuous Shooting:</td><td>16 fps (Optical), 20 fps (Live View)</td></tr>
            <tr><td>Shutter Speed:</td><td>1/8000 - 30 seconds</td></tr>
            <tr><td>Video Recording:</td><td>5.5K RAW, 4K DCI at 60fps</td></tr>
            <tr><td>Weather Sealing:</td><td>Professional Grade Dust & Moisture Resistant</td></tr>
            <tr><td>Weight:</td><td>1250g (body only)</td></tr>
            <tr><td>Warranty:</td><td>2 Years Professional</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Canon EOS-1D X Mark III Body</li>
            <li>Battery Pack LP-E19 (2)</li>
            <li>Battery Charger LC-E19</li>
            <li>USB Cable</li>
            <li>Camera Strap</li>
            <li>Professional Camera Bag</li>
            <li>128GB CFexpress Card</li>
        </ul>';
} elseif (strpos($productName, 'canon eos 1500d') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>APS-C CMOS</td></tr>
            <tr><td>Resolution:</td><td>24.1 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>100-6,400 (Expandable to 12,800)</td></tr>
            <tr><td>Autofocus Points:</td><td>9-Point AF System</td></tr>
            <tr><td>Continuous Shooting:</td><td>3 fps</td></tr>
            <tr><td>Shutter Speed:</td><td>1/4000 - 30 seconds</td></tr>
            <tr><td>Video Recording:</td><td>Full HD 1080p at 30fps</td></tr>
            <tr><td>LCD Screen:</td><td>3.0-inch Fixed LCD</td></tr>
            <tr><td>Weight:</td><td>475g (body only)</td></tr>
            <tr><td>Warranty:</td><td>1 Year</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Canon EOS 1500D Body</li>
            <li>EF-S 18-55mm f/3.5-5.6 IS II Lens</li>
            <li>Battery Pack LP-E10</li>
            <li>Battery Charger</li>
            <li>USB Cable</li>
            <li>Camera Strap</li>
            <li>16GB SD Card</li>
            <li>Camera Bag</li>
        </ul>';
} elseif (strpos($productName, 'canon eos 3000d') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>APS-C CMOS</td></tr>
            <tr><td>Resolution:</td><td>18 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>100-6,400 (Expandable to 12,800)</td></tr>
            <tr><td>Autofocus Points:</td><td>9-Point AF System</td></tr>
            <tr><td>Continuous Shooting:</td><td>3 fps</td></tr>
            <tr><td>Shutter Speed:</td><td>1/4000 - 30 seconds</td></tr>
            <tr><td>Video Recording:</td><td>Full HD 1080p at 30fps</td></tr>
            <tr><td>LCD Screen:</td><td>2.7-inch Fixed LCD</td></tr>
            <tr><td>Weight:</td><td>436g (body only)</td></tr>
            <tr><td>Warranty:</td><td>1 Year</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Canon EOS 3000D Body</li>
            <li>EF-S 18-55mm f/3.5-5.6 III Lens</li>
            <li>Battery Pack LP-E10</li>
            <li>Battery Charger</li>
            <li>USB Cable</li>
            <li>Camera Strap</li>
            <li>16GB SD Card</li>
            <li>Starter Kit</li>
        </ul>';
} elseif (strpos($productName, 'canon eos r6 mark iii') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>Full-Frame CMOS</td></tr>
            <tr><td>Resolution:</td><td>24.2 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>100-102,400 (Expandable to 204,800)</td></tr>
            <tr><td>Autofocus System:</td><td>Dual Pixel CMOS AF II</td></tr>
            <tr><td>Autofocus Points:</td><td>1053 AF Points</td></tr>
            <tr><td>Continuous Shooting:</td><td>12 fps (Mechanical), 20 fps (Electronic)</td></tr>
            <tr><td>Shutter Speed:</td><td>1/8000 - 30 seconds</td></tr>
            <tr><td>Video Recording:</td><td>4K at 60fps, 6K RAW</td></tr>
            <tr><td>In-Body Stabilization:</td><td>8-stops (5-axis)</td></tr>
            <tr><td>Weight:</td><td>670g (body only)</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Canon EOS R6 Mark III Body</li>
            <li>Battery Pack LP-E6NH (2)</li>
            <li>USB-C Charger</li>
            <li>USB Cable</li>
            <li>Camera Strap</li>
            <li>64GB SD Card UHS-II</li>
            <li>Canon Camera Connect App</li>
        </ul>';
} elseif (strpos($productName, 'canon eos r50') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>APS-C CMOS</td></tr>
            <tr><td>Resolution:</td><td>24.2 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>100-32,000 (Expandable to 51,200)</td></tr>
            <tr><td>Autofocus System:</td><td>Dual Pixel CMOS AF</td></tr>
            <tr><td>Autofocus Points:</td><td>651 AF Points</td></tr>
            <tr><td>Continuous Shooting:</td><td>12 fps (Electronic)</td></tr>
            <tr><td>Shutter Speed:</td><td>1/4000 - 30 seconds</td></tr>
            <tr><td>Video Recording:</td><td>4K UHD at 30fps</td></tr>
            <tr><td>LCD Screen:</td><td>3.0-inch Vari-angle Touchscreen</td></tr>
            <tr><td>Weight:</td><td>328g (body only)</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Canon EOS R50 Body</li>
            <li>RF-S 18-45mm f/4.5-6.3 IS STM Lens</li>
            <li>Battery Pack LP-E17</li>
            <li>USB-C Charger</li>
            <li>Camera Strap</li>
            <li>32GB SD Card</li>
            <li>Lens Hood</li>
        </ul>';
}

// Sony Products
elseif (strpos($productName, 'sony alpha a7r v') !== false || strpos($productName, 'sony alpha 7r v') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>Full-Frame Exmor R CMOS</td></tr>
            <tr><td>Resolution:</td><td>61 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>100-32,000 (Expandable to 102,400)</td></tr>
            <tr><td>Autofocus System:</td><td>AI Processing Unit with Real-time Eye AF</td></tr>
            <tr><td>Autofocus Points:</td><td>693 Phase-Detection AF Points</td></tr>
            <tr><td>Continuous Shooting:</td><td>10 fps</td></tr>
            <tr><td>Shutter Speed:</td><td>1/8000 - 30 seconds</td></tr>
            <tr><td>Video Recording:</td><td>8K at 24fps, 4K at 60fps</td></tr>
            <tr><td>In-Body Stabilization:</td><td>8-stops (5-axis)</td></tr>
            <tr><td>Weight:</td><td>723g (body only)</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Sony Alpha 7R V Body</li>
            <li>Battery NP-FZ100 (2)</li>
            <li>USB-C Charger</li>
            <li>Shoulder Strap</li>
            <li>USB Cable</li>
            <li>Sony Imaging Edge Software</li>
            <li>64GB SD Card</li>
        </ul>';
} elseif (strpos($productName, 'sony α7c') !== false || strpos($productName, 'sony alpha 7c') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>Full-Frame Exmor R CMOS</td></tr>
            <tr><td>Resolution:</td><td>24.2 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>100-51,200 (Expandable to 204,800)</td></tr>
            <tr><td>Autofocus System:</td><td>693 Phase-Detection AF Points</td></tr>
            <tr><td>Continuous Shooting:</td><td>10 fps</td></tr>
            <tr><td>Shutter Speed:</td><td>1/4000 - 30 seconds</td></tr>
            <tr><td>Video Recording:</td><td>4K at 30fps, Full HD at 120fps</td></tr>
            <tr><td>In-Body Stabilization:</td><td>5-stops (5-axis)</td></tr>
            <tr><td>Weight:</td><td>509g (body only)</td></tr>
            <tr><td>Design:</td><td>World\'s Smallest & Lightest Full-Frame</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Sony Alpha 7C Body</li>
            <li>FE 28-60mm f/4-5.6 Lens</li>
            <li>Battery NP-FZ100</li>
            <li>USB-C Charger</li>
            <li>Shoulder Strap</li>
            <li>32GB SD Card</li>
        </ul>';
} elseif (strpos($productName, 'sony α6700') !== false || strpos($productName, 'sony alpha 6700') !== false || strpos($productName, 'sony a6700') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>APS-C Exmor CMOS</td></tr>
            <tr><td>Resolution:</td><td>26 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>100-32,000 (Expandable to 102,400)</td></tr>
            <tr><td>Autofocus System:</td><td>AI-based Real-time Tracking</td></tr>
            <tr><td>Autofocus Points:</td><td>425 Phase-Detection AF Points</td></tr>
            <tr><td>Continuous Shooting:</td><td>11 fps</td></tr>
            <tr><td>Shutter Speed:</td><td>1/4000 - 30 seconds</td></tr>
            <tr><td>Video Recording:</td><td>4K at 120fps, Full HD at 240fps</td></tr>
            <tr><td>In-Body Stabilization:</td><td>5-stops (5-axis)</td></tr>
            <tr><td>Weight:</td><td>493g (body only)</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Sony Alpha 6700 Body</li>
            <li>Battery NP-FZ100 (2)</li>
            <li>USB-C Charger</li>
            <li>Shoulder Strap</li>
            <li>USB Cable</li>
            <li>32GB SD Card</li>
        </ul>';
} elseif (strpos($productName, 'sony alpha α1') !== false || strpos($productName, 'sony alpha 1') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>Full-Frame Exmor RS CMOS</td></tr>
            <tr><td>Resolution:</td><td>50.1 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>100-32,000 (Expandable to 102,400)</td></tr>
            <tr><td>Autofocus System:</td><td>759 Phase-Detection AF Points</td></tr>
            <tr><td>Continuous Shooting:</td><td>30 fps (Electronic Shutter)</td></tr>
            <tr><td>Shutter Speed:</td><td>1/8000 - 30 seconds, 1/32,000 (electronic)</td></tr>
            <tr><td>Video Recording:</td><td>8K at 30fps, 4K at 120fps</td></tr>
            <tr><td>In-Body Stabilization:</td><td>5.5-stops (5-axis)</td></tr>
            <tr><td>Weight:</td><td>737g (body only)</td></tr>
            <tr><td>Features:</td><td>Bird Eye AF, Real-time Tracking</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Sony Alpha 1 Body</li>
            <li>Battery NP-FZ100 (2)</li>
            <li>USB-C Fast Charger</li>
            <li>Professional Shoulder Strap</li>
            <li>USB Cable</li>
            <li>128GB CFexpress Type A Card</li>
            <li>Sony Imaging Edge Software Suite</li>
        </ul>';
} elseif (strpos($productName, 'sony alpha α7r') !== false || strpos($productName, 'sony alpha 7r') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>Full-Frame Exmor R CMOS</td></tr>
            <tr><td>Resolution:</td><td>36.4 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>100-25,600 (Expandable to 102,400)</td></tr>
            <tr><td>Autofocus System:</td><td>399 Phase-Detection AF Points</td></tr>
            <tr><td>Continuous Shooting:</td><td>5 fps</td></tr>
            <tr><td>Shutter Speed:</td><td>1/8000 - 30 seconds</td></tr>
            <tr><td>Video Recording:</td><td>4K at 30fps</td></tr>
            <tr><td>In-Body Stabilization:</td><td>5-stops (5-axis)</td></tr>
            <tr><td>Weight:</td><td>625g (body only)</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Sony Alpha 7R Body</li>
            <li>Battery NP-FZ100</li>
            <li>USB Charger</li>
            <li>Shoulder Strap</li>
            <li>USB Cable</li>
            <li>32GB SD Card</li>
        </ul>';
}

// Fujifilm Products
elseif (strpos($productName, 'fujifilm x-t30') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>X-Trans CMOS 4 APS-C</td></tr>
            <tr><td>Resolution:</td><td>26.1 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>160-12,800 (Expandable to 80-51,200)</td></tr>
            <tr><td>Film Simulations:</td><td>17 Color Profiles Including Classic Chrome</td></tr>
            <tr><td>Autofocus Points:</td><td>425 Phase-Detection AF Points</td></tr>
            <tr><td>Continuous Shooting:</td><td>8 fps (Mechanical), 30 fps (Electronic)</td></tr>
            <tr><td>Shutter Speed:</td><td>1/4000 - 30 seconds</td></tr>
            <tr><td>Video Recording:</td><td>4K at 30fps</td></tr>
            <tr><td>Weight:</td><td>383g (body only)</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Fujifilm X-T30 Body</li>
            <li>XC 15-45mm f/3.5-5.6 OIS PZ Lens</li>
            <li>Rechargeable Battery NP-W126S</li>
            <li>AC Power Adapter</li>
            <li>USB Cable</li>
            <li>Shoulder Strap</li>
            <li>16GB SD Card</li>
        </ul>';
} elseif (strpos($productName, 'fujifilm x-e5') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>X-Trans CMOS 5 HR APS-C</td></tr>
            <tr><td>Resolution:</td><td>40.2 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>125-12,800 (Expandable to 64-51,200)</td></tr>
            <tr><td>Film Simulations:</td><td>20 Color Profiles</td></tr>
            <tr><td>Autofocus System:</td><td>AI-powered Subject Detection</td></tr>
            <tr><td>Continuous Shooting:</td><td>20 fps (Electronic Shutter)</td></tr>
            <tr><td>Shutter Speed:</td><td>1/32,000 - 30 seconds</td></tr>
            <tr><td>Video Recording:</td><td>6.2K at 30fps, 4K at 60fps</td></tr>
            <tr><td>Weight:</td><td>364g (body only)</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Fujifilm X-E5 Body</li>
            <li>Rechargeable Battery NP-W126S (2)</li>
            <li>USB-C Charger</li>
            <li>USB Cable</li>
            <li>Shoulder Strap</li>
            <li>32GB SD Card</li>
        </ul>';
} elseif (strpos($productName, 'fujifilm x-h2s') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>X-Trans CMOS 5 HS APS-C</td></tr>
            <tr><td>Resolution:</td><td>26.1 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>160-12,800 (Expandable to 80-51,200)</td></tr>
            <tr><td>Film Simulations:</td><td>19 Color Profiles</td></tr>
            <tr><td>Autofocus System:</td><td>AI-powered Subject Detection</td></tr>
            <tr><td>Continuous Shooting:</td><td>15 fps (Mechanical), 40 fps (Electronic)</td></tr>
            <tr><td>Shutter Speed:</td><td>1/8000 - 15 minutes</td></tr>
            <tr><td>Video Recording:</td><td>6.2K at 30fps, 4K at 120fps</td></tr>
            <tr><td>Weight:</td><td>660g (body only)</td></tr>
            <tr><td>Design:</td><td>Professional Weather-Sealed Body</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Fujifilm X-H2S Body</li>
            <li>Rechargeable Battery NP-W235 (2)</li>
            <li>AC Power Adapter</li>
            <li>USB-C Cable</li>
            <li>Shoulder Strap</li>
            <li>64GB SD Card UHS-II</li>
            <li>Professional Camera Bag</li>
        </ul>';
} elseif (strpos($productName, 'fujifilm gfx100 ii') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>Medium Format CMOS 43.8 x 32.9mm</td></tr>
            <tr><td>Resolution:</td><td>102 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>80-12,800 (Expandable to 51,200)</td></tr>
            <tr><td>Autofocus System:</td><td>Phase Detection AF</td></tr>
            <tr><td>Autofocus Points:</td><td>425 Points</td></tr>
            <tr><td>Continuous Shooting:</td><td>8 fps</td></tr>
            <tr><td>Shutter Speed:</td><td>1/8000 - 60 minutes</td></tr>
            <tr><td>Video Recording:</td><td>8K at 30fps, 4K at 60fps</td></tr>
            <tr><td>In-Body Stabilization:</td><td>8-stops (5-axis)</td></tr>
            <tr><td>Weight:</td><td>1030g (body only)</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Fujifilm GFX100 II Body</li>
            <li>Rechargeable Battery NP-W235 (2)</li>
            <li>AC Power Adapter</li>
            <li>USB-C Cable</li>
            <li>Professional Shoulder Strap</li>
            <li>128GB SD Card UHS-II</li>
            <li>Camera Bag</li>
        </ul>';
} elseif (strpos($productName, 'fujifilm gfx100s ii') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>Medium Format CMOS 43.8 x 32.9mm</td></tr>
            <tr><td>Resolution:</td><td>102 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>80-12,800 (Expandable to 51,200)</td></tr>
            <tr><td>Autofocus Points:</td><td>425 Phase-Detection Points</td></tr>
            <tr><td>Continuous Shooting:</td><td>7 fps</td></tr>
            <tr><td>Shutter Speed:</td><td>1/4000 - 60 minutes</td></tr>
            <tr><td>Video Recording:</td><td>4K at 30fps</td></tr>
            <tr><td>In-Body Stabilization:</td><td>6-stops (5-axis)</td></tr>
            <tr><td>Weight:</td><td>900g (body only)</td></tr>
            <tr><td>Design:</td><td>Compact Medium Format</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Fujifilm GFX100S II Body</li>
            <li>Rechargeable Battery NP-W235 (2)</li>
            <li>AC Power Adapter</li>
            <li>USB-C Cable</li>
            <li>Shoulder Strap</li>
            <li>64GB SD Card</li>
        </ul>';
} elseif (strpos($productName, 'fujifilm x-s20') !== false) {
    echo '
        <h4>Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>X-Trans CMOS 4 APS-C</td></tr>
            <tr><td>Resolution:</td><td>26.1 Megapixels</td></tr>
            <tr><td>ISO Range:</td><td>160-12,800 (Expandable to 80-51,200)</td></tr>
            <tr><td>Film Simulations:</td><td>19 Color Profiles</td></tr>
            <tr><td>Autofocus Points:</td><td>425 Phase-Detection AF Points</td></tr>
            <tr><td>Continuous Shooting:</td><td>8 fps (Mechanical), 30 fps (Electronic)</td></tr>
            <tr><td>Shutter Speed:</td><td>1/4000 - 15 minutes</td></tr>
            <tr><td>Video Recording:</td><td>6.2K at 30fps, 4K at 60fps</td></tr>
            <tr><td>In-Body Stabilization:</td><td>7-stops (5-axis)</td></tr>
            <tr><td>Weight:</td><td>491g (body only)</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Fujifilm X-S20 Body</li>
            <li>XC 15-45mm f/3.5-5.6 OIS PZ Lens</li>
            <li>Rechargeable Battery NP-W235</li>
            <li>USB-C Charger</li>
            <li>USB Cable</li>
            <li>Shoulder Strap</li>
            <li>32GB SD Card</li>
        </ul>';
}

// DJI Products
elseif (strpos($productName, 'dji mavic 4 pro') !== false) {
    echo '
        <h4>Drone Specifications</h4>
        <table>
            <tr><td>Weight:</td><td>895g</td></tr>
            <tr><td>Camera:</td><td>Hasselblad 4/3 CMOS Sensor with Triple Camera</td></tr>
            <tr><td>Main Camera Resolution:</td><td>20 Megapixels</td></tr>
            <tr><td>Video Recording:</td><td>5.1K at 50fps, 4K at 120fps</td></tr>
            <tr><td>Flight Time:</td><td>43 minutes (max)</td></tr>
            <tr><td>Max Speed:</td><td>75.6 km/h</td></tr>
            <tr><td>Wind Resistance:</td><td>up to 43.2 km/h (Level 6)</td></tr>
            <tr><td>Transmission Range:</td><td>15 km (O4 Transmission)</td></tr>
            <tr><td>Battery Capacity:</td><td>5000 mAh</td></tr>
            <tr><td>Obstacle Sensing:</td><td>Omnidirectional</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>DJI Mavic 4 Pro Drone</li>
            <li>Intelligent Flight Battery (3)</li>
            <li>Battery Charging Hub</li>
            <li>DJI RC Pro Controller</li>
            <li>Propellers (6 pairs)</li>
            <li>ND Filters Set (ND4/8/16/32/64)</li>
            <li>Professional Carrying Case</li>
            <li>128GB microSD Card</li>
        </ul>';
} elseif (strpos($productName, 'dji mini 5 pro') !== false) {
    echo '
        <h4>Drone Specifications</h4>
        <table>
            <tr><td>Weight:</td><td>Under 249g</td></tr>
            <tr><td>Camera:</td><td>1/1.3-inch CMOS Sensor</td></tr>
            <tr><td>Resolution:</td><td>48 Megapixels</td></tr>
            <tr><td>Video Recording:</td><td>4K at 60fps, 1080p at 240fps</td></tr>
            <tr><td>Flight Time:</td><td>34 minutes (max)</td></tr>
            <tr><td>Max Speed:</td><td>57.6 km/h</td></tr>
            <tr><td>Wind Resistance:</td><td>up to 38.5 km/h (Level 5)</td></tr>
            <tr><td>Transmission Range:</td><td>10 km (O3 Transmission)</td></tr>
            <tr><td>Battery Capacity:</td><td>2250 mAh</td></tr>
            <tr><td>Obstacle Sensing:</td><td>Tri-directional</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>DJI Mini 5 Pro Drone</li>
            <li>Intelligent Flight Battery (2)</li>
            <li>DJI RC 2 Controller</li>
            <li>Propellers (4 pairs)</li>
            <li>ND Filters (ND16/64/256)</li>
            <li>Gimbal Protector</li>
            <li>Carrying Case</li>
            <li>64GB microSD Card</li>
        </ul>';
} elseif (strpos($productName, 'dji inspire 3') !== false) {
    echo '
        <h4>Drone Specifications</h4>
        <table>
            <tr><td>Weight:</td><td>4305g (with Zenmuse X9-8K)</td></tr>
            <tr><td>Camera:</td><td>Full-Frame 8K Cinema Camera</td></tr>
            <tr><td>Resolution:</td><td>8K CinemaDNG / ProRes RAW</td></tr>
            <tr><td>Video Recording:</td><td>8K at 75fps, 4K at 120fps</td></tr>
            <tr><td>Flight Time:</td><td>28 minutes (max)</td></tr>
            <tr><td>Max Speed:</td><td>94 km/h</td></tr>
            <tr><td>Wind Resistance:</td><td>up to 54 km/h (Level 7)</td></tr>
            <tr><td>Transmission Range:</td><td>15 km (O3 Pro)</td></tr>
            <tr><td>Battery Capacity:</td><td>6280 mAh (TB51)</td></tr>
            <tr><td>Obstacle Sensing:</td><td>9 Sensors, 360° Protection</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>DJI Inspire 3 Drone with Zenmuse X9-8K</li>
            <li>TB51 Intelligent Battery (4)</li>
            <li>DJI RC Plus Controller (2)</li>
            <li>Battery Station</li>
            <li>Propellers (8 pairs)</li>
            <li>1TB PROSSD</li>
            <li>Professional Case</li>
            <li>Complete Accessory Kit</li>
        </ul>';
} elseif (strpos($productName, 'dji flip') !== false) {
    echo '
        <h4>Drone Specifications</h4>
        <table>
            <tr><td>Weight:</td><td>245g</td></tr>
            <tr><td>Camera:</td><td>1/1.3-inch CMOS Sensor</td></tr>
            <tr><td>Resolution:</td><td>48 Megapixels</td></tr>
            <tr><td>Video Recording:</td><td>4K at 60fps</td></tr>
            <tr><td>Flight Time:</td><td>31 minutes (max)</td></tr>
            <tr><td>Max Speed:</td><td>54 km/h</td></tr>
            <tr><td>Wind Resistance:</td><td>up to 34 km/h (Level 5)</td></tr>
            <tr><td>Transmission Range:</td><td>10 km</td></tr>
            <tr><td>Battery Capacity:</td><td>2420 mAh</td></tr>
            <tr><td>Design:</td><td>Foldable & Ultra-Portable</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>DJI Flip Drone</li>
            <li>Intelligent Flight Battery (2)</li>
            <li>DJI RC-N2 Controller</li>
            <li>Propellers (3 pairs)</li>
            <li>Carrying Bag</li>
            <li>32GB microSD Card</li>
            <li>USB-C Cable</li>
        </ul>';
} elseif (strpos($productName, 'dji') !== false && strpos($productName, 'mavic') !== false) {
    echo '
        <h4>Drone Specifications</h4>
        <table>
            <tr><td>Weight:</td><td>249g</td></tr>
            <tr><td>Camera:</td><td>Hasselblad 4/3 CMOS Sensor</td></tr>
            <tr><td>Resolution:</td><td>20 Megapixels</td></tr>
            <tr><td>Video Recording:</td><td>4K/60fps, 1080p/240fps</td></tr>
            <tr><td>Flight Time:</td><td>46 minutes (max)</td></tr>
            <tr><td>Max Speed:</td><td>75.6 km/h (S-mode)</td></tr>
            <tr><td>Wind Resistance:</td><td>up to 38.6 km/h</td></tr>
            <tr><td>Operating Temperature:</td><td>-10°C to 40°C</td></tr>
            <tr><td>Transmission Range:</td><td>10 km</td></tr>
            <tr><td>Battery Capacity:</td><td>5935 mAh</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>DJI Mavic Pro Drone (RTF)</li>
            <li>Intelligent Flight Battery (2)</li>
            <li>Battery Charging Hub</li>
            <li>Remote Controller</li>
            <li>Propellers (4 pairs)</li>
            <li>Carrying Case</li>
        </ul>';
} elseif (strpos($productName, 'dji') !== false && strpos($productName, 'air') !== false) {
    echo '
        <h4>Drone Specifications</h4>
        <table>
            <tr><td>Weight:</td><td>249g</td></tr>
            <tr><td>Camera:</td><td>Dual Camera System</td></tr>
            <tr><td>Main Camera Resolution:</td><td>48 Megapixels</td></tr>
            <tr><td>Video Recording:</td><td>4K/60fps</td></tr>
            <tr><td>Flight Time:</td><td>42 minutes (max)</td></tr>
            <tr><td>Max Speed:</td><td>68.4 km/h</td></tr>
            <tr><td>Wind Resistance:</td><td>up to 38.6 km/h</td></tr>
            <tr><td>Transmission Range:</td><td>15 km</td></tr>
            <tr><td>Battery Capacity:</td><td>2250 mAh</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>DJI Air 3 Drone</li>
            <li>Intelligent Flight Battery</li>
            <li>Remote Controller</li>
            <li>Propellers (4 pairs)</li>
            <li>ND Filters (ND4, ND8, ND16, ND32)</li>
            <li>Carrying Case</li>
        </ul>';
}

// Insta360 Products
elseif (strpos($productName, 'insta360 x4') !== false) {
    echo '
        <h4>360° Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>Dual 1/2-inch 48MP CMOS Sensors</td></tr>
            <tr><td>Photo Resolution:</td><td>72 Megapixels 360° (11968 x 5984)</td></tr>
            <tr><td>Video Resolution:</td><td>8K 360° at 30fps</td></tr>
            <tr><td>Video Format:</td><td>H.264/H.265</td></tr>
            <tr><td>Stabilization:</td><td>FlowState Stabilization + 360° Horizon Lock</td></tr>
            <tr><td>Waterproof Rating:</td><td>IPX8 (10m without case)</td></tr>
            <tr><td>Battery Life:</td><td>81 minutes continuous recording</td></tr>
            <tr><td>Storage:</td><td>Up to 1TB microSD support</td></tr>
            <tr><td>Weight:</td><td>203g</td></tr>
            <tr><td>Features:</td><td>Invisible Selfie Stick, AI Editing</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Insta360 X4 Camera</li>
            <li>Rechargeable Battery (2)</li>
            <li>USB-C Charging Cable</li>
            <li>Lens Cap</li>
            <li>Protective Pouch</li>
            <li>Mounting Bracket</li>
            <li>Quick Start Guide</li>
        </ul>';
} elseif (strpos($productName, 'insta360 ace pro 2') !== false) {
    echo '
        <h4>Action Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>1/1.3-inch 50MP CMOS Sensor</td></tr>
            <tr><td>Photo Resolution:</td><td>50 Megapixels</td></tr>
            <tr><td>Video Resolution:</td><td>8K at 30fps, 4K at 120fps</td></tr>
            <tr><td>Video Format:</td><td>H.265 Codec</td></tr>
            <tr><td>Stabilization:</td><td>FlowState Stabilization + Horizon Lock</td></tr>
            <tr><td>Waterproof Rating:</td><td>IPX8 (12m without case, 60m with case)</td></tr>
            <tr><td>Display:</td><td>2.4-inch Flip Touchscreen</td></tr>
            <tr><td>Battery Life:</td><td>100 minutes continuous recording</td></tr>
            <tr><td>Storage:</td><td>Up to 512GB microSD support</td></tr>
            <tr><td>Weight:</td><td>179.8g</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Insta360 Ace Pro 2 Camera</li>
            <li>Rechargeable Battery (2)</li>
            <li>60m Dive Case</li>
            <li>USB-C Charging Cable</li>
            <li>Mounting Accessories Kit</li>
            <li>Lens Guards</li>
            <li>Quick Release Mount</li>
            <li>64GB microSD Card</li>
        </ul>';
} elseif (strpos($productName, 'insta360 go 3s') !== false) {
    echo '
        <h4>Action Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>1/2.3-inch CMOS Sensor</td></tr>
            <tr><td>Photo Resolution:</td><td>11.24 Megapixels</td></tr>
            <tr><td>Video Resolution:</td><td>4K at 30fps, 2.7K at 50fps</td></tr>
            <tr><td>Video Format:</td><td>H.264/H.265</td></tr>
            <tr><td>Stabilization:</td><td>FlowState Stabilization</td></tr>
            <tr><td>Waterproof Rating:</td><td>IPX8 (10m)</td></tr>
            <tr><td>Weight:</td><td>39g (camera only)</td></tr>
            <tr><td>Design:</td><td>World\'s Smallest Action Camera</td></tr>
            <tr><td>Battery Life:</td><td>45 minutes continuous</td></tr>
            <tr><td>Magnetic Mount:</td><td>Built-in Strong Magnet</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Insta360 GO 3S Camera</li>
            <li>Action Pod (Charging Case & Remote)</li>
            <li>Pivot Stand</li>
            <li>Easy Clip</li>
            <li>Magnetic Pendant</li>
            <li>USB-C Cable</li>
            <li>32GB Storage (Built-in)</li>
        </ul>';
} elseif (strpos($productName, 'insta360 go ultra') !== false) {
    echo '
        <h4>Action Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>1/2-inch CMOS Sensor</td></tr>
            <tr><td>Photo Resolution:</td><td>16 Megapixels</td></tr>
            <tr><td>Video Resolution:</td><td>4K at 60fps, 2.7K at 120fps</td></tr>
            <tr><td>Video Format:</td><td>H.265 Codec</td></tr>
            <tr><td>Stabilization:</td><td>FlowState Stabilization + Horizon Lock</td></tr>
            <tr><td>Waterproof Rating:</td><td>IPX8 (15m)</td></tr>
            <tr><td>Weight:</td><td>45g (camera only)</td></tr>
            <tr><td>Design:</td><td>Ultra-Compact Wearable</td></tr>
            <tr><td>Battery Life:</td><td>60 minutes continuous</td></tr>
            <tr><td>Magnetic Mount:</td><td>Enhanced Magnetic System</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Insta360 GO Ultra Camera</li>
            <li>Action Pod Pro (Charging & Remote)</li>
            <li>Magnetic Pivot Stand</li>
            <li>Easy Clip Pro</li>
            <li>Magnetic Pendant</li>
            <li>USB-C Cable</li>
            <li>64GB Storage (Built-in)</li>
            <li>Protective Pouch</li>
        </ul>';
} elseif (strpos($productName, 'insta360') !== false) {
    echo '
        <h4>Action Camera Specifications</h4>
        <table>
            <tr><td>Sensor Type:</td><td>Dual 1/2-inch CMOS Sensors</td></tr>
            <tr><td>Resolution:</td><td>8K 360° Video</td></tr>
            <tr><td>Frame Rate:</td><td>24/30/60fps</td></tr>
            <tr><td>Video Format:</td><td>H.265 Codec</td></tr>
            <tr><td>Photo Resolution:</td><td>21 Megapixels 360°</td></tr>
            <tr><td>Waterproof Rating:</td><td>IP68 (up to 10m)</td></tr>
            <tr><td>Stabilization:</td><td>FlowState Stabilization</td></tr>
            <tr><td>Battery Life:</td><td>70 minutes continuous</td></tr>
            <tr><td>Storage:</td><td>512GB microSD support</td></tr>
            <tr><td>Weight:</td><td>213g</td></tr>
        </table>
        <h4>Included Accessories</h4>
        <ul>
            <li>Insta360 Ace Pro 2 Camera</li>
            <li>Rechargeable Battery (2)</li>
            <li>USB-C Charging Cable</li>
            <li>Waterproof Case</li>
            <li>Mounting Accessories</li>
            <li>ND Filters Set</li>
        </ul>';
} else {
    echo '
        <h4>General Specifications</h4>
        <table>
            <tr><td>Product Name:</td><td>' . htmlspecialchars($product['name']) . '</td></tr>
            <tr><td>SKU:</td><td>' . htmlspecialchars($product['sku'] ?? 'N/A') . '</td></tr>
            <tr><td>Price:</td><td>RM ' . number_format($product['price'], 2) . '</td></tr>
            <tr><td>Stock:</td><td>' . (int)($product['stock'] ?? 0) . ' units</td></tr>
        </table>';
}
?>
</div>

<?php
include '_foot.php';
?>
