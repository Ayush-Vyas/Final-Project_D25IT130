<?php
session_start();
require_once 'db.php';

$message = '';
$message_type = '';
$show_form = false;
$token = $_GET['token'] ?? '';

if (!$token) {
    $message = "Invalid or missing token.";
    $message_type = "error";
} else {
    // Check token in DB and expiry
    $stmt = $conn->prepare("SELECT user_id, expires FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (strtotime($row['expires']) < time()) {
            $message = "Token expired. Please request a new password reset.";
            $message_type = "error";
        } else {
            $show_form = true;
            $user_id = $row['user_id'];
        }
    } else {
        $message = "Invalid token. Please request a new password reset.";
        $message_type = "error";
    }
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'], $_POST['confirm_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_type = "error";
        $show_form = true;
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $message_type = "error";
        $show_form = true;
    } else {
        // Update user password (hash it)
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password_hash, $user_id);
        if ($stmt->execute()) {
            // Delete used token
            $stmt2 = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $stmt2->bind_param("i", $user_id);
            $stmt2->execute();

            $message = "Password reset successful! You can now <a href='login.php'>login</a>.";
            $message_type = "success";
            $show_form = false;
        } else {
            $message = "Error updating password. Try again.";
            $message_type = "error";
            $show_form = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Reset Password - Ayura Hampers</title>
  <style>
    /* Same styles as forgot_password.php */
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
      font-weight:
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
    <h2>Reset Password</h2>

    <?php if ($message): ?>
      <div class="message-box <?= $message_type ?>">
        <?= $message_type === 'success' ? $message : htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <?php if ($show_form): ?>
      <form method="post" novalidate>
        <input type="password" name="password" placeholder="New password" required minlength="6" />
        <input type="password" name="confirm_password" placeholder="Confirm new password" required minlength="6" />
        <button type="submit">Reset Password</button>
      </form>
    <?php else: ?>
      <p style="text-align:center;">
        <a href="forgot_password.php">Request a new password reset link</a>
      </p>
    <?php endif; ?>
  </div>
</body>
</html>

