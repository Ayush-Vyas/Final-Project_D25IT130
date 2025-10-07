<?php
session_start();
if (!isset($_SESSION['user_email'])) {
  echo "<script>alert('Please login first'); window.location='login.php';</script>";
  exit;
}

$conn = new mysqli("localhost", "root", "", "ayura_hampers");
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);

$user_email = $_SESSION['user_email'];

// Fetch user_id using email
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($user_id);
if (!$stmt->fetch()) {
  echo "<script>alert('User not found.'); window.location='login.php';</script>";
  exit;
}
$stmt->close();

$user_id = intval($user_id);

// Now fetch orders for this user
$result = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Track Orders - Ayura Hampers</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    :root {
      --primary: #d0e8ff;       /* light watery blue */
      --accent: #2a557f;        /* deep blue */
      --background: #f0f9ff;    /* very light blue */
      --border: #b0cfee;        /* soft blue border */
      --text: #1a3e59;          /* deep blue text */

      --delivered: #28a745;
      --shipped: #007bff;
      --confirmed: #17a2b8;
      --pending: #ffc107;
      --cancelled: #dc3545;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: var(--background);
      color: var(--text);
      padding: 40px 20px;
      margin: 0;
      min-height: 100vh;
      box-sizing: border-box;
    }

    h1, h2 {
      font-weight: 700;
      color: var(--accent);
      text-align: center;
      margin-bottom: 25px;
    }
    h1 {
      font-size: 2.6rem;
      margin-top: 0;
    }
    h2 {
      font-size: 2.2rem;
      margin-top: 0;
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0 12px; /* spacing between rows */
      background: transparent;
      max-width: 900px;
      margin: 0 auto 40px auto;
    }

    thead tr {
      background-color: var(--primary);
      border-radius: 12px;
      color: var(--accent);
      font-weight: 600;
    }

    th, td {
      padding: 18px 16px;
      text-align: left;
      background: white;
      color: var(--text);
      border-radius: 10px;
      vertical-align: top;
      box-shadow: 0 3px 6px rgba(58, 145, 191, 0.1);
    }

    th {
      box-shadow: none;
      background: var(--primary);
      color: var(--accent);
      font-weight: 700;
    }

    tr:hover td {
      background-color: #e6f2ff;
      box-shadow: 0 4px 14px rgba(42, 85, 127, 0.15);
    }

    .cart-items {
      font-size: 0.95rem;
      line-height: 1.5;
      color: var(--text);
    }

    .status {
      display: inline-block;
      padding: 7px 14px;
      border-radius: 30px;
      font-size: 0.85rem;
      font-weight: 700;
      text-transform: capitalize;
      user-select: none;
      box-shadow: 0 2px 6px rgba(58, 145, 191, 0.15);
    }

    .Delivered {
      background-color: var(--delivered);
      color: white;
    }

    .Shipped {
      background-color: var(--shipped);
      color: white;
    }

    .Confirmed {
      background-color: var(--confirmed);
      color: white;
    }

    .Pending {
      background-color: var(--pending);
      color: black;
    }

    .Cancelled {
      background-color: var(--cancelled);
      color: white;
    }

    /* Responsive styling for mobile */
    @media (max-width: 768px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }

      thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
      }

      tr {
        margin-bottom: 18px;
        background: white;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 4px 16px rgba(42, 85, 127, 0.1);
      }

      td {
        border: none;
        padding: 10px 0;
        position: relative;
        text-align: right;
        font-size: 15px;
      }

      td:before {
        content: attr(data-label);
        position: absolute;
        left: 20px;
        top: 12px;
        font-weight: 700;
        color: var(--accent);
        text-transform: uppercase;
        font-size: 13px;
        text-align: left;
      }

      td:last-child {
        padding-bottom: 16px;
      }
    }

    /* Back to Homepage Button */
    .back-btn {
      display: inline-block;
      margin: 0 auto;
      padding: 12px 28px;
      font-size: 16px;
      font-weight: 600;
      text-align: center;
      text-decoration: none;
      border-radius: 30px;
      color: var(--accent);
      border: 2px solid var(--accent);
      box-shadow: 0 3px 8px rgba(42, 85, 127, 0.3);
      transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
      user-select: none;
      cursor: pointer;
      max-width: 200px;
      display: block;
    }
    .back-btn:hover,
    .back-btn:focus {
      background-color: var(--accent);
      color: white;
      box-shadow: 0 5px 14px rgba(42, 85, 127, 0.7);
      outline: none;
    }
  </style>
</head>
<body>

  <h1>Track Your Orders</h1>

  <?php if ($result && $result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Items</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Order Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td data-label="Order ID">#<?= htmlspecialchars($row['id']) ?></td>
          <td data-label="Items">
            <div class="cart-items">
              <?php
                $order_id = (int)$row['id'];
                $items_result = $conn->query("SELECT product_name, quantity, total FROM order_items WHERE order_id = $order_id");

                if ($items_result && $items_result->num_rows > 0) {
                  while ($item = $items_result->fetch_assoc()) {
                    echo "- " . htmlspecialchars($item['product_name']) . " (" . intval($item['quantity']) . " pcs) - ₹" . number_format(floatval($item['total']), 2) . "<br>";
                  }
                } else {
                  echo "No items found.";
                }
              ?>
            </div>
          </td>
          <td data-label="Payment"><?= htmlspecialchars($row['payment_method']) ?></td>
          <td data-label="Status">
            <?php
              $status_raw = $row['status'] ?? 'Pending';
              if (empty(trim($status_raw))) {
                $status_raw = 'Pending';
              }
              $status_class = preg_replace('/[^a-zA-Z]/', '', ucfirst(strtolower($status_raw)));
            ?>
            <span class="status <?= htmlspecialchars($status_class) ?>">
              <?= htmlspecialchars($status_raw) ?>
            </span>
          </td>
          <td data-label="Order Date"><?= date('d M Y, H:i', strtotime($row['created_at'] ?? '')) ?: 'N/A' ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p style="text-align: center;">You haven't placed any orders yet.</p>
  <?php endif; ?>

  <a href="dashboard.php" class="back-btn" aria-label="Back to Dashboard">← Back to Dashboard</a>

</body>
</html>
