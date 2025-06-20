<?php
session_start();
include '../config/koneksi.php';

$cart = $_SESSION['cart'] ?? [];
$reservation_id = $_GET['reservation_id'] ?? 0;
$reservation_id = (int)$reservation_id;

if ($reservation_id <= 0 || empty($cart)) {
    echo "Invalid reservation or cart is empty.";
    exit;
}

// Hitung total
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Simpan ke tabel orders
$stmt = $conn->prepare("INSERT INTO orders (reservation_id, total, status, created_at) VALUES (?, ?, 'pending', NOW())");
$stmt->bind_param("ii", $reservation_id, $total);
if (!$stmt->execute()) {
    echo "Gagal menyimpan order: " . $stmt->error;
    exit;
}
$order_id = $stmt->insert_id;
$stmt->close();

// Simpan item ke order_items (tanpa product_id)
$sql_item = "INSERT INTO order_items (order_id, product_name, quantity, price, total_price) VALUES (?, ?, ?, ?, ?)";
$item_stmt = $conn->prepare($sql_item);
if (!$item_stmt) {
    die("Gagal prepare statement untuk order_items: " . $conn->error);
}

foreach ($cart as $item) {
    $product_name = $item['name'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    $total_price = $price * $quantity;
    $item_stmt->bind_param("isiii", $order_id, $product_name, $quantity, $price, $total_price);
    $item_stmt->execute();
}
$item_stmt->close();

// Kosongkan cart
unset($_SESSION['cart']);

// Redirect ke order confirmation
header("Location: order_confirmation.php?reservation_id=" . $reservation_id);
exit;
?>
