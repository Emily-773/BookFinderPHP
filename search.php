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

  <link rel="preconnect" href="https://www.googleapis.com">
  <link rel="preconnect" href="https://books.google.com">
  <link rel="stylesheet" href="./css/styles.css?v=19">
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
      <span class="bf-user">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
      <a href="search.php" aria-current="page">🔍 Search Books</a>
      <a href="my-books.php">📖 My Books</a>
      <a href="index.php">🏠 Home</a>
      <a href="change-password.php">🔐 Change Password</a>
      <a href="logout.php">↪ Logout</a>
    </nav>
  </div>
</header>

<main class="search-page">

  <section class="search-hero" aria-labelledby="searchTitle">
    <p class="eyebrow">Search the Google Books API</p>

    <h1 id="searchTitle">Find your next great read</h1>

    <p>
      Search by title, author or subject, then save books to your personal library.
    </p>

    <div class="search-bar-wrap">

      <div class="search-input-wrap">
        <label for="searchInput" class="visually-hidden">
          Search books
        </label>

        <input id="searchInput"
               type="text"
               placeholder="Search books..."
               aria-label="Search books"
               autocomplete="off">

        <div id="suggestions"
             class="suggestions-dropdown"
             role="listbox"
             aria-label="Book suggestions">
        </div>
      </div>

      <button id="searchBtn"
              class="bf-btn bf-btn-primary"
              type="button">
        Search
      </button>

      <button id="darkModeToggle"
              class="bf-btn bf-btn-outline"
              type="button"
              aria-label="Toggle dark mode">
        🌙 Dark Mode
      </button>

      <button id="contrastToggle"
              class="bf-btn bf-btn-outline"
              type="button"
              aria-label="Toggle high contrast mode">
        Contrast
      </button>

    </div>
  </section>

  <section class="search-panel" aria-labelledby="filterTitle">

    <div class="panel-heading">
      <div>
        <p class="eyebrow">Refine results</p>

        <h2 id="filterTitle">
          Filter and Sort Current Results
        </h2>

        <p>
          Filters and sorting apply to the current search results only.
        </p>
      </div>
    </div>

    <div class="filter-grid">

      <div>
        <label for="authorFilter">
          Filter current results by author
        </label>

        <input id="authorFilter"
               type="text"
               placeholder="e.g. Jane Austen"
               aria-label="Filter current results by author">
      </div>

      <div>
        <label for="subjectFilter">
          Filter current results by subject
        </label>

        <input id="subjectFilter"
               type="text"
               placeholder="e.g. fantasy"
               aria-label="Filter current results by subject">
      </div>

      <div>
        <label for="sortResults">
          Sort current results
        </label>

        <select id="sortResults"
                aria-label="Sort current book results">

          <option value="">Default order</option>
          <option value="title-az">A–Z Title</option>
          <option value="newest">Newest Year</option>
          <option value="oldest">Oldest Year</option>

        </select>
      </div>

    </div>

    <div class="filter-actions">

      <button id="applyFiltersBtn"
              class="bf-btn bf-btn-primary"
              type="button">
        Apply Filters
      </button>

      <button id="clearFiltersBtn"
              class="bf-btn bf-btn-outline"
              type="button">
        Clear Filters
      </button>

    </div>
  </section>

  <section class="search-panel" aria-labelledby="historyTitle">

    <div class="panel-heading">
      <div>
        <p class="eyebrow">Continue exploring</p>
        <h2 id="historyTitle">Recent Searches</h2>
      </div>
    </div>

    <div id="history"
         class="history-tags"
         aria-live="polite">
    </div>

  </section>

  <section class="search-panel" aria-labelledby="resultsTitle">

    <div class="panel-heading">
      <div>
        <p class="eyebrow">Book results</p>

        <h2 id="resultsTitle">Results</h2>

        <p>
          Start a search to see matching books here.
        </p>
      </div>
    </div>

    <div id="results"
         class="row"
         aria-live="polite"
         aria-busy="false">

      <p class="empty-results-message">
        Search for a book title, author or subject to begin.
      </p>

    </div>

  </section>

</main>

<script src="./js/script.js?v=25"></script>

</body>
</html>