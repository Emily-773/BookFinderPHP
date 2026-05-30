<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>BookFinder Home</title>

  <meta name="description" content="BookFinder lets you search books, save favourites, review books and track your reading using the Google Books API.">
  <meta name="robots" content="index, follow">

  <link rel="preconnect" href="https://www.googleapis.com">
  <link rel="preconnect" href="https://books.google.com">
  <link rel="stylesheet" href="css/styles.min.css?v=20">
</head>

<body>

<header class="bf-header">
  <div class="bf-header-inner">
    <a href="index.php" class="bf-brand" aria-label="BookFinder home">
      <picture>
        <source srcset="images/logo.webp" type="image/webp">
        <img src="images/logo.png" alt="BookFinder logo" width="200" height="51">
      </picture>
    </a>

    <nav class="bf-nav" aria-label="Main navigation">
      <?php if (isset($_SESSION['user_name'])): ?>
        <span class="bf-user">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
      <?php endif; ?>

      <a href="search.php">🔍 Search Books</a>
      <a href="my-books.php">📖 My Books</a>
      <a href="index.php" aria-current="page">🏠 Home</a>

      <?php if (isset($_SESSION['user_name'])): ?>
        <a href="change-password.php">🔐 Change Password</a>
        <a href="logout.php">↪ Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="signup.php">Sign Up</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="homepage">

  <section class="home-hero-upgrade" aria-labelledby="homeTitle">
    <div class="home-hero-text">
      <p class="eyebrow">Find. Save. Review.</p>

      <h1 id="homeTitle">Find your next<br>great read</h1>

      <p class="home-lead">
        Search for books, discover new reads, save your favourites and manage your personal reading collection in one place.
      </p>

      <div class="home-actions">
        <a class="bf-btn bf-btn-primary" href="search.php">🔍 Search Books</a>
        <a class="bf-btn bf-btn-outline" href="my-books.php">📖 My Books</a>
        <a class="bf-btn bf-btn-outline" href="#recommendedBooks">⭐ View Recommendations</a>
      </div>
    </div>

    <div class="home-hero-image" aria-hidden="true">
      <picture>
        <source srcset="images/books-cup-plant.webp" type="image/webp">
        <img src="images/books-cup-plant.png"
             alt=""
             width="800"
             height="533"
             loading="eager"
             decoding="async">
      </picture>
    </div>
  </section>

  <section class="home-feature-grid upgraded-grid" aria-label="BookFinder key features">
    <article>
      <span class="feature-icon">🔍</span>
      <div>
        <h2>Search books</h2>
        <p>Find titles by keyword, author or subject using live API results.</p>
      </div>
    </article>

    <article>
      <span class="feature-icon">📚</span>
      <div>
        <h2>Build a library</h2>
        <p>Save books into your own personal collection for later.</p>
      </div>
    </article>

    <article>
      <span class="feature-icon">⭐</span>
      <div>
        <h2>Review reads</h2>
        <p>Add ratings and reviews to reflect on books you have saved.</p>
      </div>
    </article>

    <article>
      <span class="feature-icon">🕘</span>
      <div>
        <h2>Recent searches</h2>
        <p>Quickly revisit your recent searches and continue exploring.</p>
      </div>
    </article>
  </section>

  <section class="home-carousel-section upgraded-panel" aria-labelledby="popularBooksHeading">
    <div class="section-heading section-heading-row">
      <div>
        <p class="eyebrow">Popular inspiration</p>
        <h2 id="popularBooksHeading">🔥 Trending Now</h2>
        <p>Discover popular books readers are enjoying right now.</p>
      </div>

      <a href="search.php" class="bf-btn bf-btn-outline small-btn">View all trending ›</a>
    </div>

    <div class="carousel-wrapper upgraded-carousel">
      <button id="carouselPrev" class="carousel-btn" aria-label="Previous books">‹</button>

      <div id="popularCarousel" class="popular-carousel" aria-live="polite">
        <p>Loading popular books...</p>
      </div>

      <button id="carouselNext" class="carousel-btn" aria-label="Next books">›</button>
    </div>
  </section>

  <section class="home-lower-grid">
    <article class="home-mini-panel">
      <div class="mini-panel-heading">
        <h2>🕘 Recently Viewed</h2>
        <a href="search.php">View all</a>
      </div>

      <div id="recentlyViewedList" class="recently-viewed-list">
        <p>No recently viewed books yet.</p>
      </div>
    </article>

    <article class="home-mini-panel" id="recommendedBooks">
      <div class="mini-panel-heading">
        <h2>⭐ Recommended For You</h2>
        <a href="search.php">View all</a>
      </div>

      <div id="recommendedBooksList" class="recommended-list">
        <p>Loading recommendations...</p>
      </div>
    </article>
  </section>

  <section class="trust-row" aria-label="BookFinder benefits">
    <article>
      <span>🔒</span>
      <strong>Secure & Private</strong>
      <p>Your library is linked to your own account.</p>
    </article>

    <article>
      <span>☁️</span>
      <strong>Always in Sync</strong>
      <p>Access your saved books when logged in.</p>
    </article>

    <article>
      <span>💡</span>
      <strong>Smart Discovery</strong>
      <p>Find books that match your interests.</p>
    </article>

    <article>
      <span>♡</span>
      <strong>Made for Readers</strong>
      <p>Built to search, save and review.</p>
    </article>
  </section>

</main>

<footer class="bf-footer">
  <div class="bf-footer-grid">
    <div>
      <picture>
        <source srcset="images/logo.webp" type="image/webp">
        <img src="images/logo.png" alt="BookFinder logo" width="180" height="46">
      </picture>
      <p>Your personal book discovery and library management app.</p>
    </div>

    <div>
      <h2>Quick Links</h2>
      <a href="search.php">Search Books</a>
      <a href="my-books.php">My Books</a>
      <a href="index.php">Home</a>
    </div>

    <div>
      <h2>Support</h2>
      <a href="login.php">Login</a>
      <a href="signup.php">Sign Up</a>
      <a href="logout.php">Logout</a>
    </div>

    <div>
      <h2>Stay Connected</h2>
      <p>Get updates and reading ideas.</p>
      <form class="footer-form">
        <label class="visually-hidden" for="footerEmail">Email address</label>
        <input id="footerEmail" type="email" placeholder="Enter your email">
        <button type="button">Subscribe</button>
      </form>
    </div>
  </div>

  <p class="footer-copy">© 2026 BookFinder. All rights reserved.</p>
</footer>

<script src="./js/script.js?v=10"></script>
</body>
</html>