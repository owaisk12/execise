<?php
require_once 'config.php';

if (isLoggedIn()) redirect('dashboard.php');

$error   = '';
$success = '';
$token   = sanitize($_GET['token'] ?? '');
$valid   = false;

if (!$token) redirect('login.php');

// Verify token is valid and not expired
$db   = getDB();
$stmt = $db->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();
$valid = $stmt->num_rows > 0;
$stmt->close();

if (!$valid) {
    $error = 'This reset link is invalid or has expired. <a href="forgot.php">Request a new one</a>.';
}

if ($valid && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!$password || !$confirm) {
        $error = 'Please fill in both fields.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $upd  = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
        $upd->bind_param("ss", $hash, $token);
        $upd->execute();
        $upd->close();
        $success = 'Your password has been updated. <a href="login.php">Sign in</a>.';
        $valid   = false; // hide form
    }
}
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Set New Password</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="card">
    <div class="logo">🔐</div>
    <h1>New password</h1>
    <p class="subtitle">Choose a strong password</p>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($valid && !$success): ?>
    <form method="POST" action="">
      <div class="field">
        <label for="password">New password</label>
        <input type="password" id="password" name="password"
               placeholder="Min. 6 characters" required autofocus>
      </div>

      <div class="field">
        <label for="confirm_password">Confirm password</label>
        <input type="password" id="confirm_password" name="confirm_password"
               placeholder="••••••••" required>
      </div>

      <button type="submit" class="btn">Update password</button>
    </form>
    <?php endif; ?>

    <div class="links">
      <a href="login.php">← Back to sign in</a>
    </div>
  </div>
</body>
</html>
