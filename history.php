<?php
require_once('../include/Session.php');
require_once('../include/Functions.php');
require_once('../include/Crud.php');
require_once("../include/Controller.php");
$dbConnection = new DbConnection();
$conn = $dbConnection->getConnection();

$Controller = new Controller();

$user_id =  $_SESSION['user_id'];

$DispatchList = $Controller->getDispatchList($user_id);
// var_dump($DispatchList);
// die();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

    <head>
        

        <meta charset="utf-8" />
                <title>History | Kingslist </title>
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
                <meta content="" name="author" />
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />

                <!-- App favicon -->
                <link rel="shortcut icon" href="assets/images/favicon.ico">

       
         <!-- App css -->
         <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
         <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
         <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
<style>
    .metric-card {
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #17ead9 0%, #6078ea 100%);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #43e695 0%, #3bb2b8 100%);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #fccf31 0%, #f55555 100%);
    }
    
    .metric-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-white-10 {
        background-color: rgba(255,255,255,0.1);
    }
    
    .text-white-70 {
        color: rgba(255,255,255,0.7);
    }
    
    .bg-light-alpha {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.03);
    }
    
    .rounded-pill {
        border-radius: 50px !important;
    }
</style>
<style>
    /* Additional styling for time ago text */
    .text-muted.small-time {
        font-size: 0.75rem;
        opacity: 0.8;
    }
    
    /* Hover effect for table rows */
    tr.position-relative:hover {
        background-color: rgba(13, 110, 253, 0.03);
        transition: background-color 0.2s ease;
    }
    
    /* Compact progress bar */
    .progress {
        min-width: 80px;
    }
