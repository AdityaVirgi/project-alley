<?php
session_start();
include '../config/koneksi.php';

$cart = $_SESSION['cart'] ?? [];
$reservation_id = $_POST['reservation_id'] ?? $_GET['reservation_id'] ?? 0;

if (empty($cart) || !$reservation_id) {
    header("Location: menu.php");
    exit;
}

// Ambil detail reservasi
$res_sql = "SELECT * FROM reservations WHERE id = ?";
$stmt = $conn->prepare($res_sql);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$res_data = $stmt->get_result()->fetch_assoc();

// Ambil meja terkait reservasi
$tables = [];
$tbl_sql = "
    SELECT t.id, t.seats, t.zone
    FROM reservation_tables rt
    JOIN tables t ON rt.table_id = t.id
    WHERE rt.reservation_id = ?";
$stmt_tbl = $conn->prepare($tbl_sql);
$stmt_tbl->bind_param("i", $reservation_id);
$stmt_tbl->execute();
$result = $stmt_tbl->get_result();
while ($row = $result->fetch_assoc()) {
    $tables[] = $row;
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
  <meta charset="UTF-8" />
  <title>Konfirmasi Pesanan</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="confirmation-container">
    <h2>Konfirmasi Pesanan</h2>
    <p><strong>ID Reservasi:</strong> <?= htmlspecialchars($reservation_id) ?></p>

    <div class="details-box">
      <h4>Informasi Pemesan</h4>
      <p><strong>Nama:</strong> <?= htmlspecialchars($res_data['guest_name']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($res_data['email']) ?></p>
      <p><strong>No. Telp:</strong> <?= htmlspecialchars($res_data['No_Telp']) ?></p>
      <p><strong>Tanggal:</strong> <?= htmlspecialchars($res_data['reservation_date']) ?></p>
      <p><strong>Waktu:</strong> <?= htmlspecialchars($res_data['checkin']) ?> - <?= htmlspecialchars($res_data['checkout']) ?></p>
    </div>

    <div class="details-box">
      <h4>Meja yang Dipesan</h4>
      <ul>
        <?php foreach ($tables as $t): ?>
          <li>Table #<?= htmlspecialchars($t['id']) ?> (<?= $t['seats'] ?> seats, Zone: <?= htmlspecialchars($t['zone']) ?>)</li>
        <?php endforeach; ?>
      </ul>
    </div>

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
      <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($reservation_id) ?>">
      <input type="hidden" name="total" value="<?= htmlspecialchars($total) ?>">
      <button type="submit" class="btn-pay">Lanjut ke Pembayaran</button>
    </form>
  </div>
</body>
</html>