<?php
require_once '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

$current_page = 'settings'; // Set current page for nav.php highlighting

// Handle logo upload
if (isset($_POST['upload_logo'])) {
    $file = $_FILES['logo'];
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $max_size = 2 * 1024 * 1024; // 2MB
    $upload_dir = '../uploads/logos/';
    $new_name = uniqid() . '.' . $ext;
    $upload_path = $upload_dir . $new_name;

    // Check if directory exists, create it if not
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $message[] = "Failed to create upload directory!";
        }
    }

    // Check if directory is writable
    if (!is_writable($upload_dir)) {
        $message[] = "Upload directory is not writable!";
    } elseif ($file['error'] === UPLOAD_ERR_OK && in_array($ext, $allowed_ext) && $file['size'] <= $max_size) {
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Check if logo setting exists
            $check = $conn->prepare("SELECT * FROM `site_settings` WHERE `setting_key` = 'site_logo'");
            $check->execute();
            if ($check->rowCount() > 0) {
                $update = $conn->prepare("UPDATE `site_settings` SET `setting_value` = ? WHERE `setting_key` = 'site_logo'");
                $update->execute([$new_name]);
            } else {
                $insert = $conn->prepare("INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES ('site_logo', ?)");
                $insert->execute([$new_name]);
            }
            $message[] = "Logo uploaded successfully!";
        } else {
            $message[] = "Failed to move uploaded file! Check directory permissions.";
        }
    } else {
        $message[] = "Invalid file! Must be an image (jpg, png, gif) under 2MB.";
    }
}

// Fetch current logo
$select_logo = $conn->prepare("SELECT `setting_value` FROM `site_settings` WHERE `setting_key` = 'site_logo'");
$select_logo->execute();
$fetch_logo = $select_logo->fetch(PDO::FETCH_ASSOC);
$logo_path = $fetch_logo ? '../uploads/logos/' . $fetch_logo['setting_value'] : '../uploads/logos/default_logo.png';
?>

<!DOCTYPE html>
<html lang="en">
<?php include "include/head.php"; ?>
<head>
    <style>
        /* Sidebar and active styling from dashboard.php */
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
            border-left: 0.25rem solid #5a5c69 !important;
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
        <?php include "include/nav.php"; ?>
        <hr class="sidebar-divider">
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid">
                <section class="logo-settings">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Logo Settings</h1>
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

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Change Site Logo</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6>Current Logo:</h6>
                                <img src="<?= $logo_path; ?>" alt="Current Logo" style="max-height: 100px;">
                            </div>
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Upload New Logo</label>
                                    <input type="file" name="logo" class="form-control-file" accept="image/*" required>
                                </div>
                                <div class="flex-btn">
                                    <button type="submit" name="upload_logo" class="btn btn-primary">Upload Logo</button>
                                    <a href="settings.php" class="btn btn-secondary">Back</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php include "include/footer.php"; ?>
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