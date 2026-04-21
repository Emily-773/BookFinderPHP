<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BookFinder Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
  <main class="container py-5">
    <div class="auth-box mx-auto text-center">
      <h1 class="mb-4">BookFinder</h1>

      <?php if (isset($_SESSION['user_name'])): ?>
        <p class="mb-3">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
        <p><a class="btn btn-danger" href="logout.php">Logout</a></p>
      <?php else: ?>
        <p class="mb-3">Welcome to BookFinder</p>
        <p>
          <a class="btn btn-primary me-2" href="login.php">Login</a>
          <a class="btn btn-success" href="signup.php">Sign Up</a>
        </p>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
