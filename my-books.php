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

  <meta name="description" content="View, update, delete and review your saved books in your personal BookFinder collection.">
  <meta name="robots" content="index, follow">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css">
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
      <a href="index.php">Home</a>
      <a href="search.php">Search Books</a>
      <a href="my-books.php" aria-current="page">My Books</a>
      <a href="logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="my-books-page">

  <section class="my-books-hero">
    <div>
      <p class="eyebrow">Your personal library</p>
      <h1>My Books</h1>
      <p>Manage your saved books, update your reading status and add personal reviews.</p>
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

            <form action="delete-book.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this book?');">
              <input type="hidden" name="saved_book_id" value="<?php echo $book['id']; ?>">
              <button type="submit" class="btn btn-outline-danger w-100">Delete Book</button>
            </form>

            <div class="review-panel">
              <h3>Add a Review</h3>

              <form action="add-review.php" method="POST">
                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">

                <label for="rating-<?php echo $book['id']; ?>" class="form-label">Rating</label>
                <select name="rating" id="rating-<?php echo $book['id']; ?>" class="form-select mb-2" required>
                  <option value="">Choose rating</option>
                  <option value="5">5 stars</option>
                  <option value="4">4 stars</option>
                  <option value="3">3 stars</option>
                  <option value="2">2 stars</option>
                  <option value="1">1 star</option>
                </select>

                <label for="review-<?php echo $book['id']; ?>" class="form-label">Review</label>
                <textarea name="review_text"
                          id="review-<?php echo $book['id']; ?>"
                          class="form-control mb-2"
                          rows="3"
                          required></textarea>

                <button type="submit" class="btn btn-warning w-100">Add Review</button>
              </form>
            </div>

            <?php
            $review_stmt = $conn->prepare("SELECT id, rating, review_text, created_at FROM book_reviews WHERE book_id = ? AND user_id = ? ORDER BY created_at DESC");
            $review_stmt->bind_param("ii", $book['id'], $user_id);
            $review_stmt->execute();
            $reviews = $review_stmt->get_result();
            ?>

            <?php if ($reviews->num_rows > 0): ?>
              <div class="user-reviews">
                <h3>Your Reviews</h3>

                <?php while ($review = $reviews->fetch_assoc()): ?>
                  <div class="review-box">
                    <p class="stars" aria-label="<?php echo (int)$review['rating']; ?> out of 5 stars">
                      <?php
                      $rating = (int)$review['rating'];
                      for ($i = 1; $i <= 5; $i++) {
                          echo ($i <= $rating) ? "★" : "☆";
                      }
                      ?>
                    </p>

                    <p><?php echo htmlspecialchars($review['review_text']); ?></p>

                    <small>
                      <?php echo date("d/m/Y H:i", strtotime($review['created_at'])); ?>
                    </small>

                    <button class="btn btn-outline-secondary w-100 mt-3 mb-2"
                            onclick="this.nextElementSibling.style.display='block'; this.style.display='none'; return false;">
                      Edit Review
                    </button>

                    <div style="display:none;">
                      <form action="edit-review.php" method="POST">
                        <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">

                        <label for="edit-rating-<?php echo $review['id']; ?>" class="form-label">Edit rating</label>
                        <select name="rating" id="edit-rating-<?php echo $review['id']; ?>" class="form-select mb-2" required>
                          <option value="5" <?php if ($review['rating'] == 5) echo 'selected'; ?>>5 stars</option>
                          <option value="4" <?php if ($review['rating'] == 4) echo 'selected'; ?>>4 stars</option>
                          <option value="3" <?php if ($review['rating'] == 3) echo 'selected'; ?>>3 stars</option>
                          <option value="2" <?php if ($review['rating'] == 2) echo 'selected'; ?>>2 stars</option>
                          <option value="1" <?php if ($review['rating'] == 1) echo 'selected'; ?>>1 star</option>
                        </select>

                        <label for="edit-review-<?php echo $review['id']; ?>" class="form-label">Edit review</label>
                        <textarea name="review_text"
                                  id="edit-review-<?php echo $review['id']; ?>"
                                  class="form-control mb-2"
                                  rows="3"
                                  required><?php echo htmlspecialchars($review['review_text']); ?></textarea>

                        <button type="submit" class="btn btn-warning w-100 mb-2">Update Review</button>
                      </form>
                    </div>

                    <form action="delete-review.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');">
                      <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                      <button type="submit" class="btn btn-outline-danger w-100">Delete Review</button>
                    </form>
                  </div>
                <?php endwhile; ?>
              </div>
            <?php endif; ?>

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