<?php
session_start();
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireRole(['admin']);

$pageTitle = 'Add New Courier';

if ($_POST) {
    $senderName = sanitizeInput($_POST['sender_name']);
    $senderContact = sanitizeInput($_POST['sender_contact']);
    $senderAddress = sanitizeInput($_POST['sender_address']);
    $receiverName = sanitizeInput($_POST['receiver_name']);
    $receiverContact = sanitizeInput($_POST['receiver_contact']);
    $receiverCity = sanitizeInput($_POST['receiver_city']);
    $receiverAddress = sanitizeInput($_POST['receiver_address']);
    $trackingNumber = sanitizeInput($_POST['tracking_number']);
    $status = sanitizeInput($_POST['status']);
    
    try {
        global $db;
        $query = "INSERT INTO couriers (tracking_number, sender_name, sender_contact, sender_address, 
                  receiver_name, receiver_contact, receiver_city, receiver_address, status, created_by, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $trackingNumber, $senderName, $senderContact, $senderAddress,
            $receiverName, $receiverContact, $receiverCity, $receiverAddress,
            $status, $_SESSION['user_id']
        ]);
        
        showAlert('Courier added successfully!', 'success');
        redirect('couriers.php');
    } catch (Exception $e) {
        showAlert('Error adding courier: ' . $e->getMessage(), 'danger');
    }
}

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="mb-4">
            <h1 class="h3">Add New Courier</h1>
            <p class="text-muted">Create a new courier shipment</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Courier Details</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <!-- Sender Information -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Sender Information</h6>
                            <div class="mb-3">
                                <label for="sender_name" class="form-label">Sender Name</label>
                                <input type="text" class="form-control" id="sender_name" name="sender_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="sender_contact" class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" id="sender_contact" name="sender_contact" required>
                            </div>
                            <div class="mb-3">
                                <label for="sender_address" class="form-label">Address</label>
                                <textarea class="form-control" id="sender_address" name="sender_address" rows="3" required></textarea>
                            </div>
                        </div>

                        <!-- Receiver Information -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Receiver Information</h6>
                            <div class="mb-3">
                                <label for="receiver_name" class="form-label">Receiver Name</label>
                                <input type="text" class="form-control" id="receiver_name" name="receiver_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="receiver_contact" class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" id="receiver_contact" name="receiver_contact" required>
                            </div>
                            <div class="mb-3">
                                <label for="receiver_city" class="form-label">City</label>
                                <input type="text" class="form-control" id="receiver_city" name="receiver_city" required>
                            </div>
                            <div class="mb-3">
                                <label for="receiver_address" class="form-label">Address</label>
                                <textarea class="form-control" id="receiver_address" name="receiver_address" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Courier Details -->
                    <h6 class="text-primary mb-3">Courier Details</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="tracking_number" class="form-label">Tracking Number</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="tracking_number" name="tracking_number" 
                                       value="<?php echo generateTrackingNumber(); ?>" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="generateNewTracking()">
                                    Generate
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="picked-up">Picked Up</option>
                                <option value="in-transit">In Transit</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Add Courier</button>
                        <a href="couriers.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function generateNewTracking() {
    const trackingInput = document.getElementById('tracking_number');
    const timestamp = Date.now().toString().slice(-8);
    trackingInput.value = 'CP' + timestamp;
}
</script>

<?php include '../includes/footer.php'; ?>