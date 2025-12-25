
<?php
require __DIR__ . '/../_base.php';

header('Content-Type: application/json');

try {
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		throw new Exception('Invalid request.');
	}

	$email = trim($_POST['email'] ?? '');
	if ($email === '') {
		throw new Exception('Please enter your email.');
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		throw new Exception('Please enter a valid email.');
	}

	// Send thank-you email to the subscriber
	$m = get_mail();
	$m->addAddress($email);
	$m->Subject = 'Welcome to VISIONX - Subscription Confirmed';
	$m->isHTML(true);
	$m->Body = '
	<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
		<div style="background: #222; color: #fff; padding: 30px; text-align: center;">
			<h1 style="margin: 0;">VISIONX</h1>
		</div>
		<div style="padding: 30px; background: #f9f9f9;">
			<h2 style="color: #222; margin-top: 0;">Welcome to VISIONX!</h2>
			<p style="color: #333; line-height: 1.6;">Dear Valued Customer,</p>
			<p style="color: #333; line-height: 1.6;">Thank you for subscribing to the VISIONX newsletter. We are delighted to have you join our community of photography and imaging enthusiasts.</p>
			<p style="color: #333; line-height: 1.6;">As a subscriber, you will receive:</p>
			<ul style="color: #333; line-height: 1.8;">
				<li>Exclusive promotions and special offers</li>
				<li>New product announcements from leading brands</li>
				<li>Professional photography tips and techniques</li>
				<li>Industry insights and latest trends</li>
			</ul>
			<p style="color: #333; line-height: 1.6;">We look forward to keeping you informed about the latest developments in camera equipment and accessories.</p>
			<p style="color: #333; line-height: 1.6;">Best regards,<br><strong>The VISIONX Team</strong><br>TARUMT | Camera & Drones</p>
		</div>
		<div style="background: #e5e5e5; padding: 20px; text-align: center; font-size: 12px; color: #666;">
			<p style="margin: 0;">&copy; 2025 VISIONX Official Store Malaysia. All rights reserved.</p>
		</div>
	</div>';
	$m->AltBody = "Welcome to VISIONX!\n\nDear Valued Customer,\n\nThank you for subscribing to the VISIONX newsletter. We are delighted to have you join our community of photography and imaging enthusiasts.\n\nAs a subscriber, you will receive:\n- Exclusive promotions and special offers\n- New product announcements from leading brands\n- Professional photography tips and techniques\n- Industry insights and latest trends\n\nWe look forward to keeping you informed about the latest developments in camera equipment and accessories.\n\nBest regards,\nThe VISIONX Team\nTARUMT | Premium Camera Equipment & Accessories";
	$m->send();

	echo json_encode(['success' => true, 'message' => 'Thank you for subscribing! Check your inbox for our note.']);
} catch (Exception $e) {
	http_response_code(400);
	echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
