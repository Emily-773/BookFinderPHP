<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$saved_book_id = $_POST['saved_book_id'] ?? '';

if ($saved_book_id === '') {
    die('Invalid delete request.');
}

$stmt = $conn->prepare("DELETE FROM saved_books WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $saved_book_id, $user_id);

if ($stmt->execute()) {
    header("Location: my-books.php?message=deleted");
    exit;
} else {
    die("Error deleting book: " . $conn->error);
}
?>
