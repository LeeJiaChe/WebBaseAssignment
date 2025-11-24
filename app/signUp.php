<?php
session_start();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = trim($_POST['name'] ?? '');
	$email = $_POST['email'] ?? '';
	$password = $_POST['password'] ?? '';
	$password2 = $_POST['password2'] ?? '';

	if ($name === '') {
		$errors[] = 'Please enter your name.';
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = 'Please enter a valid email address.';
	}
	if (strlen($password) < 6) {
		$errors[] = 'Password must be at least 6 characters.';
	}
	if ($password !== $password2) {
		$errors[] = 'Passwords do not match.';
	}

	if (empty($errors)) {
		$usersFile = __DIR__ . '/users.json';
		$users = [];
		if (file_exists($usersFile)) {
			$raw = file_get_contents($usersFile);
			$users = json_decode($raw, true) ?: [];
		}

		// check duplicate
		foreach ($users as $u) {
			if (strcasecmp($u['email'], $email) === 0) {
				$errors[] = 'An account with that email already exists.';
				break;
			}
		}

		if (empty($errors)) {
			$users[] = [
				'name' => $name,
				'email' => $email,
				'password_hash' => password_hash($password, PASSWORD_DEFAULT),
				'created' => time(),
			];

				file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

				// Registration complete — redirect to login so user can sign in
				header('Location: /login.php?registered=1');
				exit;
		}
	}
}

$_title = 'Sign Up';
require __DIR__ . '/_head.php';
?>

<section class="content auth-page">
	<div class="auth-box">
		<h1>Create account</h1>

		<?php if (!empty($errors)): ?>
			<div class="errors" style="color: #b00020; margin-bottom: 12px;">
				<ul>
					<?php foreach ($errors as $e): ?>
						<li><?= htmlspecialchars($e) ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<form method="post" action="">
			<div>
				<label for="name">Full name</label><br>
				<input id="name" name="name" type="text" required class="form-input" value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
			</div>
			<div style="margin-top:8px;">
				<label for="email">Email</label><br>
				<input id="email" name="email" type="email" required class="form-input" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
			</div>
			<div style="margin-top:8px;">
				<label for="password">Password</label><br>
				<input id="password" name="password" type="password" required class="form-input">
			</div>
			<div style="margin-top:8px;">
				<label for="password2">Confirm password</label><br>
				<input id="password2" name="password2" type="password" required class="form-input">
			</div>
			<div style="margin-top:12px;">
				<button type="submit" class="btn">Create account</button>
			</div>
		</form>

		<p style="margin-top:12px;">Already have an account? <a href="/login.php">Log in</a></p>
	</div>
</section>

<?php require __DIR__ . '/_foot.php';

