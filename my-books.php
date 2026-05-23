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

function statusClass($status) {
    return match ($status) {
        'Reading' => 'status-reading',
        'Finished' => 'status-finished',
        default => 'status-want',
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Books | BookFinder</title>

  <meta name="description" content="View, update and manage your saved books in your personal BookFinder collection.">
  <meta name="robots" content="index, follow">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css?v=18">
</head>

<body class="bookfinder-theme">

<header class="bf-header">
  <div class="bf-header-inner">
    <a href="index.php" class="bf-logo-link">
      <picture>
        <source srcset="images/logo.webp" type="image/webp">
        <img src="images/logo.png" width="187" height="56" alt="BookFinder Logo" class="bf-logo">
      </picture>
    </a>

    <nav class="bf-nav" aria-label="Main navigation">
      <a href="index.php">🏠 Home</a>
      <a href="search.php">🔍 Search Books</a>
      <a href="my-books.php" aria-current="page">📖 My Books</a>
      <a href="change-password.php">🔐 Change Password</a>
      <a href="logout.php">↪ Logout</a>
    </nav>
  </div>
</header>

<main class="my-books-page">

  <section class="my-books-hero">
    <div>
      <p class="eyebrow">Your personal library</p>
      <h1>My Books</h1>
      <p>Manage your saved books, update your reading status and open each book to view or add reviews.</p>
    </div>

    <div class="hero-actions">
      <a href="search.php" class="btn btn-primary">Search Books</a>
      <a href="index.php" class="btn btn-outline-primary">Back to Home</a>
    </div>
  </section>

  <?php if ($message): ?>
    <div class="bf-alert" aria-live="polite">
      <?php
        $messages = [
          'saved' => 'Book saved successfully.',
          'already_saved' => 'That book is already in your list.',
          'updated' => 'Book status updated.',
          'deleted' => 'Book deleted.',
          'review_added' => 'Review added successfully.',
          'review_updated' => 'Review updated successfully.',
          'review_deleted' => 'Review deleted successfully.'
        ];
        echo htmlspecialchars($messages[$message] ?? '');
      ?>
    </div>
  <?php endif; ?>

  <?php if ($result->num_rows > 0): ?>
    <section class="books-grid" aria-label="Saved books">
      <?php while ($book = $result->fetch_assoc()): ?>

        <?php
        $summary_stmt = $conn->prepare("
            SELECT rating, review_text
            FROM book_reviews
            WHERE book_id = ? AND user_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $summary_stmt->bind_param("ii", $book['id'], $user_id);
        $summary_stmt->execute();
        $review_summary = $summary_stmt->get_result()->fetch_assoc();
        ?>

        <article class="saved-book-card">

          <div class="book-cover-wrap">
            <?php if (!empty($book['thumbnail'])): ?>
              <img src="<?php echo htmlspecialchars($book['thumbnail']); ?>"
                   alt="Cover image for <?php echo htmlspecialchars($book['title']); ?>">
            <?php else: ?>
              <div class="missing-cover">No cover available</div>
            <?php endif; ?>
          </div>

          <div class="book-card-content">
            <span class="status-badge <?php echo statusClass($book['status']); ?>">
              <?php echo htmlspecialchars($book['status']); ?>
            </span>

            <h2><?php echo htmlspecialchars($book['title']); ?></h2>

            <p class="book-author">
              <strong>Author(s):</strong>
              <?php echo htmlspecialchars($book['authors'] ?: 'Unknown'); ?>
            </p>

            <form action="update-book.php" method="POST" class="book-form">
              <input type="hidden" name="saved_book_id" value="<?php echo $book['id']; ?>">

              <label for="status-<?php echo $book['id']; ?>" class="form-label">Update reading status</label>
              <select name="status" id="status-<?php echo $book['id']; ?>" class="form-select">
                <option value="Want to Read" <?php if ($book['status'] === 'Want to Read') echo 'selected'; ?>>Want to Read</option>
                <option value="Reading" <?php if ($book['status'] === 'Reading') echo 'selected'; ?>>Reading</option>
                <option value="Finished" <?php if ($book['status'] === 'Finished') echo 'selected'; ?>>Finished</option>
              </select>

              <button type="submit" class="btn btn-primary w-100 mt-2">Update Status</button>
            </form>

            <?php if ($review_summary): ?>
              <div class="review-summary">
                <p class="review-summary-label">Your latest review</p>

                <p class="stars" aria-label="<?php echo (int)$review_summary['rating']; ?> out of 5 stars">
                  <?php
                  $rating = (int)$review_summary['rating'];
                  for ($i = 1; $i <= 5; $i++) {
                      echo ($i <= $rating) ? "★" : "☆";
                  }
                  ?>
                </p>

                <p class="review-summary-text">
                  “<?php echo htmlspecialchars(mb_strimwidth($review_summary['review_text'], 0, 80, '...')); ?>”
                </p>
              </div>
            <?php else: ?>
              <p class="no-review-text">No review added yet.</p>
            <?php endif; ?>

            <a href="book.php?id=<?php echo $book['id']; ?>" class="btn btn-warning w-100 mt-2">
              View / Add Review
            </a>

            <form action="delete-book.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this book?');">
              <input type="hidden" name="saved_book_id" value="<?php echo $book['id']; ?>">
              <button type="submit" class="btn btn-outline-danger w-100 mt-2">Delete Book</button>
            </form>

          </div>
        </article>
      <?php endwhile; ?>
    </section>
  <?php else: ?>
    <section class="empty-library">
      <h2>No saved books yet</h2>
      <p>Search for books and save them to start building your personal reading list.</p>
      <a href="search.php" class="btn btn-primary">Search Books</a>
    </section>
  <?php endif; ?>

</main>

</body>
</html>