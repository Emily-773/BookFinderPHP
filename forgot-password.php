<?php
session_start();
require_once 'includes/db.php';

$message = "";
$error = "";

/* Brevo settings */
$brevo_api_key = "";
$sender_email = "ejrutherfordnew@gmail.com";
$sender_name = "BookFinder";

/* Your live reset page */
$site_url = "https://erutherford.uosweb.co.uk";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {

        $error = "Please enter your email address.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } else {

        $stmt = $conn->prepare("
            SELECT id, email 
            FROM users 
            WHERE email = ?
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        /* Always show generic success message */
        $message = "If an account exists for that email, a password reset link has been sent.";

        if ($user) {

            $token = bin2hex(random_bytes(32));

            $expires = date(
                "Y-m-d H:i:s",
                time() + 3600
            );

            $update = $conn->prepare("
                UPDATE users
                SET reset_token = ?,
                    reset_expires = ?
                WHERE id = ?
            ");

            $update->bind_param(
                "ssi",
                $token,
                $expires,
                $user['id']
            );

            $update->execute();

            $reset_link =
                $site_url .
                "/reset-password.php?token=" .
                urlencode($token);

            $email_data = [

                "sender" => [
                    "name" => $sender_name,
                    "email" => $sender_email
                ],

                "to" => [
                    [
                        "email" => $email
                    ]
                ],

                "subject" => "Reset your BookFinder password",

                "htmlContent" => "
                    <h2>Reset your BookFinder password</h2>

                    <p>
                        You requested a password reset for your BookFinder account.
                    </p>

                    <p>
                        <a href='{$reset_link}'>
                            Click here to reset your password
                        </a>
                    </p>

                    <p>
                        This link will expire in 1 hour.
                    </p>

                    <p>
                        If you did not request this, you can ignore this email.
                    </p>
                "
            ];

            $ch = curl_init(
                "https://api.brevo.com/v3/smtp/email"
            );

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "accept: application/json",
                "api-key: " . $brevo_api_key,
                "content-type: application/json"
            ]);

            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                json_encode($email_data)
            );

            curl_setopt(
                $ch,
                CURLOPT_RETURNTRANSFER,
                true
            );

            curl_exec($ch);

            curl_close($ch);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <title>
        Forgot Password | BookFinder
    </title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <link
        rel="stylesheet"
        href="./css/styles.css?v=21"
    >

</head>

<body>

<main class="auth-page">

    <section class="auth-card">

        <picture>
            <source
                srcset="images/logo.webp"
                type="image/webp"
            >

            <img
                src="images/logo.png"
                alt="BookFinder logo"
                class="auth-logo"
                width="240"
                height="61"
            >
        </picture>

        <h1>
            Forgot Password
        </h1>

        <p>
            Enter your email address and we will send you a password reset link.
        </p>

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

            <label for="email">
                Email Address
            </label>

            <input
                type="email"
                name="email"
                id="email"
                required
            >

            <button type="submit">
                Send Reset Link
            </button>

        </form>

        <p>
            <a href="login.php">
                Back to Login
            </a>
        </p>

    </section>

</main>

</body>
</html>