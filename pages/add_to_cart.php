
<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    if ($product_id <= 0 || $quantity <= 0) {
        header("Location: menu.php?error=invalid_input");
        exit;
    }

    $stmt = $conn->prepare("SELECT id, name, price, image FROM menu WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: menu.php?error=product_not_found");
        exit;
    }

    $product = $result->fetch_assoc();

    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['id'] === $product['id']) {
            $cart_item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity
        ];
    }

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
