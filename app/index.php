<?php
require '_base.php';
$_bodyClass = 'index-page';
include '_head.php';
?>

<div class="slider-container">
	<div class="slider" id="ads-slider">
		<img src="/images/sony_ads1.jpg" alt="Sony Ad" class="slider-img">
		<img src="/images/canon_ads1.png" alt="Canon Ad 2" class="slider-img">
		<img src="/images/Fujifilm_ads2.png" alt="Fujifilm Ad" class="slider-img">
		<img src="/images/DJI_ads1.jpg" alt="DJI Ad" class="slider-img">
		<img src="/images/insta360_ads2.jpg" alt="Insta360 Ad" class="slider-img">
	</div>
	<button class="slider-btn prev" id="slider-prev">&#8249;</button>
	<button class="slider-btn next" id="slider-next">&#8250;</button>
	<div class="dot-container">
		<span class="dot" onclick="currentSlide(1)"></span>
		<span class="dot" onclick="currentSlide(2)"></span>
		<span class="dot" onclick="currentSlide(3)"></span>
		<span class="dot" onclick="currentSlide(4)"></span>
		<span class="dot" onclick="currentSlide(5)"></span>
	</div>
</div>

<script>
	const slider = document.getElementById('ads-slider');
	const images = slider.getElementsByClassName('slider-img');
    
	var slideIndex = 1;
	showSlides(slideIndex);

	function plusSlides(n) {
	  showSlides(slideIndex += n);
	}

	function currentSlide(n) {
	  showSlides(slideIndex = n);
	}

	function showSlides(n) {
	  var i;
	  var slides = document.getElementsByClassName("slider-img");
	  var dots = document.getElementsByClassName("dot");
	  if (n > slides.length) {slideIndex = 1}    
	  if (n < 1) {slideIndex = slides.length}
	  for (i = 0; i < slides.length; i++) {
	      slides[i].style.display = "none";  
	  }
	  for (i = 0; i < dots.length; i++) {
	      dots[i].className = dots[i].className.replace(" active", "");
	  }
	  slides[slideIndex-1].style.display = "block";  
	  dots[slideIndex-1].className += " active";
	}

	document.getElementById('slider-next').onclick = () => plusSlides(1);
	document.getElementById('slider-prev').onclick = () => plusSlides(-1);
	setInterval(() => plusSlides(1), 5000);

</script>

<!-- Featured Products -->
<?php
// Load DB and build a diverse featured set inline (no helper needed here)
global $db;
if (!isset($db)) {
	$db = require __DIR__ . '/lib/db.php';
}

// Pull a pool of latest featured items
$poolLimit = 60;
$stmt = $db->prepare('SELECT id, sku, name, description, price, currency, image_path, created_at FROM products WHERE featured = 1 ORDER BY created_at DESC LIMIT :limit');
$stmt->bindValue(':limit', $poolLimit, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();

$brandsWanted = ['Canon','Sony','Fujifilm','Insta360','DJI'];
$detectBrand = function ($name, $imagePath) {
	$map = [
		'Canon' => ['images/canon_product/'],
		'Sony' => ['images/sony_product/'],
		'Fujifilm' => ['images/fujifilm_product/'],
		'Insta360' => ['images/insta360_product/'],
		'DJI' => ['images/dji_product/'],
	];
	foreach ($map as $brand => $folders) {
		if (stripos($name, $brand) === 0) return $brand;
		foreach ($folders as $folder) {
			if (!empty($imagePath) && stripos($imagePath, $folder) !== false) return $brand;
		}
	}
	return 'Other';
};

$featured = [];
$selectedIds = [];
$brandSeen = [];

// First pass: newest featured per desired brand
foreach ($brandsWanted as $wanted) {
	foreach ($rows as $r) {
		$brand = $detectBrand($r['name'] ?? '', $r['image_path'] ?? '');
		if ($brand === $wanted && !in_array($r['id'], $selectedIds, true)) {
			$featured[] = $r;
			$selectedIds[] = $r['id'];
			$brandSeen[$brand] = true;
			break;
		}
	}
}

// Second pass: fill remaining slots with newest featured overall
foreach ($rows as $r) {
	if (count($featured) >= 9) break;
	if (!in_array($r['id'], $selectedIds, true)) {
		$featured[] = $r;
		$selectedIds[] = $r['id'];
		$brand = $detectBrand($r['name'] ?? '', $r['image_path'] ?? '');
		$brandSeen[$brand] = true;
	}
}

// If still missing brands, pull newest per missing brand (even if not featured)
if (count(array_intersect(array_keys($brandSeen), $brandsWanted)) < 5 && count($featured) < 9) {
	foreach ($brandsWanted as $wanted) {
		if (isset($brandSeen[$wanted])) continue;
		if (count($featured) >= 9) break;
		$q = $db->prepare('SELECT id, sku, name, description, price, currency, image_path, created_at FROM products WHERE name LIKE :prefix ORDER BY created_at DESC LIMIT 1');
		$q->execute([':prefix' => $wanted.'%']);
		$extra = $q->fetch();
		if ($extra && !in_array($extra['id'], $selectedIds, true)) {
			$featured[] = $extra;
			$selectedIds[] = $extra['id'];
			$brandSeen[$wanted] = true;
		}
	}
}
?>
<section class="featured-products" style="padding:40px 20px; max-width:1200px; margin:40px auto;">
	<h2 style="font-size:1.6rem; margin-bottom:18px; text-align:center;">Featured Products</h2>
	<div class="products-grid">
		<?php foreach ($featured as $p): ?>
			<?php $id = (int)$p['id']; ?>
			<div class="product-card">
				<a href="/product.php?id=<?= $id ?>" aria-label="View <?= htmlspecialchars($p['name']) ?>">
					<img src="<?= htmlspecialchars($p['image_path'] ?? '/images/placeholder.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>" />
				</a>
				<div class="product-info">
					<div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
					<div class="product-subtitle"><?= htmlspecialchars($p['description'] ?? '') ?></div>
					<div style="text-align:center; color:#555; margin-top:6px;">RM <?= number_format((float)$p['price'], 2) ?></div>
					<div style="text-align:center; margin-top:10px;">
						<a class="btn btn-transparent" href="/product.php?id=<?= $id ?>">View</a>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<?php
include '_foot.php';
