<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>BookFinder Home</title>

  <!-- SEO -->
  <meta name="description" content="BookFinder lets you search books, save favourites, and track your reading using the Google Books API.">
  <meta name="robots" content="index, follow">

  <!-- Preconnect (performance boost) -->
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Your CSS -->
 <link rel="preload" href="./css/styles.css?v=4" as="style">
 <link rel="stylesheet" href="./css/styles.css?v=4">
</head>

<body>

  <main class="container py-5 text-center">

    <!-- HERO SECTION -->
    <section class="mb-5">

      <!-- LOGO -->
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

      <!-- TITLE -->
      <h1 class="display-5 fw-bold">BookFinder</h1>

      <!-- DESCRIPTION -->
      <p class="lead">
        Search for books, discover new reads, and manage your personal collection using the Google Books API.
      </p>

      <!-- BUTTONS -->
      <?php if (isset($_SESSION['user_name'])): ?>
        <p class="mb-3">
          Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!
        </p>

        <a class="btn btn-success btn-lg me-2" href="search.php">Search Books</a>
        <a class="btn btn-primary btn-lg me-2" href="my-books.php">My Books</a>
        <a class="btn btn-danger btn-lg" href="logout.php">Logout</a>

      <?php else: ?>
        <a class="btn btn-primary btn-lg me-2" href="login.php">Login</a>
        <a class="btn btn-success btn-lg" href="signup.php">Sign Up</a>
      <?php endif; ?>

    </section>

    <!-- CAROUSEL -->
    <section class="mb-5 text-center" aria-labelledby="popularBooksHeading">
      <h2 id="popularBooksHeading" class="h4 mb-3">🔥 Trending Now</h2>
      <p class="text-muted">Discover popular books readers are enjoying right now.</p>

      <div class="carousel-wrapper">
        <button id="carouselPrev" class="carousel-btn" aria-label="Previous books">‹</button>

        <div id="popularCarousel" class="popular-carousel" aria-live="polite">
          <p>Loading popular books...</p>
        </div>

        <button id="carouselNext" class="carousel-btn" aria-label="Next books">›</button>
      </div>
    </section>

    <!-- FEATURES -->
    <section class="row text-start">

      <div class="col-md-4 mb-4">
        <h2 class="h5">🔍 Search Books</h2>
        <p>Use the Google Books API to search for books by title, author, or keyword.</p>
      </div>

      <div class="col-md-4 mb-4">
        <h2 class="h5">❤️ Save Favourites</h2>
        <p>Save books to your personal collection and access them anytime.</p>
      </div>

      <div class="col-md-4 mb-4">
        <h2 class="h5">📖 Track Reading</h2>
        <p>Update your reading status and manage your saved books easily.</p>
      </div>

    </section>

  </main>

  <!-- JS  -->
 <script src="./js/script.js?v=9"></script>

</body>
</html>