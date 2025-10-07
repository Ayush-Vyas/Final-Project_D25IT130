<?php
require 'header.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer (adjust path as needed)
require 'C:/wamp64/www/AHS/PHPMailer-master/src/Exception.php';
require 'C:/wamp64/www/AHS/PHPMailer-master/src/PHPMailer.php';
require 'C:/wamp64/www/AHS/PHPMailer-master/src/SMTP.php';

// Connect to DB
$conn = new mysqli("localhost", "root", "", "ayura_hampers");
if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

$message = '';
$message_type = ''; // success or error

// Handle reply form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_submit'])) {
    $contact_id = intval($_POST['contact_id'] ?? 0);
    $reply_message = trim($_POST['reply_message'] ?? '');

    if ($contact_id <= 0 || $reply_message === '') {
        $message = "Invalid reply submission.";
        $message_type = 'error';
    } else {
        // Fetch the contact info to get email and name
        $stmt = $conn->prepare("SELECT email, name FROM contact WHERE id = ?");
        $stmt->bind_param("i", $contact_id);
        $stmt->execute();
        $stmt->bind_result($user_email, $user_name);
        if ($stmt->fetch()) {
            $stmt->close();

            // Send email via PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';       // Set your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'ayurahampers@gmail.com';   // SMTP username
                $mail->Password = 'ozwr nznh awtv uasw';            // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('ayurahampers@gmail.com', 'Ayura Hampers Admin');
                $mail->addAddress($user_email, $user_name);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Reply to your contact message at Ayura Hampers';
                $mail->Body    = nl2br(htmlspecialchars($reply_message));
                $mail->AltBody = strip_tags($reply_message);

                $mail->send();

                // Mark as replied
                $updateStmt = $conn->prepare("UPDATE contact SET replied = 1 WHERE id = ?");
                $updateStmt->bind_param("i", $contact_id);
                $updateStmt->execute();
                $updateStmt->close();

                $message = "Reply sent successfully!";
                $message_type = 'success';

            } catch (Exception $e) {
                $message = "Mailer Error: " . $mail->ErrorInfo;
                $message_type = 'error';
            }
        } else {
            $stmt->close();
            $message = "Contact message not found.";
            $message_type = 'error';
        }
    }
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $del_id = intval($_POST['delete_id']);
    if ($del_id > 0) {
        // Check if replied first
        $checkStmt = $conn->prepare("SELECT replied FROM contact WHERE id = ?");
        $checkStmt->bind_param("i", $del_id);
        $checkStmt->execute();
        $checkStmt->bind_result($replied);
        if ($checkStmt->fetch()) {
            $checkStmt->close();
            if ($replied) {
                // Delete allowed
                $delStmt = $conn->prepare("DELETE FROM contact WHERE id = ?");
                $delStmt->bind_param("i", $del_id);
                if ($delStmt->execute()) {
                    $message = "Contact message deleted.";
                    $message_type = 'success';
                } else {
                    $message = "Error deleting the message.";
                    $message_type = 'error';
                }
                $delStmt->close();
            } else {
                $message = "Cannot delete without replying first.";
                $message_type = 'error';
            }
        } else {
            $checkStmt->close();
            $message = "Message not found.";
            $message_type = 'error';
        }
    } else {
        $message = "Invalid delete request.";
        $message_type = 'error';
    }
}

// Fetch all contact messages ordered by newest first
$sql = "SELECT id, name, email, issue, submitted_at, replied FROM contact ORDER BY submitted_at DESC";
$result = $conn->query($sql);
?>

