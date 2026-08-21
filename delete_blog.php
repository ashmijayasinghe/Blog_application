<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: Blog post ID is missing.");
}

$post_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch the post to verify ownership
$stmt = $conn->prepare("SELECT user_id FROM blogpost WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

// Authorization check - ensure user owns the post
if (!$post || $post['user_id'] != $user_id) {
    die("Access Denied: You can only delete your own blog posts.");
}

// Delete the post
$delete_stmt = $conn->prepare("DELETE FROM blogpost WHERE id = ? AND user_id = ?");
$delete_stmt->bind_param("ii", $post_id, $user_id);

if ($delete_stmt->execute()) {
    // Redirect to dashboard with success message
    header("Location: dashboard.php?status=deleted");
    exit();
} else {
    die("Error: Failed to delete the blog post. Please try again.");
}

$delete_stmt->close();
$stmt->close();
?>
