<li class="nav-item <?= ($current_page == 'dashboard' ? 'active' : '') ?>">
    <a class="nav-link" href="../admin/dashboard.php">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span></a>
</li>

<li class="nav-item <?= ($current_page == 'slideshows' ? 'active' : '') ?>">
    <a class="nav-link" href="../admin/slideshows.php">
        <i class="fas fa-fw fa-images"></i>
        <span>Slideshows</span></a>
</li>

<li class="nav-item <?= ($current_page == 'products' ? 'active' : '') ?>">
    <a class="nav-link" href="../admin/products.php">
        <i class="fas fa-fw fa-shopping-bag"></i>
        <span>Products</span></a>
</li>

<li class="nav-item <?= ($current_page == 'orders' ? 'active' : '') ?>">
    <a class="nav-link" href="../admin/placed_orders.php">
        <i class="fas fa-fw fa-phone"></i>
        <span>Orders</span></a>
</li>

<li class="nav-item <?= ($current_page == 'messages' ? 'active' : '') ?>">
    <a class="nav-link" href="../admin/messages.php">
        <i class="fas fa-fw fa-envelope-open-text"></i>
        <span>Messages</span></a>
</li>

<li class="nav-item <?= ($current_page == 'settings' ? 'active' : '') ?>">
    <a class="nav-link" href="../admin/settings.php">
        <i class="fas fa-fw fa-cog"></i>
        <span>Settings</span></a>
</li>

<?php if (isset($_SESSION['admin_id'])): ?>
<li class="nav-item <?= ($current_page == 'logout' ? 'active' : '') ?>">
    <a class="nav-link" href="../admin/logout.php" onclick="return confirm('Are you sure you want to logout?');">
        <i class="fas fa-fw fa-sign-out-alt"></i>
        <span>Logout</span></a>
</li>
<?php endif; ?>