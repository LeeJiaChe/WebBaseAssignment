<?php
require __DIR__ . '/../_base.php';

header('Content-Type: application/json');

try {
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		throw new Exception('Invalid request.');
	}

	$name = trim($_POST['name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$phone = trim($_POST['phone'] ?? '');
	$subject = trim($_POST['subject'] ?? '');
	$message = trim($_POST['message'] ?? '');

	if ($name === '') {
		throw new Exception('Please enter your name.');
	}
	if ($email === '') {
		throw new Exception('Please enter your email.');
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		throw new Exception('Please enter a valid email.');
	}
	if ($subject === '') {
		throw new Exception('Please select a subject.');
	}
	if ($message === '') {
		throw new Exception('Please enter your message.');
	}

	$subjectLabels = [
		'general' => 'General Inquiry',
		'product' => 'Product Question',
		'order' => 'Order Status',
		'technical' => 'Technical Support',
		'partnership' => 'Business Partnership'
	];
	$subjectLabel = $subjectLabels[$subject] ?? 'Contact Form';

	// Send email to site inbox
	$m = get_mail();
	$m->addAddress('leedemoweb123@gmail.com');
	$m->addReplyTo($email, $name);
	$m->Subject = 'VISIONX Contact Form: ' . $subjectLabel;
	$m->isHTML(true);
	$m->Body = '
	<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
		<div style="background: #222; color: #fff; padding: 20px;">
			<h2 style="margin: 0;">New Contact Form Submission</h2>
		</div>
		<div style="padding: 20px; background: #f9f9f9;">
			<table style="width: 100%; border-collapse: collapse;">
				<tr>
					<td style="padding: 8px; font-weight: bold; width: 120px;">Name:</td>
					<td style="padding: 8px;">' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</td>
				</tr>
				<tr>
					<td style="padding: 8px; font-weight: bold;">Email:</td>
					<td style="padding: 8px;">' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</td>
				</tr>
				<tr>
					<td style="padding: 8px; font-weight: bold;">Phone:</td>
					<td style="padding: 8px;">' . htmlspecialchars($phone ?: 'Not provided', ENT_QUOTES, 'UTF-8') . '</td>
				</tr>
				<tr>
					<td style="padding: 8px; font-weight: bold;">Subject:</td>
					<td style="padding: 8px;">' . htmlspecialchars($subjectLabel, ENT_QUOTES, 'UTF-8') . '</td>
				</tr>
			</table>
			<div style="margin-top: 20px;">
				<strong>Message:</strong>
				<div style="margin-top: 10px; padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
					' . nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')) . '
				</div>
			</div>
		</div>
		<div style="background: #e5e5e5; padding: 15px; text-align: center; font-size: 12px; color: #666;">
			<p style="margin: 0;">This message was sent from the VISIONX contact form.</p>
		</div>
	</div>';
	$m->AltBody = "New Contact Form Submission\n\nName: $name\nEmail: $email\nPhone: " . ($phone ?: 'Not provided') . "\nSubject: $subjectLabel\n\nMessage:\n$message";
	$m->send();

	echo json_encode(['success' => true, 'message' => 'Thank you for contacting us! We will respond shortly.']);
} catch (Exception $e) {
	http_response_code(400);
	echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
