<?php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function generateTrackingNumber() {
    return 'CP' . date('Ymd') . rand(1000, 9999);
}

function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

function getStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge bg-secondary">Pending</span>',
        'picked-up' => '<span class="badge bg-primary">Picked Up</span>',
        'in-transit' => '<span class="badge bg-info">In Transit</span>',
        'delivered' => '<span class="badge bg-success">Delivered</span>',
        'cancelled' => '<span class="badge bg-danger">Cancelled</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function showAlert($message, $type = 'info') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

function displayAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo "<div class='alert alert-{$alert['type']} alert-dismissible fade show' role='alert'>
                {$alert['message']}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
        unset($_SESSION['alert']);
    }
}

function paginate($totalRecords, $currentPage, $recordsPerPage = RECORDS_PER_PAGE) {
    $totalPages = ceil($totalRecords / $recordsPerPage);
    $offset = ($currentPage - 1) * $recordsPerPage;
    
    return [
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'offset' => $offset,
        'limit' => $recordsPerPage,
        'total_records' => $totalRecords
    ];
}

function getCourierStats() {
    global $db;
    
    $stats = [];
    
    // Total couriers
    $stmt = $db->query("SELECT COUNT(*) as total FROM couriers");
    $stats['total'] = $stmt->fetch()['total'];
    
    // Status counts
    $statusQuery = "SELECT status, COUNT(*) as count FROM couriers GROUP BY status";
    $stmt = $db->query($statusQuery);
    while ($row = $stmt->fetch()) {
        $stats[$row['status']] = $row['count'];
    }
    
    // Set defaults for missing statuses
    $statuses = ['pending', 'picked-up', 'in-transit', 'delivered', 'cancelled'];
    foreach ($statuses as $status) {
        if (!isset($stats[$status])) {
            $stats[$status] = 0;
        }
    }
    
    return $stats;
}

function getRecentCouriers($limit = 5) {
    global $db;
    
    $query = "SELECT c.*, u.name as created_by_name 
              FROM couriers c 
              LEFT JOIN users u ON c.created_by = u.id 
              ORDER BY c.created_at DESC 
              LIMIT ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$limit]);
    
    return $stmt->fetchAll();
}

function addCourierStatusHistory($courierId, $status, $location = null, $notes = null, $updatedBy = null) {
    global $db;
    
    if (!$updatedBy) {
        $updatedBy = $_SESSION['user_id'];
    }
    
    $query = "INSERT INTO courier_status_history (courier_id, status, location, notes, updated_by, created_at) 
              VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $db->prepare($query);
    
    return $stmt->execute([$courierId, $status, $location, $notes, $updatedBy]);
}

function getCourierHistory($courierId) {
    global $db;
    
    $query = "SELECT csh.*, u.name as updated_by_name 
              FROM courier_status_history csh 
              LEFT JOIN users u ON csh.updated_by = u.id 
              WHERE csh.courier_id = ? 
              ORDER BY csh.created_at ASC";
    $stmt = $db->prepare($query);
    $stmt->execute([$courierId]);
    
    return $stmt->fetchAll();
}

function exportToCSV($data, $filename, $headers = []) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    if (!empty($headers)) {
        fputcsv($output, $headers);
    }
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    return preg_match('/^[\+]?[1-9][\d]{0,15}$/', $phone);
}

function getSettings() {
    global $db;
    
    $query = "SELECT setting_key, setting_value FROM settings";
    $stmt = $db->query($query);
    $settings = [];
    
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}

function updateSetting($key, $value, $updatedBy = null) {
    global $db;
    
    if (!$updatedBy) {
        $updatedBy = $_SESSION['user_id'] ?? 1;
    }
    
    $query = "UPDATE settings SET setting_value = ?, updated_by = ?, updated_at = NOW() WHERE setting_key = ?";
    $stmt = $db->prepare($query);
    
    return $stmt->execute([$value, $updatedBy, $key]);
}
?>