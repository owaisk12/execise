<?php
require_once 'config.php';

if (isLoggedIn()) redirect('dashboard.php');

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');

    if (!$email) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Generate a secure reset token
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $upd = $db->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $upd->bind_param("sss", $token, $expires, $email);
            $upd->execute();
            $upd->close();

            // ── In production: send this link by email ──────────────────
            // mail($email, "Reset your password",
            //      "Click: http://yourdomain.com/auth/reset.php?token=$token");

            // For demo: display the link on screen
            $reset_link = "reset.php?token=" . urlencode($token);
            $success = "Reset link generated (in production this is emailed):<br>
                        <a href=\"$reset_link\" style=\"color:var(--success);word-break:break-all;\">$reset_link</a>";
        } else {
            // Don't reveal whether the email exists
            $success = "If that email is registered, a reset link has been sent.";
        }

        $stmt->close();
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="card">
    <div class="logo">🔒</div>
    <h1>Reset password</h1>
    <p class="subtitle">Enter your email and we'll send a reset link</p>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" action="">
      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email"
               placeholder="you@example.com"
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
               required autofocus>
      </div>

      <button type="submit" class="btn">Send reset link</button>
    </form>
    <?php endif; ?>

    <div class="links">
      <a href="login.php">← Back to sign in</a>
    </div>
  </div>
</body>
</html>