</style>
    </head>

    
    <!-- Top Bar Start -->
    <body>
        <!-- Top Bar Start -->
          <?php include_once('./components/topbar.php') ?>

        <!-- Top Bar End -->
        <!-- leftbar-tab-menu -->
          <?php include_once('./components/leftbar.php') ?>

        <!-- end leftbar-tab-menu-->


        <div class="page-wrapper">

            <!-- Page Content-->
            <div class="page-content">
                <div class="container-xxl">
                    <div class="row g-4">
    <!-- Top Metrics Section -->
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-tachometer-alt me-2 text-primary"></i>Dispatch Overview
                    </h4>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown">
                            <i class="far fa-calendar-alt me-1"></i> Last 30 Days
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                            <li><a class="dropdown-item active" href="#">Last 30 Days</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <!-- Top Dispatch Card -->
                    <div class="col-md-6 col-lg-3">
                        <div class="metric-card bg-gradient-primary">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="metric-icon bg-white-10">
                                        <i class="fas fa-fire text-white"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 text-white"><?= max(array_column($DispatchList, 'dispatch_count')) ?></h3>
                                        <p class="text-white-70 mb-0">Top Dispatch</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-white-50">
                                        "<?= $DispatchList[array_search(max(array_column($DispatchList, 'dispatch_count')), $DispatchList)]['title'] ?>" campaign
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Dispatches Card -->
                    <div class="col-md-6 col-lg-3">
                        <div class="metric-card bg-gradient-info">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="metric-icon bg-white-10">
                                        <i class="fas fa-paper-plane text-white"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 text-white"><?= array_sum(array_column($DispatchList, 'dispatch_count')) ?></h3>
                                        <p class="text-white-70 mb-0">Total Dispatches</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-white-50">
                                        <?= count($DispatchList) ?> campaigns
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Success Rate Card -->
                    <div class="col-md-6 col-lg-3">
                        <div class="metric-card bg-gradient-success">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="metric-icon bg-white-10">
                                        <i class="fas fa-check-circle text-white"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 text-white">92%</h3>
                                        <p class="text-white-70 mb-0">Success Rate</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-white-50">
                                        <i class="fas fa-arrow-up me-1"></i> 3% from last month
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity Card -->
                    <div class="col-md-6 col-lg-3">
                        <div class="metric-card bg-gradient-warning">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="metric-icon bg-white-10">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h3 class="mb-0 text-white"><?= date('M j', strtotime($DispatchList[0]['created_at'])) ?></h3>
                                        <p class="text-white-70 mb-0">Last Activity</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <small class="text-white-50">
                                        <?= $DispatchList[0]['title'] ?> campaign
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                    
<div class="row">
    <!-- Dispatch History Section -->
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-history me-2 text-primary"></i>Dispatch History
                    </h4>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search campaigns...">
                        </div>
                        <button class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light-alpha">
                            <tr>
                                <th class="border-0 rounded-start">Campaign</th>
                                <th class="border-0">Content Preview</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Recipients</th>
                                <th class="border-0">Date</th>
                                <th class="border-0 rounded-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php 
    // Sort by created_at date descending (newest first)
    usort($DispatchList, function($a, $b) {
        return strtotime($b['created_at']) <=> strtotime($a['created_at']);
    });
    
    // Display campaigns (now sorted by most recent)
    foreach ($DispatchList as $dispatch): 
        $statusClass = [
            0 => 'warning',
            1 => 'info',
            2 => 'success',
            3 => 'danger',
            4 => 'secondary'
        ][$dispatch['status']] ?? 'secondary';
        
        $statusText = [
            0 => 'Pending',
            1 => 'Dispatching',
            2 => 'Complete',
            3 => 'Failed',
            4 => 'Incomplete'
        ][$dispatch['status']] ?? 'Unknown';
        
        // Calculate time ago
        $createdAt = new DateTime($dispatch['created_at']);
        $now = new DateTime();
        $interval = $now->diff($createdAt);
        $timeAgo = '';
        
        if ($interval->y > 0) {
            $timeAgo = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        } elseif ($interval->m > 0) {
            $timeAgo = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        } elseif ($interval->d > 0) {
            $timeAgo = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        } elseif ($interval->h > 0) {
            $timeAgo = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        } elseif ($interval->i > 0) {
            $timeAgo = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        } else {
            $timeAgo = 'Just now';
        }
    ?>
    <tr class="position-relative">
        <td>
            <div class="d-flex align-items-center">
                <div class="avatar-xs bg-primary bg-opacity-10 text-primary rounded me-2 p-2">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div>
                    <span class="fw-semibold d-block"><?= htmlspecialchars($dispatch['title']) ?></span>
                    <small class="text-muted"><?= $timeAgo ?></small>
                </div>
            </div>
        </td>
        <td>
            <a href="#" class="text-decoration-none preview-content" data-bs-toggle="popover" data-content="<?= htmlspecialchars($dispatch['body']) ?>">
                <i class="far fa-eye me-1 text-muted"></i> Preview
            </a>
        </td>
        <td>
            <span class="badge bg-<?= $statusClass ?> bg-opacity-10 text-<?= $statusClass ?> rounded-pill px-3 py-1">
                <?= $statusText ?>
            </span>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <span class="fw-bold me-2"><?= $dispatch['dispatch_count'] ?></span>
                <div class="progress flex-grow-1" style="height: 6px;">
                    <div class="progress-bar bg-<?= $statusClass ?>" role="progressbar" 
                         style="width: <?= $dispatch['status'] == 2 ? '100' : ($dispatch['status'] == 3 ? '100' : '60') ?>%" 
                         aria-valuenow="<?= $dispatch['status'] == 2 ? '100' : ($dispatch['status'] == 3 ? '100' : '60') ?>" 
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </td>
        <td>
            <div class="text-nowrap">
                <small class="d-block text-muted"><?= date('M j, Y', strtotime($dispatch['created_at'])) ?></small>
                <small><?= date('h:i A', strtotime($dispatch['created_at'])) ?></small>
            </div>
        </td>
        <td>
            <div class="d-flex gap-2">
                <?php if ($dispatch['status'] != 2): ?>
                    <a href="dispatchMessage.php?id=<?php echo $dispatch['dmsg_id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="fas fa-redo me-1"></i> Retry
                    </a>
                <?php endif; ?>
                <a href="dispatch-report.php?id=<?php echo $dispatch['dmsg_id']; ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-chart-bar me-1"></i> Report
                </a>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>


                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted">Showing 10 of <?= count($DispatchList) ?> campaigns</small>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize popovers for content preview
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            trigger: 'hover',
            placement: 'top',
            container: 'body',
            html: true,
            content: function() {
                return '<div class="p-2">' + popoverTriggerEl.getAttribute('data-content') + '</div>';
            }
        });
    });
});
</script>
</div>

                                
                </div><!-- container -->
                
                
                <!--Start Footer-->
                
                      <?php include_once('./components/footer.php') ?>

                
                <!--end footer-->
            </div>
            <!-- end page content -->
        </div>
        <!-- end page-wrapper -->

        <!-- Javascript  -->  
        <!-- vendor js -->
        
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
        <script src="assets/js/pages/analytics-reports.init.js"></script>
        <script src="assets/js/app.js"></script>
    </body>
    <!--end body-->
</html>