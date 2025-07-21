<?php
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateTrackingNumber() {
    return 'CP' . date('Ymd') . rand(1000, 9999);
}

function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

function getStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge badge-secondary">Pending</span>',
        'picked-up' => '<span class="badge badge-primary">Picked Up</span>',
        'in-transit' => '<span class="badge badge-info">In Transit</span>',
        'delivered' => '<span class="badge badge-success">Delivered</span>',
        'cancelled' => '<span class="badge badge-danger">Cancelled</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge badge-secondary">Unknown</span>';
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
        'limit' => $recordsPerPage
    ];
}
?>