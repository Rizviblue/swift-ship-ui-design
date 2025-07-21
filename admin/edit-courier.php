<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireRole(['admin']);

$pageTitle = 'Edit Courier';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('couriers.php');
}

$courierId = intval($_GET['id']);

// Get courier details
global $db;
$query = "SELECT * FROM couriers WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$courierId]);
$courier = $stmt->fetch();

if (!$courier) {
    showAlert('Courier not found!', 'danger');
    redirect('couriers.php');
}

if ($_POST) {
    $senderName = sanitizeInput($_POST['sender_name']);
    $senderContact = sanitizeInput($_POST['sender_contact']);
    $senderAddress = sanitizeInput($_POST['sender_address']);
    $receiverName = sanitizeInput($_POST['receiver_name']);
    $receiverContact = sanitizeInput($_POST['receiver_contact']);
    $receiverCity = sanitizeInput($_POST['receiver_city']);
    $receiverAddress = sanitizeInput($_POST['receiver_address']);
    $status = sanitizeInput($_POST['status']);
    $weight = floatval($_POST['weight'] ?? 0);
    $dimensions = sanitizeInput($_POST['dimensions'] ?? '');
    $deliveryFee = floatval($_POST['delivery_fee'] ?? 0);
    $assignedAgent = !empty($_POST['assigned_agent']) ? intval($_POST['assigned_agent']) : null;
    $branchId = !empty($_POST['branch_id']) ? intval($_POST['branch_id']) : null;
    $notes = sanitizeInput($_POST['notes'] ?? '');
    
    try {
        $updateQuery = "UPDATE couriers SET 
                        sender_name = ?, sender_contact = ?, sender_address = ?,
                        receiver_name = ?, receiver_contact = ?, receiver_city = ?, receiver_address = ?,
                        status = ?, weight = ?, dimensions = ?, delivery_fee = ?,
                        assigned_agent = ?, branch_id = ?, notes = ?, updated_at = NOW()
                        WHERE id = ?";
        
        $stmt = $db->prepare($updateQuery);
        $stmt->execute([
            $senderName, $senderContact, $senderAddress,
            $receiverName, $receiverContact, $receiverCity, $receiverAddress,
            $status, $weight, $dimensions, $deliveryFee,
            $assignedAgent, $branchId, $notes, $courierId
        ]);
        
        // Add status history if status changed
        if ($status !== $courier['status']) {
            addCourierStatusHistory($courierId, $status, null, 'Status updated by admin');
        }
        
        showAlert('Courier updated successfully!', 'success');
        redirect('view-courier.php?id=' . $courierId);
    } catch (Exception $e) {
        showAlert('Error updating courier: ' . $e->getMessage(), 'danger');
    }
}

// Get agents and branches for dropdowns
$agentsQuery = "SELECT id, name FROM users WHERE role = 'agent' AND status = 'active' ORDER BY name";
$agents = $db->query($agentsQuery)->fetchAll();

$branchesQuery = "SELECT id, name FROM branches WHERE status = 'active' ORDER BY name";
$branches = $db->query($branchesQuery)->fetchAll();

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3">Edit Courier</h1>
                    <p class="text-muted">Tracking Number: <?php echo $courier['tracking_number']; ?></p>
                </div>
                <div>
                    <a href="view-courier.php?id=<?php echo $courier['id']; ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Courier Details</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <!-- Sender Information -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Sender Information</h6>
                            <div class="mb-3">
                                <label for="sender_name" class="form-label">Sender Name *</label>
                                <input type="text" class="form-control" id="sender_name" name="sender_name" 
                                       value="<?php echo htmlspecialchars($courier['sender_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="sender_contact" class="form-label">Contact Number *</label>
                                <input type="tel" class="form-control" id="sender_contact" name="sender_contact" 
                                       value="<?php echo htmlspecialchars($courier['sender_contact']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="sender_address" class="form-label">Address *</label>
                                <textarea class="form-control" id="sender_address" name="sender_address" rows="3" required><?php echo htmlspecialchars($courier['sender_address']); ?></textarea>
                            </div>
                        </div>

                        <!-- Receiver Information -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Receiver Information</h6>
                            <div class="mb-3">
                                <label for="receiver_name" class="form-label">Receiver Name *</label>
                                <input type="text" class="form-control" id="receiver_name" name="receiver_name" 
                                       value="<?php echo htmlspecialchars($courier['receiver_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="receiver_contact" class="form-label">Contact Number *</label>
                                <input type="tel" class="form-control" id="receiver_contact" name="receiver_contact" 
                                       value="<?php echo htmlspecialchars($courier['receiver_contact']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="receiver_city" class="form-label">City *</label>
                                <input type="text" class="form-control" id="receiver_city" name="receiver_city" 
                                       value="<?php echo htmlspecialchars($courier['receiver_city']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="receiver_address" class="form-label">Address *</label>
                                <textarea class="form-control" id="receiver_address" name="receiver_address" rows="3" required><?php echo htmlspecialchars($courier['receiver_address']); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Courier Details -->
                    <h6 class="text-primary mb-3">Courier Details</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" <?php echo $courier['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="picked-up" <?php echo $courier['status'] === 'picked-up' ? 'selected' : ''; ?>>Picked Up</option>
                                <option value="in-transit" <?php echo $courier['status'] === 'in-transit' ? 'selected' : ''; ?>>In Transit</option>
                                <option value="delivered" <?php echo $courier['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $courier['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="delivery_fee" class="form-label">Delivery Fee ($)</label>
                            <input type="number" step="0.01" class="form-control" id="delivery_fee" name="delivery_fee" 
                                   value="<?php echo $courier['delivery_fee']; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="weight" class="form-label">Weight (kg)</label>
                            <input type="number" step="0.01" class="form-control" id="weight" name="weight" 
                                   value="<?php echo $courier['weight']; ?>">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label for="dimensions" class="form-label">Dimensions</label>
                            <input type="text" class="form-control" id="dimensions" name="dimensions" 
                                   value="<?php echo htmlspecialchars($courier['dimensions']); ?>" placeholder="L x W x H">
                        </div>
                        <div class="col-md-4">
                            <label for="assigned_agent" class="form-label">Assigned Agent</label>
                            <select class="form-select" id="assigned_agent" name="assigned_agent">
                                <option value="">Select Agent</option>
                                <?php foreach ($agents as $agent): ?>
                                    <option value="<?php echo $agent['id']; ?>" 
                                            <?php echo $courier['assigned_agent'] == $agent['id'] ? 'selected' : ''; ?>>
                                        <?php echo $agent['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="branch_id" class="form-label">Branch</label>
                            <select class="form-select" id="branch_id" name="branch_id">
                                <option value="">Select Branch</option>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?php echo $branch['id']; ?>" 
                                            <?php echo $courier['branch_id'] == $branch['id'] ? 'selected' : ''; ?>>
                                        <?php echo $branch['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Additional notes or instructions"><?php echo htmlspecialchars($courier['notes']); ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Courier</button>
                        <a href="view-courier.php?id=<?php echo $courier['id']; ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>