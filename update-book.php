<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$saved_book_id = $_POST['saved_book_id'] ?? '';
$status = trim($_POST['status'] ?? '');

$allowed_statuses = ['Want to Read', 'Reading', 'Finished'];

if ($saved_book_id === '' || !in_array($status, $allowed_statuses, true)) {
    die('Invalid update request.');
}

$stmt = $conn->prepare("UPDATE saved_books SET status = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("sii", $status, $saved_book_id, $user_id);

if ($stmt->execute()) {
    header("Location: my-books.php?message=updated");
    exit;
} else {
    die("Error updating book: " . $conn->error);
}
?>
