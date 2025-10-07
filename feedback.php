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

// Get user_id from database if not in session
if (!isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $_SESSION['user_email']);
    $stmt->execute();
    $stmt->bind_result($user_id);
    if ($stmt->fetch()) {
        $_SESSION['user_id'] = $user_id;
    } else {
        echo "<script>alert('User not found. Please login again.'); window.location='login.php';</script>";
        exit;
    }
    $stmt->close();
} else {
    $user_id = $_SESSION['user_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback = trim($_POST['feedback']);

    if (!empty($feedback)) {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, feedback, submitted_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $user_id, $feedback);
        if ($stmt->execute()) {
            echo "<script>alert('Feedback submitted successfully'); window.location='dashboard.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error saving feedback.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Feedback cannot be empty.');</script>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Feedback - Ayura Hampers</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="Submit your valuable feedback to Ayura Hampers to help us serve you better." />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #e6f2ff, #f0f9ff);
      color: #1a3e59;
      margin: 0;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    .feedback-container {
      background: #ffffffcc;
      padding: 40px 35px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(58, 145, 191, 0.2);
      max-width: 500px;
      width: 90%;
      color: #1a3e59;
      user-select: none;
      animation: fadeInUp 0.6s ease forwards;
      text-align: center;
      box-sizing: border-box;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h2 {
      margin-top: 0;
      color: #2a557f;
      font-size: 26px;
      margin-bottom: 25px;
      font-weight: 700;
      user-select: text;
    }

    textarea {
      width: 100%;
      border-radius: 12px;
      border: 2px solid #b0cfee;
      padding: 16px;
      font-size: 16px;
      resize: vertical;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      font-family: 'Segoe UI', sans-serif;
      color: #1a3e59;
      background-color: #f5fbff;
      box-shadow: inset 0 2px 6px rgba(58, 145, 191, 0.1);
      box-sizing: border-box;
      min-height: 130px;
    }

    textarea:focus {
      border-color: #3a91bf;
      outline: none;
      box-shadow: 0 0 8px rgba(58, 145, 191, 0.5);
      background-color: #e6f2ff;
    }

    #charCount {
      display: block;
      text-align: right;
      font-size: 13px;
      color: #4a668e;
      margin-top: 6px;
      user-select: none;
      font-family: 'Segoe UI', sans-serif;
    }

    button {
      background: #3a91bf;
      color: white;
      border: none;
      padding: 14px 25px;
      font-size: 17px;
      border-radius: 10px;
      margin-top: 22px;
      cursor: pointer;
      width: 100%;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(58, 145, 191, 0.4);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      user-select: none;
    }

    button:hover,
    button:focus {
      background: #2a557f;
      box-shadow: 0 6px 16px rgba(42, 85, 127, 0.7);
      outline: none;
    }

    button:focus {
      outline: 3px solid #2a557f;
      outline-offset: 3px;
    }

    .back-btn {
      display: inline-block;
      margin-top: 18px;
      background: transparent;
      color: #3a91bf;
      font-weight: 600;
      text-decoration: none;
      border: 2px solid #3a91bf;
      padding: 10px 22px;
      border-radius: 30px;
      transition: all 0.3s ease;
      user-select: none;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(58, 145, 191, 0.3);
      font-size: 15px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .back-btn:hover,
    .back-btn:focus {
      background: #3a91bf;
      color: white;
      box-shadow: 0 4px 14px rgba(58, 145, 191, 0.6);
      outline: none;
    }

    .back-btn svg {
      width: 16px;
      height: 16px;
      stroke: currentColor;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
      transition: transform 0.3s ease;
    }

    .back-btn:hover svg,
    .back-btn:focus svg {
      transform: translateX(-4px);
    }

    @media (max-width: 480px) {
      .feedback-container {
        padding: 30px 20px;
      }

      textarea {
        font-size: 14px;
        padding: 14px;
      }

      button {
        font-size: 15px;
        padding: 12px 20px;
      }

      .back-btn {
        font-size: 14px;
        padding: 10px 18px;
      }
    }
  </style>
</head>
<body>

<div class="feedback-container" role="main">
  <h2>Give Your Feedback</h2>
  <form method="POST" action="" novalidate>
    <textarea name="feedback" rows="6" placeholder="Write your feedback here..." required maxlength="500" aria-describedby="charCount"></textarea>
    <small id="charCount">0 / 500</small>
    <button type="submit" id="submitBtn">Submit Feedback</button>
  </form>

  <a href="dashboard.php" class="back-btn" aria-label="Back to Dashboard">
    <!-- Left arrow SVG icon -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
    </svg>
    Back to Dashboard
  </a>
</div>

<script>
  const textarea = document.querySelector('textarea[name="feedback"]');
  const charCount = document.getElementById('charCount');
  const maxLength = 500;

  // Update character count on input
  textarea.addEventListener('input', () => {
    charCount.textContent = `${textarea.value.length} / ${maxLength}`;
  });

  // Disable submit button and change text on form submit
  const form = document.querySelector('form');
  const submitBtn = document.getElementById('submitBtn');

  form.addEventListener('submit', () => {
    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending...';
  });
</script>

</body>
</html>