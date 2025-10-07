<?php require 'header.php'; ?>
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #e6f0fa; /* light ocean blue */
    color: #1a2e45; /* dark navy blue */
    margin: 0;
    padding: 0;
  }

  .admin-dashboard {
    max-width: 900px;
    margin: 50px auto;
    background-color: #ffffff;
    padding: 40px 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(11, 61, 145, 0.15);
    text-align: center;
  }

  .admin-dashboard h1 {
    color: #0b3d91; /* deep ocean blue */
    font-size: 2.2em;
    margin-bottom: 20px;
    letter-spacing: 1px;
  }

  .admin-dashboard p {
    font-size: 1.1em;
    margin-bottom: 30px;
    color: #4a668e; /* softer blue */
  }

  .admin-links {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
  }

  .admin-links a {
    text-decoration: none;
    padding: 15px 30px;
    border-radius: 8px;
    background-color: #0b3d91; /* deep ocean blue */
    color: white;
    font-weight: 600;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 12px rgba(11, 61, 145, 0.5);
  }

  .admin-links a:hover {
    background-color: #07306d; /* darker blue */
    box-shadow: 0 6px 18px rgba(7, 48, 109, 0.7);
  }
</style>

<div class="admin-dashboard">
  <h1>Welcome to Admin Panel</h1>
  <p>Use the navigation menu to manage Users, Orders, Promo Codes, and Feedback.</p>

  <div class="admin-links">
    <a href="users.php">Manage Users</a>
    <a href="orders.php">Manage Orders</a>
    <a href="promo_codes.php">Manage Promo Codes</a>
    <a href="feedback.php">View Feedback</a>
    <a href="income.php">View Income</a>
    <a href="contact_messages.php">View Contact Messages</a>
  </div>
</div>
  
<?php require 'footer.php'; ?>
