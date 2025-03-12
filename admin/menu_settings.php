<?php
require_once '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}
$current_page = 'settings'; // Set current page for navigation highlighting

// Handle adding a new menu item
if (isset($_POST['add_menu_item'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $link = filter_var($_POST['link'], FILTER_SANITIZE_STRING);
    $order = filter_var($_POST['order'], FILTER_SANITIZE_NUMBER_INT);
    $enable = isset($_POST['enable']) ? '1' : '0';

    if (!empty($name) && !empty($link) && !empty($order)) {
        $insert = $conn->prepare("INSERT INTO `menu_items` (`name`, `link`, `order`, `enable`) VALUES (?, ?, ?, ?)");
        $insert->execute([$name, $link, $order, $enable]);
        $message = "Menu item added successfully!";
    } else {
        $message = "All fields are required!";
    }
}

// Handle updating an existing menu item
if (isset($_POST['update_menu_item'])) {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $link = filter_var($_POST['link'], FILTER_SANITIZE_STRING);
    $order = filter_var($_POST['order'], FILTER_SANITIZE_NUMBER_INT);
    $enable = isset($_POST['enable']) ? '1' : '0';

    if (!empty($name) && !empty($link) && !empty($order)) {
        $update = $conn->prepare("UPDATE `menu_items` SET `name` = ?, `link` = ?, `order` = ?, `enable` = ? WHERE `id` = ?");
        $update->execute([$name, $link, $order, $enable, $id]);
        $message = "Menu item updated successfully!";
    } else {
        $message = "All fields are required!";
    }
}

// Handle deleting a menu item
if (isset($_GET['delete'])) {
    $id = filter_var($_GET['delete'], FILTER_SANITIZE_NUMBER_INT);
    $delete = $conn->prepare("DELETE FROM `menu_items` WHERE `id` = ?");
    $delete->execute([$id]);
    header('location:menu_settings.php');
    exit;
}

// Fetch menu item for editing
$edit_item = null;
if (isset($_GET['edit'])) {
    $id = filter_var($_GET['edit'], FILTER_SANITIZE_NUMBER_INT);
    $select = $conn->prepare("SELECT * FROM `menu_items` WHERE `id` = ?");
    $select->execute([$id]);
    $edit_item = $select->fetch(PDO::FETCH_ASSOC);
}

// Fetch all menu items for display
$select = $conn->prepare("SELECT * FROM `menu_items` ORDER BY `order` ASC");
$select->execute();
$menu_items = $select->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<?php include "include/head.php"; ?>
<body id="page-top">
<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../admin/dashboard.php">
            <div class="sidebar-brand-text mx-3">Admin</div>
        </a>
        <hr class="sidebar-divider my-0">
        <?php include "include/nav.php"; ?>
        <hr class="sidebar-divider">
    </ul>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Menu Settings</h1>
                <a href="settings.php" class="btn btn-secondary mb-4">Back to Settings</a>

                <!-- Add/Edit Form -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary"><?= isset($edit_item) ? 'Edit' : 'Add'; ?> Menu Item</h6>
                    </div>
                    <div class="card-body">
                        <?php if (isset($message)) echo "<p>$message</p>"; ?>
                        <form action="" method="post">
                            <input type="hidden" name="id" value="<?= $edit_item['id'] ?? ''; ?>">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="<?= $edit_item['name'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Link</label>
                                <input type="text" name="link" class="form-control" value="<?= $edit_item['link'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Order</label>
                                <input type="number" name="order" class="form-control" value="<?= $edit_item['order'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label><input type="checkbox" name="enable" <?= (isset($edit_item) && $edit_item['enable'] == '1') || !isset($edit_item) ? 'checked' : ''; ?>> Enable</label>
                            </div>
                            <button type="submit" name="<?= isset($edit_item) ? 'update_menu_item' : 'add_menu_item'; ?>" class="btn btn-primary"><?= isset($edit_item) ? 'Update' : 'Add'; ?></button>
                            <?php if (isset($edit_item)) echo '<a href="menu_settings.php" class="btn btn-secondary">Cancel</a>'; ?>
                        </form>
                    </div>
                </div>

                <!-- Menu Items Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Current Menu Items</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Name</th>
                                    <th>Link</th>
                                    <th>Enable</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menu_items as $item) { ?>
                                    <tr>
                                        <td><?= $item['order']; ?></td>
                                        <td><?= $item['name']; ?></td>
                                        <td><?= $item['link']; ?></td>
                                        <td><?= $item['enable'] == '1' ? 'Yes' : 'No'; ?></td>
                                        <td>
                                            <a href="menu_settings.php?edit=<?= $item['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <a href="menu_settings.php?delete=<?= $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this menu item?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "include/footer.php"; ?>
    </div>
</div>
</body>
</html>