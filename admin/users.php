<?php
require 'header.php';
require_once __DIR__ . '/../db.php';

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $id");
    header('Location: users.php');
    exit;
}

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<style>
  a.back-btn {
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
  a.back-btn:hover {
    background-color: #07306d;
  }

  h2 {
    color: #0b3d91;
    margin-bottom: 20px;
    font-weight: 700;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 50px;
    font-family: 'Segoe UI', sans-serif;
    color: #1a2e45;
  }

  thead th {
    background-color: #d6e1f7; /* light blue */
    padding: 12px;
    border: 1px solid #a9bde9;
    text-align: left;
  }

  tbody td {
    padding: 12px;
    border: 1px solid #a9bde9;
  }

  tbody tr:nth-child(even) {
    background-color: #f3f7ff; /* very light blue */
  }

  tbody tr:hover {
    background-color: #e0e7ff;
  }

  .btn-danger {
    background-color: #c94c4c;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-block;
    transition: background-color 0.3s ease;
  }

  .btn-danger:hover {
    background-color: #992e2e;
  }
</style>

<a href="index.php" class="back-btn">← Back to Admin Panel</a>
<h2>Users</h2>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Role</th>
      <th>Registered At</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php while($user = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $user['id'] ?></td>
      <td><?= htmlspecialchars($user['name'] ?? '') ?></td>
      <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
      <td><?= htmlspecialchars(ucfirst($user['role'] ?? 'user')) ?></td>
      <td><?= htmlspecialchars($user['created_at'] ?? '') ?></td>
      <td>
        <a href="users.php?delete=<?= $user['id'] ?>" class="btn-danger" onclick="return confirm('Delete user?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php require 'footer.php'; ?>
