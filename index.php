<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Guests see the landing page; logged-in users get the full home page
if (!isset($_SESSION['user_id'])) {
    header("Location: landing.php");
    exit();
}

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Product.php';

$categoryModel = new Category();
$productModel  = new Product();

// Retrieve categories and products for homepage
$categories          = $categoryModel->getActiveCategories();
$popular_products    = $productModel->getPopularProducts(8);
$discounted_products = $productModel->getDiscountedProducts(8);

// Load view
include __DIR__ . '/views/home.php';
