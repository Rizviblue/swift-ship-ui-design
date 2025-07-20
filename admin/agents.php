<?php
session_start();
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireRole(['admin']);

$pageTitle = 'Agent Management';

// Handle form submission for adding new agent
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'add_agent') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $password = password_hash('agent123', HASH_ALGO); // Default password
    
    try {
        global $db;
        $query = "INSERT INTO users (name, email, password, role, phone, status, created_at) 
                  VALUES (?, ?, ?, 'agent', ?, 'active', NOW())";
        $stmt = $db->prepare($query);
        $stmt->execute([$name, $email, $password, $phone]);
        
        showAlert('Agent added successfully! Default password: agent123', 'success');
    } catch (Exception $e) {
        showAlert('Error adding agent: ' . $e->getMessage(), 'danger');
    }
}

// Get agents
global $db;
$query = "SELECT u.*, COUNT(c.id) as total_couriers 
          FROM users u 
          LEFT JOIN couriers c ON u.id = c.assigned_agent 
          WHERE u.role = 'agent' 
          GROUP BY u.id 
          ORDER BY u.created_at DESC";
$stmt = $db->query($query);
$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$statsQuery = "SELECT 
    COUNT(*) as total_agents,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_agents,
    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_agents
    FROM users WHERE role = 'agent'";
$statsStmt = $db->query($statsQuery);
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="mb-4">
            <h1 class="h3">Agent Management</h1>
            <p class="text-muted">Manage courier agents and staff</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-people fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Total Agents</h5>
                                <h2><?php echo $stats['total_agents']; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-check fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Active</h5>
                                <h2><?php echo $stats['active_agents']; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-x fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Inactive</h5>
                                <h2><?php echo $stats['inactive_agents']; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-building fs-1 me-3"></i>
                            <div>
                                <h5 class="card-title">Branches</h5>
                                <h2>5</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agents Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">All Agents</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAgentModal">
                    <i class="bi bi-plus-circle me-2"></i>Add Agent
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Total Couriers</th>
                                <th>Status</th>
                                <th>Join Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agents as $agent): ?>
                            <tr>
                                <td><?php echo $agent['name']; ?></td>
                                <td><?php echo $agent['email']; ?></td>
                                <td><?php echo $agent['phone'] ?? 'N/A'; ?></td>
                                <td><?php echo $agent['total_couriers']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $agent['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($agent['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($agent['created_at'], 'M d, Y'); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="view-agent.php?id=<?php echo $agent['id']; ?>" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit-agent.php?id=<?php echo $agent['id']; ?>" 
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Agent Modal -->
<div class="modal fade" id="addAgentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Agent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_agent">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="alert alert-info">
                        <small>Default password will be: <strong>agent123</strong></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Agent</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>