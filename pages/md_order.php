<?php
include '../config/koneksi.php';

// Ambil semua data orders dengan join reservation untuk info meja dan nama customer
$orders = mysqli_query($conn, "
  SELECT o.*, r.guest_name, rt.table_id 
  FROM orders o
  LEFT JOIN reservations r ON o.reservation_id = r.id
  LEFT JOIN reservation_tables rt ON r.id = rt.reservation_id
  ORDER BY o.id DESC
");

// Ambil detail item untuk setiap order
$orderItems = [];
$res = mysqli_query($conn, "SELECT * FROM order_items");
while ($row = mysqli_fetch_assoc($res)) {
  $orderItems[$row['order_id']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Master Data Orders</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .detail-row {
      display: none;
      background-color: #f9f9f9;
    }
    .detail-row td {
      padding: 20px;
      border-top: none;
    }
    .btn-toggle {
      cursor: pointer;
      padding: 6px 10px;
      border: none;
      background-color: transparent;
      font-size: 16px;
    }
    .btn-toggle:hover {
      color: #6b4f2e;
    }
  </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
  <h2 class="page-title">📦 Master Data Management</h2>
  <div class="tab-menu">
    <button onclick="location.href='md_item.php'">Menu Items</button>
    <button onclick="location.href='md_table.php'">Tables</button>
    <button onclick="location.href='md_rspv.php'">Reservations</button>
    <button class="active">Orders</button>
    <button disabled>Access Rights</button>
  </div>

  <section class="data-section">
    <div class="section-header">
      <h3 class="section-title">Orders Management</h3>
      <button class="add-btn" onclick="alert('Add Order form belum dibuat')">+ Add Order</button>
    </div>

    <table class="menu-table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Table</th>
          <th>Customer</th>
          <th>Date & Time</th>
          <th>Items</th>
          <th>Total</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($order = mysqli_fetch_assoc($orders)): ?>
        <tr>
          <td>#<?= $order['id'] ?></td>
          <td>Table <?= $order['table_id'] ?? '-' ?></td>
          <td><?= htmlspecialchars($order['guest_name'] ?? '-') ?></td>
          <td>
            <?= substr($order['created_at'], 0, 10) ?><br>
            <?= substr($order['created_at'], 11, 5) ?>
          </td>
          <td><?= isset($orderItems[$order['id']]) ? count($orderItems[$order['id']]) : 0 ?> items</td>
          <td>Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
          <td>
            <span style="color: <?= $order['status'] === 'preparing' ? 'blue' : 'green' ?>;">
              <?= ucfirst($order['status']) ?>
            </span>
          </td>
          <td class="actions">
            <button class="btn-toggle" onclick="toggleDetail(<?= $order['id'] ?>)">👁️</button>
            <a href="?edit=<?= $order['id'] ?>" class="edit-btn">&#9998;</a>
            <a href="?delete=<?= $order['id'] ?>" class="delete-btn" onclick="return confirm('Delete this Order?')">&#128465;</a>
          </td>
        </tr>
        <tr class="detail-row" id="detail-<?= $order['id'] ?>">
          <td colspan="8">
            <strong>Order Details - #<?= $order['id'] ?></strong><br><br>
            <table class="menu-table">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Quantity</th>
                  <th>Price</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <?php if (isset($orderItems[$order['id']])): ?>
                  <?php foreach ($orderItems[$order['id']] as $item): ?>
                    <tr>
                      <td><?= htmlspecialchars($item['product_name']) ?></td>
                      <td><?= $item['quantity'] ?></td>
                      <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                      <td>Rp <?= number_format($item['total_price'], 0, ',', '.') ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="4">No items found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </section>
</div>

<script>
function toggleDetail(orderId) {
  const row = document.getElementById('detail-' + orderId);
  if (row.style.display === 'table-row') {
    row.style.display = 'none';
  } else {
    row.style.display = 'table-row';
  }
}
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
