<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard - Ayura Hampers</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- FontAwesome 6 CDN -->
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    rel="stylesheet"
  />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #e6f2ff, #f0f9ff);
      color: #1a3e59;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    header {
      background: #3a91bf;
      color: white;
      padding: 20px;
      text-align: center;
      font-size: 24px;
      font-weight: 700;
      letter-spacing: 1px;
      user-select: none;
      flex-shrink: 0;
    }

    nav {
      background: #d7ebff;
      padding: 15px;
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 12px;
      flex-shrink: 0;
    }

    nav a {
      margin: 0;
      text-decoration: none;
      color: #1a3e59;
      font-weight: 600;
      padding: 10px 22px;
      border-radius: 25px;
      background: #ffffffcc;
      box-shadow: 0 2px 5px rgba(58, 145, 191, 0.3);
      transition: background-color 0.3s ease, color 0.3s ease;
      user-select: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    nav a:hover,
    nav a:focus {
      background-color: #3a91bf;
      color: #e6f2ff;
      box-shadow: 0 4px 10px rgba(58, 145, 191, 0.6);
      outline: none;
    }

    nav a:focus {
      outline: 3px solid #2a557f;
      outline-offset: 3px;
    }

    .logout-btn {
      background: #3a91bf;
      color: white;
      border-radius: 30px;
      padding: 12px 28px;
      font-size: 14px;
      font-weight: 600;
      box-shadow: 0 3px 8px rgba(58, 145, 191, 0.4);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .logout-btn:hover,
    .logout-btn:focus {
      background: #2a557f;
      box-shadow: 0 5px 14px rgba(42, 85, 127, 0.7);
      outline: none;
    }

    main {
      max-width: 800px;
      margin: 30px auto 50px auto;
      background: #ffffffcc;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(58, 145, 191, 0.15);
      text-align: center;
      color: #1a3e59;
      user-select: none;
      animation: fadeIn 0.6s ease forwards;
      flex-grow: 1;
    }

    main h2 {
      margin-top: 0;
      margin-bottom: 24px;
      color: #2a557f;
      font-size: 28px;
    }

    main p {
      font-size: 16px;
      color: #3a5771;
      line-height: 1.5;
      margin-bottom: 10px;
    }

    main:hover {
      background-color: #ffffffee;
      transition: background-color 0.3s ease;
    }

    footer {
      text-align: center;
      padding: 20px;
      color: #4a668e;
      font-size: 14px;
      user-select: none;
      flex-shrink: 0;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 600px) {
      nav {
        gap: 8px;
      }
      nav a {
        padding: 8px 16px;
        font-size: 14px;
      }
      main {
        margin: 20px 10px 40px 10px;
        padding: 20px;
      }
    }

    @media (max-width: 400px) {
      nav {
        flex-direction: column;
        align-items: center;
      }
      nav a {
        width: 100%;
        text-align: center;
      }
    }
  </style>
</head>
<body>

<header>
  Welcome to Your Dashboard
</header>

<nav>
  <a href="index.php"><i class="fas fa-home"></i> Home</a>
  <a href="feedback.php"><i class="fas fa-comment-dots"></i> Give Feedback</a>
  <a href="track_orders.php"><i class="fas fa-box"></i> Track Orders</a>
  <a href="offers.php"><i class="fas fa-gift"></i> Today's Offers</a>
  <a href="checkout.php"><i class="fas fa-shopping-cart"></i> Checkout</a>
  <a href="logout.php" class="logout-btn" role="button" tabindex="0"><i class="fas fa-lock"></i> Logout</a>
</nav>

<main>
  <h2>Hello, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>!</h2>
  <p>Welcome back to Ayura Hampers. Ready to find your perfect gift today?</p>
  <p>Use the links above to manage your orders, leave feedback, check offers, or log out.</p>
</main>

<footer>
  &copy; <?= date('Y') ?> Ayura Hampers. All rights reserved.
</footer>

</body>
</html>