<?php
require '_base.php';
$_bodyClass = 'index-page';
include '_head.php';
?>

<div class="slider-container">
	<div class="slider" id="ads-slider">
		<img src="/images/sony_ads1.jpg" alt="Sony Ad" class="slider-img">
		<img src="/images/canon_ads2.jpg" alt="Canon Ad 2" class="slider-img">
		<img src="/images/Fujifilm_ads1.jpg" alt="Fujifilm Ad" class="slider-img">
		<img src="/images/DJI_ads1.jpg" alt="DJI Ad" class="slider-img">
		<img src="/images/insta360_ads1.jpg" alt="Insta360 Ad" class="slider-img">
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
// Load DB and product helpers and render featured products dynamically
$pdo = require __DIR__ . '/lib/db.php';
require_once __DIR__ . '/lib/products.php';
$featured = get_featured_products($pdo, 6);
?>
<section class="featured-products" style="padding:40px 20px; max-width:1200px; margin:40px auto;">
	<h2 style="font-size:1.6rem; margin-bottom:18px; text-align:center;">Featured Products</h2>
	<div class="products-grid">
		<?php foreach ($featured as $p): ?>
			<?php $id = (int)$p['id']; ?>
			<div class="product-card">
				<img src="<?= htmlspecialchars($p['image_path'] ?? '/images/placeholder.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>" />
				<div class="product-info">
					<div>
						<div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
						<div class="product-subtitle"><?= htmlspecialchars($p['description'] ?? '') ?></div>
					</div>
					<div class="product-footer">
						<a class="learn-link" href="product.php?id=<?= $id ?>">Learn more</a>
						<a class="buy-btn" href="cart.php?product_id=<?= $id ?>">Buy now</a>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>

<?php
include '_foot.php';
