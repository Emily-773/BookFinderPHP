<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $rating = $_POST['rating'];
    $review_text = trim($_POST['review_text']);

    if ($rating < 1 || $rating > 5 || empty($review_text)) {
        header("Location: my-books.php");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO book_reviews (user_id, book_id, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $book_id, $rating, $review_text);
    $stmt->execute();

  header("Location: my-books.php?message=review_added");
    exit;
}
?>