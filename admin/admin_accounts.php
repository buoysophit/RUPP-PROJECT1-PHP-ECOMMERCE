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
    $delete_admins = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
    $delete_admins->execute([$delete_id]);
    header('location:admin_accounts.php');
}

// Fetch the current admin's profile to avoid undefined variable warning
$select_profile = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
$select_profile->execute([$admin_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if(!$fetch_profile){
    header('location:admin_login.php'); // Redirect if profile not found
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
                        <h1 class="h3 text-gray-800">Admin Accounts</h1>
                        <a href="settings.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to Settings
                        </a>
                    </div>

                    <div class="row">
                        <!-- Add New Admin Card -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        New Admin
                                    </div>
                                    <p class="mb-4">Add new admin</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerAdminModal">Register Admin</button>
                                </div>
                            </div>
                        </div>

                        <?php
                        $select_accounts = $conn->prepare("SELECT * FROM `admins`");
                        $select_accounts->execute();
                        if($select_accounts->rowCount() > 0){
                            while($fetch_accounts = $select_accounts->fetch(PDO::FETCH_ASSOC)){
                                ?>
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card border-left-info shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Admin Account
                                            </div>
                                            <div class="mb-0 font-weight-bold text-gray-800">
                                                <p>Admin ID: <span><?= $fetch_accounts['id']; ?></span></p>
                                                <p>Admin Name: <span><?= $fetch_accounts['name']; ?></span></p>
                                                <div class="d-flex justify-content-between mt-3">
                                                    <a href="admin_accounts.php?delete=<?= $fetch_accounts['id']; ?>"
                                                       onclick="return confirm('delete this account?')"
                                                       class="btn btn-danger btn-sm">Delete</a>
                                                    <?php
                                                    if($fetch_accounts['id'] == $admin_id){
                                                        echo '<button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateProfileModal">Update</button>';
                                                    }
                                                    ?>
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

<!-- Register Admin Modal -->
<div class="modal fade" id="registerAdminModal" tabindex="-1" aria-labelledby="registerAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div class="modal-header" style="background: #4e73df; color: white; border-radius: 8px 8px 0 0; padding: 15px;">
                <h5 class="modal-title" id="registerAdminModalLabel">Register New Admin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="" method="post">
                    <div class="form-group mb-3">
                        <input type="text"
                               name="name"
                               required
                               placeholder="Enter your username"
                               maxlength="20"
                               class="form-control"
                               oninput="this.value = this.value.replace(/\s/g, '')"
                               style="border: 1px solid #ccc; border-radius: 4px; padding: 10px;">
                    </div>

                    <div class="form-group mb-3">
                        <input type="password"
                               name="pass"
                               required
                               placeholder="Enter your password"
                               maxlength="20"
                               class="form-control"
                               oninput="this.value = this.value.replace(/\s/g, '')"
                               style="border: 1px solid #ccc; border-radius: 4px; padding: 10px;">
                    </div>

                    <div class="form-group mb-3">
                        <input type="password"
                               name="cpass"
                               required
                               placeholder="Confirm your password"
                               maxlength="20"
                               class="form-control"
                               oninput="this.value = this.value.replace(/\s/g, '')"
                               style="border: 1px solid #ccc; border-radius: 4px; padding: 10px;">
                    </div>

                    <input type="submit"
                           value="Register Now"
                           class="btn btn-primary w-100"
                           name="submit"
                           style="background: #4e73df; border: none; border-radius: 25px; padding: 10px; font-weight: bold;">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Update Profile Modal -->
<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <div class="modal-header" style="background: #4e73df; color: white; border-radius: 8px 8px 0 0; padding: 15px;">
                <h5 class="modal-title" id="updateProfileModalLabel">Update Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="" method="post">
                    <input type="hidden" name="prev_pass" value="<?= $fetch_profile['password']; ?>">

                    <div class="form-group mb-3">
                        <input type="text"
                               name="name"
                               value="<?= $fetch_profile['name']; ?>"
                               required
                               placeholder="Enter your username"
                               maxlength="20"
                               class="form-control"
                               oninput="this.value = this.value.replace(/\s/g, '')"
                               style="border: 1px solid #ccc; border-radius: 4px; padding: 10px;">
                    </div>

                    <div class="form-group mb-3">
                        <input type="password"
                               name="old_pass"
                               placeholder="Enter old password"
                               maxlength="20"
                               class="form-control"
                               oninput="this.value = this.value.replace(/\s/g, '')"
                               style="border: 1px solid #ccc; border-radius: 4px; padding: 10px;">
                    </div>

                    <div class="form-group mb-3">
                        <input type="password"
                               name="new_pass"
                               placeholder="Enter new password"
                               maxlength="20"
                               class="form-control"
                               oninput="this.value = this.value.replace(/\s/g, '')"
                               style="border: 1px solid #ccc; border-radius: 4px; padding: 10px;">
                    </div>

                    <div class="form-group mb-3">
                        <input type="password"
                               name="confirm_pass"
                               placeholder="Confirm new password"
                               maxlength="20"
                               class="form-control"
                               oninput="this.value = this.value.replace(/\s/g, '')"
                               style="border: 1px solid #ccc; border-radius: 4px; padding: 10px;">
                    </div>

                    <input type="submit"
                           value="Update Now"
                           class="btn btn-primary w-100"
                           name="submit"
                           style="background: #4e73df; border: none; border-radius: 25px; padding: 10px; font-weight: bold;">
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>