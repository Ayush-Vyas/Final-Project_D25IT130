<?php
session_start();
include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if (!isset($_SESSION['user_email'])) {
  echo "<h2 style='text-align:center;margin-top:50px;font-family:Segoe UI;color:#1a3e59;'>🚫 Please <a href='login.php' style='color:#3a91bf;text-decoration:none;'>login</a> to view your profile.</h2>";
  exit;
}

$user_email = $_SESSION['user_email'];

$stmt = $conn->prepare("SELECT id, name, email, profile_photo, birthdate, gender FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
  echo "<h2 style='text-align:center;margin-top:50px;font-family:Segoe UI;color:#1a3e59;'>🚫 User not found.</h2>";
  exit;
}
$row = $result->fetch_assoc();
$user_id = $row['id'];
$name = $row['name'] ?? '';
$email = $row['email'] ?? '';
$photo = $row['profile_photo'] ?? '';
$birthdate = $row['birthdate'] ?? '';
$gender = $row['gender'] ?? '';
$stmt->close();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Handle photo removal
  if (isset($_POST['remove_photo'])) {
    if (!empty($photo) && file_exists($photo)) {
      unlink($photo);
    }
    $photo = '';
    $stmt = $conn->prepare("UPDATE users SET profile_photo = NULL WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->close();
    $message = "🗑️ Profile photo removed successfully.";
  }

  else {
    $new_name = trim($_POST['full_name']);
    $new_email = trim($_POST['email']);
    $new_birthdate = $_POST['birthdate'] ?? null;
    $new_gender = $_POST['gender'] ?? null;

    $upload_path = "uploads/";
    if (!file_exists($upload_path)) {
      mkdir($upload_path, 0777, true);
    }

    $new_photo = $photo;

    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
      if (!empty($photo) && file_exists($photo)) {
        unlink($photo);
      }
      $file_tmp = $_FILES['profile_photo']['tmp_name'];
      $file_name = time() . "_" . basename($_FILES['profile_photo']['name']);
      $target = $upload_path . $file_name;

      if (move_uploaded_file($file_tmp, $target)) {
        $new_photo = $target;
      }
    }

    if ($new_email !== $email) {
      $token = bin2hex(random_bytes(32));
      $stmt = $conn->prepare("INSERT INTO email_verification (user_id, email, token, created_at) VALUES (?, ?, ?, NOW()) 
        ON DUPLICATE KEY UPDATE email=VALUES(email), token=VALUES(token), created_at=NOW()");
      $stmt->bind_param("iss", $user_id, $new_email, $token);
      $stmt->execute();
      $stmt->close();

      $verifyLink = "http://localhost/AHS/verify_email.php?token=$token";

      $mail = new PHPMailer(true);
      try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ayurahampers@gmail.com';
        $mail->Password = 'ozwr nznh awtv uasw'; // Add Gmail app password here
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('no-reply@ayurahampers.com', 'Ayura Hampers');
        $mail->addAddress($new_email, $new_name);

        $mail->isHTML(false);
        $mail->Subject = 'Verify your new email address';
        $mail->Body = "Hi $new_name,\n\nPlease verify your new email address by clicking the link below:\n$verifyLink\n\nThanks,\nAyura Hampers Team";

        $mail->send();
        $message = "A verification email has been sent to your new email address. Please verify to complete the change.";
      } catch (Exception $e) {
        $message = "Verification email could not be sent. Mailer Error: {$mail->ErrorInfo}";
      }
    } else {
      $update = $conn->prepare("UPDATE users SET name = ?, profile_photo = ?, birthdate = ?, gender = ? WHERE email = ?");
      $update->bind_param("sssss", $new_name, $new_photo, $new_birthdate, $new_gender, $user_email);

      if ($update->execute()) {
        $message = '✅ Profile updated successfully!';
        $name = $new_name;
        $photo = $new_photo;
        $birthdate = $new_birthdate;
        $gender = $new_gender;
      } else {
        $message = '❌ Failed to update profile.';
      }
      $update->close();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Profile - Ayura Hampers</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #e6f2ff, #f0f9ff);
      padding: 40px;
      display: flex;
      justify-content: center;
      color: #1a3e59;
      min-height: 100vh;
      margin: 0;
    }
    .profile-box {
      background-color: #ffffffcc;
      padding: 30px 40px;
      border-radius: 14px;
      box-shadow: 0 0 15px rgba(42, 85, 127, 0.2);
      width: 100%;
      max-width: 460px;
      border: 2px solid #3a91bf;
      text-align: center;
    }
    h2 {
      color: #2a557f;
      margin-bottom: 25px;
      user-select: none;
    }
    label {
      display: block;
      margin: 10px 0 5px;
      color: #2a557f;
      font-weight: 700;
      text-align: left;
    }
    input[type="text"],
    input[type="email"],
    input[type="date"],
    select {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #a6d1ff;
      border-radius: 6px;
      background-color: #f0f9ff;
      font-size: 15px;
      color: #1a3e59;
    }
    input:focus, select:focus {
      border-color: #3a91bf;
      outline: none;
      background-color: #e6f2ff;
    }
    input[type="file"] {
      margin-top: 10px;
      font-size: 14px;
    }
    button {
      margin-top: 15px;
      width: 100%;
      padding: 12px;
      background-color: #3a91bf;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background-color: #2a557f;
    }
    .note { margin-top: 18px; font-size: 14px; color: #2a557f; }
    .note a { color: #3a91bf; text-decoration: none; }
    .back-home { margin-top: 25px; }
    .back-home a {
      display: inline-block; padding: 10px 18px; background-color: #3a91bf;
      color: white; text-decoration: none; border-radius: 6px; font-weight: bold;
    }
    .profile-photo {
      width: 100px; height: 100px; border-radius: 50%; object-fit: cover;
      border: 3px solid #3a91bf; margin-bottom: 15px;
    }
    .message {
      color: #1a3e59;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="profile-box">
    <h2>👤 Your Profile</h2>

    <?php if (!empty($photo)): ?>
      <img src="<?= htmlspecialchars($photo) ?>" alt="Profile Photo" class="profile-photo">
    <?php endif; ?>

    <?php if ($message): ?>
      <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
      <label for="full_name">Full Name</label>
      <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($name) ?>" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

      <label for="gender">Gender</label>
      <select id="gender" name="gender" required>
        <option value="" disabled <?= $gender === '' ? 'selected' : '' ?>>Select Gender</option>
        <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
      </select>

      <label for="birthdate">Birthdate</label>
      <input type="date" id="birthdate" name="birthdate" value="<?= htmlspecialchars($birthdate) ?>">

      <label for="profile_photo">Upload New Photo</label>
      <input type="file" id="profile_photo" name="profile_photo" accept="image/*">

      <button type="submit">Update Profile</button>
    </form>

    <?php if (!empty($photo)): ?>
      <form method="POST" onsubmit="return confirm('Are you sure you want to remove your profile photo?');">
        <input type="hidden" name="remove_photo" value="1">
        <button type="submit" style="background-color: #bf3a3a;">🗑️ Remove Profile Photo</button>
      </form>
    <?php endif; ?>

    <p class="note">Want to logout? <a href="logout.php">Click here</a></p>
    <div class="back-home">
      <a href="index.php">⬅️ Back to Homepage</a>
    </div>
  </div>
</body>
</html>
