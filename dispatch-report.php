<?php
/*ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);*/

require_once('../include/Session.php');
require_once('../include/Functions.php');
require_once('../include/Crud.php');
require_once("../include/Controller.php");

$dbConnection = new DbConnection();
$conn = $dbConnection->getConnection();

$Controller = new Controller();

// Get dispatch ID from request
$dmsg_id = sanitizeString($_GET['id']);
// $dmsg_id = sanitizeString($secondparam);
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// Fetch dispatch log data
$dispatch_log = $Controller->message_dispatch_log($user_id, $dmsg_id);
$getDispatchLogDetails = $Controller->getDispatchLogDetails($dmsg_id);
$details = $getDispatchLogDetails[0];
// var_dump($dispatch_log);
// die(); // Debugging line to check the contents of $details
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>


    <meta charset="utf-8" />
    <title>Dispatch Report | Kingslist</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">


    <link href="assets/libs/simple-datatables/style.css" rel="stylesheet" type="text/css" />
    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />

    <style>
        /* Custom Styles */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }

        .text-white-70 {
            color: rgba(255, 255, 255, 0.7);
        }

        .bg-light-alpha {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .avatar-xs {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #dispatch-log-table th {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        #dispatch-log-table tbody tr {
            transition: all 0.2s ease;
        }

        #dispatch-log-table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.03);
        }

        .export-buttons .btn {
            transition: all 0.2s ease;
        }

        .export-buttons .btn:hover {
            transform: translateY(-2px);
        }

        .error-details {
            transition: all 0.2s ease;
        }

        .error-details:hover {
            color: #dc3545 !important;
        }

        .rounded-start {
            border-top-left-radius: 12px !important;
            border-bottom-left-radius: 12px !important;
        }

        .rounded-end {
            border-top-right-radius: 12px !important;
            border-bottom-right-radius: 12px !important;
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



                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="card shadow-lg border-0 mb-4 overflow-hidden">
                            <!-- Card Header with Gradient Background -->
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                    <div class="mb-2 mb-md-0">
                                        <h5 class="mb-1 text-white">
                                            <i class="fas fa-file-alt me-2"></i>Dispatch Report
                                        </h5>
                                        <p class="mb-0 text-white-70"><?= htmlspecialchars($details['title']) ?></p>
                                    </div>
                                    <div class="export-buttons d-flex flex-wrap gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-light rounded-pill export-btn" data-type="csv">
                                            <i class="fas fa-file-csv me-1"></i> CSV
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-light rounded-pill export-btn" data-type="sql">
                                            <i class="fas fa-database me-1"></i> SQL
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-light rounded-pill export-btn" data-type="txt">
                                            <i class="fas fa-file-alt me-1"></i> TXT
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-light rounded-pill export-btn" data-type="json">
                                            <i class="fas fa-code me-1"></i> JSON
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table id="dispatch-log-table" class="table table-hover align-middle mb-0">
                                        <thead class="bg-light-alpha">
                                            <tr>
                                                <th class="border-0 rounded-start">Recipient</th>
                                                <th class="border-0">Profile</th>
                                                <th class="border-0">Status</th>
                                                <th class="border-0">Details</th>
                                                <th class="border-0 rounded-end">Sent At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($dispatch_log)): ?>
                                                <?php foreach ($dispatch_log as $log): ?>
                                                    <tr class="position-relative">
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-xs bg-primary bg-opacity-10 text-primary rounded-circle me-2">
                                                                    <i class="fas fa-user"></i>
                                                                </div>
                                                                <div>
                                                                    <div class="fw-semibold"><?= htmlspecialchars($log['fullname'] ?? 'N/A') ?></div>
                                                                    <small class="text-muted">@<?= htmlspecialchars($log['kc_username'] ?? 'N/A') ?></small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <a href="https://kingschat.online/user/<?= htmlspecialchars($log['kc_username'] ?? '') ?>"
                                                                target="_blank"
                                                                class="btn btn-sm btn-outline-primary rounded-pill">
                                                                <i class="fas fa-external-link-alt me-1"></i> View
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?= $log['status'] === 'success' ? 'success' : 'danger' ?> bg-opacity-10 text-<?= $log['status'] === 'success' ? 'success' : 'danger' ?> rounded-pill px-3 py-1">
                                                                <i class="fas fa-<?= $log['status'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> me-1"></i>
                                                                <?= ucfirst(htmlspecialchars($log['status'] ?? 'pending')) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($log['error'])): ?>
                                                                <a href="#" class="text-decoration-none error-details"
                                                                    data-bs-toggle="tooltip"
                                                                    title="<?= htmlspecialchars($log['error']) ?>">
                                                                    <i class="fas fa-info-circle text-danger"></i> Details
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-muted">No errors</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <small class="text-muted"><?= date('M j, Y', strtotime($log['created_at'])) ?></small>
                                                                <small><?= date('h:i A', strtotime($log['created_at'])) ?></small>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
    <td colspan="5" class="text-center py-5">
        <i class="fas fa-circle-notch fa-spin fa-3x text-primary mb-3"></i>
        <h5 class="text-primary">Processing dispatch logs</h5>
        <p class="text-muted small mb-0">Please wait while we gather the information...</p>
    </td>
</tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer bg-transparent border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Showing <?= count($dispatch_log) ?> records
                                    </small>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                                            <i class="fas fa-filter me-1"></i> Filter
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#" data-filter="all">All</a></li>
                                            <li><a class="dropdown-item" href="#" data-filter="success">Success Only</a></li>
                                            <li><a class="dropdown-item" href="#" data-filter="failed">Failed Only</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Initialize tooltips
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });

                        // Filter functionality
                        document.querySelectorAll('[data-filter]').forEach(item => {
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                const filter = this.getAttribute('data-filter');
                                const rows = document.querySelectorAll('#dispatch-log-table tbody tr');

                                rows.forEach(row => {
                                    if (filter === 'all') {
                                        row.style.display = '';
                                    } else {
                                        const status = row.querySelector('.badge').textContent.toLowerCase().trim();
                                        row.style.display = status.includes(filter) ? '' : 'none';
                                    }
                                });
                            });
                        });

                        // Export buttons (placeholder functionality)
                        document.querySelectorAll('.export-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const type = this.getAttribute('data-type');
                                alert(`Exporting as ${type.toUpperCase()}...`);
                                // Implement actual export functionality here
                            });
                        });
                    });
                </script>
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

    <script src="assets/libs/simple-datatables/umd/simple-datatables.js"></script>
    <script src="assets/js/pages/datatable.init.js"></script>

    <script src="assets/js/app.js"></script>

</body>
<!--end body-->

</html>