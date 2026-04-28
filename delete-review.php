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

    if (empty($review_id)) {
        header("Location: my-books.php");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM book_reviews WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $review_id, $user_id);
    $stmt->execute();

    header("Location: my-books.php?message=review_deleted");
    exit;
}
?>