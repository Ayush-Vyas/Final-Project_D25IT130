<?php
session_start();
require_once 'db.php';
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$message_type = "";

if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $message_type = "success";  // you can style success messages differently
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    // Check if email exists
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        // Generate OTP and save in session with expiry (5 min)
        $otp = random_int(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_email'] = $email;
        $_SESSION['otp_expiry'] = time() + 300; // 5 minutes

        // Send OTP mail
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ayurahampers@gmail.com'; // YOUR email
            $mail->Password = 'ozwr nznh awtv uasw'; // YOUR app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('ayurahampers@gmail.com', 'Ayura Hampers');
            $mail->addAddress($email, $user['name']);

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code for Ayura Hampers Login';
            $mail->Body = "Hello {$user['name']},<br><br>Your OTP code is: <b>$otp</b><br>This code expires in 5 minutes.<br><br>Thanks!";

            $mail->send();

            header('Location: verify_otp.php');
            exit;
        } catch (Exception $e) {
            $message = "Failed to send OTP email: " . $mail->ErrorInfo;
            $message_type = "error";
        }
    } else {
        $message = "Email not found. Please register first.";
        $message_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Ayura Hampers</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #e6f2ff, #f0f9ff); /* watery blue gradient */
      height: 100vh;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      color: #1a3e59; /* deep blue text */
    }
    .container {
      background: #ffffffcc; /* translucent white */
      border-radius: 24px;
      box-shadow: 0 15px 35px rgba(58, 145, 191, 0.25); /* blue tinted shadow */
      width: 350px;
      padding: 40px;
      position: relative;
      user-select: none;
    }
    h2 {
      color: #2a557f; /* deep blue */
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
      border: 1.8px solid #a6d1ff; /* light blue border */
      background: #f0f9ff; /* very light watery blue background */
      color: #1a3e59;
      font-weight: 600;
      margin-bottom: 20px;
      box-shadow: inset 2px 2px 8px #d6ebff, inset -2px -2px 8px #b9d8ff;
      transition: 0.3s ease;
    }
    input::placeholder {
      color: #7eaade; /* soft blue placeholder */
      font-weight: 500;
    }
    input:focus {
      outline: none;
      border-color: #3a91bf; /* main blue */
      box-shadow: 0 0 10px rgba(58, 145, 191, 0.5);
      background: #e6f2ff;
    }
    button {
      width: 100%;
      padding: 14px 0;
      background: linear-gradient(45deg, #3a91bf, #2a557f); /* main blue gradient */
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
    .form-footer {
      text-align: center;
      margin-top: 15px;
      font-weight: 600;
      color: #2a557f;
    }
    .form-footer a {
      color: #3a91bf;
      font-weight: 700;
      text-decoration: none;
      margin-left: 6px;
      transition: color 0.3s ease;
    }
    .form-footer a:hover {
      color: #a6d1ff;
      text-decoration: underline;
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
    <h2>Login</h2>
    <?php if ($message): ?>
      <div class="message-box <?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
      <input type="email" name="email" placeholder="Enter your email" required />
      <button type="submit">Send OTP</button>
    </form>
        <div class="form-footer">
      <a href="forgot_password.php">Forgot Password?</a>
    </div>
    <div class="form-footer">
      New to Ayura? <a href="register.php">Click here to register</a>
    </div>
  </div>
</body>
</html>
