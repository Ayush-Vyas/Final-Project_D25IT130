<?php
session_start();

$message = "";
$message_type = "";

if (!isset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['otp_expiry'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputOtp = trim($_POST['otp']);
    $now = time();

    if ($now > $_SESSION['otp_expiry']) {
        $message = "OTP expired. Please login again.";
        $message_type = "error";
        session_unset();
        session_destroy();
    } elseif ($inputOtp == $_SESSION['otp']) {
        // OTP correct, log user in
        $_SESSION['user_email'] = $_SESSION['otp_email'];

        // Clear OTP session vars
        unset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['otp_expiry']);

        header('Location: index.php');
        exit;
    } else {
        $message = "Invalid OTP. Please try again.";
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Verify OTP - Ayura Hampers</title>
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
    <h2>Enter OTP</h2>
    <?php if ($message): ?>
      <div class="message-box <?= $message_type ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
      <input type="text" name="otp" placeholder="Enter OTP" required maxlength="6" pattern="\d{6}" />
      <button type="submit">Verify</button>
    </form>
  </div>
</body>
</html>
