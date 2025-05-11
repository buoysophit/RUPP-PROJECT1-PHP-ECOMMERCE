<?php
require_once 'components/connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

include 'components/wishlist_cart.php';

// Get product ID from URL
$pid = isset($_GET['pid']) ? $_GET['pid'] : '';

// Fetch product details
$select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
$select_products->execute([$pid]);

if ($select_products->rowCount() > 0) {
    $fetch_product = $select_products->fetch(PDO::FETCH_ASSOC);
} else {
    header('location:index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $fetch_product['name']; ?> - Nita Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .product-detail {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 3rem;
            background: var(--white);
            border-radius: 1rem;
            box-shadow: var(--box-shadow);
        }

        .product-images {
            position: relative;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .thumbnail-container {
            display: flex;
            gap: 1rem;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.5rem;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .thumbnail:hover {
            border-color: var(--main-color);
        }

        .product-info {
            padding: 2rem;
        }

        .product-name {
            font-size: 2.5rem;
            color: var(--black);
            margin-bottom: 1rem;
        }

        .product-price {
            font-size: 2rem;
            color: var(--main-color);
            margin-bottom: 2rem;
        }

        .product-description {
            font-size: 1.6rem;
            color: var(--light-color);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quantity-selector .qty {
            width: 100px;
            text-align: center;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        .action-buttons .btn {
            flex: 1;
        }

        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'components/user_header.php'; ?>

    <section class="product-detail">
        <div class="product-images">
            <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="" class="main-image" id="mainImage">
            <div class="thumbnail-container">
                <?php if($fetch_product['image_01']) { ?>
                    <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="" class="thumbnail" onclick="changeImage(this.src)">
                <?php } ?>
                <?php if($fetch_product['image_02']) { ?>
                    <img src="uploaded_img/<?= $fetch_product['image_02']; ?>" alt="" class="thumbnail" onclick="changeImage(this.src)">
                <?php } ?>
                <?php if($fetch_product['image_03']) { ?>
                    <img src="uploaded_img/<?= $fetch_product['image_03']; ?>" alt="" class="thumbnail" onclick="changeImage(this.src)">
                <?php } ?>
            </div>
        </div>

        <div class="product-info">
            <h1 class="product-name"><?= $fetch_product['name']; ?></h1>
            <div class="product-price">$<?= $fetch_product['price']; ?></div>
            <p class="product-description"><?= $fetch_product['details']; ?></p>
            
            <form action="" method="post">
                <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
                <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
                <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
                <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
                
                <div class="quantity-selector">
                    <span>Quantity:</span>
                    <input type="number" name="qty" class="qty" min="1" max="99" value="1">
                </div>
                
                <div class="action-buttons">
                    <button type="submit" class="btn" name="add_to_cart">Add to Cart</button>
                    <button type="submit" class="btn" name="add_to_wishlist">Add to Wishlist</button>
                </div>
            </form>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>

    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
        }
    </script>
</body>
</html>