<?php
session_start();
require_once 'db.php'; // your DB connection file

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_type = "error";
    } else {
        // Check if user already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Email is already registered.";
            $message_type = "error";
        } else {
            // Insert new user (hash password)
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $email, $hashed);
            if ($stmt->execute()) {
                header('Location: login.php?success=' . urlencode('Registration successful! Please login.'));
                exit;
            } else {
                $message = "Registration failed. Please try again.";
                $message_type = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register - Ayura Hampers</title>
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
    <h2>Register</h2>

    <?php if ($message): ?>
      <div class="message-box <?= $message_type ?>">
        <?= $message ?>
      </div>
    <?php endif; ?>

    <form action="auth_register.php" method="post" novalidate>
      <input type="text" name="fullname" placeholder="Full Name" required />
      <input type="email" name="email" placeholder="Email" required />
      <input type="password" name="password" placeholder="Password" required />
      <input type="password" name="confirm_password" placeholder="Confirm Password" required />
      <button type="submit">Register</button>
    </form>

    <div class="form-footer">
      Already have an account? <a href="login.php">Login</a>
    </div>
  </div>
</body>
</html>
