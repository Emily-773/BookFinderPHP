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
  <title>BookFinder Search</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css">
</head>
<body>

  <nav class="navbar navbar-light bg-light">
    <div class="container d-flex justify-content-between align-items-start gap-3 flex-wrap">
      <span class="navbar-brand">📚 BookFinder</span>

      <div class="d-flex align-items-center gap-2">
        <span class="fw-semibold">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="my-books.php" class="btn btn-outline-primary btn-sm">My Books</a>
        <a href="index.php" class="btn btn-outline-secondary btn-sm">Home</a>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
      </div>

      <div class="d-flex align-items-start">
        <div class="position-relative me-2">
          <input id="searchInput"
                 class="form-control"
                 type="text"
                 placeholder="Search books..."
                 aria-label="Search books"
                 autocomplete="off">
          <div id="suggestions" class="suggestions-dropdown" role="listbox" aria-label="Book suggestions"></div>
        </div>

        <button id="searchBtn" class="btn btn-primary" type="button">Search</button>
        <button id="darkModeToggle" class="btn btn-outline-secondary ms-2" type="button" aria-label="Toggle dark mode">
          🌙
        </button>
      </div>
    </div>
  </nav>

  <main class="container mt-4">
    <h2 class="text-center mb-4">Find your next great read</h2>

    <section class="mb-5">
      <h4>Recent Searches</h4>
      <div id="history" class="mt-2" aria-live="polite"></div>
    </section>

    <section>
      <h4 class="mb-3">Results</h4>
      <div id="results" class="row" aria-live="polite" aria-busy="false"></div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./js/script.js"></script>
</body>
</html>
