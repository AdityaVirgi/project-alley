<?php
session_start();

// Ambil cart dan reservation_id
$cart = $_SESSION['cart'] ?? [];
$reservation_id = $_POST['reservation_id'] ?? $_GET['reservation_id'] ?? 0;

// Redirect ke menu jika cart kosong atau reservation_id tidak valid
if (empty($cart) || !$reservation_id) {
    header("Location: menu.php");
    exit;
}

// Hitung total
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Konfirmasi Pesanan</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="confirmation-container">
    <h2>Konfirmasi Pesanan</h2>
    <p><strong>ID Reservasi:</strong> <?= htmlspecialchars($reservation_id) ?></p>

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
        <?php foreach ($cart as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="total">
      Total Pembayaran: Rp <?= number_format($total, 0, ',', '.') ?>
    </div>

    <form action="midtrans_payment.php" method="post">
      <input type="hidden" name="reservation_id" value="<?= $reservation_id ?>">
      <input type="hidden" name="total" value="<?= $total ?>">
      <button type="submit" class="btn-pay">Lanjut ke Pembayaran</button>
    </form>
  </div>
</body>
</html>
