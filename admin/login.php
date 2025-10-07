<?php
session_start();

require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Check if username/password submitted
    if (isset($_POST['username'], $_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Hardcoded admin credentials
        if ($username === 'admin' && $password === 'admin123') {
            // Generate OTP
            $otp = random_int(100000, 999999);
            $_SESSION['admin_otp'] = $otp;
            $_SESSION['otp_time'] = time();
            $_SESSION['admin_user'] = $username; // temp store user

            // Send OTP email
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'ayurahampers@gmail.com';  // your email
                $mail->Password = 'ozwr nznh awtv uasw';       // app password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('ayushvyas172@gmail.com', 'Ayura Hampers Admin');
                $mail->addAddress('ayushvyas172@gmail.com'); // admin email

                $mail->isHTML(true);
                $mail->Subject = 'Your Admin Panel OTP Code';
                $mail->Body = "Your OTP code is <b>$otp</b>. It expires in 5 minutes.";

                $mail->send();
                header('Location: verify_otp.php');
                exit;
            } catch (Exception $e) {
                $message = "OTP email sending failed: " . $mail->ErrorInfo;
            }
        } else {
            $message = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Login - Ayura Hampers</title>
<style>
<style>
  /* Reset and base */
  * {
    box-sizing: border-box;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #0b3d91 0%, #1e5bb8 100%);
    height: 100vh;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #1a2e45;
  }

  form {
    background: #f0f7ff;
    padding: 30px 35px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(11, 61, 145, 0.3);
    width: 320px;
    text-align: center;
  }

  form h2 {
    margin-bottom: 24px;
    color: #0b3d91;
    font-weight: 700;
    font-size: 1.8rem;
    letter-spacing: 0.05em;
  }

  input[type=text],
  input[type=password] {
    width: 100%;
    padding: 12px 14px;
    margin: 12px 0 18px;
    border: 2px solid #a9c4ff;
    border-radius: 8px;
    font-size: 1rem;
    color: #0b3d91;
    font-weight: 600;
    transition: border-color 0.3s ease;
  }

  input[type=text]:focus,
  input[type=password]:focus {
    border-color: #07306d;
    outline: none;
    background: #e1eafc;
  }

  button {
    width: 100%;
    padding: 12px;
    background-color: #0b3d91;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    letter-spacing: 0.05em;
    transition: background-color 0.3s ease;
  }

  button:hover {
    background-color: #07306d;
  }

  .message {
    margin-bottom: 16px;
    font-weight: 600;
    font-size: 0.95rem;
    color: #e05c5c;
  }
</style>
</style>
</head>
<body>

<form method="POST">
    <h2>Admin Login</h2>
    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <input type="text" name="username" placeholder="Username" required autocomplete="off" />
    <input type="password" name="password" placeholder="Password" required autocomplete="off" />
    <button type="submit">Send OTP</button>
</form>

</body>
</html>
