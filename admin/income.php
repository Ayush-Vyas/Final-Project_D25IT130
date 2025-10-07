<?php 
require 'header.php'; 
// Replace with your DB connection code if needed
$conn = new mysqli('localhost', 'root', '', 'ayura_hampers');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Default date range (last 30 days)
$start_date = date('Y-m-d', strtotime('-30 days'));
$end_date = date('Y-m-d');

// Override dates if form submitted
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

// Prepare and execute query for total income in range (only completed & successful orders)
$stmt = $conn->prepare("SELECT SUM(total_amount) as total_income, COUNT(*) as total_transactions 
                        FROM orders 
                        WHERE payment_method IS NOT NULL 
                        AND payment_method != '' 
                        AND DATE(created_at) BETWEEN ? AND ?");

$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$stmt->bind_result($total_income, $total_transactions);
$stmt->fetch();
$stmt->close();

// Fetch transaction details for the table
$stmt2 = $conn->prepare("SELECT id, user_id, email, payment_method, total_amount, created_at 
                         FROM orders 
                         WHERE payment_method IS NOT NULL
                         AND payment_method != ''
                         AND DATE(created_at) BETWEEN ? AND ?
                         ORDER BY created_at DESC
                         LIMIT 100");
$stmt2->bind_param("ss", $start_date, $end_date);
$stmt2->execute();
$result = $stmt2->get_result();
?>

<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #e6f0fa; /* light ocean blue */
    color: #1a2e45; /* dark navy blue */
    margin: 0;
    padding: 0 20px 50px 20px;
  }

  .income-container {
    max-width: 1000px;
    margin: 40px auto;
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(11, 61, 145, 0.15);
  }

  h1 {
    color: #0b3d91;
    text-align: center;
    margin-bottom: 30px;
    font-size: 2.4em;
    letter-spacing: 1px;
  }

  .summary-cards {
    display: flex;
    justify-content: space-around;
    margin-bottom: 30px;
  }

  .card {
    background-color: #0b3d91;
    color: white;
    padding: 25px 35px;
    border-radius: 10px;
    width: 45%;
    box-shadow: 0 6px 15px rgba(7, 48, 109, 0.5);
    font-size: 1.4em;
    font-weight: 600;
    text-align: center;
  }

  form.filter-form {
    margin-bottom: 25px;
    text-align: center;
  }

  form.filter-form input[type="date"] {
    padding: 8px 12px;
    margin: 0 10px;
    border-radius: 6px;
    border: 1.5px solid #0b3d91;
    font-size: 1em;
    color: #0b3d91;
  }

  form.filter-form button {
    background-color: #0b3d91;
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 7px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  form.filter-form button:hover {
    background-color: #07306d;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
  }

  table thead {
    background-color: #0b3d91;
    color: white;
  }

  table th, table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: center;
    font-size: 1em;
  }

  table tbody tr:nth-child(even) {
    background-color: #f7f9fc;
  }

  .no-data {
    text-align: center;
    padding: 20px;
    font-style: italic;
    color: #666;
  }
  .back-btn {
  display: inline-block;
  margin: 20px 0 0 20px;
  padding: 10px 20px;
  background-color: #0b3d91; /* deep ocean blue */
  color: white;
  font-weight: 600;
  border-radius: 8px;
  text-decoration: none;
  box-shadow: 0 4px 12px rgba(11, 61, 145, 0.5);
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
  user-select: none;
}

.back-btn:hover {
  background-color: #07306d; /* darker blue */
  box-shadow: 0 6px 18px rgba(7, 48, 109, 0.7);
}
</style>
<a href="index.php" class="back-btn">← Back to Admin Panel</a>
<div class="income-container">
  <h1>Income Overview</h1>

  <form class="filter-form" method="get" action="income.php">
    <label for="start_date">From:</label>
    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" max="<?= date('Y-m-d') ?>">
    <label for="end_date">To:</label>
    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" max="<?= date('Y-m-d') ?>">
    <button type="submit">Filter</button>
  </form>

  <div class="summary-cards">
    <div class="card">
      Total Income<br>
      ₹<?= number_format($total_income ?? 0, 2) ?>
    </div>
    <div class="card">
      Transactions<br>
      <?= number_format($total_transactions ?? 0) ?>
    </div>
  </div>

  <?php if ($result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Email</th>
            <th>Payment Method</th>
            <th>Amount (₹)</th>
            <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['user_id']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['payment_method']) ?></td>
                <td><?= number_format($row['total_amount'], 2) ?></td>
                <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
            </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="no-data">No transactions found for this date range.</div>
  <?php endif; ?>
</div>
<?php 
$stmt2->close();
$conn->close();
require 'footer.php'; 
?>
