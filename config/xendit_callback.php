<?php
// Untuk menerima JSON dari Xendit
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Logging untuk debugging
file_put_contents('callback_log.txt', date('Y-m-d H:i:s') . " - " . print_r($data, true) . "\n", FILE_APPEND);

// Ambil invoice ID dan status
$invoice_id = $data['id'] ?? null;
$status = strtoupper($data['status'] ?? '');

// Update ke database jika invoice ID ditemukan dan status PAID
if ($invoice_id && $status === 'PAID') {
    include '../config/koneksi.php';

    // Update status di tabel orders berdasarkan invoice_id
    $stmt = $conn->prepare("UPDATE orders SET status = 'paid' WHERE invoice_id = ?");
    $stmt->bind_param("s", $invoice_id);
    $stmt->execute();
    $stmt->close();
}

http_response_code(200); // Kirim respon sukses ke Xendit
?>
