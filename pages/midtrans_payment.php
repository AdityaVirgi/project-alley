<?php
session_start();
include '../config/koneksi.php';

$cart = $_SESSION['cart'] ?? [];
$reservation_id = $_POST['reservation_id'] ?? 0;
$total = $_POST['total'] ?? 0;

if (empty($cart) || !$reservation_id || !$total) {
    echo "Data tidak valid.";
    exit;
}

// 1. Simpan ke table orders
$stmt = $conn->prepare("INSERT INTO orders (reservation_id, total) VALUES (?, ?)");
$stmt->bind_param("ii", $reservation_id, $total);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// 2. Simpan ke table order_items
$item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price, total_price) VALUES (?, ?, ?, ?, ?)");
foreach ($cart as $item) {
    $name = $item['name'];
    $qty = $item['quantity'];
    $price = $item['price'];
    $total_item = $qty * $price;
    $item_stmt->bind_param("isiii", $order_id, $name, $qty, $price, $total_item);
    $item_stmt->execute();
}
$item_stmt->close();

// 3. Kosongkan cart
unset($_SESSION['cart']);

// 4. Redirect (sementara) ke halaman sukses
header("Location: payment_gateway_redirect.php?order_id=" . $order_id);
exit;
