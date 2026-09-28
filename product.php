<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/ProductController.php';

$controller = new ProductController();

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Please log in to submit a review.";
        header("Location: login.php");
        exit();
    }
    
    $productId = intval($_POST['product_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 5);
    $review_text = trim($_POST['review'] ?? '');
    
    if ($productId > 0 && !empty($review_text)) {
        try {
            $db = getDB();
            
            // Check if review already exists for this user/product
            $check = $db->prepare("SELECT id FROM reviews WHERE user_id = :uid AND product_id = :pid");
            $check->execute(['uid' => $_SESSION['user_id'], 'pid' => $productId]);
            $existing = $check->fetchColumn();
            
            if ($existing) {
                // Update review
                $stmt = $db->prepare("UPDATE reviews SET rating = :rating, review = :review, created_at = CURRENT_TIMESTAMP WHERE id = :id");
                $stmt->execute(['rating' => $rating, 'review' => $review_text, 'id' => $existing]);
                $_SESSION['success'] = "Your review has been updated.";
            } else {
                // Insert review
                $stmt = $db->prepare("INSERT INTO reviews (user_id, product_id, rating, review) VALUES (:uid, :pid, :rating, :review)");
                $stmt->execute(['uid' => $_SESSION['user_id'], 'pid' => $productId, 'rating' => $rating, 'review' => $review_text]);
                $_SESSION['success'] = "Thank you! Your review has been submitted.";
            }
        } catch (PDOException $e) {
            error_log("Error saving review: " . $e->getMessage());
            $_SESSION['error'] = "Failed to submit review. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Invalid review details.";
    }
    
    header("Location: product.php?id=" . $productId);
    exit();
}

$controller->details();
