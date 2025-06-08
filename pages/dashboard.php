<?php
include '../config/koneksi.php';

$today = date('Y-m-d');

// Revenue Hari Ini
$res = mysqli_query($conn, "SELECT SUM(total) as revenue FROM orders WHERE DATE(created_at) = '$today'");
$revenue_today = ($row = mysqli_fetch_assoc($res)) ? $row['revenue'] : 0;

// Total Orders
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = '$today'");
$total_orders = ($row = mysqli_fetch_assoc($res)) ? $row['total'] : 0;

// Customers Aktif
$res = mysqli_query($conn, "SELECT COUNT(DISTINCT reservation_id) as total FROM orders WHERE DATE(created_at) = '$today'");
$active_customers = ($row = mysqli_fetch_assoc($res)) ? $row['total'] : 0;

// Rata-rata Order
$avg_order_value = $total_orders > 0 ? $revenue_today / $total_orders : 0;

// Recent Orders (gabung item per order)
$recent_orders = [];
$res = mysqli_query($conn, "
  SELECT o.*, r.guest_name, rt.table_id 
  FROM orders o
  JOIN reservations r ON o.reservation_id = r.id
  LEFT JOIN reservation_tables rt ON r.id = rt.reservation_id
  ORDER BY o.created_at DESC
  LIMIT 5
");

while ($row = mysqli_fetch_assoc($res)) {
    $order_id = $row['id'];
    $items_res = mysqli_query($conn, "SELECT product_name, quantity FROM order_items WHERE order_id = $order_id");

    $items = [];
    while ($item = mysqli_fetch_assoc($items_res)) {
        $items[] = $item['quantity'] . 'x ' . $item['product_name'];
    }

    $row['items'] = implode(', ', $items);
    $recent_orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard Pengelola</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
  <div class="dashboard-title">
    <h2>Dashboard Pengelola</h2>
  </div>

  <div class="cards_dash">
    <div class="card_dash">
      <h3>Revenue Hari Ini</h3>
      <div class="amount">Rp <?= number_format($revenue_today, 0, ',', '.') ?></div>
    </div>
    <div class="card_dash">
      <h3>Total Orders</h3>
      <div class="amount"><?= $total_orders ?></div>
    </div>
    <div class="card_dash">
      <h3>Customers Aktif</h3>
      <div class="amount"><?= $active_customers ?></div>
    </div>
    <div class="card_dash">
      <h3>Rata-rata Order</h3>
      <div class="amount">Rp <?= number_format($avg_order_value, 0, ',', '.') ?></div>
    </div>
  </div>

  <div class="charts">
    <div class="chart-box">
      <h3>Orders per Jam</h3>
      <canvas id="ordersChart"></canvas>
    </div>
    <div class="chart-box">
      <h3>Revenue Mingguan</h3>
      <canvas id="revenueChart"></canvas>
    </div>
  </div>

  <div class="recent-orders">
    <h3>Recent Orders</h3>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Table</th>
          <th>Item</th>
          <th>Total</th>
          <th>Status</th>
          <th>Waktu</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recent_orders as $order): ?>
        <tr>
          <td>#<?= $order['id'] ?></td>
          <td><?= htmlspecialchars($order['guest_name']) ?></td>
          <td>Table <?= htmlspecialchars($order['table_id']) ?></td>
          <td><?= htmlspecialchars($order['items']) ?></td>
          <td>Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
          <td><span class="badge <?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
          <td><?= date('H:i', strtotime($order['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
const ctx1 = document.getElementById('ordersChart').getContext('2d');
new Chart(ctx1, {
  type: 'line',
  data: {
    labels: ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00'],
    datasets: [{
      label: 'Orders',
      data: [15, 25, 20, 30, 45, 40, 35, 30, 25, 15],
      borderColor: '#6d4c41',
      backgroundColor: 'transparent',
      tension: 0.4
    }]
  },
  options: { responsive: true }
});

const ctx2 = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx2, {
  type: 'bar',
  data: {
    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
    datasets: [{
      label: 'Revenue',
      data: [1300000, 1450000, 1100000, 1450000, 1800000, 2100000, 1600000],
      backgroundColor: '#4caf50'
    }]
  },
  options: { responsive: true }
});
</script>
</body>
</html>
