<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $review_id = $_POST['review_id'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $review_text = trim($_POST['review_text'] ?? '');

    if (empty($review_id) || empty($rating) || empty($review_text)) {
        header("Location: my-books.php");
        exit;
    }

    if ($rating < 1 || $rating > 5) {
        header("Location: my-books.php");
        exit;
    }

    $stmt = $conn->prepare("UPDATE book_reviews SET rating = ?, review_text = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("isii", $rating, $review_text, $review_id, $user_id);
    $stmt->execute();

    header("Location: my-books.php?message=review_updated");
    exit;
}
?>