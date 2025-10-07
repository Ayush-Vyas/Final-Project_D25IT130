<?php
session_start();

if (!isset($_SESSION['user_email'])) {
  echo "<script>alert('Please login first'); window.location='login.php';</script>";
  exit;
}

$conn = new mysqli("localhost", "root", "", "ayura_hampers");
if ($conn->connect_error) {
  die("DB Connection Failed: " . $conn->connect_error);
}

$user_email = $_SESSION['user_email'];

// Fetch user_id, name, email from email
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($user_id, $name, $email);
if (!$stmt->fetch()) {
  // User not found - clear session and redirect to login
  session_unset();
  session_destroy();
  echo "<script>alert('User not found. Please login again.'); window.location='login.php';</script>";
  exit;
}
$stmt->close();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $issue = trim($_POST['issue'] ?? '');

  if ($issue === '') {
    $message = "Please select an issue.";
  } else {
    if ($issue === 'Other') {
      $custom_issue = trim($_POST['other_issue'] ?? '');
      if ($custom_issue === '') {
        $message = "Please describe your issue in the box.";
      } else {
        $issue = $custom_issue;
      }
    }

    if (!$message) {
      $stmt = $conn->prepare("INSERT INTO contact (name, email, issue, submitted_at) VALUES (?, ?, ?, NOW())");
      $stmt->bind_param("sss", $name, $email, $issue);
      if ($stmt->execute()) {
        $message = "Thank you! Your message has been received.";
        echo "<script>
          setTimeout(function() {
            window.location.href = 'index.php';
          }, 2000);
        </script>";
      } else {
        $message = "Error saving your message, please try again.";
      }
      $stmt->close();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact - Ayura Hampers</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #e6f2ff;
      color: #1a3e59;
      padding: 30px 20px;
      max-width: 600px;
      margin: 40px auto;
      box-shadow: 0 10px 25px rgba(58, 145, 191, 0.15);
      border-radius: 15px;
      user-select: none;
    }
    h1 {
      text-align: center;
      margin-bottom: 30px;
      color: #3a91bf;
      letter-spacing: 1.5px;
      font-weight: 700;
      user-select: none;
    }
    label {
      font-weight: 600;
      display: block;
      margin-bottom: 6px;
      margin-top: 20px;
      color: #2a557f;
      user-select: none;
    }
    input[type="text"],
    input[type="email"],
    select,
    textarea {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid #a6d1ff;
      border-radius: 8px;
      font-size: 16px;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      background: #f0f9ff;
      color: #1a3e59;
      resize: vertical;
      box-sizing: border-box;
      user-select: text;
      box-shadow: inset 0 2px 5px rgba(58,145,191,0.1);
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    select:focus,
    textarea:focus {
      border-color: #3a91bf;
      outline: none;
      background: #e6f2ff;
      box-shadow: 0 0 8px rgba(58, 145, 191, 0.5);
    }
    input[readonly] {
      background: #dbe9ff;
      cursor: not-allowed;
      user-select: none;
    }
    input[type="submit"],
    .back-button {
      margin-top: 30px;
      background: #3a91bf;
      color: white;
      border: none;
      border-radius: 30px;
      padding: 14px 0;
      width: 100%;
      font-weight: 700;
      font-size: 17px;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(58, 145, 191, 0.6);
      transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.2s ease;
      display: block;
      text-align: center;
      text-decoration: none;
      user-select: none;
    }
    input[type="submit"]:hover,
    .back-button:hover,
    input[type="submit"]:focus,
    .back-button:focus {
      background: #2a557f;
      box-shadow: 0 6px 16px rgba(42, 85, 127, 0.7);
      outline: none;
      transform: scale(1.05);
    }
    .message {
      text-align: center;
      margin-top: 20px;
      font-weight: 600;
      color: #2a557f;
      user-select: none;
    }
    .error {
      color: #d9534f;
    }
    .contact-info {
      margin-top: 40px;
      font-size: 1rem;
      color: #486d8a;
      user-select: none;
    }
    .contact-info h2 {
      font-weight: 700;
      color: #2a557f;
      margin-bottom: 10px;
      user-select: none;
    }
    .contact-info a {
      color: #3a91bf;
      text-decoration: none;
      font-weight: 600;
      user-select: text;
    }
    .contact-info a:hover {
      text-decoration: underline;
    }
    #otherIssueContainer {
      margin-top: 15px;
      max-height: 0;
      overflow: hidden;
      opacity: 0;
      transition: max-height 0.4s ease, opacity 0.4s ease;
    }
    #otherIssueContainer.show {
      max-height: 200px;
      opacity: 1;
    }

    /* Responsive */
    @media (max-width: 480px) {
      body {
        padding: 20px 15px;
        margin: 20px auto;
      }
      input[type="submit"],
      .back-button {
        font-size: 16px;
        padding: 12px 0;
      }
    }
  </style>
