<?php
include 'components/connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

include 'components/wishlist_cart.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>

<section class="products">
    <h1 class="heading">latest products</h1>
    <div class="category-filter">
        <div class="category-buttons">
            <a href="shop.php" class="category-btn <?= !isset($_GET['category']) ? 'active' : ''; ?>">All</a>
            <?php
            $select_categories = $conn->prepare("SELECT DISTINCT category FROM `products` WHERE category IS NOT NULL AND category != ''");
            $select_categories->execute();
            while ($row = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                $category = htmlspecialchars($row['category']);
                $active_class = (isset($_GET['category']) && $_GET['category'] == $category) ? 'active' : '';
                echo '<a href="shop.php?category=' . urlencode($category) . '" class="category-btn ' . $active_class . '">' . $category . '</a>';
            }
            ?>
        </div>
    </div>
    <div class="box-container">
        <?php
        $category_filter = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_SANITIZE_STRING) : '';
        if ($category_filter) {
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE category = ?");
            $select_products->execute([$category_filter]);
        } else {
            $select_products = $conn->prepare("SELECT * FROM `products`");
            $select_products->execute();
        }

        if ($select_products->rowCount() > 0) {
            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <form action="" method="post" class="box">
            <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
            <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
            <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
            <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
            <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
            <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
            <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
            <div class="name"><?= $fetch_product['name']; ?></div>
            <div class="flex">
                <div class="price"><span>$</span><?= $fetch_product['price']; ?></div>
                <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
            </div>
            <input type="submit" value="add to cart" class="btn" name="add_to_cart">
        </form>
        <?php
            }
        } else {
            echo '<p class="empty">no products found!</p>';
        }
        ?>
    </div>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>