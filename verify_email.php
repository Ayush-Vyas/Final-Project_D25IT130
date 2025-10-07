<?php
session_start();
include 'config.php';

if (!isset($_GET['token'])) {
    die("No token provided.");
}

$token = $_GET['token'];

$stmt = $conn->prepare("SELECT user_id, email, created_at FROM email_verification WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid or expired token.");
}

$row = $result->fetch_assoc();
$user_id = $row['user_id'];
$new_email = $row['email'];
$created_at = $row['created_at'];

$expire_time = strtotime($created_at) + 24*60*60;
if (time() > $expire_time) {
    $del_stmt = $conn->prepare("DELETE FROM email_verification WHERE token = ?");
    $del_stmt->bind_param("s", $token);
    $del_stmt->execute();
    $del_stmt->close();

    die("Token expired. Please try updating your email again.");
}

$update = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
$update->bind_param("si", $new_email, $user_id);

if ($update->execute()) {
    // Delete verification record
    $del_stmt = $conn->prepare("DELETE FROM email_verification WHERE token = ?");
    $del_stmt->bind_param("s", $token);
    $del_stmt->execute();
    $del_stmt->close();

    // Destroy current session to clear old user_email
    session_unset();
    session_destroy();

    // Redirect to login page with success message
    header("Location: login.php?msg=" . urlencode("✅ Your email has been successfully updated. Please log in with your new email."));
    exit;
}

$stmt->close();
$update->close();
?>
