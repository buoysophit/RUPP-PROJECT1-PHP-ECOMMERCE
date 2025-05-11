<?php
require_once 'components/connect.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['pending_order'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid session']));
}

$order = $_SESSION['pending_order'];
$data = json_decode(file_get_contents('php://input'), true);

try {
    // Insert the order into database
    $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, payment_id) VALUES(?,?,?,?,?,?,?,?,?)");
    $insert_order->execute([
        $order['user_id'],
        $order['name'],
        $order['number'],
        $order['email'],
        $order['method'],
        $order['address'],
        $order['total_products'],
        $order['total_price'],
        $data['paymentID']
    ]);

    // Clear the cart
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
    $delete_cart->execute([$order['user_id']]);

    // Clear the pending order from session
    unset($_SESSION['pending_order']);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}