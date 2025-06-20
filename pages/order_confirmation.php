<?php
session_start();

// WAJIB autoload dulu, dan path HARUS relatif terhadap lokasi file ini (di /pages/)
require_once __DIR__ . '/../vendor/autoload.php';

use Xendit\Xendit;
use Xendit\Invoice;


include '../config/koneksi.php';

Xendit::setApiKey('xnd_development_KNfmhtqrPrA68MrB068bbtPju6g9pI4Htpx4xkyOqGIYRSNIDfKxbMw9HohHReJ');


//echo "Xendit class loaded successfully!";


$cart = $_SESSION['cart'] ?? [];
$reservation_id = $_POST['reservation_id'] ?? $_GET['reservation_id'] ?? 0;

// Ambil detail reservasi
$res_sql = "SELECT * FROM reservations WHERE id = ?";
$stmt = $conn->prepare($res_sql);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$res_data = $stmt->get_result()->fetch_assoc();

if (!$res_data) {
    die("Reservasi tidak ditemukan.");
}

// Ambil order
$order_sql = "SELECT id, status, total FROM orders WHERE reservation_id = ? LIMIT 1";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$order_data = $stmt->get_result()->fetch_assoc();

$status = $order_data['status'] ?? 'pending';
$order_id = $order_data['id'] ?? 0;
$total = $order_data['total'] ?? 0;

// Ambil item
$order_items = [];
if ($order_id) {
    $item_sql = "SELECT product_name, price, quantity FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($item_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $item_result = $stmt->get_result();
    while ($row = $item_result->fetch_assoc()) {
        $order_items[] = $row;
    }
}

// Ambil meja
$tables = [];
$tbl_sql = "SELECT t.id, t.seats, t.zone FROM reservation_tables rt JOIN tables t ON rt.table_id = t.id WHERE rt.reservation_id = ?";
$stmt_tbl = $conn->prepare($tbl_sql);
$stmt_tbl->bind_param("i", $reservation_id);
$stmt_tbl->execute();
$result = $stmt_tbl->get_result();
while ($row = $result->fetch_assoc()) {
    $tables[] = $row;
}

// Buat invoice jika POST dan status masih pending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $status === 'pending') {
    $external_id = 'order-' . time(); // Buat ID unik

    $params = [
        'external_id' => $external_id,
        'payer_email' => $res_data['email'] ?? 'email@dummy.com',
        'description' => 'Pembayaran reservasi #' . $reservation_id,
        'amount' => $total,
        'success_redirect_url' => 'http://localhost/alley-jkt/payment_success.php'
    ];

    try {
        $invoice = Invoice::create($params);
        $invoice_id = $invoice['id'];

        // Simpan ke database
        $update = $conn->prepare("UPDATE orders SET external_id = ?, invoice_id = ? WHERE id = ?");
        $update->bind_param("ssi", $external_id, $invoice_id, $order_id);
        $update->execute();

        // Redirect ke halaman pembayaran
        header("Location: " . $invoice['invoice_url']);
        exit;
    } catch (Exception $e) {
        echo "Gagal membuat invoice: " . $e->getMessage();
        exit;
    }
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
      <?php foreach ($order_items as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['product_name']) ?></td>
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

  <?php if ($status === 'pending'): ?>
    <form method="post">
      <div class="paid-message" style="margin-top: 20px; color: red;">
        Reservasi ini belum dibayar, segera lanjutkan pembayaran!
      </div>
      <button type="submit" class="btn-pay">Bayar Sekarang via Xendit</button>
    </form>
  <?php else: ?>
    <div class="paid-message" style="margin-top: 20px; color: green;">
      Reservasi ini telah dibayar. Terima kasih!
    </div>
  <?php endif; ?>

</div>
</body>
</html>
