<?php
include '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
}

// Add product logic
if (isset($_POST['add_product'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
    $details = filter_var($_POST['details'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

    $image_01 = filter_var($_FILES['image_01']['name'], FILTER_SANITIZE_STRING);
    $image_size_01 = $_FILES['image_01']['size'];
    $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
    $image_folder_01 = '../uploaded_img/' . $image_01;

    $image_02 = filter_var($_FILES['image_02']['name'], FILTER_SANITIZE_STRING);
    $image_size_02 = $_FILES['image_02']['size'];
    $image_tmp_name_02 = $_FILES['image_02']['tmp_name'];
    $image_folder_02 = '../uploaded_img/' . $image_02;

    $image_03 = filter_var($_FILES['image_03']['name'], FILTER_SANITIZE_STRING);
    $image_size_03 = $_FILES['image_03']['size'];
    $image_tmp_name_03 = $_FILES['image_03']['tmp_name'];
    $image_folder_03 = '../uploaded_img/' . $image_03;

    $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
    $select_products->execute([$name]);

    if ($select_products->rowCount() > 0) {
        $message[] = 'Product name already exists!';
    } else {
        $insert_products = $conn->prepare("INSERT INTO `products`(name, details, price, category, image_01, image_02, image_03) VALUES(?,?,?,?,?,?,?)");
        $insert_products->execute([$name, $details, $price, $category, $image_01, $image_02, $image_03]);

        if ($insert_products) {
            if ($image_size_01 > 2000000 || $image_size_02 > 2000000 || $image_size_03 > 2000000) {
                $message[] = 'Image size is too large!';
            } else {
                if (!move_uploaded_file($image_tmp_name_01, $image_folder_01)) {
                    $message[] = 'Failed to upload Image 01!';
                }
                if (!move_uploaded_file($image_tmp_name_02, $image_folder_02)) {
                    $message[] = 'Failed to upload Image 02!';
                }
                if (!move_uploaded_file($image_tmp_name_03, $image_folder_03)) {
                    $message[] = 'Failed to upload Image 03!';
                }
                if (file_exists($image_folder_01) && file_exists($image_folder_02) && file_exists($image_folder_03)) {
                    $message[] = 'New product added successfully!';
                } else {
                    $message[] = 'Product added, but some images failed to upload!';
                }
            }
        }
    }
}

// Delete product 
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $delete_product_image->execute([$delete_id]);
    $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
    if ($fetch_delete_image) {
        unlink('../uploaded_img/' . $fetch_delete_image['image_01']);
        unlink('../uploaded_img/' . $fetch_delete_image['image_02']);
        unlink('../uploaded_img/' . $fetch_delete_image['image_03']);
    }
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $delete_product->execute([$delete_id]);
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
    $delete_cart->execute([$delete_id]);
    $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
    $delete_wishlist->execute([$delete_id]);
    header('location:products.php');
}

// Update product logic
if (isset($_POST['update'])) {
    $pid = $_POST['pid'];
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
    $details = filter_var($_POST['details'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

    $update_product = $conn->prepare("UPDATE `products` SET name = ?, price = ?, details = ?, category = ? WHERE id = ?");
    $update_product->execute([$name, $price, $details, $category, $pid]);

    $message[] = 'Product updated successfully!';

    $old_image_01 = $_POST['old_image_01'];
    $image_01 = $_FILES['image_01']['name'];
    $image_01 = filter_var($image_01, FILTER_SANITIZE_STRING);
    $image_size_01 = $_FILES['image_01']['size'];
    $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
    $image_folder_01 = '../uploaded_img/' . $image_01;

    if (!empty($image_01)) {
        if ($image_size_01 > 2000000) {
            $message[] = 'Image size is too large!';
        } else {
            $update_image_01 = $conn->prepare("UPDATE `products` SET image_01 = ? WHERE id = ?");
            $update_image_01->execute([$image_01, $pid]);
            move_uploaded_file($image_tmp_name_01, $image_folder_01);
            unlink('../uploaded_img/' . $old_image_01);
            $message[] = 'Image 01 updated successfully!';
        }
    }

    $old_image_02 = $_POST['old_image_02'];
    $image_02 = $_FILES['image_02']['name'];
    $image_02 = filter_var($image_02, FILTER_SANITIZE_STRING);
    $image_size_02 = $_FILES['image_02']['size'];
    $image_tmp_name_02 = $_FILES['image_02']['tmp_name'];
    $image_folder_02 = '../uploaded_img/' . $image_02;

    if (!empty($image_02)) {
        if ($image_size_02 > 2000000) {
            $message[] = 'Image size is too large!';
        } else {
            $update_image_02 = $conn->prepare("UPDATE `products` SET image_02 = ? WHERE id = ?");
            $update_image_02->execute([$image_02, $pid]);
            move_uploaded_file($image_tmp_name_02, $image_folder_02);
            unlink('../uploaded_img/' . $old_image_02);
            $message[] = 'Image 02 updated successfully!';
        }
    }

    $old_image_03 = $_POST['old_image_03'];
    $image_03 = $_FILES['image_03']['name'];
    $image_03 = filter_var($image_03, FILTER_SANITIZE_STRING);
    $image_size_03 = $_FILES['image_03']['size'];
    $image_tmp_name_03 = $_FILES['image_03']['tmp_name'];
    $image_folder_03 = '../uploaded_img/' . $image_03;

    if (!empty($image_03)) {
        if ($image_size_03 > 2000000) {
            $message[] = 'Image size is too large!';
        } else {
            $update_image_03 = $conn->prepare("UPDATE `products` SET image_03 = ? WHERE id = ?");
            $update_image_03->execute([$image_03, $pid]);
            move_uploaded_file($image_tmp_name_03, $image_folder_03);
            unlink('../uploaded_img/' . $old_image_03);
            $message[] = 'Image 03 updated successfully!';
        }
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
        <?php
        $current_page = 'products';
        include "include/nav.php";
        ?>
        <hr class="sidebar-divider">
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Products</h1>
                    <button class="btn btn-primary btn-icon-split" data-toggle="modal" data-target="#addProductModal">
                        <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                        <span class="text">Add New Product</span>
                    </button>
                </div>
                <?php
                if (isset($message)) {
                    foreach ($message as $msg) {
                        echo '<div class="alert alert-info fade show" role="alert">' . $msg . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>';
                    }
                }
                ?>

                <!-- Category Filter Dropdown -->
                <form class="form-inline mb-4" method="get">
                    <div class="input-group w-100" style="max-width: 300px;">
                        <select name="category" class="form-control bg-light border-0 small" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php
                            $select_categories = $conn->prepare("SELECT DISTINCT category FROM `products` WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
                            $select_categories->execute();
                            while ($row = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                                $selected = (isset($_GET['category']) && $_GET['category'] == $row['category']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($row['category']) . '" ' . $selected . '>' . htmlspecialchars($row['category']) . '</option>';
                            }
                            ?>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-filter fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Search Bar Inside Content -->
                <form class="form-inline mb-4">
                    <div class="input-group w-100">
                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search products..." aria-label="Search" id="searchInput">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Products Added</h6>
                    </div>
                    <div class="card-body">
                        <div class="row" id="productContainer">
                            <?php
                            $category_filter = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_SANITIZE_STRING) : '';
                            if ($category_filter) {
                                $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = ? ORDER BY name ASC");
                                $select_products->execute([$category_filter]);
                            } else {
                                $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY category ASC, name ASC");
                                $select_products->execute();
                            }

                            if ($select_products->rowCount() > 0) {
                                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                                    $image_path = '../uploaded_img/' . $fetch_products['image_01'];
                                    $image_display = file_exists($image_path) ? $image_path : 'https://via.placeholder.com/200?text=Image+Not+Found';
                                    ?>
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4 product-item">
                                        <div class="card h-100 shadow-sm product-card">
                                            <div class="card-img-top-wrapper">
                                                <img src="<?= $image_display; ?>" class="card-img-top" alt="<?= $fetch_products['name']; ?>">
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title"><?= $fetch_products['name']; ?></h5>
                                                <p class="card-text text-muted small"><?= $fetch_products['category'] ?? 'No Category'; ?></p>
                                                <p class="card-text text-success font-weight-bold">$<?= $fetch_products['price']; ?>/-</p>
                                                <p class="card-text text-muted"><?= substr($fetch_products['details'], 0, 100); ?>...</p>
                                                <?php if (!file_exists($image_path)) { ?>
                                                    <p class="text-danger small">Image missing: <?= $fetch_products['image_01']; ?></p>
                                                <?php } ?>
                                            </div>
                                            <div class="card-footer bg-transparent border-0">
                                                <button class="btn btn-warning btn-sm btn-icon-split" data-toggle="modal" data-target="#updateProductModal<?= $fetch_products['id']; ?>">
                                                    <span class="icon text-white-50"><i class="fas fa-edit"></i></span>
                                                    <span class="text">Update</span>
                                                </button>
                                                <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="btn btn-danger btn-sm btn-icon-split ml-2" onclick="return confirm('Delete this product?');">
                                                    <span class="icon text-white-50"><i class="fas fa-trash"></i></span>
                                                    <span class="text">Delete</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Update Product Modal -->
                                    <div class="modal fade" id="updateProductModal<?= $fetch_products['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateProductModalLabel<?= $fetch_products['id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="updateProductModalLabel<?= $fetch_products['id']; ?>">Update Product</h5>
                                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">×</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="" method="post" enctype="multipart/form-data">
                                                        <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
                                                        <input type="hidden" name="old_image_01" value="<?= $fetch_products['image_01']; ?>">
                                                        <input type="hidden" name="old_image_02" value="<?= $fetch_products['image_02']; ?>">
                                                        <input type="hidden" name="old_image_03" value="<?= $fetch_products['image_03']; ?>">
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" class="img-fluid" alt="Image 01">
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <img src="../uploaded_img/<?= $fetch_products['image_02']; ?>" class="img-fluid" alt="Image 02">
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <img src="../uploaded_img/<?= $fetch_products['image_03']; ?>" class="img-fluid" alt="Image 03">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label font-weight-bold">Product Name</label>
                                                                <input type="text" name="name" required class="form-control" maxlength="100" placeholder="Enter product name" value="<?= $fetch_products['name']; ?>">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label font-weight-bold">Product Price</label>
                                                                <input type="number" name="price" required class="form-control" min="0" max="9999999999" placeholder="Enter product price" onkeypress="if(this.value.length == 10) return false;" value="<?= $fetch_products['price']; ?>">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label class="form-label font-weight-bold">Category</label>
                                                                <input type="text" name="category" required class="form-control" maxlength="100" placeholder="Enter category" value="<?= $fetch_products['category'] ?? ''; ?>">
                                                            </div>
                                                            <div class="col-12 mb-3">
                                                                <label class="form-label font-weight-bold">Product Details</label>
                                                                <textarea name="details" class="form-control" required cols="30" rows="5"><?= $fetch_products['details']; ?></textarea>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label font-weight-bold">Update Image 01</label>
                                                                <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="form-control">
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label font-weight-bold">Update Image 02</label>
                                                                <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="form-control">
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label font-weight-bold">Update Image 03</label>
                                                                <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="form-control">
                                                            </div>
                                                        </div>
                                                        <button type="submit" name="update" class="btn btn-primary btn-icon-split">
                                                            <span class="icon text-white-50"><i class="fas fa-save"></i></span>
                                                            <span class="text">Update</span>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<div class="col-12"><p class="text-muted text-center">No products added yet!</p></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include "include/footer.php" ?>
    </div>
</div>

<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" required maxlength="100" placeholder="Enter product name" name="name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Price</label>
                            <input type="number" min="0" class="form-control" required max="9999999999" placeholder="Enter product price" name="price">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" required maxlength="100" placeholder="Enter category" name="category">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Image 01</label>
                            <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Image 02</label>
                            <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Image 03</label>
                            <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="form-control" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Product Details</label>
                            <textarea name="details" placeholder="Enter product details" class="form-control" required maxlength="500" rows="5"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-icon-split" name="add_product">
                        <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                        <span class="text">Add Product</span>
                    </button>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
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
                <a class="btn btn-primary" href="login.html">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Custom Inline CSS -->
<style>
    .heading { font-size: 1.75rem; font-weight: bold; color: #4e73df; margin-bottom: 1.5rem; }
    .form-control:focus { border-color: #4e73df; box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25); }
    .btn-primary { background-color: #4e73df; border-color: #4e73df; }
    .btn-primary:hover { background-color: #2e59d9; border-color: #2653d4; }
    .product-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
    .card-img-top-wrapper { position: relative; padding-top: 75%; overflow: hidden; }
    .card-img-top { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; border-bottom: 1px solid #e3e6f0; }
    .card-title { font-size: 1.1rem; font-weight: 500; color: #333; }
    .card-text { font-size: 0.9rem; }
    .btn-icon-split .icon { padding: 0.375rem 0.75rem; }
    .btn-icon-split .text { padding: 0.375rem 0.75rem; }
    @media (max-width: 768px) { .card-body { padding: 1rem; } .btn-sm { font-size: 0.8rem; } }
</style>

<!-- JavaScript for Search Functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const products = document.querySelectorAll('.product-item');
            products.forEach(product => {
                const name = product.querySelector('.card-title').textContent.toLowerCase();
                const details = product.querySelector('.card-text.text-muted').textContent.toLowerCase();
                if (name.includes(searchTerm) || details.includes(searchTerm)) {
                    product.style.display = '';
                } else {
                    product.style.display = 'none';
                }
            });
        });
    });
</script>
</body>
</html>