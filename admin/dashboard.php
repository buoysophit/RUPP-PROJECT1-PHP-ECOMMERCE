<?php
include '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}
$current_page = 'dashboard';
// Fetch admin profile data
$select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if (!$fetch_profile) {
    header('location:admin_login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include "include/head.php" ?>
<head>
    <style>
        /* Sidebar and active styling from previous requests */
        .bg-gradient-primary {
            background-color: #E53888 !important;
            background-image: linear-gradient(180deg, #E53888 10%, #C02D6F 100%) !important;
        }
        .sidebar-brand-text, .nav-link, .nav-link i {
            color: #fff !important;
        }
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
        .btn-primary {
            background-color: #E53888 !important;
            border-color: #E53888 !important;
        }
        .btn-primary:hover {
            background-color: #C02D6F !important;
            border-color: #C02D6F !important;
        }
        .border-left-primary {
            border-left: 0.25rem solid #E53888 !important;
        }
        .nav-item.active .nav-link {
            color: #000000 !important;
            background-color: rgba(255, 255, 255, 0.2) !important;
        }
        .nav-item.active .nav-link i {
            color: #000000 !important;
        }
        .border-left-dark {
            border-left: 0.25rem solid #5a5c69 !important; /* Matches existing dark border */
        }
    </style>
</head>
<body id="page-top">
<div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../admin/dashboard.php">
            <div class="sidebar-brand-text mx-3">Admin</div>
        </a>
        <hr class="sidebar-divider my-0">
        <?php include "include/nav.php" ?>
        <hr class="sidebar-divider">
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid">
                <section class="dashboard">
                    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

                    <div class="row">
                        <!-- Welcome Box -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <h3 class="font-weight-bold text-primary text-uppercase mb-1">Welcome!</h3>
                                    <p class="h5 mb-4"><?= $fetch_profile['name']; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Pendings -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <?php
                                    $total_pendings = 0;
                                    $select_pendings = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
                                    $select_pendings->execute(['pending']);
                                    if ($select_pendings->rowCount() > 0) {
                                        while ($fetch_pendings = $select_pendings->fetch(PDO::FETCH_ASSOC)) {
                                            $total_pendings += $fetch_pendings['total_price'];
                                        }
                                    }
                                    ?>
                                    <h3 class="font-weight-bold text-success mb-1">$<?= $total_pendings; ?></h3>
                                    <p class="mb-4">Total Pendings</p>
                                    <a href="placed_orders.php" class="btn btn-success">See Orders</a>
                                </div>
                            </div>
                        </div>

                        <!-- Completed Orders -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <?php
                                    $total_completes = 0;
                                    $select_completes = $conn->prepare("SELECT * FROM `orders` WHERE payment_status = ?");
                                    $select_completes->execute(['completed']);
                                    if ($select_completes->rowCount() > 0) {
                                        while ($fetch_completes = $select_completes->fetch(PDO::FETCH_ASSOC)) {
                                            $total_completes += $fetch_completes['total_price'];
                                        }
                                    }
                                    ?>
                                    <h3 class="font-weight-bold text-info mb-1">$<?= $total_completes; ?></h3>
                                    <p class="mb-4">Completed Orders</p>
                                    <a href="placed_orders.php" class="btn btn-info">See Orders</a>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Placed -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <?php
                                    $select_orders = $conn->prepare("SELECT * FROM `orders`");
                                    $select_orders->execute();
                                    $number_of_orders = $select_orders->rowCount();
                                    ?>
                                    <h3 class="font-weight-bold text-warning mb-1"><?= $number_of_orders; ?></h3>
                                    <p class="mb-4">Orders Placed</p>
                                    <a href="placed_orders.php" class="btn btn-warning">See Orders</a>
                                </div>
                            </div>
                        </div>

                        <!-- Products Added -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <?php
                                    $select_products = $conn->prepare("SELECT * FROM `products`");
                                    $select_products->execute();
                                    $number_of_products = $select_products->rowCount();
                                    ?>
                                    <h3 class="font-weight-bold text-primary mb-1"><?= $number_of_products; ?></h3>
                                    <p class="mb-4">Products Added</p>
                                    <a href="products.php" class="btn btn-primary">See Products</a>
                                </div>
                            </div>
                        </div>

                        <!-- Normal Users -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <?php
                                    $select_users = $conn->prepare("SELECT * FROM `users`");
                                    $select_users->execute();
                                    $number_of_users = $select_users->rowCount();
                                    ?>
                                    <h3 class="font-weight-bold text-success mb-1"><?= $number_of_users; ?></h3>
                                    <p class="mb-4">Normal Users</p>
                                    <a href="users_accounts.php" class="btn btn-success">See Users</a>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Users -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <?php
                                    $select_admins = $conn->prepare("SELECT * FROM `admins`");
                                    $select_admins->execute();
                                    $number_of_admins = $select_admins->rowCount();
                                    ?>
                                    <h3 class="font-weight-bold text-info mb-1"><?= $number_of_admins; ?></h3>
                                    <p class="mb-4">Admin Users</p>
                                    <a href="admin_accounts.php" class="btn btn-info">See Admins</a>
                                </div>
                            </div>
                        </div>

                        <!-- New Messages -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <?php
                                    $select_messages = $conn->prepare("SELECT * FROM `messages`");
                                    $select_messages->execute();
                                    $number_of_messages = $select_messages->rowCount();
                                    ?>
                                    <h3 class="font-weight-bold text-warning mb-1"><?= $number_of_messages; ?></h3>
                                    <p class="mb-4">New Messages</p>
                                    <a href="messagess.php" class="btn btn-warning">See Messages</a>
                                </div>
                            </div>
                        </div>

                        <!-- Slideshows Added -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-dark shadow h-100 py-2">
                                <div class="card-body">
                                    <?php
                                    $select_slideshows = $conn->prepare("SELECT * FROM `slideshows`");
                                    $select_slideshows->execute();
                                    $number_of_slideshows = $select_slideshows->rowCount();
                                    ?>
                                    <h3 class="font-weight-bold text-dark mb-1"><?= $number_of_slideshows; ?></h3>
                                    <p class="mb-4">Slideshows Added</p>
                                    <a href="slideshows.php" class="btn btn-dark">See Slideshows</a>
                                </div>
                            </div>
                        </div>

                        <!-- Categories Added -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <?php
                                    $select_categories = $conn->prepare("SELECT DISTINCT category FROM `products` WHERE category IS NOT NULL AND category != ''");
                                    $select_categories->execute();
                                    $number_of_categories = $select_categories->rowCount();
                                    ?>
                                    <h3 class="font-weight-bold text-primary mb-1"><?= $number_of_categories; ?></h3>
                                    <p class="mb-4">Categories Added</p>
                                    <a href="products.php" class="btn btn-primary">See Products</a>
                                </div>
                            </div>
                        </div>
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