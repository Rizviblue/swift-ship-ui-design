<?php
session_start();
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireRole(['admin']);

$pageTitle = 'Admin Dashboard';

// Get dashboard statistics
global $db;

// Total couriers
$stmt = $db->query("SELECT COUNT(*) as total FROM couriers");
$totalCouriers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// In transit
$stmt = $db->prepare("SELECT COUNT(*) as total FROM couriers WHERE status = ?");
$stmt->execute(['in-transit']);
$inTransit = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Delivered
$stmt = $db->prepare("SELECT COUNT(*) as total FROM couriers WHERE status = ?");
$stmt->execute(['delivered']);
$delivered = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Cancelled
$stmt = $db->prepare("SELECT COUNT(*) as total FROM couriers WHERE status = ?");
$stmt->execute(['cancelled']);
$cancelled = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Recent couriers
$stmt = $db->query("SELECT c.*, u1.name as sender_name, u2.name as receiver_name 
                   FROM couriers c 
                   LEFT JOIN users u1 ON c.sender_id = u1.id 
                   LEFT JOIN users u2 ON c.receiver_id = u2.id 
                   ORDER BY c.created_at DESC LIMIT 5");
$recentCouriers = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                                <h2><?php echo $totalCouriers; ?></h2>
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
                                <h2><?php echo $inTransit; ?></h2>
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
                                <h2><?php echo $delivered; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-x-circle fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Cancelled</h5>
                                <h2><?php echo $cancelled; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Couriers -->
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
                                <th>Destination</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentCouriers as $courier): ?>
                            <tr>
                                <td><strong><?php echo $courier['tracking_number']; ?></strong></td>
                                <td><?php echo $courier['sender_name'] ?? 'N/A'; ?></td>
                                <td><?php echo $courier['receiver_name'] ?? $courier['receiver_name']; ?></td>
                                <td><?php echo $courier['receiver_city']; ?></td>
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
</div>

<?php include '../includes/footer.php'; ?>