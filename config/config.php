<?php
// Application Configuration
define('APP_NAME', 'CourierPro');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/courier-management/');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'courier_management');
define('DB_USER', 'root');
define('DB_PASS', '');

// Security
define('HASH_ALGO', PASSWORD_DEFAULT);
define('SESSION_LIFETIME', 3600); // 1 hour

// File Upload
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB

// Pagination
define('RECORDS_PER_PAGE', 10);

// Status Constants
define('STATUS_PENDING', 'pending');
define('STATUS_PICKED_UP', 'picked-up');
define('STATUS_IN_TRANSIT', 'in-transit');
define('STATUS_DELIVERED', 'delivered');
define('STATUS_CANCELLED', 'cancelled');
?>