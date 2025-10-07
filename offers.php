<?php
$conn = new mysqli('localhost', 'root', '', 'ayura_hampers');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT promo_code, discount_amount, min_order_amount, expiry_date FROM promo_codes WHERE active = 1 ORDER BY expiry_date");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Offers - Ayura Hampers</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f9ff; /* light watery blue */
      color: #1a3e59; /* deep blue text */
      margin: 0;
      padding: 40px 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      flex-direction: column;
      box-sizing: border-box;
    }

    .offers-box {
      background: #ffffff;
      padding: 35px 30px;
      border-radius: 18px;
      box-shadow: 0 6px 18px rgba(42, 85, 127, 0.15);
      max-width: 520px;
      width: 100%;
      text-align: center;
      margin-bottom: 30px;
    }

    h2 {
      color: #2a557f; /* deep blue */
      font-size: 28px;
      margin-bottom: 30px;
      font-weight: 700;
      letter-spacing: 1px;
    }

    ul {
      list-style-type: none;
      padding: 0;
      margin: 0;
    }

    li {
      background: #d0e8ff; /* soft watery blue */
      margin: 16px 0;
      padding: 18px 20px;
      border-radius: 14px;
      font-size: 17px;
      color: #1a3e59;
      box-shadow: 0 3px 8px rgba(42, 85, 127, 0.1);
      transition: background 0.3s ease, box-shadow 0.3s ease;
    }

    li:hover {
      background: #a3c8ff;
      box-shadow: 0 6px 20px rgba(42, 85, 127, 0.2);
      color: #123057;
    }

    strong {
      color: #2a557f; /* deep blue */
      font-size: 19px;
      font-weight: 700;
    }

    .no-offers {
      color: #567890;
      font-style: italic;
      font-size: 16px;
    }

    /* Back to Dashboard Button */
    .back-btn {
      display: inline-block;
      padding: 12px 28px;
      font-size: 16px;
      font-weight: 600;
      text-align: center;
      text-decoration: none;
      border-radius: 30px;
      color: #2a557f;
      border: 2px solid #2a557f;
      box-shadow: 0 3px 8px rgba(42, 85, 127, 0.3);
      transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
      user-select: none;
      cursor: pointer;
      max-width: 200px;
      margin-top: 10px;
    }
    .back-btn:hover,
    .back-btn:focus {
      background-color: #2a557f;
      color: white;
      box-shadow: 0 5px 14px rgba(42, 85, 127, 0.7);
      outline: none;
    }
  </style>
</head>
<body>

  <div class="offers-box">
    <h2>Today's Offers 🎁</h2>
    <ul>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <li>
            <strong><?= htmlspecialchars($row['promo_code']) ?></strong> – 
            ₹<?= number_format($row['discount_amount']) ?> off on orders above 
            ₹<?= number_format($row['min_order_amount']) ?> 
            (valid till <?= date('d M Y', strtotime($row['expiry_date'])) ?>)
          </li>
        <?php endwhile; ?>
      <?php else: ?>
        <li class="no-offers">No active promo codes available at the moment.</li>
      <?php endif; ?>
    </ul>
  </div>

  <a href="dashboard.php" class="back-btn" aria-label="Back to Dashboard">← Back to Dashboard</a>

</body>
</html>
