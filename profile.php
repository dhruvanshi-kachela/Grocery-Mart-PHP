<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Address.php';
require_once __DIR__ . '/models/Order.php';

// Enforce auth
AuthController::checkAuth();
$userId = $_SESSION['user_id'];

$userModel = new User();
$addressModel = new Address();
$orderModel = new Order();

$page = $_GET['page'] ?? 'dashboard';
$error = '';
$success = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Update profile info
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($name) || empty($mobile)) {
            $_SESSION['error'] = "Name and Mobile number are required.";
        } elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
            $_SESSION['error'] = "Mobile number must be exactly 10 digits.";
        } else {
            // Update base details
            $result = $userModel->updateProfile($userId, $name, $mobile);
            if ($result) {
                $_SESSION['user_name'] = $name; // Update session name
                $_SESSION['success'] = "Profile details updated successfully!";
                
                // If password is also provided, update it
                if (!empty($password)) {
                    if (strlen($password) < 8) {
                        $_SESSION['error'] = "Profile details updated, but password must be at least 8 characters long.";
                    } else {
                        $userModel->updatePassword($userId, $password);
                        $_SESSION['success'] = "Profile and password updated successfully!";
                    }
                }
            } else {
                $_SESSION['error'] = "Failed to update profile details.";
            }
        }
        header("Location: profile.php?page=profile");
        exit();
    }

    // 2. Add shipping address
    if (isset($_POST['action']) && $_POST['action'] === 'add_address') {
        $full_address = trim($_POST['full_address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $zipcode = trim($_POST['zipcode'] ?? '');

        if (!empty($full_address) && !empty($city) && !empty($state) && !empty($zipcode)) {
            $addrId = $addressModel->add($userId, $full_address, $city, $state, $zipcode, 'India');
            if ($addrId) {
                $_SESSION['success'] = "Address added successfully!";
            } else {
                $_SESSION['error'] = "Failed to add address.";
            }
        } else {
            $_SESSION['error'] = "All address fields are required.";
        }
        header("Location: profile.php?page=addresses");
        exit();
    }

    // 3. Edit shipping address
    if (isset($_POST['action']) && $_POST['action'] === 'edit_address') {
        $addrId = intval($_POST['address_id'] ?? 0);
        $full_address = trim($_POST['full_address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $zipcode = trim($_POST['zipcode'] ?? '');

        if ($addrId > 0 && !empty($full_address) && !empty($city) && !empty($state) && !empty($zipcode)) {
            // Verify address belongs to user before editing
            $existing = $addressModel->findById($addrId, $userId);
            if ($existing) {
                $result = $addressModel->update($addrId, $userId, $full_address, $city, $state, $zipcode, 'India');
                if ($result) {
                    $_SESSION['success'] = "Address updated successfully!";
                } else {
                    $_SESSION['error'] = "Failed to update address.";
                }
            } else {
                $_SESSION['error'] = "Unauthorized action.";
            }
        } else {
            $_SESSION['error'] = "All address fields are required.";
        }
        header("Location: profile.php?page=addresses");
        exit();
    }
}

// Handle GET actions
// 1. Delete shipping address
if (isset($_GET['delete_address'])) {
    $addrId = intval($_GET['delete_address']);
    if ($addrId > 0) {
        $result = $addressModel->delete($addrId, $userId);
        if ($result) {
            $_SESSION['success'] = "Address deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete address.";
        }
    }
    header("Location: profile.php?page=addresses");
    exit();
}

// 2. Cancel order
if (isset($_GET['cancel_order'])) {
    $orderId = intval($_GET['cancel_order']);
    if ($orderId > 0) {
        $result = $orderModel->cancelByUser($orderId, $userId);
        if ($result) {
            $_SESSION['success'] = "Order successfully cancelled and stocks restored.";
        } else {
            $_SESSION['error'] = "Failed to cancel order. Shipped orders cannot be cancelled.";
        }
    }
    header("Location: profile.php?page=orders");
    exit();
}

// Fetch dashboard data based on page view
$user = $userModel->findById($userId);
$addresses = $addressModel->getByUser($userId);
$orders = $orderModel->getByUser($userId);

// Calculate stats for dashboard overview
$stats = [
    'total_orders' => count($orders),
    'pending_orders' => 0,
    'delivered_orders' => 0,
    'wishlist_count' => 0
];
foreach ($orders as $o) {
    if (in_array($o['order_status'], ['Placed', 'Packed', 'Shipped', 'Out for Delivery'])) {
        $stats['pending_orders']++;
    } elseif ($o['order_status'] === 'Delivered') {
        $stats['delivered_orders']++;
    }
}

// Wishlist count
try {
    $stmt_w = getDB()->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = :uid");
    $stmt_w->execute(['uid' => $userId]);
    $stats['wishlist_count'] = intval($stmt_w->fetchColumn());
} catch (PDOException $e) {
    error_log("Error wishlist count: " . $e->getMessage());
}

// If specific order is requested for tracking/details
$order_details = null;
$order_items = [];
if ($page === 'orders' && isset($_GET['order_id'])) {
    $orderId = intval($_GET['order_id']);
    $order_details = $orderModel->findByIdAndUser($orderId, $userId);
    if ($order_details) {
        $order_items = $orderModel->getOrderItems($orderId);
    } else {
        $_SESSION['error'] = "Order not found.";
        header("Location: profile.php?page=orders");
        exit();
    }
}

// Load unified view
include __DIR__ . '/views/dashboard.php';
