<?php
session_start();
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireRole(['admin']);

$pageTitle = 'System Settings';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['update_company'])) {
        updateSettings([
            'company_name' => $_POST['company_name'],
            'company_email' => $_POST['company_email'],
            'company_phone' => $_POST['company_phone'],
            'company_address' => $_POST['company_address']
        ]);
        showAlert('Company information updated successfully!', 'success');
    }
    
    if (isset($_POST['update_delivery'])) {
        updateSettings([
            'default_delivery_time' => $_POST['default_delivery_time'],
            'max_delivery_time' => $_POST['max_delivery_time'],
            'base_delivery_fee' => $_POST['base_delivery_fee'],
            'per_km_rate' => $_POST['per_km_rate']
        ]);
        showAlert('Delivery settings updated successfully!', 'success');
    }
    
    if (isset($_POST['update_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if ($newPassword === $confirmPassword) {
            // Verify current password and update
            global $db;
            $query = "SELECT password FROM users WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, HASH_ALGO);
                $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
                $updateStmt = $db->prepare($updateQuery);
                $updateStmt->execute([$hashedPassword, $_SESSION['user_id']]);
                
                showAlert('Password updated successfully!', 'success');
            } else {
                showAlert('Current password is incorrect!', 'danger');
            }
        } else {
            showAlert('New passwords do not match!', 'danger');
        }
    }
}

// Get current settings
$settings = getSettings();

function getSettings() {
    global $db;
    $query = "SELECT setting_key, setting_value FROM settings";
    $stmt = $db->query($query);
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

function updateSettings($newSettings) {
    global $db;
    foreach ($newSettings as $key => $value) {
        $query = "UPDATE settings SET setting_value = ?, updated_by = ?, updated_at = NOW() WHERE setting_key = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$value, $_SESSION['user_id'], $key]);
    }
}

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="mb-4">
            <h1 class="h3">System Settings</h1>
            <p class="text-muted">Manage system settings and preferences</p>
        </div>

        <div class="row">
            <!-- Company Information -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Company Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="<?php echo $settings['company_name'] ?? 'CourierPro Ltd.'; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="company_email" class="form-label">Company Email</label>
                                <input type="email" class="form-control" id="company_email" name="company_email" 
                                       value="<?php echo $settings['company_email'] ?? 'contact@courierpro.com'; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="company_phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="company_phone" name="company_phone" 
                                       value="<?php echo $settings['company_phone'] ?? '+1-555-0100'; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="company_address" class="form-label">Address</label>
                                <textarea class="form-control" id="company_address" name="company_address" rows="3" required><?php echo $settings['company_address'] ?? '123 Business Street, Corporate City, State 12345'; ?></textarea>
                            </div>
                            <button type="submit" name="update_company" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- System Preferences -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">System Preferences</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label class="form-label mb-0">Email Notifications</label>
                                    <small class="text-muted d-block">Send email notifications for important events</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" checked>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label class="form-label mb-0">SMS Notifications</label>
                                    <small class="text-muted d-block">Send SMS updates to customers</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sms_notifications" checked>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <label class="form-label mb-0">Auto Status Updates</label>
                                    <small class="text-muted d-block">Automatically update courier status</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_updates">
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="form-label mb-0">Customer Portal</label>
                                    <small class="text-muted d-block">Enable customer self-service portal</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="customer_portal" checked>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-primary">Save Preferences</button>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Security Settings</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delivery Settings -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Delivery Settings</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="default_delivery_time" class="form-label">Default Delivery Time (hours)</label>
                                <input type="number" class="form-control" id="default_delivery_time" name="default_delivery_time" 
                                       value="<?php echo $settings['default_delivery_time'] ?? '48'; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="max_delivery_time" class="form-label">Maximum Delivery Time (hours)</label>
                                <input type="number" class="form-control" id="max_delivery_time" name="max_delivery_time" 
                                       value="<?php echo $settings['max_delivery_time'] ?? '168'; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="base_delivery_fee" class="form-label">Base Delivery Fee ($)</label>
                                <input type="number" step="0.01" class="form-control" id="base_delivery_fee" name="base_delivery_fee" 
                                       value="<?php echo $settings['base_delivery_fee'] ?? '10.00'; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="per_km_rate" class="form-label">Per KM Rate ($)</label>
                                <input type="number" step="0.01" class="form-control" id="per_km_rate" name="per_km_rate" 
                                       value="<?php echo $settings['per_km_rate'] ?? '1.50'; ?>" required>
                            </div>
                            <button type="submit" name="update_delivery" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>