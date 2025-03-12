<?php
require_once '../components/connect.php';
session_start();
$current_page = 'slideshows';
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}


// Handle slideshow actions
if (isset($_POST['add_slideshow'])) {
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $subtitle = filter_var($_POST['subtitle'], FILTER_SANITIZE_STRING);
    $text = filter_var($_POST['text'], FILTER_SANITIZE_STRING);
    $ssorder = filter_var($_POST['ssorder'], FILTER_SANITIZE_NUMBER_INT);
    $enable = filter_var($_POST['enable'], FILTER_SANITIZE_STRING);
    $link = filter_var($_POST['link'], FILTER_SANITIZE_STRING);

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    if ($image_size > 2000000) {
        $message[] = 'Image size is too large!';
    } else {
        if (move_uploaded_file($image_tmp_name, $image_folder)) {
            $insert_slideshow = $conn->prepare("INSERT INTO `slideshows` (title, subtitle, text, img, ssorder, enable, link) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert_slideshow->execute([$title, $subtitle, $text, $image, $ssorder, $enable, $link]);
            $message[] = 'Slideshow added successfully!';
        } else {
            $message[] = 'Failed to upload image!';
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $select_slideshow = $conn->prepare("SELECT img FROM `slideshows` WHERE ssid = ?");
    $select_slideshow->execute([$delete_id]);
    $fetch_slideshow = $select_slideshow->fetch(PDO::FETCH_ASSOC);
    if ($fetch_slideshow) {
        unlink('../uploaded_img/' . $fetch_slideshow['img']);
    }
    $delete_slideshow = $conn->prepare("DELETE FROM `slideshows` WHERE ssid = ?");
    $delete_slideshow->execute([$delete_id]);
    header('location:slideshows.php');
}

if (isset($_POST['update_slideshow'])) {
    $ssid = $_POST['ssid'];
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $subtitle = filter_var($_POST['subtitle'], FILTER_SANITIZE_STRING);
    $text = filter_var($_POST['text'], FILTER_SANITIZE_STRING);
    $ssorder = filter_var($_POST['ssorder'], FILTER_SANITIZE_NUMBER_INT);
    $enable = filter_var($_POST['enable'], FILTER_SANITIZE_STRING);
    $link = filter_var($_POST['link'], FILTER_SANITIZE_STRING);

    $update_slideshow = $conn->prepare("UPDATE `slideshows` SET title = ?, subtitle = ?, text = ?, ssorder = ?, enable = ?, link = ? WHERE ssid = ?");
    $update_slideshow->execute([$title, $subtitle, $text, $ssorder, $enable, $link, $ssid]);

    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    if (!empty($image)) {
        if ($image_size > 2000000) {
            $message[] = 'Image size is too large!';
        } else {
            move_uploaded_file($image_tmp_name, $image_folder);
            $update_image = $conn->prepare("UPDATE `slideshows` SET img = ? WHERE ssid = ?");
            $update_image->execute([$image, $ssid]);
            unlink('../uploaded_img/' . $old_image);
            $message[] = 'Slideshow updated with new image!';
        }
    } else {
        $message[] = 'Slideshow updated successfully!';
    }
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
                <section class="slideshows">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Slideshows</h1>
                        <button class="btn btn-primary btn-icon-split" data-bs-toggle="modal" data-bs-target="#addSlideshowModal">
                            <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                            <span class="text">Add New Slideshow</span>
                        </button>
                    </div>

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
                        <?php
                        $select_slideshows = $conn->prepare("SELECT * FROM `slideshows` ORDER BY ssorder ASC");
                        $select_slideshows->execute();
                        if ($select_slideshows->rowCount() > 0) {
                            while ($fetch_slideshow = $select_slideshows->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Slideshow
                                    </div>
                                    <div class="mb-0 font-weight-bold text-gray-800">
                                        <p>ID: <span><?= $fetch_slideshow['ssid']; ?></span></p>
                                        <p>Title: <span><?= $fetch_slideshow['title']; ?></span></p>
                                        <p>Subtitle: <span><?= $fetch_slideshow['subtitle']; ?></span></p>
                                        <p>Order: <span><?= $fetch_slideshow['ssorder']; ?></span></p>
                                        <p>Enable: <span><?= $fetch_slideshow['enable'] == '1' ? 'Yes' : 'No'; ?></span></p>
                                        <img src="../uploaded_img/<?= $fetch_slideshow['img']; ?>" alt="<?= $fetch_slideshow['title']; ?>" class="img-fluid mb-2" style="max-height: 100px;">
                                        <div class="d-flex justify-content-between mt-3">
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateSlideshowModal<?= $fetch_slideshow['ssid']; ?>">Update</button>
                                            <a href="slideshows.php?delete=<?= $fetch_slideshow['ssid']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this slideshow?');">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Update Slideshow Modal -->
                        <div class="modal fade" id="updateSlideshowModal<?= $fetch_slideshow['ssid']; ?>" tabindex="-1" aria-labelledby="updateSlideshowModalLabel<?= $fetch_slideshow['ssid']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateSlideshowModalLabel<?= $fetch_slideshow['ssid']; ?>">Update Slideshow</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="ssid" value="<?= $fetch_slideshow['ssid']; ?>">
                                            <input type="hidden" name="old_image" value="<?= $fetch_slideshow['img']; ?>">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Title</label>
                                                    <input type="text" class="form-control" name="title" required maxlength="500" value="<?= $fetch_slideshow['title']; ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Subtitle</label>
                                                    <input type="text" class="form-control" name="subtitle" required maxlength="500" value="<?= $fetch_slideshow['subtitle']; ?>">
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label class="form-label">Text</label>
                                                    <textarea class="form-control" name="text" required rows="3"><?= $fetch_slideshow['text']; ?></textarea>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Current Image</label>
                                                    <img src="../uploaded_img/<?= $fetch_slideshow['img']; ?>" alt="<?= $fetch_slideshow['title']; ?>" class="img-fluid" style="max-height: 100px;">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">New Image (optional)</label>
                                                    <input type="file" class="form-control" name="image" accept="image/*">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Order</label>
                                                    <input type="number" class="form-control" name="ssorder" required min="1" value="<?= $fetch_slideshow['ssorder']; ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Enable</label>
                                                    <select class="form-control" name="enable" required>
                                                        <option value="1" <?= $fetch_slideshow['enable'] == '1' ? 'selected' : ''; ?>>Yes</option>
                                                        <option value="0" <?= $fetch_slideshow['enable'] == '0' ? 'selected' : ''; ?>>No</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Link</label>
                                                    <input type="text" class="form-control" name="link" required maxlength="500" value="<?= $fetch_slideshow['link']; ?>">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-icon-split" name="update_slideshow">
                                                <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                                                <span class="text">Update Slideshow</span>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<div class="col-12"><p class="text-muted text-center">No slideshows available!</p></div>';
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

<!-- Add Slideshow Modal -->
<div class="modal fade" id="addSlideshowModal" tabindex="-1" aria-labelledby="addSlideshowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSlideshowModalLabel">Add New Slideshow</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required maxlength="500" placeholder="Enter slideshow title">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="subtitle" required maxlength="500" placeholder="Enter slideshow subtitle">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Text</label>
                            <textarea class="form-control" name="text" required placeholder="Enter slideshow text" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Order</label>
                            <input type="number" class="form-control" name="ssorder" required min="1" placeholder="Enter display order">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Enable</label>
                            <select class="form-control" name="enable" required>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Link</label>
                            <input type="text" class="form-control" name="link" required maxlength="500" placeholder="Enter slideshow link">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-icon-split" name="add_slideshow">
                        <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                        <span class="text">Add Slideshow</span>
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

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