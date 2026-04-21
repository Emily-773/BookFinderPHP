<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM saved_books WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$message = $_GET['message'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Books | BookFinder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
  <main class="container py-5">
    <div class="auth-box mx-auto" style="max-width: 900px;">
      <h1 class="mb-4 text-center">My Books</h1>

      <?php if ($message === 'saved'): ?>
        <p class="text-success text-center">Book saved successfully.</p>
      <?php elseif ($message === 'already_saved'): ?>
        <p class="text-warning text-center">That book is already in your list.</p>
      <?php elseif ($message === 'updated'): ?>
        <p class="text-success text-center">Book status updated.</p>
      <?php elseif ($message === 'deleted'): ?>
        <p class="text-success text-center">Book deleted.</p>
      <?php endif; ?>

      <div class="text-center mb-4">
        <a href="index.php" class="btn btn-primary me-2">Back to Home</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
      </div>

      <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
          <?php while ($book = $result->fetch_assoc()): ?>
            <div class="col-md-6">
              <div class="card h-100 shadow-sm">
                <?php if (!empty($book['thumbnail'])): ?>
                  <img src="<?php echo htmlspecialchars($book['thumbnail']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>">
                <?php endif; ?>

                <div class="card-body">
                  <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                  <p class="card-text">
                    <strong>Author(s):</strong>
                    <?php echo htmlspecialchars($book['authors'] ?: 'Unknown'); ?>
                  </p>
                  <p class="card-text">
                    <strong>Status:</strong>
                    <?php echo htmlspecialchars($book['status']); ?>
                  </p>

                  <form action="update-book.php" method="POST" class="mb-2">
                    <input type="hidden" name="saved_book_id" value="<?php echo $book['id']; ?>">

                    <label for="status-<?php echo $book['id']; ?>" class="form-label">Update status</label>
                    <select name="status" id="status-<?php echo $book['id']; ?>" class="form-select mb-2">
                      <option value="Want to Read" <?php if ($book['status'] === 'Want to Read') echo 'selected'; ?>>Want to Read</option>
                      <option value="Reading" <?php if ($book['status'] === 'Reading') echo 'selected'; ?>>Reading</option>
                      <option value="Finished" <?php if ($book['status'] === 'Finished') echo 'selected'; ?>>Finished</option>
                    </select>

                    <button type="submit" class="btn btn-success w-100">Update</button>
                  </form>

                  <form action="delete-book.php" method="POST">
                    <input type="hidden" name="saved_book_id" value="<?php echo $book['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger w-100">Delete</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <p class="text-center">You have not saved any books yet.</p>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
