<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = isset($_POST['product_name']) ? trim($_POST['product_name']) : '';
    $price = isset($_POST['price']) ? (int)$_POST['price'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    // Validasi
    if (empty($product_name) || $price <= 0 || $quantity <= 0) {
        header("Location: menu.php?error=invalid_input");
        exit;
    }

    // Inisialisasi cart jika belum ada
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;

    // Update quantity jika produk sudah ada
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['name'] === $product_name) {
            $cart_item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    // Jika belum ada, tambahkan item baru
    if (!$found) {
        $_SESSION['cart'][] = [
            'name' => $product_name,
            'price' => $price,
            'quantity' => $quantity
        ];
    }

    // Redirect kembali ke menu (bisa bawa reservation_id jika ada)
    $redirect = 'menu.php';
    if (!empty($_POST['reservation_id'])) {
        $redirect .= '?reservation_id=' . intval($_POST['reservation_id']);
    }

    header("Location: $redirect");
    exit;
} else {
    echo "Invalid request method.";
    exit;
}
?>
