<?php
session_start();
require_once 'db.php';

require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Generate a unique token and expiry (1 hour)
        $token = bin2hex(random_bytes(16));
        $expires = date("Y-m-d H:i:s", time() + 3600);

        // Store token and expiry in DB (you need a password_resets table or add columns)
        // Example table: password_resets (user_id, token, expires)
        $stmt2 = $conn->prepare("INSERT INTO password_resets (user_id, token, expires) VALUES (?, ?, ?) 
                                 ON DUPLICATE KEY UPDATE token = ?, expires = ?");
        $stmt2->bind_param("issss", $user['id'], $token, $expires, $token, $expires);
        $stmt2->execute();

        // Send reset email
        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=$token";

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ayurahampers@gmail.com';  // your email
            $mail->Password = 'ozwr nznh awtv uasw';       // your app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('ayurahampers@gmail.com', 'Ayura Hampers');
            $mail->addAddress($email, $user['name']);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request for Ayura Hampers';
            $mail->Body = "
                Hi {$user['name']},<br><br>
                You requested a password reset. Click the link below to reset your password:<br>
                <a href='$reset_link'>$reset_link</a><br><br>
                This link will expire in 1 hour.<br><br>
                If you didn't request this, please ignore this email.<br><br>
                Thanks,<br>Ayura Hampers Team
            ";

            $mail->send();
            $message = "Password reset link sent! Please check your email.";
            $message_type = "success";

        } catch (Exception $e) {
            $message = "Failed to send email. Please try again later.";
            $message_type = "error";
        }
    } else {
        $message = "No account found with that email.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Forgot Password - Ayura Hampers</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #e6f2ff, #f0f9ff);
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      color: #1a3e59;
    }
    .container {
      background: #ffffffcc;
      border-radius: 24px;
      box-shadow: 0 15px 35px rgba(58, 145, 191, 0.25);
      width: 350px;
      padding: 40px;
      position: relative;
      user-select: none;
    }
    h2 {
      color: #2a557f;
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 30px;
      letter-spacing: 1.1px;
      text-align: center;
      user-select: none;
    }
    input {
      width: 100%;
      padding: 14px;
      font-size: 17px;
      border-radius: 12px;
      border: 1.8px solid #a6d1ff;
      background: #f0f9ff;
      color: #1a3e59;
      font-weight: 600;
      margin-bottom: 20px;
      box-shadow: inset 2px 2px 8px #d6ebff, inset -2px -2px 8px #b9d8ff;
      transition: 0.3s ease;
    }
    input::placeholder {
      color: #7eaade;
      font-weight: 500;
    }
    input:focus {
      outline: none;
      border-color: #3a91bf;
      box-shadow: 0 0 10px rgba(58, 145, 191, 0.5);
      background: #e6f2ff;
    }
    button {
      width: 100%;
      padding: 14px 0;
      background: linear-gradient(45deg, #3a91bf, #2a557f);
      color: #f0f9ff;
      font-size: 18px;
      font-weight: 700;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      box-shadow: 0 6px 12px rgba(58, 145, 191, 0.5);
      transition: background 0.4s ease, transform 0.2s ease;
      user-select: none;
      position: relative;
    }
    button:hover {
      background: linear-gradient(45deg, #2a557f, #3a91bf);
      transform: scale(1.05);
      box-shadow: 0 10px 20px rgba(58, 145, 191, 0.7);
    }
    .message-box {
      margin-bottom: 20px;
      padding: 15px 20px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 1rem;
      word-wrap: break-word;
      text-align: center;
    }
    .message-box.success {
      background-color: #d4edda;
      color: #155724;
      border: 1.5px solid #c3e6cb;
    }
    .message-box.error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1.5px solid #f5c6cb;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Forgot Password</h2>
    <?php if ($message): ?>
      <div class="message-box <?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
      <input type="email" name="email" placeholder="Enter your email" required />
      <button type="submit">Send Reset Link</button>
    </form>
  </div>
</body>
</html>