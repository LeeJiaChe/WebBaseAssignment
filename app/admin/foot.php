    </main>    
    </main>    

        <section class="footer-top">
            <div class="footer-inner">
                <div class="footer-col footer-col-logo">
                    <a href="<?= '../index.php' ?>">
                        <img src="../images/VisionX.png" alt="VisionX Logo" class="footer-logo-img">
                    </a>
                </div>
                <div class="footer-col footer-col-about">
                    <h3>About VISIONX</h3>
                    <p>VISIONX is a trusted destination in Malaysia for cameras, drones, and imaging accessories. With extensive experience in the industry, we focus on delivering top-tier products from leading brands such as Canon, Fujifilm, DJI, Sony, Insta360, and more.</p>
                    <p>Fully operated by TARUMT, VISIONX is dedicated to offering high-quality gear at highly competitive prices. Our goal is to ensure complete customer satisfaction through reliable service, fast delivery, and dependable warranty support.</p>
                    <p>At VISIONX, you’ll find the ideal combination of what every creator needs: premium equipment, great value, quick shipping, and full after-sales assistance.</p>
                </div>
                <div class="footer-col footer-col-actions">
                    <h3>Newsletter</h3>
                    <form class="footer-newsletter" action="../api/subscribe.php" method="post" style="display:flex;flex-direction:column;gap:10px;align-items:flex-start;">
                        <input type="email" name="email" placeholder="Email Address" aria-label="Email address" required style="width:320px;max-width:100%;padding:14px 16px;border:1px solid #c9c9c9;border-radius:18px;font-size:16px;">
                        <button type="submit" style="padding:12px 20px;background:#000;color:#fff;border:none;border-radius:14px;font-weight:700;cursor:pointer;">Subscribe</button>
                    </form>

                    <h3 style="margin-top:20px">Follow Us</h3>
                    <div class="social-links">
                        <a href="https://www.facebook.com/tarumtkl" aria-label="Facebook" class="social-icon" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
                        <a href="https://www.instagram.com/jiache0331_/?utm_source=ig_web_button_share_sheet" aria-label="Instagram" class="social-icon" target="_blank" rel="noopener noreferrer"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <footer class="site-footer-bar">
            <div class="site-footer-inner">&copy; 2025 - VISIONX Official Store Malaysia</div>
        </footer>
<script>
function setNewsletterMsg(form, text, isError) {
    let el = form.querySelector('.newsletter-msg');
    if (!el) {
        el = document.createElement('div');
        el.className = 'newsletter-msg';
        el.style.marginTop = '6px';
        el.style.fontSize = '14px';
        form.appendChild(el);
    }
    el.textContent = text;
    el.style.color = isError ? '#c62828' : '#2e7d32';
}

document.querySelectorAll('form.footer-newsletter').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(form);

        // Instant feedback without waiting for the network
        setNewsletterMsg(form, 'Sending...', false);

        fetch(form.action, { method: 'POST', body: fd })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                setNewsletterMsg(form, data.message || (data.success ? 'Subscribed.' : 'Something went wrong'), !data.success);
            })
            .catch(function() { setNewsletterMsg(form, 'Network error. Please try again.', true); });
    });
});
</script>
</body>
</html>