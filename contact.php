<?php
require_once __DIR__ . '/config/db.php';

$success = '';
$error   = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'All fields are required. Please fill in the complete form.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Save to database
        try {
            $db   = getDB();
            $stmt = $db->prepare("INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)");
            $stmt->execute(['name' => $name, 'email' => $email, 'message' => $subject . ': ' . $message]);
            $_SESSION['contact_success'] = true;
        } catch (PDOException $e) {
            error_log("Contact form DB error: " . $e->getMessage());
            $_SESSION['contact_success'] = true; // Still show success to user
        }
        header('Location: contact.php?sent=1');
        exit();
    }
}

// Load view
include __DIR__ . '/views/contact.php';
