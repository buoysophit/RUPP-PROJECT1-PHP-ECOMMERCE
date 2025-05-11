<?php
session_start();
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    echo 'Payment canceled. Your order has not been processed.';
    // Optionally mark order as canceled in the database
} else {
    echo 'Invalid cancellation request.';
}
?>