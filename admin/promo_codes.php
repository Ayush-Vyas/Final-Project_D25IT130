<?php
require 'header.php';
require_once __DIR__ . '/../db.php';

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);
$message = '';

// Handle form submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $promo_code = $conn->real_escape_string($_POST['promo_code']);
    $discount_amount = floatval($_POST['discount_amount']);
    $min_order_amount = floatval($_POST['min_order_amount']);
    $expiry_date = $conn->real_escape_string($_POST['expiry_date']);
    $active = isset($_POST['active']) ? 1 : 0;

    if (isset($_POST['id']) && intval($_POST['id']) > 0) {
        // Update
        $updateId = intval($_POST['id']);
        $conn->query("UPDATE promo_codes SET promo_code='$promo_code', discount_amount=$discount_amount, min_order_amount=$min_order_amount, expiry_date='$expiry_date', active=$active WHERE id=$updateId");
        $message = 'Promo code updated successfully.';
    } else {
        // Insert
        $conn->query("INSERT INTO promo_codes (promo_code, discount_amount, min_order_amount, expiry_date, active) VALUES ('$promo_code', $discount_amount, $min_order_amount, '$expiry_date', $active)");
        $message = 'Promo code added successfully.';
    }
}

// Handle delete action
if ($action === 'delete' && $id > 0) {
    $conn->query("DELETE FROM promo_codes WHERE id=$id");
    header('Location: promo_codes.php');
    exit;
}

// Fetch list
$result = $conn->query("SELECT * FROM promo_codes ORDER BY id DESC");

// Fetch data for editing
$editData = [];
if ($action === 'edit' && $id > 0) {
    $query = $conn->query("SELECT * FROM promo_codes WHERE id=$id");
    if ($query && $query->num_rows > 0) {
        $editData = $query->fetch_assoc();
    }
}
?>

<style>
  a.back-btn, a.add-btn {
    display: inline-block;
    margin-bottom: 20px;
    padding: 10px 18px;
    background-color: #0b3d91; /* ocean blue */
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s ease;
  }
  a.back-btn:hover, a.add-btn:hover {
    background-color: #07306d;
  }
  a.add-btn {
    background-color: #2e8b57; /* green */
  }
  a.add-btn:hover {
    background-color: #20603f;
  }

  h2 {
    color: #0b3d91;
    font-weight: 700;
    margin-bottom: 20px;
  }

  .message {
    padding: 12px 20px;
    background-color: #d4edda;
    color: #155724;
    border-radius: 8px;
    margin-bottom: 20px;
    max-width: 500px;
    font-weight: 600;
  }

  form {
    max-width: 500px;
    font-family: 'Segoe UI', sans-serif;
  }

  form label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #1a2e45;
  }

  form input[type="text"],
  form input[type="number"],
  form input[type="date"] {
    width: 100%;
    padding: 8px 12px;
    margin-bottom: 16px;
    border: 1px solid #a9bde9;
    border-radius: 8px;
    font-size: 1rem;
    color: #0b3d91;
    font-weight: 600;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
  }
  form input[type="text"]:focus,
  form input[type="number"]:focus,
  form input[type="date"]:focus {
    border-color: #07306d;
    outline: none;
  }

  .form-check {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
  }

  .form-check input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin-right: 10px;
    cursor: pointer;
  }

  .form-check label {
    margin: 0;
    font-weight: 600;
    color: #1a2e45;
    cursor: pointer;
  }

  button[type="submit"] {
    background-color: #0b3d91;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  button[type="submit"]:hover {
    background-color: #07306d;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    font-family: 'Segoe UI', sans-serif;
    color: #1a2e45;
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
    padding: 10px 10px;
    border: 1px solid #a9bde9;
    font-size: 0.9rem;
    vertical-align: middle;
  }

  tbody tr:nth-child(even) {
    background-color: #f3f7ff;
  }

  tbody tr:hover {
    background-color: #e0e7ff;
  }

  .btn-warning, .btn-danger {
    padding: 6px 12px;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    font-size: 0.85rem;
    text-decoration: none;
    display: inline-block;
    transition: background-color 0.3s ease;
  }

  .btn-warning {
    background-color: #e0a800;
  }
  .btn-warning:hover {
    background-color: #b38600;
  }

  .btn-danger {
    background-color: #c94c4c;
  }
  .btn-danger:hover {
    background-color: #992e2e;
  }
</style>

<a href="index.php" class="back-btn">← Back to Admin Panel</a>
<h2>Promo Codes</h2>

<?php if (!empty($message)): ?>
  <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
  <?php
    $editData = is_array($editData) ? $editData : [];
    $codeValue = htmlspecialchars($editData['promo_code'] ?? '', ENT_QUOTES);
    $discountValue = htmlspecialchars($editData['discount_amount'] ?? '', ENT_QUOTES);
    $minOrderValue = htmlspecialchars($editData['min_order_amount'] ?? '', ENT_QUOTES);
    $expiryDateValue = htmlspecialchars($editData['expiry_date'] ?? '', ENT_QUOTES);
    $activeChecked = !empty($editData['active']) ? 'checked' : '';
  ?>
  <a href="promo_codes.php" class="back-btn">← Back to List</a>
  <form method="POST" novalidate>
    <input type="hidden" name="id" value="<?= intval($editData['id'] ?? 0) ?>">

    <label for="promo_code">Promo Code</label>
    <input type="text" id="promo_code" name="promo_code" required value="<?= $codeValue ?>">

    <label for="discount_amount">Discount Amount</label>
    <input type="number" step="0.01" id="discount_amount" name="discount_amount" required value="<?= $discountValue ?>">

    <label for="min_order_amount">Minimum Order Amount</label>
    <input type="number" step="0.01" id="min_order_amount" name="min_order_amount" required value="<?= $minOrderValue ?>">

    <label for="expiry_date">Expiry Date</label>
    <input type="date" id="expiry_date" name="expiry_date" required value="<?= $expiryDateValue ?>">

    <div class="form-check">
      <input type="checkbox" id="active" name="active" <?= $activeChecked ?>>
      <label for="active">Active</label>
    </div>

    <button type="submit"><?= $action === 'edit' ? 'Update' : 'Add' ?> Promo Code</button>
  </form>

<?php else: ?>
  <a href="promo_codes.php?action=add" class="add-btn">+ Add New Promo Code</a>

  <table>
    <thead>
      <tr>
        <th style="width: 5%;">ID</th>
        <th style="width: 25%;">Promo Code</th>
        <th style="width: 10%;">Discount</th>
        <th style="width: 15%;">Min Order</th>
        <th style="width: 15%;">Expiry Date</th>
        <th style="width: 10%;">Active</th>
        <th style="width: 20%;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= intval($row['id']) ?></td>
          <td><?= htmlspecialchars($row['promo_code'], ENT_QUOTES) ?></td>
          <td>₹<?= number_format($row['discount_amount'], 2) ?></td>
          <td>₹<?= number_format($row['min_order_amount'], 2) ?></td>
          <td><?= htmlspecialchars($row['expiry_date'], ENT_QUOTES) ?></td>
          <td><?= $row['active'] ? 'Yes' : 'No' ?></td>
          <td>
            <a href="promo_codes.php?action=edit&id=<?= intval($row['id']) ?>" class="btn-warning">Edit</a>
            <a href="promo_codes.php?action=delete&id=<?= intval($row['id']) ?>"
               class="btn-danger" onclick="return confirm('Delete this promo code?')">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php require 'footer.php'; ?>
