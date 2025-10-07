<?php
session_start();

// Admin auth check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'header.php';

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_id'], $_POST['reply_message'])) {
    $feedbackId = intval($_POST['reply_id']);
    $replyMessage = trim($_POST['reply_message']);

    // Get user email
    $stmt = $conn->prepare("SELECT u.email, u.name FROM feedback f LEFT JOIN users u ON f.user_id = u.id WHERE f.id = ?");
    $stmt->bind_param("i", $feedbackId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ayurahampers@gmail.com'; // Update with your email
            $mail->Password = 'ozwr nznh awtv uasw';      // Update with App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('ayurahampers@gmail.com', 'Ayura Hampers Admin');
            $mail->addAddress($user['email'], $user['name']);

            $mail->isHTML(true);
            $mail->Subject = 'Reply to Your Feedback - Ayura Hampers';
            $mail->Body    = nl2br(htmlspecialchars($replyMessage));

            $mail->send();

            // Mark feedback as replied
            $conn->query("UPDATE feedback SET replied = 1 WHERE id = $feedbackId");

            echo "<div class='message success'>Reply sent successfully!</div>";
        } catch (Exception $e) {
            echo "<div class='message error'>Mail Error: {$mail->ErrorInfo}</div>";
        }
    }
}

// Prevent deletion if not replied
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $check = $conn->query("SELECT replied FROM feedback WHERE id = $id");
    $row = $check->fetch_assoc();
    if ($row && $row['replied']) {
        $conn->query("DELETE FROM feedback WHERE id = $id");
    }
    header('Location: feedback.php');
    exit;
}

// Get feedback data
$sql = "SELECT f.id, f.user_id, f.feedback, f.submitted_at, f.replied, u.name, u.email 
        FROM feedback f 
        LEFT JOIN users u ON f.user_id = u.id
        ORDER BY f.id DESC";

$result = $conn->query($sql);
?>

<style>
  .container {
    max-width: 900px;
    margin: 0 auto 40px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #1a2e45;
  }

  a.back-btn {
    display: inline-block;
    margin-bottom: 25px;
    padding: 10px 18px;
    background-color: #0b3d91; /* ocean blue */
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s ease;
  }
  a.back-btn:hover {
    background-color: #07306d;
  }

  h2 {
    color: #0b3d91;
    font-weight: 700;
    margin-bottom: 30px;
  }

  .message {
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    max-width: 600px;
    font-weight: 600;
    font-size: 1rem;
  }
  .message.success {
    background-color: #d4edda;
    color: #155724;
  }
  .message.error {
    background-color: #f8d7da;
    color: #721c24;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    word-wrap: break-word;
  }

  thead th {
    background-color: #d6e1f7;
    padding: 12px 10px;
    border: 1px solid #a9bde9;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
  }

  tbody td {
    padding: 12px 10px;
    border: 1px solid #a9bde9;
    font-size: 0.9rem;
    vertical-align: top;
  }

  tbody tr:nth-child(even) {
    background-color: #f3f7ff;
  }

  tbody tr:hover {
    background-color: #e0e7ff;
  }

  form.reply-form {
    margin: 0;
  }

  form.reply-form textarea {
    width: 100%;
    padding: 8px 12px;
    margin-bottom: 8px;
    border: 1px solid #a9bde9;
    border-radius: 8px;
    font-size: 0.9rem;
    color: #0b3d91;
    font-weight: 600;
    resize: vertical;
    min-height: 50px;
    transition: border-color 0.3s ease;
  }
  form.reply-form textarea:focus {
    border-color: #07306d;
    outline: none;
  }

  button.btn-primary {
    background-color: #0b3d91;
    color: white;
    border: none;
    padding: 6px 14px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  button.btn-primary:hover {
    background-color: #07306d;
  }

  .text-success {
    font-weight: 700;
    color: #2e8b57;
    font-size: 0.9rem;
  }

  a.btn-danger {
    background-color: #c94c4c;
    color: white;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 6px 14px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-block;
    margin-top: 6px;
    transition: background-color 0.3s ease;
  }
  a.btn-danger:hover {
    background-color: #992e2e;
  }
</style>

<div class="container">
    <a href="index.php" class="back-btn">← Back to Admin Panel</a>

    <h2>Customer Feedback</h2>

    <?php if ($result->num_rows === 0): ?>
        <p>No feedback submitted yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width:5%;">ID</th>
                    <th style="width:6%;">User ID</th>
                    <th style="width:12%;">Name</th>
                    <th style="width:15%;">Email</th>
                    <th style="width:27%;">Feedback</th>
                    <th style="width:13%;">Submitted At</th>
                    <th style="width:7%;">Replied</th>
                    <th style="width:15%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fb = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= intval($fb['id']) ?></td>
                        <td><?= intval($fb['user_id']) ?></td>
                        <td><?= htmlspecialchars($fb['name'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($fb['email'] ?? 'N/A') ?></td>
                        <td style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($fb['feedback'])) ?></td>
                        <td><?= htmlspecialchars($fb['submitted_at']) ?></td>
                        <td><?= $fb['replied'] ? '✅' : '❌' ?></td>
                        <td>
                            <?php if (!$fb['replied']): ?>
                                <form method="POST" class="reply-form" onsubmit="return confirm('Send this reply?');">
                                    <input type="hidden" name="reply_id" value="<?= intval($fb['id']) ?>">
                                    <textarea name="reply_message" placeholder="Type your reply here..." required></textarea>
                                    <button type="submit" class="btn-primary">Send Reply</button>
                                </form>
                            <?php else: ?>
                                <span class="text-success">Replied</span><br>
                                <a href="feedback.php?delete=<?= intval($fb['id']) ?>" class="btn-danger" onclick="return confirm('Delete this feedback?')">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require 'footer.php'; ?>
