<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/CartController.php';

$cartController = new CartController();

// Route AJAX wishlist toggling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $cartController->handleWishlistAjax();
}

// Default display wishlist
$cartController->wishlist();
