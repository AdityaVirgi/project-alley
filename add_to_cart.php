<?php
session_start();
$_SESSION['cart'][] = $_POST['item_id']; // atau array lain sesuai struktur


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['product_name'];
    $price = (int)$_POST['price'];
    $quantity = (int)$_POST['quantity'];

    // 👉 Validasi jumlah quantity
    if ($quantity <= 0) {
        header("Location: menu.php");
        exit;
    }

    $item = [
        'name' => $product_name,
        'price' => $price,
        'quantity' => $quantity
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['name'] === $product_name) {
            $cart_item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = $item;
    }

    header("Location: menu.php");
    exit;
}
?>
