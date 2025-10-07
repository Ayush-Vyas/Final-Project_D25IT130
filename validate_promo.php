<?php
// validate_promo.php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['promo_code'], $data['cart'])) {
  echo json_encode(['valid' => false, 'message' => 'Invalid input']);
  exit;
}

$promo_code = trim($data['promo_code']);
$cart = $data['cart'];

$conn = new mysqli('localhost', 'root', '', 'ayura_hampers');
if ($conn->connect_error) {
  echo json_encode(['valid' => false, 'message' => 'DB connection error']);
  exit;
}

// Calculate cart total
$total = 0;
foreach ($cart as $item) {
  $total += $item['total'];
}

// Use promo_codes table instead of offers
$sql = $conn->prepare("SELECT discount_amount, min_order_amount, active FROM promo_codes WHERE promo_code=? LIMIT 1");
$sql->bind_param('s', $promo_code);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows === 0) {
  echo json_encode(['valid' => false, 'message' => 'Promo code not found']);
  exit;
}

$row = $result->fetch_assoc();

if ((int)$row['active'] !== 1) {
  echo json_encode(['valid' => false, 'message' => 'Promo code is inactive']);
  exit;
}

$discount = (float)$row['discount_amount'];
$min_order = (float)$row['min_order_amount'];

if ($total < $min_order) {
  echo json_encode([
    'valid' => false,
    'message' => "Minimum order amount for this promo code is ₹$min_order"
  ]);
  exit;
}

echo json_encode([
  'valid' => true,
  'discount' => $discount
]);
