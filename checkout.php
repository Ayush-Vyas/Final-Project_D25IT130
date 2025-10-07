<?php
session_start();

if (!isset($_SESSION['user_email'])) {
  echo "<script>alert('Please login first'); window.location='login.php';</script>";
  exit;
}

include 'config.php'; // database connection

$user_email = $_SESSION['user_email'];

// Fetch user info
$stmt = $conn->prepare("SELECT name, email FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Checkout - Ayura Hampers</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #e6f0fa;
      padding: 40px 20px;
      color: #1a2e45;
      max-width: 700px;
      margin: auto;
    }
    h1 {
      font-size: 2.2rem;
      margin-bottom: 25px;
      text-align: center;
      color: #0b3d91;
      letter-spacing: 1px;
    }
    form {
      background: #ffffff;
      padding: 35px 30px;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(11, 61, 145, 0.15);
      border: 1px solid #a3c0f9;
    }
    label {
      display: block;
      margin-top: 18px;
      font-weight: 700;
      color: #1a2e45;
      font-size: 1.05rem;
    }
    input[type="text"],
    input[type="email"],
    textarea,
    select {
      width: 100%;
      padding: 12px 14px;
      margin-top: 7px;
      border: 1.8px solid #a3c0f9;
      border-radius: 8px;
      box-sizing: border-box;
      font-size: 1rem;
      color: #1a2e45;
      transition: border-color 0.3s ease;
    }
    input:focus,
    textarea:focus,
    select:focus {
      border-color: #0b3d91;
      outline: none;
      background: #e1eaff;
    }
    button, #applyPromoBtn {
      margin-top: 25px;
      background-color: #0b3d91;
      color: white;
      border: none;
      padding: 14px 22px;
      font-size: 1.1rem;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-weight: 700;
      box-shadow: 0 4px 12px rgba(11, 61, 145, 0.5);
    }
    button:hover, #applyPromoBtn:hover {
      background-color: #07306d;
      box-shadow: 0 6px 18px rgba(7, 48, 109, 0.7);
    }
    #applyPromoBtn {
      margin-left: 12px;
      width: auto;
      padding: 12px 18px;
      font-weight: 800;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
      margin-bottom: 20px;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(11, 61, 145, 0.15);
    }
    th, td {
      padding: 14px 12px;
      text-align: center;
      border-bottom: 1px solid #a3c0f9;
      color: #1a2e45;
      font-weight: 600;
    }
    th {
      background-color: #d6e4ff;
      color: #0b3d91;
      font-size: 1.05rem;
    }
    tr:last-child td {
      border-bottom: none;
    }
    img {
      border-radius: 8px;
      box-shadow: 0 3px 8px rgba(11, 61, 145, 0.3);
    }
    .cart-title {
      font-size: 1.7rem;
      margin-bottom: 18px;
      font-weight: 700;
      color: #0b3d91;
      text-align: center;
    }
    .empty-msg {
      font-size: 1.3rem;
      color: #4a668e;
      text-align: center;
      margin-top: 40px;
      font-style: italic;
    }
    .totals {
      text-align: right;
      font-weight: 700;
      margin-top: 12px;
      color: #1a2e45;
      font-size: 1.1rem;
    }
    #promoMsg {
      margin-top: 14px;
      font-weight: 700;
      color: #1b5e20;
    }
    #promoError {
      margin-top: 14px;
      font-weight: 700;
      color: #b71c1c;
    }
  </style>
</head>
<body>

  <h1>Checkout</h1>

  <div id="cart-section">
    <div class="cart-title">Your Items</div>
    <div id="cart-details"></div>
  </div>

  <form id="checkoutForm">
    <label for="email">Email Address:</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" readonly />

    <label for="name">Full Name:</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" readonly />

    <label for="address">Delivery Address:</label>
    <textarea id="address" name="address" rows="4" required></textarea>

    <label for="payment">Payment Method:</label>
    <select id="payment" name="payment_method" required>
      <option value="">--Select--</option>
      <option value="COD">Cash on Delivery</option>
      <option value="UPI">UPI</option>
    </select>

    <label for="promoCode">Promo Code:</label>
    <div style="display:flex; align-items:center;">
      <input type="text" id="promoCode" placeholder="Enter promo code" />
      <button type="button" id="applyPromoBtn">Apply Code</button>
    </div>
    <div id="promoMsg"></div>
    <div id="promoError"></div>

    <button type="submit">Place Order</button>
  </form>

  <script>
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    let discount = 0;
    let appliedPromo = '';

    function calculateCartTotal() {
      return cart.reduce((sum, item) => sum + item.total, 0);
    }

    function renderCart() {
      if (cart.length === 0) {
        document.getElementById("cart-details").innerHTML = "<p class='empty-msg'>Your cart is empty.</p>";
        return;
      }

      let html = `<table>
        <tr>
          <th>Image</th>
          <th>Name</th>
          <th>Price</th>
          <th>Qty</th>
          <th>Total</th>
        </tr>`;

      cart.forEach(item => {
        html += `
          <tr>
            <td><img src="${item.image}" alt="${item.name}" width="60"></td>
            <td>${item.name}</td>
            <td>₹${item.price.toFixed(2)}</td>
            <td>${item.quantity}</td>
            <td>₹${item.total.toFixed(2)}</td>
          </tr>`;
      });

      html += `</table>`;

      const total = calculateCartTotal();
      const finalTotal = total - discount;

      html += `
        <p class="totals">Subtotal: ₹${total.toFixed(2)}</p>
        <p class="totals">Discount: ₹${discount.toFixed(2)}</p>
        <p class="totals">Total: ₹${finalTotal.toFixed(2)}</p>
      `;

      document.getElementById("cart-details").innerHTML = html;
    }

    renderCart();

    document.getElementById("applyPromoBtn").addEventListener("click", () => {
      const promoCode = document.getElementById("promoCode").value.trim();
      document.getElementById("promoMsg").textContent = '';
      document.getElementById("promoError").textContent = '';

      if (!promoCode) {
        alert("Please enter a promo code.");
        return;
      }

      fetch('validate_promo.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ promo_code: promoCode, cart: cart })
      })
      .then(res => res.json())
      .then(data => {
        if (data.valid) {
          discount = parseFloat(data.discount);
          appliedPromo = promoCode;
          document.getElementById("promoMsg").textContent = `Promo code applied! You saved ₹${discount.toFixed(2)}.`;
          renderCart();
        } else {
          discount = 0;
          appliedPromo = '';
          document.getElementById("promoError").textContent = data.message;
          renderCart();
        }
      })
      .catch(() => {
        discount = 0;
        appliedPromo = '';
        document.getElementById("promoError").textContent = "Error validating promo code.";
        renderCart();
      });
    });

    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const email = document.getElementById('email').value.trim();
      const name = document.getElementById('name').value.trim();
      const address = document.getElementById('address').value.trim();
      const payment = document.getElementById('payment').value;

      if (!cart.length) {
        alert("Your cart is empty!");
        return;
      }

      fetch('place_order.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: email,
    name: name,
    address: address,
    payment_method: payment,
    cart: cart,
    discount: discount,
    promo_code: appliedPromo
  })
})
.then(response => response.json())  // parse as JSON
.then(data => {
  if (data.success) {
    alert("Order placed successfully!");
    localStorage.removeItem("cart");
    window.location.href = "index.php";
  } else {
    alert("Error placing order: " + data.message);
  }
})
.catch(() => {
  alert("Network or server error while placing order.");
});

    });
  </script>
</body>
</html>
