<?php
include '../config/koneksi.php';

$order_id = $_GET['order_id'] ?? 0;
if (!$order_id) {
    echo "Order ID tidak ditemukan.";
    exit;
}

// Ambil data order
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id = $order_id"));
if (!$order) {
    echo "Data pesanan tidak valid.";
    exit;
}

// Ambil item dalam order
$order_items = [];
$result = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = $order_id");
while ($row = mysqli_fetch_assoc($result)) {
    $order_items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Redirect ke Pembayaran</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="confirmation-container">
    <h2>Menyiapkan Pembayaran...</h2>
    <p><strong>Order ID:</strong> <?= $order_id ?></p>
    <p><strong>Reservation ID:</strong> <?= $order['reservation_id'] ?></p>

    <table class="confirmation-table">
      <thead>
        <tr>
          <th>Item</th>
          <th>Harga</th>
          <th>Qty</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($order_items as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['product_name']) ?></td>
          <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
          <td><?= $item['quantity'] ?></td>
          <td>Rp <?= number_format($item['total_price'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="total">
      Total Pembayaran: Rp <?= number_format($order['total'], 0, ',', '.') ?>
    </div>

    <p style="text-align:center; margin-top:30px;">
      <em>Anda akan diarahkan ke halaman pembayaran dalam beberapa detik...</em>
    </p>
  </div>

  <!-- Auto redirect simulasi -->
  <script>
    setTimeout(() => {
      window.location.href = "https://demo.midtrans.com"; // ganti dengan Snap URL nanti
    }, 3000);
  </script>
</body>
</html>
