<?php
session_start();
require_once 'includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $message = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $message = 'Password must be at least 6 characters.';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = 'An account with this email already exists.';
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashedPassword);

            if ($stmt->execute()) {
                // Auto login after signup
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;

                header("Location: index.php");
                exit;
            } else {
                $message = 'Something went wrong. Please try again.';
            }
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
  <title>Sign Up | BookFinder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
  <main class="container py-5">
    <div class="auth-box mx-auto">
      <h2 class="mb-4 text-center">Create a BookFinder Account</h2>

      <form method="POST" action="signup.php" novalidate>
        <div class="mb-3">
          <label for="signupName" class="form-label">Name</label>
          <input
            type="text"
            id="signupName"
            name="name"
            class="form-control"
            required
            value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
          >
        </div>

        <div class="mb-3">
          <label for="signupEmail" class="form-label">Email</label>
          <input
            type="email"
            id="signupEmail"
            name="email"
            class="form-control"
            required
            value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
          >
        </div>

        <div class="mb-3">
          <label for="signupPassword" class="form-label">Password</label>
          <input
            type="password"
            id="signupPassword"
            name="password"
            class="form-control"
            minlength="6"
            required
          >
        </div>

        <button type="submit" class="btn btn-success w-100">Sign Up</button>
      </form>

      <p id="signupMessage" class="mt-3 text-center" aria-live="polite">
        <?php echo htmlspecialchars($message); ?>
      </p>

      <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a></p>
      <p class="text-center"><a href="index.php">Back to Home</a></p>
    </div>
  </main>
</body>
</html>
