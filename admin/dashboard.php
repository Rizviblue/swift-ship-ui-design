<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireRole(['admin']);

$pageTitle = 'Admin Dashboard';

// Get dashboard statistics
$stats = getCourierStats();
$recentCouriers = getRecentCouriers(5);

// Get additional stats
global $db;

// Total users by role
$userStats = [];
$userQuery = "SELECT role, COUNT(*) as count FROM users WHERE status = 'active' GROUP BY role";
$stmt = $db->query($userQuery);
while ($row = $stmt->fetch()) {
    $userStats[$row['role']] = $row['count'];
}

// Today's stats
$todayQuery = "SELECT COUNT(*) as today_count FROM couriers WHERE DATE(created_at) = CURDATE()";
$stmt = $db->query($todayQuery);
$todayCount = $stmt->fetch()['today_count'];

// This month's stats
$monthQuery = "SELECT COUNT(*) as month_count FROM couriers WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
$stmt = $db->query($monthQuery);
$monthCount = $stmt->fetch()['month_count'];

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3">Admin Dashboard</h1>
                <p class="text-muted">Monitor and manage your courier operations</p>
            </div>
            <div>
                <a href="add-courier.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    Add Courier
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-box-seam fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Total Couriers</h5>
                                <h2><?php echo $stats['total']; ?></h2>
                                <small>Today: <?php echo $todayCount; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-truck fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">In Transit</h5>
                                <h2><?php echo $stats['in-transit']; ?></h2>
                                <small>Active shipments</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Delivered</h5>
                                <h2><?php echo $stats['delivered']; ?></h2>
                                <small>This month: <?php echo $monthCount; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Pending</h5>
                                <h2><?php echo $stats['pending']; ?></h2>
                                <small>Awaiting pickup</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Couriers -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Couriers</h5>
                        <a href="couriers.php" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tracking Number</th>
                                        <th>Sender</th>
                                        <th>Receiver</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentCouriers as $courier): ?>
                                    <tr>
                                        <td><strong><?php echo $courier['tracking_number']; ?></strong></td>
                                        <td><?php echo $courier['sender_name']; ?></td>
                                        <td><?php echo $courier['receiver_name']; ?></td>
                                        <td><?php echo getStatusBadge($courier['status']); ?></td>
                                        <td><?php echo formatDate($courier['created_at'], 'M d, Y'); ?></td>
                                        <td>
                                            <a href="view-courier.php?id=<?php echo $courier['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">System Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Admins</span>
                            <span class="badge bg-primary"><?php echo $userStats['admin'] ?? 0; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Agents</span>
                            <span class="badge bg-info"><?php echo $userStats['agent'] ?? 0; ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Customers</span>
                            <span class="badge bg-success"><?php echo $userStats['customer'] ?? 0; ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Success Rate</span>
                            <span class="badge bg-success">
                                <?php 
                                $successRate = $stats['total'] > 0 ? round(($stats['delivered'] / $stats['total']) * 100, 1) : 0;
                                echo $successRate . '%';
                                ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="add-courier.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add New Courier
                            </a>
                            <a href="agents.php" class="btn btn-outline-primary">
                                <i class="bi bi-people me-2"></i>Manage Agents
                            </a>
                            <a href="reports.php" class="btn btn-outline-primary">
                                <i class="bi bi-bar-chart me-2"></i>View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>