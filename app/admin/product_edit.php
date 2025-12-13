<?php
require __DIR__ . '/_auth.php';
require __DIR__ . '/../lib/db.php';
require __DIR__ . '/../_base.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
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
            $stmt = $pdo->prepare('UPDATE products SET sku=:sku,name=:name,description=:description,price=:price,currency=:currency,featured=:featured'.(isset($image_path)?',image_path=:image_path':'').' WHERE id=:id');
            $params = [':sku'=>$sku,':name'=>$name,':description'=>$description,':price'=>$price,':currency'=>$currency,':featured'=>$featured,':id'=>$id];
            if (isset($image_path)) $params[':image_path']=$image_path;
            $stmt->execute($params);
        } else {
            $stmt = $pdo->prepare('INSERT INTO products (sku,name,description,price,currency,image_path,featured,created_at) VALUES (:sku,:name,:description,:price,:currency,:image_path,:featured,NOW())');
            $stmt->execute([':sku'=>$sku,':name'=>$name,':description'=>$description,':price'=>$price,':currency'=>$currency,':image_path'=>$image_path ?? '',':featured'=>$featured]);
        }
        header('Location: products.php'); exit;
    }
}

$_title = $id ? 'Edit Product' : 'Create Product';
require __DIR__ . '/../_head.php';
?>

<section class="content">
    <h1><?= htmlspecialchars($_title) ?></h1>
    <?php if (!empty($errors)): ?>
        <div class="errors"><ul><?php foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>';?></ul></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div><label>SKU</label><br><input name="sku" class="form-input" value="<?= htmlspecialchars($product['sku']) ?>"></div>
        <div><label>Name</label><br><input name="name" class="form-input" value="<?= htmlspecialchars($product['name']) ?>"></div>
        <div><label>Description</label><br><textarea name="description" class="form-input" rows="6"><?= htmlspecialchars($product['description']) ?></textarea></div>
        <div><label>Price</label><br><input name="price" class="form-input" value="<?= htmlspecialchars($product['price']) ?>"></div>
        <div><label>Currency</label><br><input name="currency" class="form-input" value="<?= htmlspecialchars($product['currency'] ?? 'USD') ?>"></div>
        <div><label>Featured</label> <input type="checkbox" name="featured" <?= !empty($product['featured']) ? 'checked':'' ?>></div>
        <div><label>Image</label><br><?php if (!empty($product['image_path'])): ?><img src="<?= htmlspecialchars($product['image_path']) ?>" style="height:80px;display:block;margin-bottom:8px;"/><?php endif; ?><input type="file" name="image"></div>
        <div style="margin-top:12px;"><button class="btn" type="submit">Save</button> <a href="products.php">Cancel</a></div>
    </form>
</section>

<?php require __DIR__ . '/../_foot.php'; ?>
