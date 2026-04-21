<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$book_id = $_POST['book_id'] ?? '';
$title = trim($_POST['title'] ?? '');
$authors = trim($_POST['authors'] ?? '');
$thumbnail = trim($_POST['thumbnail'] ?? '');
$status = trim($_POST['status'] ?? 'Want to Read');

if ($book_id === '' || $title === '') {
    die('Missing required book data.');
}

// Optional: prevent duplicate saves for same user + same Google book
$check = $conn->prepare("SELECT id FROM saved_books WHERE user_id = ? AND book_id = ?");
$check->bind_param("is", $user_id, $book_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $check->close();
    header("Location: my-books.php?message=already_saved");
    exit;
}
$check->close();

$stmt = $conn->prepare("INSERT INTO saved_books (user_id, book_id, title, authors, thumbnail, status) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $user_id, $book_id, $title, $authors, $thumbnail, $status);

if ($stmt->execute()) {
    header("Location: my-books.php?message=saved");
    exit;
} else {
    die("Error saving book: " . $conn->error);
}
?>
