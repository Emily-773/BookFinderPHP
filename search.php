<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Search Books | BookFinder</title>

  <meta name="description" content="Search for books using BookFinder and the Google Books API, then save favourites to your personal reading collection.">
  <meta name="robots" content="index, follow">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css">
</head>

<body>

  <nav class="navbar navbar-light bg-light">
    <div class="container d-flex justify-content-between align-items-center gap-3 flex-wrap">

      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <picture>
          <source srcset="images/logo.webp" type="image/webp">
          <img src="images/logo.png"
               width="150"
               height="45"
               alt="BookFinder Logo">
        </picture>
      </a>

      <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="fw-semibold">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="my-books.php" class="btn btn-outline-primary btn-sm">My Books</a>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">Home</a>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
      </div>

    </div>
  </nav>

  <main class="container mt-4">

    <section class="text-center mb-4">
      <h1 class="h2 mb-3">Find your next great read</h1>

      <div class="d-flex justify-content-center align-items-start flex-wrap gap-2">
        <div class="position-relative">
          <label for="searchInput" class="visually-hidden">Search books</label>
          <input id="searchInput"
                 class="form-control"
                 type="text"
                 placeholder="Search books..."
                 aria-label="Search books"
                 autocomplete="off">
          <div id="suggestions" class="suggestions-dropdown" role="listbox" aria-label="Book suggestions"></div>
        </div>

        <button id="searchBtn" class="btn btn-primary" type="button">Search</button>

        <button id="darkModeToggle" class="btn btn-outline-secondary" type="button" aria-label="Toggle dark mode">
          🌙
        </button>
      </div>
    </section>

    <section class="mb-4">
      <h2 class="h4">Filter and Sort Current Results</h2>
      <p class="text-muted mb-3">
        Filters and sorting apply to the current search results only.
      </p>

      <div class="row g-3 align-items-end">
        <div class="col-md-4">
          <label for="authorFilter" class="form-label">Filter current results by author</label>
          <input id="authorFilter"
                 class="form-control"
                 type="text"
                 placeholder="e.g. Jane Austen"
                 aria-label="Filter current results by author">
        </div>

        <div class="col-md-4">
          <label for="subjectFilter" class="form-label">Filter current results by subject</label>
          <input id="subjectFilter"
                 class="form-control"
                 type="text"
                 placeholder="e.g. fantasy"
                 aria-label="Filter current results by subject">
        </div>

        <div class="col-md-4">
          <label for="sortResults" class="form-label">Sort current results</label>
          <select id="sortResults" class="form-select" aria-label="Sort current book results">
            <option value="">Default order</option>
            <option value="title-az">A–Z Title</option>
            <option value="newest">Newest Year</option>
            <option value="oldest">Oldest Year</option>
          </select>
        </div>
      </div>

      <div class="mt-3">
        <button id="applyFiltersBtn" class="btn btn-success" type="button">Apply Filters</button>
        <button id="clearFiltersBtn" class="btn btn-outline-secondary" type="button">Clear Filters</button>
      </div>
    </section>

    <section class="mb-5">
      <h2 class="h4">Recent Searches</h2>
      <div id="history" class="mt-2" aria-live="polite"></div>
    </section>

    <section>
      <h2 class="h4 mb-3">Results</h2>
      <div id="results" class="row" aria-live="polite" aria-busy="false"></div>
    </section>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/script.js"></script>
</body>
</html>
