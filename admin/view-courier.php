<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireRole(['admin']);

$pageTitle = 'View Courier';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('couriers.php');
}

$courierId = intval($_GET['id']);

// Get courier details
global $db;
$query = "SELECT c.*, u.name as created_by_name, a.name as agent_name, b.name as branch_name
          FROM couriers c 
          LEFT JOIN users u ON c.created_by = u.id 
          LEFT JOIN users a ON c.assigned_agent = a.id
          LEFT JOIN branches b ON c.branch_id = b.id
          WHERE c.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$courierId]);
$courier = $stmt->fetch();

if (!$courier) {
    showAlert('Courier not found!', 'danger');
    redirect('couriers.php');
}

// Get courier history
$history = getCourierHistory($courierId);

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3">Courier Details</h1>
                    <p class="text-muted">Tracking Number: <?php echo $courier['tracking_number']; ?></p>
                </div>
                <div>
                    <a href="edit-courier.php?id=<?php echo $courier['id']; ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                    <a href="couriers.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Courier Information -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Package Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Sender Details</h6>
                                <p><strong>Name:</strong> <?php echo $courier['sender_name']; ?></p>
                                <p><strong>Contact:</strong> <?php echo $courier['sender_contact']; ?></p>
                                <p><strong>Address:</strong><br><?php echo nl2br($courier['sender_address']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Receiver Details</h6>
                                <p><strong>Name:</strong> <?php echo $courier['receiver_name']; ?></p>
                                <p><strong>Contact:</strong> <?php echo $courier['receiver_contact']; ?></p>
                                <p><strong>City:</strong> <?php echo $courier['receiver_city']; ?></p>
                                <p><strong>Address:</strong><br><?php echo nl2br($courier['receiver_address']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status History -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Status History</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php foreach ($history as $item): ?>
                            <div class="timeline-item d-flex align-items-start mb-3">
                                <div class="timeline-marker me-3 mt-1">
                                    <div class="rounded-circle bg-primary" style="width: 12px; height: 12px;"></div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><?php echo getStatusBadge($item['status']); ?></h6>
                                        <small class="text-muted">
                                            <?php echo formatDate($item['created_at'], 'M d, Y g:i A'); ?>
                                        </small>
                                    </div>
                                    <?php if ($item['location']): ?>
                                        <p class="mb-1 text-muted">Location: <?php echo $item['location']; ?></p>
                                    <?php endif; ?>
                                    <?php if ($item['notes']): ?>
                                        <p class="mb-1"><?php echo $item['notes']; ?></p>
                                    <?php endif; ?>
                                    <small class="text-muted">Updated by: <?php echo $item['updated_by_name']; ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Information -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Current Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <?php echo getStatusBadge($courier['status']); ?>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Package Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Weight:</strong> <?php echo $courier['weight']; ?> kg</p>
                        <p><strong>Dimensions:</strong> <?php echo $courier['dimensions'] ?: 'Not specified'; ?></p>
                        <p><strong>Delivery Fee:</strong> $<?php echo number_format($courier['delivery_fee'], 2); ?></p>
                        <p><strong>Assigned Agent:</strong> <?php echo $courier['agent_name'] ?: 'Unassigned'; ?></p>
                        <p><strong>Branch:</strong> <?php echo $courier['branch_name'] ?: 'Not assigned'; ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">System Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Created:</strong> <?php echo formatDate($courier['created_at'], 'M d, Y g:i A'); ?></p>
                        <p><strong>Created By:</strong> <?php echo $courier['created_by_name']; ?></p>
                        <p><strong>Last Updated:</strong> <?php echo formatDate($courier['updated_at'], 'M d, Y g:i A'); ?></p>
                        <?php if ($courier['notes']): ?>
                            <p><strong>Notes:</strong><br><?php echo nl2br($courier['notes']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-item:not(:last-child) .timeline-marker::after {
    content: '';
    position: absolute;
    left: 5px;
    top: 20px;
    width: 2px;
    height: 40px;
    background-color: #dee2e6;
}
.timeline-marker {
    position: relative;
}
</style>

<?php include '../includes/footer.php'; ?>