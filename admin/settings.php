<?php
require_once '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}
$current_page = 'settings'; // Set the current page for nav.php

// Fetch admin profile data (optional, for consistency with dashboard)
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
                <section class="settings">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Settings</h1>
                        
                    </div>

                    <!-- Display Messages -->
                    <?php
                    if (isset($message)) {
                        foreach ($message as $msg) {
                            echo '<div class="alert alert-info alert-dismissible fade show" role="alert">' . $msg . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                        }
                    }
                    ?>

                    <div class="row">
                        <!-- General Settings Card -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        General Settings
                                    </div>
                                    <p class="mb-4">Configure site-wide settings.</p>
                                    <a href="#" class="btn btn-primary btn-sm">Edit Settings</a>
                                </div>
                            </div>
                        </div>

                        <!-- Theme Settings Card -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Theme Settings
                                    </div>
                                    <p class="mb-4">Customize the admin panel appearance.</p>
                                    <a href="#" class="btn btn-success btn-sm">Change Theme</a>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Accounts Card -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Admin Accounts
                                    </div>
                                    <p class="mb-4">Manage admin accounts.</p>
                                    <a href="admin_accounts.php" class="btn btn-warning btn-sm">Manage Admins</a>
                                </div>
                            </div>
                        </div>

                        <!-- User Accounts Card -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        User Accounts
                                    </div>
                                    <p class="mb-4">Manage user accounts.</p>
                                    <a href="users_accounts.php" class="btn btn-danger btn-sm">Manage Users</a>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Settings Card -->
<div class="col-xl-4 col-md-6 mb-4">
    <div class="card border-left-info shadow h-100 py-2">
        <div class="card-body">
            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Menu Settings</div>
            <p class="mb-4">Manage the menu items for the user header.</p>
            <a href="menu_settings.php" class="btn btn-info btn-sm">Manage Menu</a>
        </div>
    </div>
</div>

<!-- Logo Settings Card -->
<div class="col-xl-4 col-md-6 mb-4">
    <div class="card border-left-dark shadow h-100 py-2">
        <div class="card-body">
            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Logo Settings</div>
            <p class="mb-4">Change the site logo.</p>
            <a href="logo_settings.php" class="btn btn-dark btn-sm">Manage Logo</a>
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

<!-- Logout Modal -->
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
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>