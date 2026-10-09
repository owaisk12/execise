<?php
require_once 'config.php';

if (!isLoggedIn()) redirect('login.php');

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .pill {
      display: inline-block;
      background: rgba(108,99,255,.15);
      border: 1px solid rgba(108,99,255,.3);
      color: #a09aff;
      border-radius: 999px;
      padding: .25rem .8rem;
      font-size: .8125rem;
      font-weight: 500;
      margin-bottom: 1rem;
    }
    .card { text-align: center; }
    .logout {
      display: inline-block;
      margin-top: 1.2rem;
      color: var(--muted);
      font-size: .875rem;
      text-decoration: none;
    }
    .logout:hover { color: var(--danger); }
  </style>
</head>
<body>
  <div class="card">
    <div class="logo">✅</div>
    <div class="pill">Authenticated</div>
    <h1>Hello, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
    <p class="subtitle" style="margin-top:.5rem;">
      You're signed in. This is your protected dashboard.
    </p>
    <br>
    <a href="?logout=1" class="logout">Sign out →</a>
  </div>
</body>
</html>
