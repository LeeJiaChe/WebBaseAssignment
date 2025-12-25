<?php
require '_base.php';
$_title = 'Contact Us - VisionX';
$_bodyClass = 'transparent-header-page';
include '_head.php';
?>

<style>
.contact-header {
    background: linear-gradient(135deg, #333333 0%, #8c8c8c 100%);
    color: white;
    box-shadow: -8px 0 24px rgba(0, 0, 0, 0.25);
    padding: 135px 0 50px 0;
    margin: -20px -50px 30px -50px;
    text-align: center;
}

.contact-header h1 {
    margin: 0;
    font-size: 2.5rem;
}

.contact-header p {
    margin: 10px 0 0 0;
    font-size: 1.1rem;
    opacity: 0.9;
}

.contact-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    margin-top: 20px;
    padding: 0 20px;
}

.contact-info {
    background: #e5e5e5;
    padding: 30px;
    border-radius: 8px;
}

.contact-info h2 {
    margin-top: 0;
    color: #222;
    font-size: 1.8rem;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin: 25px 0;
}

.contact-item i {
    font-size: 24px;
    color: #7a7a7aff;
    width: 30px;
}

.contact-item-content h3 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
    color: #333;
}

.contact-item-content p {
    margin: 0;
    color: #666;
    line-height: 1.6;
}

.contact-form-section {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.contact-form-section h2 {
    margin-top: 0;
    color: #222;
    font-size: 1.8rem;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    outline: none;
    border-color: #1a73e8;
}

.submit-btn {
    background: #797979ff;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
}

.submit-btn:hover {
    background: #9e9d9d;
}

.social-links {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}

.social-links a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: #ffffffff;
    color: black;
    border-radius: 50%;
    text-decoration: none;
    transition: background 0.3s;
}

.social-links a:hover {
    background: #f1f1f1;

    color: #000;
}

@media (max-width: 768px) {
    .contact-container {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="contact-header">
    <h1>Contact Us</h1>
    <p>Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
</div>

<div class="contact-container">
    <!-- Contact Information -->
    <div class="contact-info">
        <h2>Get In Touch</h2>
        
        <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <div class="contact-item-content">
                <h3>Address</h3>
                <p>123 Camera Street<br>Photography District<br>Kuala Lumpur, Malaysia</p>
            </div>
        </div>

        <div class="contact-item">
            <i class="fas fa-phone"></i>
            <div class="contact-item-content">
                <h3>Phone</h3>
                <p>+60 3-1234 5678<br>Mon-Fri: 9:00 AM - 6:00 PM</p>
            </div>
        </div>

        <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <div class="contact-item-content">
                <h3>Email</h3>
                <p>support@visionx.com<br>sales@visionx.com</p>
            </div>
        </div>

        <div class="contact-item">
            <i class="fas fa-clock"></i>
            <div class="contact-item-content">
                <h3>Business Hours</h3>
                <p>Monday - Friday: 9:00 AM - 6:00 PM<br>
                   Saturday: 10:00 AM - 4:00 PM<br>
                   Sunday: Closed</p>
            </div>
        </div>

        <div class="social-links">
            <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
        </div>
    </div>

    <!-- Contact Form -->
    <div class="contact-form-section">
        <h2>Send Us a Message</h2>
        <form id="contactForm" method="post" action="api/contact.php">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" required placeholder="John Doe">
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required placeholder="john@example.com">
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="+60 12-345 6789">
            </div>

            <div class="form-group">
                <label for="subject">Subject *</label>
                <select id="subject" name="subject" required>
                    <option value="" disabled selected>Select a subject</option>
                    <option value="general">General Inquiry</option>
                    <option value="product">Product Question</option>
                    <option value="order">Order Status</option>
                    <option value="technical">Technical Support</option>
                    <option value="partnership">Business Partnership</option>
                </select>
            </div>

            <div class="form-group">
                <label for="message">Message *</label>
                <textarea id="message" name="message" required placeholder="Tell us how we can help you..."></textarea>
            </div>

            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </div>
</div>

<script>
function setContactMsg(form, text, isError) {
    let el = form.querySelector('.contact-msg');
    if (!el) {
        el = document.createElement('div');
        el.className = 'contact-msg';
        el.style.marginTop = '12px';
        el.style.padding = '12px';
        el.style.borderRadius = '4px';
        el.style.fontSize = '14px';
        form.appendChild(el);
    }
    el.textContent = text;
    if (isError) {
        el.style.background = '#ffebee';
        el.style.color = '#c62828';
        el.style.border = '1px solid #ffcdd2';
    } else {
        el.style.background = '#e8f5e9';
        el.style.color = '#2e7d32';
        el.style.border = '1px solid #c8e6c9';
    }
}

document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const fd = new FormData(form);
    const btn = form.querySelector('.submit-btn');
    
    btn.disabled = true;
    btn.textContent = 'Sending...';
    setContactMsg(form, 'Sending your message...', false);
    
    fetch(form.action, { method: 'POST', body: fd })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            setContactMsg(form, data.message || (data.success ? 'Message sent.' : 'Something went wrong'), !data.success);
            if (data.success) {
                form.reset();
            }
        })
        .catch(function() { 
            setContactMsg(form, 'Network error. Please try again.', true); 
        })
        .finally(function() {
            btn.disabled = false;
            btn.textContent = 'Send Message';
        });
});
</script>

<?php include '_foot.php'; ?>
