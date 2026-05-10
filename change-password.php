<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = "";
$error = "";

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Please complete all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $error = "Your new password must be at least 8 characters long.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->bind_param("si", $hashed_password, $user_id);

            if ($update->execute()) {
                $message = "Password changed successfully.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        } else {
            $error = "Your current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password | BookFinder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="./css/styles.css?v=20">
</head>

<body>

<main class="auth-page">
    <section class="auth-card">

        <picture>
            <source srcset="images/logo.webp" type="image/webp">
            <img src="images/logo.png" 
                 alt="BookFinder logo" 
                 class="auth-logo" 
                 width="240" 
                 height="61">
        </picture>

        <h1>Change Password</h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label for="current_password">Current Password</label>
            <input 
                type="password" 
                name="current_password" 
                id="current_password" 
                required>

            <label for="new_password">New Password</label>
            <input 
                type="password" 
                name="new_password" 
                id="new_password" 
                minlength="8"
                required>

            <label for="confirm_password">Confirm New Password</label>
            <input 
                type="password" 
                name="confirm_password" 
                id="confirm_password" 
                minlength="8"
                required>

            <button type="submit">Update Password</button>

        </form>

        <p>
            <a href="index.php">Back to Home</a>
        </p>

    </section>
</main>

</body>
</html>