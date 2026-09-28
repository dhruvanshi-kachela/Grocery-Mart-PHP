<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/mail.php';
require_once __DIR__ . '/controllers/CheckoutController.php';

$checkoutController = new CheckoutController();

// Route to success page if order was completed
if (isset($_GET['success']) && isset($_GET['order_id'])) {
    $checkoutController->success();
} else {
    $checkoutController->checkout();
}
