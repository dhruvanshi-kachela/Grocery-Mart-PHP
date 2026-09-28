<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/CartController.php';

$cartController = new CartController();

// Route AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'apply_coupon') {
        $cartController->applyCoupon();
    } else {
        $cartController->handleCartAjax();
    }
}

// Route Coupon removal
if (isset($_GET['remove_coupon'])) {
    $cartController->removeCoupon();
}

// Default display cart
$cartController->cart();
