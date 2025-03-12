<?php
include '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];
$current_page = 'settings'; // Updated to reflect it's under Settings

if(!isset($admin_id)){
    header('location:admin_login.php');
}

if(isset($_GET['delete'])){
    $delete_id = $_GET['delete'];
    $delete_user = $conn->prepare("DELETE FROM `users` WHERE id = ?");
    $delete_user->execute([$delete_id]);
    $delete_orders = $conn->prepare("DELETE FROM `orders` WHERE user_id = ?");
    $delete_orders->execute([$delete_id]);
    $delete_messages = $conn->prepare("DELETE FROM `messages` WHERE user_id = ?");
    $delete_messages->execute([$delete_id]);
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
    $delete_cart->execute([$delete_id]);
    $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE user_id = ?");
    $delete_wishlist->execute([$delete_id]);
    header('location:users_accounts.php');
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
                <section class="accounts">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 text-gray-800">User Accounts</h1>
                        <a href="settings.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Settings
                        </a>
                    </div>

                    <div class="row">
                        <?php
                        $select_accounts = $conn->prepare("SELECT * FROM `users`");
                        $select_accounts->execute();
                        if($select_accounts->rowCount() > 0){
                            while($fetch_accounts = $select_accounts->fetch(PDO::FETCH_ASSOC)){
                                ?>
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                User Account
                                            </div>
                                            <div class="mb-0 font-weight-bold text-gray-800">
                                                <p>User ID: <span><?= $fetch_accounts['id']; ?></span></p>
                                                <p>Username: <span><?= $fetch_accounts['name']; ?></span></p>
                                                <p>Email: <span><?= $fetch_accounts['email']; ?></span></p>
                                                <div class="d-flex justify-content-end mt-3">
                                                    <a href="users_accounts.php?delete=<?= $fetch_accounts['id']; ?>"
                                                       onclick="return confirm('delete this account? the user related information will also be delete!')"
                                                       class="btn btn-danger btn-sm">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }else{
                            echo '<div class="col-12"><div class="card"><div class="card-body"><p class="text-center mb-0">No accounts available!</p></div></div></div>';
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