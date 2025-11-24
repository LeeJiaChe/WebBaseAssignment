<?php
require '_base.php';
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

<?php
include '_foot.php';
