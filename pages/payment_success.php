<?php
include '../config/koneksi.php';
require '../vendor/autoload.php';

// Log data callback ke file (untuk testing)
file_put_contents("xendit_callback.log", file_get_contents("php://input"), FILE_APPEND);

// Ambil data callback
$data = json_decode(file_get_contents('php://input'), true);

// Cek jika statusnya paid
if ($data && isset($data['status']) && $data['status'] === 'PAID') {
    $external_id = $data['external_id'];
    $amount = $data['amount'];
    $payment_time = $data['paid_at'];

    // Update status reservasi di database (sesuaikan tabel & field)
    $update = $conn->prepare("UPDATE reservations SET status = 'Paid', payment_time = ? WHERE id = ?");
    $update->bind_param("si", $payment_time, $external_id); // asumsi external_id = id reservasi
    $update->execute();

    http_response_code(200);
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'invalid']);
}
?>
