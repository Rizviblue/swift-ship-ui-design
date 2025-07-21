<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireRole(['admin']);

$pageTitle = 'Courier Management';

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $courierId = intval($_GET['delete']);
    
    try {
        global $db;
        $db->beginTransaction();
        
        // Delete status history first
        $deleteHistoryQuery = "DELETE FROM courier_status_history WHERE courier_id = ?";
        $stmt = $db->prepare($deleteHistoryQuery);
        $stmt->execute([$courierId]);
        
        // Delete courier
        $deleteCourierQuery = "DELETE FROM couriers WHERE id = ?";
        $stmt = $db->prepare($deleteCourierQuery);
        $stmt->execute([$courierId]);
        
        $db->commit();
        showAlert('Courier deleted successfully!', 'success');
    } catch (Exception $e) {
        $db->rollBack();
        showAlert('Error deleting courier: ' . $e->getMessage(), 'danger');
    }
    
    redirect('couriers.php');
}

// Pagination and filtering
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';

$recordsPerPage = RECORDS_PER_PAGE;
$offset = ($page - 1) * $recordsPerPage;

// Build query
global $db;
$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(c.tracking_number LIKE ? OR c.sender_name LIKE ? OR c.receiver_name LIKE ? OR c.receiver_city LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($statusFilter)) {
    $whereConditions[] = "c.status = ?";
    $params[] = $statusFilter;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM couriers c $whereClause";
$countStmt = $db->prepare($countQuery);
$countStmt->execute($params);
$totalRecords = $countStmt->fetch()['total'];

// Get couriers
$query = "SELECT c.*, u.name as created_by_name, a.name as agent_name, b.name as branch_name
          FROM couriers c 
          LEFT JOIN users u ON c.created_by = u.id 
          LEFT JOIN users a ON c.assigned_agent = a.id
          LEFT JOIN branches b ON c.branch_id = b.id
          $whereClause 
          ORDER BY c.created_at DESC 
          LIMIT $recordsPerPage OFFSET $offset";
$stmt = $db->prepare($query);
$stmt->execute($params);
$couriers = $stmt->fetchAll();

$pagination = paginate($totalRecords, $page, $recordsPerPage);

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="mb-4">
            <h1 class="h3">Courier Management</h1>
            <p class="text-muted">Manage all courier shipments</p>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Search by tracking number, sender, receiver, or city" 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="picked-up" <?php echo $statusFilter === 'picked-up' ? 'selected' : ''; ?>>Picked Up</option>
                            <option value="in-transit" <?php echo $statusFilter === 'in-transit' ? 'selected' : ''; ?>>In Transit</option>
                            <option value="delivered" <?php echo $statusFilter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <a href="add-courier.php" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Add Courier
                            </a>
                            <a href="?export=csv<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($statusFilter) ? '&status=' . urlencode($statusFilter) : ''; ?>" class="btn btn-outline-primary">
                                <i class="bi bi-download"></i> Export
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Couriers Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">All Couriers (<?php echo $totalRecords; ?>)</h5>
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
                                <th>Agent</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($couriers as $courier): ?>
                            <tr>
                                <td><strong><?php echo $courier['tracking_number']; ?></strong></td>
                                <td><?php echo $courier['sender_name']; ?></td>
                                <td><?php echo $courier['receiver_name']; ?></td>
                                <td><?php echo $courier['receiver_city']; ?></td>
                                <td><?php echo getStatusBadge($courier['status']); ?></td>
                                <td><?php echo $courier['agent_name'] ?? 'Unassigned'; ?></td>
                                <td><?php echo formatDate($courier['created_at'], 'M d, Y'); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="view-courier.php?id=<?php echo $courier['id']; ?>" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit-courier.php?id=<?php echo $courier['id']; ?>" 
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="?delete=<?php echo $courier['id']; ?>" 
                                           class="btn btn-outline-danger" title="Delete"
                                           onclick="return confirm('Are you sure you want to delete this courier?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <nav aria-label="Couriers pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>">Previous</a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $pagination['total_pages']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($statusFilter); ?>">Next</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Handle CSV export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $exportQuery = "SELECT c.tracking_number, c.sender_name, c.receiver_name, c.receiver_city, 
                    c.status, c.delivery_fee, c.created_at, a.name as agent_name
                    FROM couriers c 
                    LEFT JOIN users a ON c.assigned_agent = a.id
                    $whereClause 
                    ORDER BY c.created_at DESC";
    $exportStmt = $db->prepare($exportQuery);
    $exportStmt->execute($params);
    $exportData = $exportStmt->fetchAll();
    
    $csvData = [];
    foreach ($exportData as $row) {
        $csvData[] = [
            $row['tracking_number'],
            $row['sender_name'],
            $row['receiver_name'],
            $row['receiver_city'],
            $row['status'],
            $row['agent_name'] ?? 'Unassigned',
            $row['delivery_fee'],
            formatDate($row['created_at'], 'Y-m-d H:i:s')
        ];
    }
    
    $headers = ['Tracking Number', 'Sender', 'Receiver', 'City', 'Status', 'Agent', 'Fee', 'Date'];
    exportToCSV($csvData, 'couriers_' . date('Y-m-d') . '.csv', $headers);
}

include '../includes/footer.php';
?>