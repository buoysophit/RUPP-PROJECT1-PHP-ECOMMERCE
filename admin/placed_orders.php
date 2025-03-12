<?php
include '../components/connect.php';

session_start();
$current_page = 'orders'; // Add this line
$admin_id = $_SESSION['admin_id'];
if(!isset($admin_id)){
    header('location:admin_login.php');
}

if(isset($_POST['update_payment'])){
    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];
    $payment_status = filter_var($payment_status, FILTER_SANITIZE_STRING);
    $update_payment = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
    $update_payment->execute([$payment_status, $order_id]);
    $message[] = 'payment status updated!';
}

if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
    $delete_order->execute([$delete_id]);
    header('location:placed_orders.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include "include/head.php" ?>
<body id="page-top">
<div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
            <div class="sidebar-brand-text mx-3">Admin</div>
        </a>
        <hr class="sidebar-divider my-0">
        <?php include "include/nav.php" ?>
        <hr class="sidebar-divider">
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid">
                <section class="orders">
                    <h1 class="h3 mb-4 text-gray-800">Placed Orders</h1>

                    <div class="row">
                        <?php
                        $select_orders = $conn->prepare("SELECT * FROM `orders`");
                        $select_orders->execute();
                        if($select_orders->rowCount() > 0){
                            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
                                ?>
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                        Order Details
                                                    </div>
                                                    <div class="mb-0 font-weight-bold text-gray-800">
                                                        <p>Placed on: <span><?= $fetch_orders['placed_on']; ?></span></p>
                                                        <p>Name: <span><?= $fetch_orders['name']; ?></span></p>
                                                        <p>Number: <span><?= $fetch_orders['number']; ?></span></p>
                                                        <p>Address: <span><?= $fetch_orders['address']; ?></span></p>
                                                        <p>Total Products: <span><?= $fetch_orders['total_products']; ?></span></p>
                                                        <p>Total Price: <span>$<?= $fetch_orders['total_price']; ?>/-</span></p>
                                                        <p>Payment Method: <span><?= $fetch_orders['method']; ?></span></p>

                                                        <form action="" method="post">
                                                            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                                                            <select name="payment_status" class="form-control mb-2">
                                                                <option selected disabled><?= $fetch_orders['payment_status']; ?></option>
                                                                <option value="pending">pending</option>
                                                                <option value="completed">completed</option>
                                                            </select>
                                                            <div class="d-flex justify-content-between">
                                                                <input type="submit" value="Update" class="btn btn-primary btn-sm" name="update_payment">
                                                                <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>"
                                                                   class="btn btn-danger btn-sm"
                                                                   onclick="return confirm('delete this order?');">Delete</a>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }else{
                            echo '<div class="col-12"><div class="card"><div class="card-body"><p class="text-center mb-0">No orders placed yet!</p></div></div></div>';
                        }
                        ?>
                    </div>
                </section>
            </div>
        </div>
        <?php include "include/footer.php" ?>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="login.html">Logout</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>