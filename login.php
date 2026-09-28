<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController();

// Handle logout FIRST — before the "already logged in" check
if (isset($_GET['logout'])) {
    $auth->logout();
}

// Redirect to home if user is already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$auth->login();

// View file is loaded
include __DIR__ . '/views/login.php';
