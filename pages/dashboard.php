<?php
include '../config/koneksi.php';

$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$today = $date;
$yesterday = date('Y-m-d', strtotime($date . ' -1 day'));


// Revenue Hari Ini
$res = mysqli_query($conn, "SELECT SUM(total) as revenue FROM orders WHERE DATE(created_at) = '$today' AND status = 'paid'");
$revenue_today = ($row = mysqli_fetch_assoc($res)) ? (int)$row['revenue'] : 0;

// Total Orders
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = '$today' AND status = 'paid'");
$total_orders = ($row = mysqli_fetch_assoc($res)) ? (int)$row['total'] : 0;

// Customers Aktif
$res = mysqli_query($conn, "SELECT COUNT(DISTINCT reservation_id) as total FROM orders WHERE DATE(created_at) = '$today' AND status = 'paid'");
$active_customers = ($row = mysqli_fetch_assoc($res)) ? (int)$row['total'] : 0;

// Rata-rata Order
$avg_order_value = $total_orders > 0 ? $revenue_today / $total_orders : 0;

// Recent Orders
$recent_orders = [];
$res = mysqli_query($conn, "
  SELECT o.*, r.guest_name, 
         GROUP_CONCAT(DISTINCT rt.table_id ORDER BY rt.table_id SEPARATOR ', ') AS table_list
  FROM orders o
  JOIN reservations r ON o.reservation_id = r.id
  LEFT JOIN reservation_tables rt ON r.id = rt.reservation_id
  WHERE DATE(o.created_at) = '$today'
  GROUP BY o.id
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

// Revenue Kemarin
$res = mysqli_query($conn, "SELECT SUM(total) as revenue FROM orders WHERE DATE(created_at) = '$yesterday' AND status = 'paid'");
$revenue_yesterday = ($row = mysqli_fetch_assoc($res)) ? (int)$row['revenue'] : 0;

// Orders Kemarin
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = '$yesterday' AND status = 'paid'");
$total_orders_yesterday = ($row = mysqli_fetch_assoc($res)) ? (int)$row['total'] : 0;

// Customers Aktif Kemarin
$res = mysqli_query($conn, "SELECT COUNT(DISTINCT reservation_id) as total FROM orders WHERE DATE(created_at) = '$yesterday' AND status = 'paid'");
$active_customers_yesterday = ($row = mysqli_fetch_assoc($res)) ? (int)$row['total'] : 0;

// Rata-rata Order Kemarin
$avg_order_value_yesterday = $total_orders_yesterday > 0 ? $revenue_yesterday / $total_orders_yesterday : 0;


function compareChange($today, $yesterday) {
    if ($yesterday == 0 && $today > 0) return '<span class="change up">↑ 100%</span>';
    if ($yesterday == 0 && $today == 0) return '<span class="change neutral">0%</span>';
    
    $diff = $today - $yesterday;
    $percent = ($diff / $yesterday) * 100;
    $arrow = $percent >= 0 ? '↑' : '↓';
    $class = $percent >= 0 ? 'up' : 'down';

    return '<span class="change ' . $class . '">' . $arrow . ' ' . number_format(abs($percent), 1) . '%</span>';
}


// Top Items by Category
$top_items_by_category = [];
$categories = ['coffee', 'non-coffee', 'pastry', 'food'];
foreach ($categories as $cat) {
    $res = mysqli_query($conn, "
        SELECT m.category, oi.product_name, SUM(oi.quantity) as total
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        JOIN menu m ON oi.product_name = m.name
        WHERE DATE(o.created_at) = '$today' AND o.status = 'paid' AND m.category = '$cat'
        GROUP BY oi.product_name
        ORDER BY total DESC
        LIMIT 5
    ");
    $top_items_by_category[$cat] = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $top_items_by_category[$cat][] = $row;
    }
}

// Handle AJAX
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');

    $hourly_orders = [];
    $labels = [];
    for ($hour = 8; $hour <= 22; $hour++) {
        $start = sprintf('%02d:00:00', $hour);
        $end = sprintf('%02d:59:59', $hour);
        $res = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = '$date' AND TIME(created_at) BETWEEN '$start' AND '$end' AND status = 'paid'");
        $row = mysqli_fetch_assoc($res);
        $hourly_orders[] = (int)$row['total'];
        $labels[] = sprintf('%02d:00', $hour);
    }

    $weekly_revenue = [];
    for ($i = 0; $i < 7; $i++) {
        $day = date('Y-m-d', strtotime("last monday +{$i} days"));
        $res = mysqli_query($conn, "SELECT SUM(total) as revenue FROM orders WHERE DATE(created_at) = '$day' AND status = 'paid'");
        $row = mysqli_fetch_assoc($res);
        $weekly_revenue[] = (int)$row['revenue'];
    }

    echo json_encode([
        'revenue' => $revenue_today,
        'orders' => $total_orders,
        'customers' => $active_customers,
        'average' => $avg_order_value,
        'hourly_orders' => $hourly_orders,
        'weekly_revenue' => $weekly_revenue,
        'top_menus_by_category' => $top_items_by_category
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard Pengelola</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
  <div class="dashboard-title">
    <h2>Dashboard Pengelola</h2>
    <input type="date" id="filter-date" value="<?= $date ?>" />
  </div>

  <div class="cards_dash">
  <div class="card_dash">
    <div class="label">Revenue Hari Ini</div>
    <div class="amount" id="revenue">Rp <?= number_format($revenue_today, 0, ',', '.') ?></div>
    <div class="change"><?= compareChange($revenue_today, $revenue_yesterday) ?> dari kemarin</div>
  </div>
  <div class="card_dash">
    <div class="label">Total Orders</div>
    <div class="amount" id="orders"><?= $total_orders ?></div>
    <div class="change"><?= compareChange($total_orders, $total_orders_yesterday) ?> dari kemarin</div>
  </div>
  <div class="card_dash">
    <div class="label">Customers Aktif</div>
    <div class="amount" id="customers"><?= $active_customers ?></div>
    <div class="change"><?= compareChange($active_customers, $active_customers_yesterday) ?> dari kemarin</div>
  </div>
  <div class="card_dash">
    <div class="label">Rata-rata Order</div>
    <div class="amount" id="average">Rp <?= number_format($avg_order_value, 0, ',', '.') ?></div>
    <div class="change"><?= compareChange($avg_order_value, $avg_order_value_yesterday) ?> dari kemarin</div>
  </div>
</div>


  <div class="charts">
    <div class="chart-box"><h3>Orders per Jam</h3><canvas id="ordersChart"></canvas></div>
    <div class="chart-box"><h3>Revenue Mingguan</h3><canvas id="revenueChart"></canvas></div>
  </div>
  <div class="charts-bar">
    <div class="chart-bar-box"><h3>Top Menu: Coffee</h3><canvas id="topCoffeeChart"></canvas></div>
    <div class="chart-bar-box"><h3>Top Menu: Non-Coffee</h3><canvas id="topNonCoffeeChart"></canvas></div>
    <div class="chart-bar-box"><h3>Top Menu: Pastry</h3><canvas id="topPastryChart"></canvas></div>
    <div class="chart-bar-box"><h3>Top Menu: Food</h3><canvas id="topFoodChart"></canvas></div>
  </div>

      <div class="recent-orders">
      <h3>Recent Orders</h3>
      <table>
        <thead>
          <tr>
            <th>Order ID</th><th>Customer</th><th>Table</th><th>Item</th><th>Total</th><th>Status</th><th>Waktu</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent_orders as $order): ?>
          <tr>
            <td>#<?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['guest_name']) ?></td>
            <td><?= $order['table_list'] ? 'Table ' . htmlspecialchars($order['table_list']) : '<em>Take Away</em>' ?></td>
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
function loadDashboardData(date) {
  $.get('dashboard.php', { ajax: 1, date: date }, function(data) {
    $('#revenue').text('Rp ' + numberFormat(data.revenue));
    $('#orders').text(data.orders);
    $('#customers').text(data.customers);
    $('#average').text('Rp ' + numberFormat(data.average));

    updateChart(ordersChart, data.hourly_orders);
    updateChart(revenueChart, data.weekly_revenue);

    const categoryCharts = {
      'coffee': topCoffeeChart,
      'non-coffee': topNonCoffeeChart,
      'pastry': topPastryChart,
      'food': topFoodChart
    };

    for (const cat in categoryCharts) {
      const chart = categoryCharts[cat];
      const items = data.top_menus_by_category[cat] || [];
      chart.data.labels = items.map(item => item.product_name);
      chart.data.datasets[0].data = items.map(item => parseInt(item.total));
      chart.update();
    }
  }, 'json');
}

function numberFormat(num) {
  return num.toLocaleString('id-ID');
}

const ordersChart = new Chart(document.getElementById('ordersChart'), {
  type: 'line',
  data: {
    labels: ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00'],
    datasets: [{
      label: 'Orders',
      data: [],
      borderColor: '#6d4c41',
      fill: false,
      tension: 0.3
    }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: true,
        max: 50,
        ticks: { stepSize: 5 }
      }
    }
  }
});

const revenueChart = new Chart(document.getElementById('revenueChart'), {
  type: 'bar',
  data: {
    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
    datasets: [{ label: 'Revenue', data: [], backgroundColor: '#4caf50' }]
  }
});

const createPieChart = (id) => new Chart(document.getElementById(id), {
  type: 'pie',
  data: {
    labels: [],
    datasets: [{
      data: [],
      backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4caf50', '#9c27b0']
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'bottom' }
    }
  }
});

const topCoffeeChart = createPieChart('topCoffeeChart');
const topNonCoffeeChart = createPieChart('topNonCoffeeChart');
const topPastryChart = createPieChart('topPastryChart');
const topFoodChart = createPieChart('topFoodChart');

function updateChart(chart, data) {
  chart.data.datasets[0].data = data;
  chart.update();
}

$('#filter-date').on('change', function() {
  loadDashboardData(this.value);
});

$(document).ready(function() {
  loadDashboardData($('#filter-date').val());
});
</script>
</body>
</html>
