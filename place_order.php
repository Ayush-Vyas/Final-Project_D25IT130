<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'ayura_hampers');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB connection failed: ' . $conn->connect_error]);
    exit;
}

// If user_id not in session, but email is, fetch user_id and store in session
if (!isset($_SESSION['user_id'])) {
    if (isset($_SESSION['user_email'])) {
        $user_email = $_SESSION['user_email'];
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $stmt->bind_result($user_id);
        if ($stmt->fetch()) {
            $_SESSION['user_id'] = $user_id;  // store for later use
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
}

// Now you can use $_SESSION['user_id'] safely
$user_id = intval($_SESSION['user_id']);

// Read JSON input
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input: ' . json_last_error_msg()]);
    exit;
}

if (
    !$data || 
    empty($data['cart']) || 
    empty($data['email']) || 
    empty($data['name']) || 
    empty($data['address']) || 
    empty($data['payment_method'])
) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$email = $conn->real_escape_string($data['email']);
$name = $conn->real_escape_string($data['name']);
$address = $conn->real_escape_string($data['address']);
$payment_method = $conn->real_escape_string($data['payment_method']);
$cart = $data['cart'];
$promo_code = isset($data['promo_code']) ? $conn->real_escape_string($data['promo_code']) : '';
$discount = isset($data['discount']) ? floatval($data['discount']) : 0;

// Calculate total amount from cart
$total_amount = 0;
foreach ($cart as $item) {
    $total_amount += floatval($item['total']);
}
$final_amount = max($total_amount - $discount, 0);

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO orders (user_id, email, name, address, payment_method, promo_code, discount_amount, total_amount, order_date, created_at, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'Confirmed')");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("isssssdd", $user_id, $email, $name, $address, $payment_method, $promo_code, $discount, $final_amount);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $order_id = $stmt->insert_id;
    $stmt->close();

    $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, total) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt_items) {
        throw new Exception("Prepare failed (items): " . $conn->error);
    }
    foreach ($cart as $item) {
        $product_id = intval($item['id'] ?? 0);
        $product_name = $conn->real_escape_string($item['name']);
        $price = floatval($item['price']);
        $quantity = intval($item['quantity']);
        $total = floatval($item['total']);
        $stmt_items->bind_param("iisdid", $order_id, $product_id, $product_name, $price, $quantity, $total);
        if (!$stmt_items->execute()) {
            throw new Exception("Execute failed (item): " . $stmt_items->error);
        }
    }
    $stmt_items->close();

    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Order placed successfully', 'order_id' => $order_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Order failed: ' . $e->getMessage()]);
}

$conn->close();
?>
