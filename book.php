<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($book_id <= 0) {
    header("Location: my-books.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM saved_books WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $book_id, $user_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    header("Location: my-books.php");
    exit;
}

$googleVolumeId = $book['volume_id'] ?: $book['book_id'];
$message = $_GET['message'] ?? '';

function statusClass($status) {
    return match ($status) {
        'Reading' => 'status-reading',
        'Finished' => 'status-finished',
        default => 'status-want',
    };
}

$review_stmt = $conn->prepare("SELECT id, rating, review_text, created_at FROM book_reviews WHERE book_id = ? AND user_id = ? ORDER BY created_at DESC");
$review_stmt->bind_param("ii", $book_id, $user_id);
$review_stmt->execute();
$reviews = $review_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($book['title']); ?> | BookFinder</title>

  <meta name="description" content="View book details and manage your personal reviews on BookFinder.">
  <meta name="robots" content="index, follow">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.min.css?v=20">
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
      <a href="my-books.php">📖 My Books</a>
      <a href="change-password.php">🔐 Change Password</a>
      <a href="logout.php">↪ Logout</a>
    </nav>
  </div>
</header>

<main class="my-books-page">

  <section class="my-books-hero">
    <div>
      <p class="eyebrow">Book details</p>
      <h1><?php echo htmlspecialchars($book['title']); ?></h1>
      <p>View this book, update its reading status and manage your personal review.</p>
    </div>

    <div class="hero-actions">
      <a href="my-books.php" class="btn btn-outline-primary">Back to My Books</a>
      <a href="search.php" class="btn btn-primary">Search Books</a>
    </div>
  </section>

  <?php if ($message): ?>
    <div class="bf-alert" aria-live="polite">
      <?php
        $messages = [
          'updated' => 'Book status updated.',
          'review_added' => 'Review added successfully.',
          'review_updated' => 'Review updated successfully.',
          'review_deleted' => 'Review deleted successfully.'
        ];
        echo htmlspecialchars($messages[$message] ?? '');
      ?>
    </div>
  <?php endif; ?>

  <section class="book-details-layout">

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

      <hr>

     <div id="googleBookDetails"
     data-volume-id="<?php echo htmlspecialchars($googleVolumeId); ?>"
     data-title="<?php echo htmlspecialchars($book['title']); ?>"
     data-authors="<?php echo htmlspecialchars($book['authors']); ?>">
     </div>

      <h3>Book Information</h3>

      <p><strong>Pages:</strong> <span id="js-pages">Loading...</span></p>
      <p><strong>Publisher:</strong> <span id="js-publisher">Loading...</span></p>
      <p><strong>Published:</strong> <span id="js-published">Loading...</span></p>
      <p><strong>Categories:</strong> <span id="js-categories">Loading...</span></p>
      <p><strong>Language:</strong> <span id="js-language">Loading...</span></p>
      <p><strong>ISBN 10:</strong> <span id="js-isbn10">Loading...</span></p>
      <p><strong>ISBN 13:</strong> <span id="js-isbn13">Loading...</span></p>
      <p><strong>Average Rating:</strong> ⭐ <span id="js-rating">Loading...</span></p>

      <p>
        <a id="js-preview-link"
           href="#"
           target="_blank"
           rel="noopener noreferrer"
           class="btn btn-primary btn-sm mb-3"
           style="display:none;">
          Preview on Google Books
        </a>
      </p>

      <hr>

      <h3>Description</h3>
      <div class="book-description" id="js-description">
        Loading description...
      </div>

      <hr>

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
                    rows="4"
                    required></textarea>

          <button type="submit" class="btn btn-warning w-100">Add Review</button>
        </form>
      </div>

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
      <?php else: ?>
        <div class="user-reviews">
          <h3>Your Reviews</h3>
          <p>You have not reviewed this book yet.</p>
        </div>
      <?php endif; ?>

    </div>
  </section>

</main>

<script src="./js/book-details.js?v=4"></script>

</body>
</html>