</head>
<body>

  <h1>Contact Us</h1>

  <?php if ($message): ?>
    <p class="message <?= strpos($message, 'Error') !== false || strpos($message, 'Please') !== false ? 'error' : '' ?>">
      <?= htmlspecialchars($message) ?>
    </p>
  <?php endif; ?>

  <form action="" method="post" novalidate>
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" readonly>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly>

    <label for="issue">Issue:</label>
    <select id="issue" name="issue" required onchange="toggleOtherIssue()">
      <option value="" disabled <?= !isset($_POST['issue']) ? 'selected' : '' ?>>Select an issue</option>
      <option value="Product not received" <?= (($_POST['issue'] ?? '') === "Product not received") ? 'selected' : '' ?>>Product not received</option>
      <option value="Damaged product" <?= (($_POST['issue'] ?? '') === "Damaged product") ? 'selected' : '' ?>>Damaged product</option>
      <option value="Wrong item delivered" <?= (($_POST['issue'] ?? '') === "Wrong item delivered") ? 'selected' : '' ?>>Wrong item delivered</option>
      <option value="Payment issues" <?= (($_POST['issue'] ?? '') === "Payment issues") ? 'selected' : '' ?>>Payment issues</option>
      <option value="Other" <?= (($_POST['issue'] ?? '') === "Other") ? 'selected' : '' ?>>Other</option>
    </select>

    <div id="otherIssueContainer" class="<?= (($_POST['issue'] ?? '') === "Other") ? 'show' : '' ?>">
      <label for="other_issue">Please describe your issue:</label>
      <textarea id="other_issue" name="other_issue" rows="4" placeholder="Type your issue here..."><?= htmlspecialchars($_POST['other_issue'] ?? '') ?></textarea>
    </div>

    <input type="submit" value="Send">
  </form>

  <a href="index.php" class="back-button" tabindex="0">Back to Homepage</a>

  <div class="contact-info">
    <h2>Get in Touch</h2>
    <p>Instagram: <a href="https://www.instagram.com/ayura.hampers" target="_blank" rel="noopener noreferrer">@ayurahampers</a></p>
    <p>WhatsApp Chat: <a href="https://wa.me/+918469434870" target="_blank" rel="noopener noreferrer">+91 84694 34870</a></p>
    <p>Phone: <a href="tel:+918469434870">+91 84694 34870</a></p>
  </div>

  <script>
    function toggleOtherIssue() {
      const issueSelect = document.getElementById('issue');
      const otherContainer = document.getElementById('otherIssueContainer');
      if (issueSelect.value === 'Other') {
        otherContainer.classList.add('show');
        document.getElementById('other_issue').setAttribute('required', 'required');
      } else {
        otherContainer.classList.remove('show');
        document.getElementById('other_issue').removeAttribute('required');
      }
    }
    window.onload = toggleOtherIssue;
  </script>

</body>
</html>
