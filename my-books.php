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

  <meta name="description" content="View, update, delete and review your saved books in your personal BookFinder collection.">
  <meta name="robots" content="index, follow">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css">
</head>

<body>
  <main class="container py-5">
    <div class="auth-box mx-auto" style="max-width: 900px;">

      <div class="text-center mb-4">
        <a href="index.php">
          <picture>
            <source srcset="images/logo.webp" type="image/webp">
            <img src="images/logo.png"
                 width="187"
                 height="56"
                 alt="BookFinder Logo"
                 class="img-fluid mb-3">
          </picture>
        </a>

        <h1 class="mb-3">My Books</h1>

        <p class="text-muted">
          Manage your saved books, update your reading status, and add book reviews.
        </p>
      </div>

      <?php if ($message === 'saved'): ?>
        <p class="text-success text-center" aria-live="polite">Book saved successfully.</p>
      <?php elseif ($message === 'already_saved'): ?>
        <p class="text-warning text-center" aria-live="polite">That book is already in your list.</p>
      <?php elseif ($message === 'updated'): ?>
        <p class="text-success text-center" aria-live="polite">Book status updated.</p>
      <?php elseif ($message === 'deleted'): ?>
        <p class="text-success text-center" aria-live="polite">Book deleted.</p>
      <?php elseif ($message === 'review_added'): ?>
        <p class="text-success text-center" aria-live="polite">Review added successfully.</p>
      <?php elseif ($message === 'review_updated'): ?>
        <p class="text-success text-center" aria-live="polite">Review updated successfully.</p>
      <?php elseif ($message === 'review_deleted'): ?>
        <p class="text-success text-center" aria-live="polite">Review deleted successfully.</p>
      <?php endif; ?>

      <div class="text-center mb-4">
        <a href="search.php" class="btn btn-success me-2">Search Books</a>
        <a href="index.php" class="btn btn-primary me-2">Back to Home</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
      </div>

      <?php if ($result->num_rows > 0): ?>
        <div class="row g-4">
          <?php while ($book = $result->fetch_assoc()): ?>
            <div class="col-md-6">
              <div class="card h-100 shadow-sm">
                <?php if (!empty($book['thumbnail'])): ?>
                  <img src="<?php echo htmlspecialchars($book['thumbnail']); ?>"
                       class="card-img-top"
                       alt="Cover image for <?php echo htmlspecialchars($book['title']); ?>">
                <?php endif; ?>

                <div class="card-body">
                  <h2 class="h5 card-title"><?php echo htmlspecialchars($book['title']); ?></h2>

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

                  <form action="delete-book.php" method="POST" class="mb-3">
                    <input type="hidden" name="saved_book_id" value="<?php echo $book['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger w-100">Delete</button>
                  </form>

                  <hr>

                  <h3 class="h6">Add a Review</h3>

                  <form action="add-review.php" method="POST" class="mb-3">
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

                    <button type="submit" class="btn btn-primary w-100">Add Review</button>
                  </form>

                  <?php
                  $review_stmt = $conn->prepare("SELECT id, rating, review_text, created_at FROM book_reviews WHERE book_id = ? AND user_id = ? ORDER BY created_at DESC");
                  $review_stmt->bind_param("ii", $book['id'], $user_id);
                  $review_stmt->execute();
                  $reviews = $review_stmt->get_result();
                  ?>

                  <?php if ($reviews->num_rows > 0): ?>
                    <h3 class="h6">Your Reviews</h3>

                    <?php while ($review = $reviews->fetch_assoc()): ?>
                      <div class="review-box mt-2 p-3 border rounded">
                        <strong>
                          <?php
                          $rating = (int)$review['rating'];
                          for ($i = 1; $i <= 5; $i++) {
                              echo ($i <= $rating) ? "★" : "☆";
                          }
                          ?>
                        </strong>

                        <p class="mb-1"><?php echo htmlspecialchars($review['review_text']); ?></p>

                        <small class="text-muted">
                          <?php echo date("d/m/Y H:i", strtotime($review['created_at'])); ?>
                        </small>

                        <button class="btn btn-outline-secondary w-100 mt-3 mb-2"
                                onclick="this.nextElementSibling.style.display='block'; this.style.display='none'; return false;">
                          Edit Review
                        </button>

                        <div style="display:none;">
                          <form action="edit-review.php" method="POST" class="mt-3">
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
                  <?php endif; ?>

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