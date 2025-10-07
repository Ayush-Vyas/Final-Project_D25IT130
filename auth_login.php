<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows == 1) {
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
      $_SESSION['user_id'] = $id;
      header("Location: index.php");
      exit();
    } else {
      header("Location: login.php?error=" . urlencode("Incorrect password."));
      exit();
    }
  } else {
    header("Location: login.php?error=" . urlencode("Email not found."));
    exit();
  }
} else {
  header("Location: login.php");
  exit();
}
?>
