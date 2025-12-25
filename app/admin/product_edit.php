<?php
require __DIR__ . '/_auth.php';
require __DIR__ . '/../_base.php';

global $db;
if (!isset($db)) {
    $db = require __DIR__ . '/../lib/db.php';
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

if ($id) {
    $stmt = $db->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();
    if (!$product) {
        echo 'Product not found'; exit;
    }
} else {
    $product = ['sku'=>'','name'=>'','description'=>'','price'=>'','currency'=>'USD','image_path'=>'','featured'=>0];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $currency = trim($_POST['currency'] ?? 'USD');
    $featured = isset($_POST['featured']) ? 1 : 0;

    if ($sku === '' || $name === '') {
        $errors[] = 'SKU and name are required.';
    }

    // handle image upload
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $f = $_FILES['image'];
        $allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/gif'=>'.gif'];
        if (!isset($allowed[$f['type']])) {
            $errors[] = 'Invalid image type.';
        } else {
            $ext = $allowed[$f['type']];
            $safe = preg_replace('/[^a-z0-9._-]/i','_', $sku);
            $dir = __DIR__ . '/../images/products';
            if (!is_dir($dir)) mkdir($dir,0755,true);
            $fname = 'images/products/'.$safe.'_'.time().$ext;
            if (!move_uploaded_file($f['tmp_name'], __DIR__.'/..'.'/'.$fname)) {
                $errors[] = 'Failed to move uploaded file.';
            } else {
                $image_path = $fname;
            }
        }
    }

    if (empty($errors)) {
        if ($id) {
            $stmt = $db->prepare('UPDATE products SET sku=:sku,name=:name,description=:description,price=:price,currency=:currency,featured=:featured'.(isset($image_path)?',image_path=:image_path':'').' WHERE id=:id');
            $params = [':sku'=>$sku,':name'=>$name,':description'=>$description,':price'=>$price,':currency'=>$currency,':featured'=>$featured,':id'=>$id];
            if (isset($image_path)) $params[':image_path']=$image_path;
            $stmt->execute($params);
        } else {
            $stmt = $db->prepare('INSERT INTO products (sku,name,description,price,currency,image_path,featured,created_at) VALUES (:sku,:name,:description,:price,:currency,:image_path,:featured,NOW())');
            $stmt->execute([':sku'=>$sku,':name'=>$name,':description'=>$description,':price'=>$price,':currency'=>$currency,':image_path'=>$image_path ?? '',':featured'=>$featured]);
        }
        header('Location: products.php'); exit;
    }
}

$_title = $id ? 'Edit Product' : 'Create Product';
require __DIR__ . '/head.php';
?>

<style>
.edit-product-container {
    max-width: 900px;
    margin: 0 auto;
}

.page-header {
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-header h1 {
    margin: 0;
    font-size: 28px;
    color: #333;
    font-weight: 700;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #f5f5f5;
    color: #666;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.back-link:hover {
    background: #e0e0e0;
    color: #333;
}

.error-alert {
    background: #fff3f3;
    border: 1px solid #ffcdd2;
    border-radius: 8px;
    padding: 16px 20px;
    margin-bottom: 24px;
    color: #c62828;
}

.error-alert ul {
    margin: 0;
    padding-left: 20px;
}

.error-alert li {
    margin: 4px 0;
}

.product-form {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 40px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}

.form-group {
    margin-bottom: 24px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.form-label .required {
    color: #e53935;
    margin-left: 4px;
}

.form-input,
.form-textarea,
.form-select {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
    outline: none;
    border-color: #2b7cff;
    box-shadow: 0 0 0 3px rgba(43,124,255,0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 120px;
}

.form-checkbox-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
}

.form-checkbox {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.checkbox-label {
    font-size: 15px;
    color: #333;
    cursor: pointer;
    user-select: none;
}

.image-upload-section {
    padding: 24px;
    border: 2px dashed #ddd;
    border-radius: 8px;
    background: #fafafa;
    text-align: center;
}

.current-image {
    margin-bottom: 20px;
}

.current-image img {
    max-width: 200px;
    max-height: 200px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.current-image-label {
    display: block;
    font-size: 13px;
    color: #666;
    margin-bottom: 12px;
    font-weight: 600;
}

.file-input-wrapper {
    position: relative;
    display: inline-block;
}

.file-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.file-input-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #2b7cff;
    color: white;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-input-button:hover {
    background: #1a6edf;
}

.file-name-display {
    margin-top: 12px;
    font-size: 13px;
    color: #666;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #eee;
}

.btn-primary {
    padding: 14px 32px;
    background: #2b7cff;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: #1a6edf;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(43,124,255,0.3);
}

.btn-secondary {
    padding: 14px 32px;
    background: #f5f5f5;
    color: #666;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: #e0e0e0;
    color: #333;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .product-form {
        padding: 24px;
    }
    
    .form-actions {
        flex-direction: column-reverse;
    }
    
    .btn-primary,
    .btn-secondary {
        width: 100%;
        text-align: center;
    }
}
</style>

<div class="edit-product-container">
    <div class="page-header">
        <h1><?= htmlspecialchars($_title) ?></h1>
        <a href="products.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="error-alert">
            <strong><i class="fas fa-exclamation-circle"></i> Error:</strong>
            <ul>
                <?php foreach($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="product-form">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">
                    SKU<span class="required">*</span>
                </label>
                <input type="text" name="sku" class="form-input" value="<?= htmlspecialchars($product['sku']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Currency<span class="required">*</span>
                </label>
                <input type="text" name="currency" class="form-input" value="<?= htmlspecialchars($product['currency'] ?? 'RM') ?>" required>
            </div>

            <div class="form-group full-width">
                <label class="form-label">
                    Product Name<span class="required">*</span>
                </label>
                <input type="text" name="name" class="form-input" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>

            <div class="form-group full-width">
                <label class="form-label">
                    Description
                </label>
                <textarea name="description" class="form-textarea"><?= htmlspecialchars($product['description']) ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Price<span class="required">*</span>
                </label>
                <input type="number" step="0.01" name="price" class="form-input" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Featured Product
                </label>
                <div class="form-checkbox-wrapper">
                    <input type="checkbox" name="featured" id="featured" class="form-checkbox" <?= !empty($product['featured']) ? 'checked':'' ?>>
                    <label for="featured" class="checkbox-label">Mark this product as featured</label>
                </div>
            </div>
        </div>

        <div class="form-group full-width">
            <label class="form-label">Product Image</label>
            <div class="image-upload-section">
                <?php if (!empty($product['image_path'])): ?>
                    <div class="current-image">
                        <span class="current-image-label">Current Image:</span>
                        <img src="../<?= htmlspecialchars($product['image_path']) ?>" alt="Product Image">
                    </div>
                <?php endif; ?>
                
                <div class="file-input-wrapper">
                    <input type="file" name="image" class="file-input" id="imageInput" accept="image/*">
                    <label for="imageInput" class="file-input-button">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <?= !empty($product['image_path']) ? 'Change Image' : 'Upload Image' ?>
                    </label>
                </div>
                <div class="file-name-display" id="fileName">
                    <?= !empty($product['image_path']) ? 'Choose a new file to update the image' : 'No file chosen' ?>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="products.php" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Save Product
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'No file chosen';
    document.getElementById('fileName').textContent = fileName;
});
</script>

<?php require __DIR__ . '/foot.php'; ?>
