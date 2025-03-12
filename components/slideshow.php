<?php
require_once 'connect.php';
$select_slideshows = $conn->prepare("SELECT * FROM `slideshows` WHERE enable = '1' ORDER BY ssorder ASC");
$select_slideshows->execute();
?>

<div class="swiper home-slider">
    <div class="swiper-wrapper">
        <?php
        if ($select_slideshows->rowCount() > 0) {
            while ($fetch_slideshow = $select_slideshows->fetch(PDO::FETCH_ASSOC)) {
        ?>
            <div class="swiper-slide slide">
                <div class="image">
                    <img src="uploaded_img/<?= $fetch_slideshow['img']; ?>" alt="<?= $fetch_slideshow['title']; ?>">
                </div>
                <div class="content">
                    <span><?= $fetch_slideshow['subtitle']; ?></span>
                    <h3><?= $fetch_slideshow['title']; ?></h3>
                    <a href="<?= $fetch_slideshow['link']; ?>" class="btn">shop now</a>
                </div>
            </div>
        <?php
            }
        } else {
            echo '<p class="empty">No slideshows available!</p>';
        }
        ?>
    </div>
    <div class="swiper-pagination"></div>
</div>