<?php
function getMenuItems($userRole) {
    $menuItems = [];
    
    switch ($userRole) {
        case 'admin':
            $menuItems = [
                ['title' => 'Dashboard', 'url' => 'admin/dashboard.php', 'icon' => 'bi bi-speedometer2'],
                ['title' => 'Add Courier', 'url' => 'admin/add-courier.php', 'icon' => 'bi bi-plus-circle'],
                ['title' => 'Courier List', 'url' => 'admin/couriers.php', 'icon' => 'bi bi-box-seam'],
                ['title' => 'Agent Management', 'url' => 'admin/agents.php', 'icon' => 'bi bi-people'],
                ['title' => 'Customer Management', 'url' => 'admin/customers.php', 'icon' => 'bi bi-person-lines-fill'],
                ['title' => 'Reports', 'url' => 'admin/reports.php', 'icon' => 'bi bi-bar-chart'],
                ['title' => 'Settings', 'url' => 'admin/settings.php', 'icon' => 'bi bi-gear']
            ];
            break;
            
        case 'agent':
            $menuItems = [
                ['title' => 'Dashboard', 'url' => 'agent/dashboard.php', 'icon' => 'bi bi-speedometer2'],
                ['title' => 'My Couriers', 'url' => 'agent/couriers.php', 'icon' => 'bi bi-box-seam'],
                ['title' => 'Update Status', 'url' => 'agent/update-status.php', 'icon' => 'bi bi-arrow-repeat'],
                ['title' => 'Reports', 'url' => 'agent/reports.php', 'icon' => 'bi bi-file-text']
            ];
            break;
            
        case 'customer':
            $menuItems = [
                ['title' => 'Track Courier', 'url' => 'customer/track.php', 'icon' => 'bi bi-search'],
                ['title' => 'My Orders', 'url' => 'customer/orders.php', 'icon' => 'bi bi-list-ul'],
                ['title' => 'Contact', 'url' => 'customer/contact.php', 'icon' => 'bi bi-envelope']
            ];
            break;
    }
    
    return $menuItems;
}
?>

<div class="sidebar bg-light border-end" style="width: 250px; min-height: calc(100vh - 56px);">
    <div class="p-3">
        <h6 class="text-muted text-uppercase"><?php echo ucfirst($_SESSION['user_role']); ?> Panel</h6>
        <ul class="nav nav-pills flex-column">
            <?php
            $menuItems = getMenuItems($_SESSION['user_role']);
            $currentPage = basename($_SERVER['PHP_SELF']);
            
            foreach ($menuItems as $item):
                $isActive = (basename($item['url']) === $currentPage) ? 'active' : '';
            ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $isActive; ?>" href="<?php echo BASE_URL . $item['url']; ?>">
                        <i class="<?php echo $item['icon']; ?> me-2"></i>
                        <?php echo $item['title']; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>