<?php
require 'header.php';
require_once __DIR__ . '/../db.php';

// Update order status
if (isset($_POST['update_status'], $_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE orders SET status='$status' WHERE id=$order_id");
    header('Location: orders.php');
    exit;
}

// Delete order + order items
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM orders WHERE id = $id");
    $conn->query("DELETE FROM order_items WHERE order_id = $id");
    header('Location: orders.php');
    exit;
}

$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>
<a href="index.php" class="btn btn-secondary mb-3">← Back to Admin Panel</a>
<h2>Orders</h2>

<table class="table table-bordered table-striped table-responsive">
  <thead>
    <tr>
      <th>Order ID</th>
      <th>User ID</th>
      <th>Email</th>
      <th>Name</th>
      <th>Address</th>
      <th>Payment</th>
      <th>Promo Code</th>
      <th>Discount</th>
      <th>Total</th>
      <th>Status</th>
      <th>Items</th>
      <th>Order Date</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php while($order = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $order['id'] ?></td>
      <td><?= $order['user_id'] ?></td>
      <td><?= htmlspecialchars($order['email']) ?></td>
      <td><?= htmlspecialchars($order['name']) ?></td>
      <td><?= nl2br(htmlspecialchars($order['address'])) ?></td>
      <td><?= htmlspecialchars($order['payment_method']) ?></td>
      <td><?= htmlspecialchars($order['promo_code']) ?></td>
      <td>₹<?= number_format($order['discount_amount'], 2) ?></td>
      <td>₹<?= number_format($order['total_amount'], 2) ?></td>
      <td>
        <form method="POST" style="min-width:120px;">
          <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
          <input type="hidden" name="update_status" value="1">
          <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <?php 
              $statuses = ['Confirmed', 'Dispatched', 'Shipped', 'Out for Delivery', 'Delivered', 'Cancelled'];
              foreach ($statuses as $status): 
            ?>
              <option value="<?= $status ?>" <?= ($order['status'] === $status) ? 'selected' : '' ?>>
                <?= $status ?>
              </option>
            <?php endforeach; ?>
          </select>
        </form>
      </td>
      <td>
        <?php
        $items = $conn->query("SELECT product_name, quantity, total FROM order_items WHERE order_id=" . $order['id']);
        while ($item = $items->fetch_assoc()) {
            echo htmlspecialchars($item['product_name']) . " ({$item['quantity']}) - ₹" . number_format($item['total'], 2) . "<br>";
        }
        ?>
      </td>
      <td><?= htmlspecialchars($order['order_date'] ?? $order['created_at']) ?></td>
      <td>
        <a href="orders.php?delete=<?= $order['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this order?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php require 'footer.php'; ?>
