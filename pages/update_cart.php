<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $index = isset($_POST['item_index']) ? intval($_POST['item_index']) : -1;
    $action = $_POST['action'] ?? '';

    if ($index >= 0 && isset($_SESSION['cart'][$index])) {
        switch ($action) {
            case 'increase':
                $_SESSION['cart'][$index]['quantity'] += 1;
                break;
            case 'decrease':
                $_SESSION['cart'][$index]['quantity'] -= 1;
                if ($_SESSION['cart'][$index]['quantity'] <= 0) {
                    array_splice($_SESSION['cart'], $index, 1);
                }
                break;
            case 'delete':
                array_splice($_SESSION['cart'], $index, 1);
                break;
        }
    }

    echo json_encode([
        'success' => true,
        'cart' => $_SESSION['cart'],
        'total_items' => array_sum(array_column($_SESSION['cart'], 'quantity'))
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
