<?php
session_start();
require_once 'includes/db.php';

$error = "";
$message = "";

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Invalid reset token.");
}

/* Find matching token */
$stmt = $conn->prepare("
    SELECT id, reset_expires 
    FROM users 
    WHERE reset_token = ?
");
$stmt->bind_param("s", $token);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("This reset link is invalid or has expired.");
}

$user = $result->fetch_assoc();

/* Check expiry */
if (strtotime($user['reset_expires']) < time()) {
    die("This reset link has expired.");
}

$user_id = $user['id'];

/* Process form */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($password) || empty($confirm)) {
        $error = "Please complete all fields.";
    }
    elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    }
    elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    }
    else {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $update = $conn->prepare("
            UPDATE users
            SET password = ?,
                reset_token = NULL,
                reset_expires = NULL
            WHERE id = ?
        ");

        $update->bind_param("si", $hashed, $user_id);

        if ($update->execute()) {
            $message = "Password updated successfully.";
        } else {
            $error = "Something went wrong.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | BookFinder</title>

    <link rel="stylesheet" href="./css/styles.css?v=21">
</head>

<body>

<main class="auth-page">

    <section class="auth-card">

        <picture>
            <source srcset="images/logo.webp" type="image/webp">
            <img src="images/logo.png"
                 alt="BookFinder Logo"
                 class="auth-logo"
                 width="240"
                 height="61">
        </picture>

        <h1>Reset Password</h1>

        <?php if ($message): ?>

            <div class="alert alert-success">
                <?php echo htmlspecialchars($message); ?>
            </div>

            <p>
                <a href="login.php">Return to Login</a>
            </p>

        <?php else: ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <label for="password">
                    New Password
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                >

                <label for="confirm_password">
                    Confirm Password
                </label>

                <input
                    type="password"
                    name="confirm_password"
                    id="confirm_password"
                    required
                >

                <button type="submit">
                    Update Password
                </button>

            </form>

        <?php endif; ?>

    </section>

</main>

</body>
</html>