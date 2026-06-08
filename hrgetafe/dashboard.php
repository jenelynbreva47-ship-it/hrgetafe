<?php
/**
 * DASHBOARD PAGE
 * HRGetafe - Human Resources Information System
 */

require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

requireLogin();
$current_user = getCurrentUser();
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="content-wrapper p-4">
                
                <!-- Welcome Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                            <div class="card-body">
                                <h2 class="card-title">Welcome, <?php echo htmlspecialchars($current_user['first_name'] ?? $_SESSION['username']); ?>!</h2>
                                <p class="card-text mb-0">
                                    Role: <strong><?php echo getRoleName($_SESSION['role']); ?></strong> | 
                                    Last Login: <strong><?php echo date('F j, Y g:i A'); ?></strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Stats -->
                <div class="row mb-4">
                    <!-- Total Employees -->
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-2">Total Employees</p>
                                        <?php
                                        $result = $conn->query("SELECT COUNT(*) as count FROM employees WHERE status = 1");
                                        $count = $result->fetch_assoc()['count'];
                                        ?>
                                        <h3 class="mb-0"><?php echo $count; ?></h3>
                                    </div>
                                    <div class="text-primary" style="font-size: 40px;">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Present Today -->
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-2">Present Today</p>
                                        <?php
                                        $today = date('Y-m-d');
                                        $result = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE date_attendance = '$today' AND status = 'Present'");
                                        $present = $result->fetch_assoc()['count'];
                                        ?>
                                        <h3 class="mb-0"><?php echo $present; ?></h3>
                                    </div>
                                    <div class="text-success" style="font-size: 40px;">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pending Leave Requests -->
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-2">Pending Leaves</p>
                                        <?php
                                        $result = $conn->query("SELECT COUNT(*) as count FROM leave_requests WHERE status = 'Pending'");
                                        $pending = $result->fetch_assoc()['count'];
                                        ?>
                                        <h3 class="mb-0"><?php echo $pending; ?></h3>
                                    </div>
                                    <div class="text-warning" style="font-size: 40px;">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pending Payroll -->
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-2">Payroll (Draft)</p>
                                        <?php
                                        $result = $conn->query("SELECT COUNT(*) as count FROM payroll WHERE status = 'Draft'");
                                        $draft = $result->fetch_assoc()['count'];
                                        ?>
                                        <h3 class="mb-0"><?php echo $draft; ?></h3>
                                    </div>
                                    <div class="text-info" style="font-size: 40px;">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0"><i class="fas fa-history"></i> Recent Activities</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $query = "SELECT a.*, u.username FROM audit_logs a 
                                         LEFT JOIN users u ON a.user_id = u.id 
                                         ORDER BY a.created_at DESC LIMIT 10";
                                $result = $conn->query($query);
                                
                                if ($result->num_rows > 0):
                                ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>User</th>
                                                <th>Action</th>
                                                <th>Module</th>
                                                <th>Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($activity = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <small><?php echo htmlspecialchars($activity['username'] ?? 'System'); ?></small>
                                                </td>
                                                <td>
                                                    <small><?php echo htmlspecialchars($activity['action']); ?></small>
                                                </td>
                                                <td>
                                                    <small><span class="badge bg-info"><?php echo htmlspecialchars($activity['module']); ?></span></small>
                                                </td>
                                                <td>
                                                    <small><?php echo date('M d, Y g:i A', strtotime($activity['created_at'])); ?></small>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle"></i> No activities yet.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
