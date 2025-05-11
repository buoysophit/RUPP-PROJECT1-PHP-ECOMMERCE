<?php
require_once 'components/connect.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['pending_order'])) {
    header('location: checkout.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Add this line to define user_id
$order = $_SESSION['pending_order'];

// PayPal API Configuration
define('PAYPAL_CLIENT_ID', 'ASLFFSFh34vGyo35tRJjx3kTHgDLubThB9vemtZT9lJ5HDYR4sZSW4aNoC05e0JWqNHEgn0mXcwKV-Vl');
define('PAYPAL_CLIENT_SECRET', 'EBZg8AG4RDNoIpZxhaE80FkpnAbEs9y5QbqivALg3b8Z5Clb5SkR-iSgL_w9dZnbXpzLqLjFoAZ-nF7a');
define('PAYPAL_CURRENCY', 'USD');
define('PAYPAL_MODE', 'sandbox'); // Change to 'live' for production

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Payment</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID ?>&currency=<?= PAYPAL_CURRENCY ?>"></script>
</head>
<body>
<?php include 'components/user_header.php'; ?>

<section class="paypal-payment">
    <h1 class="heading">PayPal Payment</h1>
    <div class="payment-container">
        <div class="order-summary">
            <h3>Order Summary</h3>
            <p>Total Amount: $<?= $order['total_price'] ?></p>
        </div>
        <div id="paypal-button-container"></div>
    </div>
</section>

<script>
paypal.Buttons({
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: '<?= $order['total_price'] ?>'
                }
            }]
        });
    },
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            // Send payment details to server
            fetch('complete_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    orderID: data.orderID,
                    paymentID: details.id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'orders.php?payment=success';
                }
            });
        });
    }
}).render('#paypal-button-container');
</script>

<?php include 'components/footer.php'; ?>
</body>
</html>