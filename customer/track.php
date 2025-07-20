<?php
session_start();
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireRole(['customer']);

$pageTitle = 'Track Your Courier';
$trackingResult = null;

if ($_POST && !empty($_POST['tracking_number'])) {
    $trackingNumber = sanitizeInput($_POST['tracking_number']);
    
    global $db;
    $query = "SELECT * FROM couriers WHERE tracking_number = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$trackingNumber]);
    $trackingResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$trackingResult) {
        showAlert('Tracking number not found.', 'warning');
    }
}

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="mb-4">
            <h1 class="h3">Track Your Courier</h1>
            <p class="text-muted">Enter your tracking number to see delivery status</p>
        </div>

        <!-- Tracking Form -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-search me-2"></i>
                            Track Package
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="tracking_number" class="form-label">Tracking Number</label>
                                <input type="text" class="form-control" id="tracking_number" name="tracking_number" 
                                       placeholder="Enter tracking number (e.g., CP12345678)" 
                                       value="<?php echo $_POST['tracking_number'] ?? ''; ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>
                                Track Package
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($trackingResult): ?>
        <!-- Tracking Results -->
        <div class="row">
            <div class="col-md-8 mx-auto">
                <!-- Package Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Package Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <small class="text-muted">Tracking Number</small>
                                    <div class="fw-bold"><?php echo $trackingResult['tracking_number']; ?></div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Status</small>
                                    <div><?php echo getStatusBadge($trackingResult['status']); ?></div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Current Location</small>
                                    <div class="fw-medium">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <?php echo $trackingResult['receiver_city']; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <small class="text-muted">From</small>
                                    <div class="fw-medium"><?php echo $trackingResult['sender_name']; ?></div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">To</small>
                                    <div class="fw-medium"><?php echo $trackingResult['receiver_name']; ?></div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Created Date</small>
                                    <div class="fw-medium"><?php echo formatDate($trackingResult['created_at'], 'M d, Y'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Delivery Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php
                            $timeline = [
                                ['status' => 'Order Placed', 'completed' => true, 'date' => $trackingResult['created_at']],
                                ['status' => 'Picked Up', 'completed' => in_array($trackingResult['status'], ['picked-up', 'in-transit', 'delivered']), 'date' => ''],
                                ['status' => 'In Transit', 'completed' => in_array($trackingResult['status'], ['in-transit', 'delivered']), 'date' => ''],
                                ['status' => 'Out for Delivery', 'completed' => $trackingResult['status'] === 'delivered', 'date' => ''],
                                ['status' => 'Delivered', 'completed' => $trackingResult['status'] === 'delivered', 'date' => $trackingResult['status'] === 'delivered' ? $trackingResult['updated_at'] : '']
                            ];
                            
                            foreach ($timeline as $index => $item):
                            ?>
                            <div class="timeline-item d-flex align-items-start mb-3">
                                <div class="timeline-marker me-3 mt-1">
                                    <div class="rounded-circle <?php echo $item['completed'] ? 'bg-primary' : 'bg-light border'; ?>" 
                                         style="width: 16px; height: 16px;"></div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 <?php echo $item['completed'] ? 'text-dark' : 'text-muted'; ?>">
                                            <?php echo $item['status']; ?>
                                        </h6>
                                        <?php if ($item['date']): ?>
                                        <small class="text-muted">
                                            <?php echo formatDate($item['date'], 'M d, Y g:i A'); ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.timeline-item:not(:last-child) .timeline-marker::after {
    content: '';
    position: absolute;
    left: 7px;
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