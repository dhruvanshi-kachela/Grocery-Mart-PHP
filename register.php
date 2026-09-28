<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/AuthController.php';

// Redirect to home if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$auth = new AuthController();
$auth->register();

// View file is loaded
include __DIR__ . '/views/register.php';
