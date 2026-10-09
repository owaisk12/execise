<?php
// ─── Database Configuration ───────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'auth_demo');

// ─── Session start ─────────────────────────────────────────────────────────
session_start();

// ─── DB Connection ─────────────────────────────────────────────────────────
function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

// ─── Helpers ───────────────────────────────────────────────────────────────
function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
