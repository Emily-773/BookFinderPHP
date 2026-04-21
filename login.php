<?php
session_start();
require_once 'includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = 'Please enter both email and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                header("Location: index.php");
                exit;
            } else {
                $message = 'Incorrect password.';
            }
        } else {
            $message = 'No account found with that email.';
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | BookFinder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
  <main class="container py-5">
    <div class="auth-box mx-auto">
      <h2 class="mb-4 text-center">Login to BookFinder</h2>

      <form method="POST" action="login.php" novalidate>
        <div class="mb-3">
          <label for="loginEmail" class="form-label">Email</label>
          <input
            type="email"
            id="loginEmail"
            name="email"
            class="form-control"
            required
            value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
          >
        </div>

        <div class="mb-3">
          <label for="loginPassword" class="form-label">Password</label>
          <input
            type="password"
            id="loginPassword"
            name="password"
            class="form-control"
            required
          >
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>

      <p id="loginMessage" class="mt-3 text-center" aria-live="polite">
        <?php echo htmlspecialchars($message); ?>
      </p>

      <p class="mt-3 text-center">No account? <a href="signup.php">Sign up here</a></p>
      <p class="text-center"><a href="index.php">Back to Home</a></p>
    </div>
  </main>
</body>
</html>
