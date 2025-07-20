<?php
session_start();
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireRole(['admin']);

$pageTitle = 'Customer Management';

// Get customers with their order statistics
global $db;
$query = "SELECT u.*, 
          COUNT(c1.id) as total_orders,
          COUNT(c2.id) as total_sent,
          MAX(c1.created_at) as last_order
          FROM users u 
          LEFT JOIN couriers c1 ON u.id = c1.receiver_id 
          LEFT JOIN couriers c2 ON u.id = c2.sender_id
          WHERE u.role = 'customer' 
          GROUP BY u.id 
          ORDER BY u.created_at DESC";
$stmt = $db->query($query);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$statsQuery = "SELECT 
    COUNT(*) as total_customers,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_customers,
    SUM(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as active_this_month
    FROM users WHERE role = 'customer'";
$statsStmt = $db->query($statsQuery);
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

// Get total orders
$ordersQuery = "SELECT COUNT(*) as total_orders FROM couriers";
$ordersStmt = $db->query($ordersQuery);
$totalOrders = $ordersStmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="mb-4">
            <h1 class="h3">Customer Management</h1>
            <p class="text-muted">Manage customer accounts and orders</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-people fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Total Customers</h5>
                                <h2><?php echo $stats['total_customers']; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-check fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Active</h5>
                                <h2><?php echo $stats['active_customers']; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-box-seam fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Total Orders</h5>
                                <h2><?php echo $totalOrders; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-month fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Active This Month</h5>
                                <h2><?php echo $stats['active_this_month']; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search Customers</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Search by name, email, or phone" 
                               value="<?php echo $_GET['search'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" <?php echo ($_GET['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($_GET['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Customers</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Total Orders</th>
                                <th>Status</th>
                                <th>Join Date</th>
                                <th>Last Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo $customer['name']; ?></td>
                                <td><?php echo $customer['email']; ?></td>
                                <td><?php echo $customer['phone'] ?? 'N/A'; ?></td>
                                <td><?php echo $customer['total_orders']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $customer['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($customer['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($customer['created_at'], 'M d, Y'); ?></td>
                                <td><?php echo $customer['last_order'] ? formatDate($customer['last_order'], 'M d, Y') : 'Never'; ?></td>
                                <td>
                                    <a href="view-customer.php?id=<?php echo $customer['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary" title="View Details">
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