<?php
if(isset($message)){
    foreach($message as $message){
        echo '
         <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
    }
}
?>

<header class="header">

    <section class="flex">

        <a href="index.php" class="logo">NITA-STORE<span></span></a>

        <nav class="navbar">
            <a href="index.php">Home</a>
            <a href="shop.php">Shop</a>
            <a href="contact.php">Contact</a>
            <a href="about.php">About</a>
            <a href="orders.php">Orders</a>
        </nav>

        <div class="icons">
            <?php
            $count_wishlist_items = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
            $count_wishlist_items->execute([$user_id]);
            $total_wishlist_counts = $count_wishlist_items->rowCount();

            $count_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_counts = $count_cart_items->rowCount();
            ?>
            <div id="menu-btn" class="fas fa-bars"></div>
            <a href="search_page.php"><i class="fas fa-magnifying-glass"></i></a>
            <a href="wishlist.php"><i class="fas fa-heart"></i><span>(<?= $total_wishlist_counts; ?>)</span></a>
            <a href="cart.php"><i class="fas fa-cart-shopping"></i><span>(<?= $total_cart_counts; ?>)</span></a>
            <div id="user-btn" class="fas fa-user-circle"></div>
        </div>

        <div class="profile">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if($select_profile->rowCount() > 0){
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <p><?= $fetch_profile["name"]; ?></p>
                <a href="update_user.php" class="btn">Update profile</a>
                <div class="flex-btn">
                    <a href="user_register.php" class="option-btn">Register</a>
                    <a href="user_login.php" class="option-btn">Login</a>
                </div>
                <a href="components/user_logout.php" class="delete-btn" onclick="return confirm('logout from the website?');">logout</a>
                <?php
            }else{
                ?>
                <p>Please Login or Register first!</p>
                <div class="flex-btn">
                    <a href="user_register.php" class="option-btn">Register</a>
                    <a href="user_login.php" class="option-btn">Login</a>
                </div>
                <?php
            }
            ?>
        </div>

    </section>

</header>

<style>
    /* Navbar links */
    .navbar a {
        margin: 0 1rem;
        font-size: 2rem;
        color: var(--black);
        text-decoration: none; /* Ensures no underline by default */
    }

    .navbar a:hover {
        color: var(--main-color);
        text-decoration: none; /* Removes underline on hover */
    }

    /* Icons links */
    .icons a {
        position: relative;
        margin: 0 1rem; /* Matches margin-left from .header .flex .icons > * */
        color: var(--black); /* Matches color from main CSS */
        font-size: 2.5rem; /* Matches .header .flex .icons > * */
        text-decoration: none; /* Ensures no underline by default */
    }

    .icons a:hover {
        text-decoration: none; /* Removes underline on hover */
    }

    .icons a span {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--main-color); /* Uses --main-color (#E53888) for consistency */
        color: var(--white); /* Matches selection color from main CSS */
        border-radius: 50%;
        width: 2rem; /* Slightly larger for better proportionality with 2.5rem icons */
        height: 2rem;
        font-size: 1.2rem; /* Adjusted for readability */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icons #menu-btn,
    .icons #user-btn {
        font-size: 2.5rem; /* Matches .header .flex .icons > * */
        cursor: pointer;
        color: var(--black); /* Matches color from main CSS */
    }

    .icons a:hover i {
        color: var(--main-color); /* Matches hover color from .header .flex .icons > *:hover */
    }
</style>