<style>
  .contact-container {
    max-width: 1000px;
    margin: 50px auto;
    background: #fff;
    padding: 30px 25px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(11, 61, 145, 0.15);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  h2 {
    color: #0b3d91;
    margin-bottom: 25px;
    text-align: center;
    letter-spacing: 1px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    word-wrap: break-word;
  }
  thead {
    background-color: #0b3d91;
    color: white;
  }
  th, td {
    padding: 12px 15px;
    border: 1px solid #d0d7e2;
    text-align: left;
    vertical-align: top;
  }
  tbody tr:nth-child(even) {
    background-color: #f7faff;
  }
  tbody tr:hover {
    background-color: #e1e8f7;
  }
  .no-data {
    text-align: center;
    color: #4a668e;
    font-style: italic;
    padding: 20px;
  }
  button, input[type="submit"] {
    cursor: pointer;
    background-color: #0b3d91;
    border: none;
    color: white;
    padding: 7px 14px;
    border-radius: 6px;
    font-weight: 600;
    transition: background-color 0.3s ease;
  }
  button:hover, input[type="submit"]:hover {
    background-color: #07306d;
  }
  button:disabled {
    background-color: #999999;
    cursor: not-allowed;
  }
  .message {
    max-width: 1000px;
    margin: 20px auto;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: 600;
  }
  .message.success {
    background-color: #d4edda;
    color: #155724;
  }
  .message.error {
    background-color: #f8d7da;
    color: #721c24;
  }
  /* Modal styles */
  .modal {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 80px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
  }
  .modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 25px;
    border-radius: 12px;
    max-width: 600px;
    position: relative;
  }
  .close-btn {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    right: 20px;
    top: 10px;
  }
  .close-btn:hover,
  .close-btn:focus {
    color: black;
  }
  textarea {
    width: 100%;
    height: 150px;
    resize: vertical;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #a6d1ff;
    font-size: 16px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #1a3e59;
    background: #f0f9ff;
  }
  textarea:focus {
    outline: none;
    border-color: #3a91bf;
    background: #e6f2ff;
  }
  .modal-header {
    font-size: 1.3em;
    margin-bottom: 15px;
    color: #0b3d91;
    user-select: none;
  }
</style>

<div class="contact-container">
  <h2>Contact Messages</h2>

  <?php if ($message): ?>
    <div class="message <?= $message_type === 'success' ? 'success' : 'error' ?>">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <?php if ($result && $result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th style="width: 30px;">#</th>
          <th style="width: 140px;">Name</th>
          <th style="width: 180px;">Email</th>
          <th>Issue</th>
          <th style="width: 140px;">Submitted At</th>
          <th style="width: 130px;">Actions</th>
        </tr>
      </thead>
      <tbody>
  <?php
    $count = 1;
    while ($row = $result->fetch_assoc()):
      $isReplied = (bool)$row['replied'];
  ?>
    <tr>
      <td><?= $count++ ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td style="white-space: pre-wrap;"><?= htmlspecialchars($row['issue']) ?></td>
      <td><?= date('d M Y, H:i', strtotime($row['submitted_at'])) ?></td>
      <td style="white-space: nowrap;">
  <?php if (!$isReplied): ?>
    <button class="reply-btn" 
      data-id="<?= $row['id'] ?>" 
      data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>" 
      data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
      title="Reply to this message"
      style="margin-right: 10px; padding: 7px 16px;">
      Reply
    </button>
  <?php else: ?>
    <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this message?');">
      <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
      <input type="submit" value="Delete" style="padding: 7px 16px;">
    </form>
  <?php endif; ?>
</td>

    </tr>
  <?php endwhile; ?>
</tbody>

    </table>
  <?php else: ?>
    <p class="no-data">No contact messages found.</p>
  <?php endif; ?>

  <div style="margin-top: 30px; text-align:center;">
    <a href="index.php" style="display: inline-block; background:#0b3d91; color:#fff; padding: 12px 25px; border-radius: 30px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 12px rgba(11, 61, 145, 0.6); transition: background-color 0.3s ease;">Back to Homepage</a>
  </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" id="modalClose">&times;</span>
    <div class="modal-header">Reply to User</div>
    <form method="post" id="replyForm">
      <input type="hidden" name="contact_id" id="contact_id" value="">
      <label for="reply_message">Message:</label>
      <textarea name="reply_message" id="reply_message" required></textarea>
      <br>
      <input type="submit" name="reply_submit" value="Send Reply" style="margin-top: 15px; width: 100%;">
    </form>
  </div>
</div>

<script>
  // Modal logic
  const modal = document.getElementById('replyModal');
  const modalCloseBtn = document.getElementById('modalClose');
  const replyBtns = document.querySelectorAll('.reply-btn');
  const contactIdInput = document.getElementById('contact_id');
  const replyMessageTextarea = document.getElementById('reply_message');

  replyBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      contactIdInput.value = btn.getAttribute('data-id');
      replyMessageTextarea.value = "";
      modal.style.display = 'block';
      replyMessageTextarea.focus();
    });
  });

  modalCloseBtn.onclick = () => {
    modal.style.display = 'none';
  };

  window.onclick = (event) => {
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  };
</script>

<?php require 'footer.php'; ?>
