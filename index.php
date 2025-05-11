<?php
require_once 'components/connect.php';
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
    <title>home</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>

<div class="home-bg">
    <section class="home">
        <?php include 'components/slideshow.php'; ?>
    </section>
</div>

<section class="services">
    <h1 class="heading">our services</h1>
    <div class="service-container">
        <div class="service">
            <i class="fas fa-truck"></i>
            <h3>Free Shipping</h3>
            <p>On orders over $50</p>
        </div>
        <div class="service">
            <i class="fas fa-headset"></i>
            <h3>24/7 Support</h3>
            <p>Always here to help</p>
        </div>
        <div class="service">
            <i class="fas fa-undo"></i>
            <h3>Money Back Guarantee</h3>
            <p>30-day return policy</p>
        </div>
        <div class="service">
            <i class="fas fa-lock"></i>
            <h3>Secure Payments</h3>
            <p>Safe and encrypted transactions</p>
        </div>
    </div>
</section>

<section class="category">
    <h1 class="heading">shop by fashion category</h1>
    <div class="swiper category-slider">
        <div class="swiper-wrapper">
            <a href="shop.php" class="swiper-slide slide category-item">
                <div class="image-container">
                    <img src="images/catagory/cloth.png" alt="Clothing">
                </div>
                <h3>clothing</h3>
            </a>
            <a href="shop.php" class="swiper-slide slide category-item">
                <div class="image-container">
                    <img src="images/catagory/bage.png" alt="Accessories">
                </div>
                <h3>accessories</h3>
            </a>
            <a href="shop.php" class="swiper-slide slide category-item">
                <div class="image-container">
                    <img src="images/catagory/shose.png" alt="Shoes">
                </div>
                <h3>shoes</h3>
            </a>
            <a href="shop.php" class="swiper-slide slide category-item">
                <div class="image-container">
                    <img src="images/catagory/makeup.png" alt="Makeup">
                </div>
                <h3>makeup</h3>
            </a>
            <a href="shop.php" class="swiper-slide slide category-item">
                <div class="image-container">
                    <img src="images/catagory/jwl.png" alt="Jewelry">
                </div>
                <h3>jewelry</h3>
            </a>
      
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<section class="home-products new-collection">
    <h1 class="heading">new collection products</h1>
    <div class="box-container">
        <?php
        $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6");
        $select_products->execute();
        if ($select_products->rowCount() > 0) {
            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <div class="box">
            <form action="" method="post">
                <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
                <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
                <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
                <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
                <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
                <a href="product_detail.php?pid=<?= $fetch_product['id']; ?>" class="product-link">
                    <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
                    <div class="name"><?= $fetch_product['name']; ?></div>
                </a>
                <div class="flex">
                    <div class="price"><span>$</span><?= $fetch_product['price']; ?></div>
                    <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
                </div>
                <input type="submit" value="add to cart" class="btn" name="add_to_cart">
            </form>
        </div>
        <?php
            }
        } else {
            echo '<p class="empty">no products added yet!</p>';
        }
        ?>
    </div>
</section>

<section class="home-products">
    <h1 class="heading">latest products</h1>
    <div class="swiper products-slider">
        <div class="swiper-wrapper">
        <?php
        $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6");
        $select_products->execute();
        if ($select_products->rowCount() > 0) {
            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
        <form action="" method="post" class="swiper-slide slide">
            <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
            <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
            <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
            <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
            <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
            <a href="quick_view?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
            <a href="product_detail?pid=<?= $fetch_product['id']; ?>" class="product-link">
                <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
                <div class="name"><?= $fetch_product['name']; ?></div>
            </a>
            <div class="flex">
                <div class="price"><span>$</span><?= $fetch_product['price']; ?></div>
                <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
            </div>
            <input type="submit" value="add to cart" class="btn" name="add_to_cart">
        </form>
        <?php
            }
        } else {
            echo '<p class="empty">no products added yet!</p>';
        }
        ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>



<?php include 'components/footer.php'; ?>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>

<script>
var swiper = new Swiper(".home-slider", {
    loop: true,
    spaceBetween: 20,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
});

var swiper = new Swiper(".category-slider", {
    loop: true,
    spaceBetween: 20,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    breakpoints: {
        0: {
            slidesPerView: 1,
        },
        450: {
            slidesPerView: 2,
        },
        768: {
            slidesPerView: 3,
        },
        1024: {
            slidesPerView: 4,
        },
    },
});

var swiper = new Swiper(".products-slider", {
    loop: true,
    spaceBetween: 20,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    breakpoints: {
        0: {
            slidesPerView: 1,
        },
        450: {
            slidesPerView: 2,
        },
        768: {
            slidesPerView: 2,
        },
        1024: {
            slidesPerView: 3,
        },
    },
});
</script>

</body>
</html>