<?php
session_start();

$message = '';

if (!isset($_SESSION['admin_otp'], $_SESSION['admin_user'], $_SESSION['otp_time'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputOtp = trim($_POST['otp'] ?? '');

    // Check OTP timeout (5 minutes)
    if (time() - $_SESSION['otp_time'] > 300) {
        $message = "OTP expired. Please login again.";
        session_unset();
        session_destroy();
    } elseif ($inputOtp == $_SESSION['admin_otp']) {
        // OTP valid, set admin logged in session
        $_SESSION['admin_logged_in'] = true;
        unset($_SESSION['admin_otp'], $_SESSION['otp_time']);
        header('Location: index.php');  // Admin panel main page
        exit;
    } else {
        $message = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Verify OTP - Admin Login</title>
<style>
body { font-family: Arial, sans-serif; background: #f7f7f7; display: flex; justify-content: center; align-items: center; height: 100vh; }
form { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 300px; }
input[type=text] { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
button { width: 100%; padding: 10px; background: #d19c64; border: none; color: white; font-weight: bold; cursor: pointer; }
button:hover { background: #b07d3b; }
.message { color: red; text-align: center; margin-bottom: 10px; }
</style>
</head>
<body>

<form method="POST">
    <h2>Enter OTP</h2>
    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <input type="text" name="otp" placeholder="6-digit OTP" required pattern="\d{6}" maxlength="6" autocomplete="off" />
    <button type="submit">Verify OTP</button>
</form>

</body>
</html>
