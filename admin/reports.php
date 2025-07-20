<?php
session_start();
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireRole(['admin']);

$pageTitle = 'Reports & Analytics';

// Handle report generation
if ($_POST && isset($_POST['generate_report'])) {
    $reportType = sanitizeInput($_POST['report_type']);
    $startDate = sanitizeInput($_POST['start_date']);
    $endDate = sanitizeInput($_POST['end_date']);
    
    // Generate CSV report based on type
    generateReport($reportType, $startDate, $endDate);
}

// Get report statistics
global $db;

// Monthly reports count
$monthlyReportsQuery = "SELECT COUNT(*) as count FROM couriers WHERE MONTH(created_at) = MONTH(CURRENT_DATE())";
$monthlyReports = $db->query($monthlyReportsQuery)->fetch(PDO::FETCH_ASSOC)['count'];

// Total deliveries
$totalDeliveriesQuery = "SELECT COUNT(*) as count FROM couriers WHERE status = 'delivered'";
$totalDeliveries = $db->query($totalDeliveriesQuery)->fetch(PDO::FETCH_ASSOC)['count'];

// Success rate
$totalCouriersQuery = "SELECT COUNT(*) as count FROM couriers WHERE status != 'pending'";
$totalCouriers = $db->query($totalCouriersQuery)->fetch(PDO::FETCH_ASSOC)['count'];
$successRate = $totalCouriers > 0 ? round(($totalDeliveries / $totalCouriers) * 100, 1) : 0;

// Average delivery time (mock data)
$avgDeliveryTime = "2.3 days";

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="mb-4">
            <h1 class="h3">Reports & Analytics</h1>
            <p class="text-muted">Generate and download business reports</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bar-chart fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Monthly Reports</h5>
                                <h2><?php echo $monthlyReports; ?></h2>
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
                                <h5 class="card-title">Total Deliveries</h5>
                                <h2><?php echo $totalDeliveries; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-trending-up fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Success Rate</h5>
                                <h2><?php echo $successRate; ?>%</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Avg. Delivery Time</h5>
                                <h2><?php echo $avgDeliveryTime; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Generate Report Form -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Generate Report</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="report_type" class="form-label">Report Type</label>
                                <select class="form-select" id="report_type" name="report_type" required>
                                    <option value="">Select Report Type</option>
                                    <option value="courier_summary">Courier Summary Report</option>
                                    <option value="agent_performance">Agent Performance Report</option>
                                    <option value="customer_activity">Customer Activity Report</option>
                                    <option value="delivery_status">Delivery Status Report</option>
                                </select>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="generate_report" class="btn btn-primary w-100">
                                <i class="bi bi-download me-2"></i>Download Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quick Reports -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                <div>
                                    <h6 class="mb-1">Today's Deliveries</h6>
                                    <small class="text-muted">All deliveries for today</small>
                                </div>
                                <a href="?quick_report=today" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                <div>
                                    <h6 class="mb-1">Weekly Summary</h6>
                                    <small class="text-muted">Last 7 days performance</small>
                                </div>
                                <a href="?quick_report=weekly" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                <div>
                                    <h6 class="mb-1">Monthly Analytics</h6>
                                    <small class="text-muted">Current month overview</small>
                                </div>
                                <a href="?quick_report=monthly" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                <div>
                                    <h6 class="mb-1">Agent Performance</h6>
                                    <small class="text-muted">All agents this month</small>
                                </div>
                                <a href="?quick_report=agents" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Handle quick reports
if (isset($_GET['quick_report'])) {
    $reportType = $_GET['quick_report'];
    $today = date('Y-m-d');
    
    switch ($reportType) {
        case 'today':
            generateReport('daily', $today, $today);
            break;
        case 'weekly':
            $weekAgo = date('Y-m-d', strtotime('-7 days'));
            generateReport('weekly', $weekAgo, $today);
            break;
        case 'monthly':
            $monthStart = date('Y-m-01');
            generateReport('monthly', $monthStart, $today);
            break;
        case 'agents':
            generateReport('agent_performance', $monthStart, $today);
            break;
    }
}

function generateReport($type, $startDate, $endDate) {
    global $db;
    
    $filename = $type . '_report_' . date('Y-m-d') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    switch ($type) {
        case 'courier_summary':
        case 'daily':
        case 'weekly':
        case 'monthly':
            fputcsv($output, ['Tracking Number', 'Sender', 'Receiver', 'Destination', 'Status', 'Created Date']);
            
            $query = "SELECT tracking_number, sender_name, receiver_name, receiver_city, status, created_at 
                      FROM couriers 
                      WHERE DATE(created_at) BETWEEN ? AND ? 
                      ORDER BY created_at DESC";
            $stmt = $db->prepare($query);
            $stmt->execute([$startDate, $endDate]);
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }
            break;
            
        case 'agent_performance':
            fputcsv($output, ['Agent Name', 'Email', 'Total Assigned', 'Delivered', 'Success Rate']);
            
            $query = "SELECT u.name, u.email, 
                      COUNT(c.id) as total_assigned,
                      SUM(CASE WHEN c.status = 'delivered' THEN 1 ELSE 0 END) as delivered
                      FROM users u 
                      LEFT JOIN couriers c ON u.id = c.assigned_agent 
                      WHERE u.role = 'agent' 
                      GROUP BY u.id";
            $stmt = $db->query($query);
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $successRate = $row['total_assigned'] > 0 ? 
                    round(($row['delivered'] / $row['total_assigned']) * 100, 1) . '%' : '0%';
                fputcsv($output, [
                    $row['name'], 
                    $row['email'], 
                    $row['total_assigned'], 
                    $row['delivered'], 
                    $successRate
                ]);
            }
            break;
    }
    
    fclose($output);
    exit();
}

include '../includes/footer.php';
?>