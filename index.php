<?php
require_once('../include/Session.php');
require_once('../include/Functions.php');
require_once('../include/Crud.php');
require_once("../include/Controller.php");
$dbConnection = new DbConnection();
$conn = $dbConnection->getConnection();
$user_id =  $_SESSION['user_id'];

$Controller = new Controller();
$fullname = $_SESSION['fullname'];
$recentTransactionHistory = $Controller->recentTransactionHistory($user_id);
$recentDispatch = $Controller->recentDispatch($user_id);
// var_dump($recentDispatch);
// die();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>


    <meta charset="utf-8" />
    <title>Dashboard | Kingslist </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="https://kingslist.pro/app_new_v2/assets/images/kingslist.png">

    <!-- Font Awesome (version 6) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Google Fonts - Poppins (sleek modern) + Open Sans (clean readable) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/libs/jsvectormap/css/jsvectormap.min.css">

    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <style>
        .dashboard-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.3s ease;
            background: linear-gradient(145deg, #ffffff, #f7f7f7);
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .card-body h4 {
            font-size: 18px;
            font-weight: 600;
        }

        .card-body h3 {
            font-size: 30px;
            font-weight: 700;
        }

        .card-body p {
            font-size: 14px;
        }

        .thumb-xl {
            height: 60px;
            width: 60px;
            font-size: 28px;
        }

        .btn-sm {
            margin-top: 10px;
        }

        @media (max-width: 991.98px) {
            .col-lg-3 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 20px;
            }
        }

        .btn-gradient-primary {
            background: linear-gradient(to right, #4e73df, #1cc88a);
            color: white;
            border: none;
            transition: 0.3s ease-in-out;
        }

        .btn-gradient-primary:hover {
            background: linear-gradient(to right, #1cc88a, #4e73df);
            transform: scale(1.03);
            color: #fff;
        }
    </style>
    <style>
        .table {
            --bs-table-bg: transparent;
            --bs-table-striped-bg: rgba(13, 110, 253, 0.02);
            --bs-table-hover-bg: rgba(13, 110, 253, 0.05);
        }

        .table thead th {
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
        }

        .table tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .table tbody tr:last-child {
            border-bottom: 0;
        }

        .table-hover tbody tr:hover {
            transform: translateX(4px);
        }

        .badge {
            font-weight: 500;
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

    <style>
        /* Add these styles to your CSS */
        .dashboard-card {
            border-radius: 12px;
            border: none;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .welcome-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9f5ff 100%);
            border-left: 4px solid #0d6efd;
        }

        .welcome-icon {
            font-size: 2.5rem;
            color: rgba(13, 110, 253, 0.2);
        }

        .text-gradient-primary {
            background: linear-gradient(to right, #0d6efd, #00b4ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hover-shadow-sm:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .thumb-xl {
            width: 50px;
            height: 50px;
        }

        .bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-soft {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-warning-soft {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .rounded-pill {
            border-radius: 50px !important;
        }

        .display-6 {
            font-size: 1.75rem;
        }
    </style>
    <style>
        .bg-purple {
            background-color: #6f42c1 !important;
        }

        .text-purple {
            color: #6f42c1 !important;
        }

        .bg-light-alpha {
            background-color: rgba(111, 66, 193, 0.05);
        }

        .dispatch-row:hover {
            cursor: pointer;
            background-color: rgba(111, 66, 193, 0.03) !important;
        }

        .progress {
            width: 80px;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .avatar-xs {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-content:hover {
            color: #6f42c1 !important;
        }
        
    </style>

</head>

<body>

    <!-- Top Bar Start -->

    <?php include_once('./components/topbar.php') ?>
    <!-- Top Bar End -->

    <!-- leftbar-tab-menu -->
    <?php include_once('./components/leftbar.php') ?>

    <!--end startbar-->

    <!-- end leftbar-tab-menu-->

    <div class="page-wrapper">

        <!-- Page Content-->
        <div class="page-content">
            <div class="container-xxl">


                <div class="row g-4">
    <!-- Welcome Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm welcome-card-gradient">
            <div class="card-body text-center p-3">
                <div class="welcome-icon mb-3">
                    <div class="avatar-lg bg-white-10 text-white rounded-circle d-flex align-items-center justify-content-center mx-auto">
                        <i class="fa-regular fa-face-smile fa-lg"></i>
                    </div>
                </div>
                <h4 class="mt-0 fw-bold text-white">Welcome <?= $fullname ?></h4>
                <h5 class="text-white-70 my-2" id="message">Loading your dashboard...</h5>
                <a id="action-link" class="btn btn-white btn-sm w-100 mt-2 rounded-pill shadow-sm" href="#">
                    <span id="action-text">Get started <i class="fas fa-arrow-right ms-1"></i></span>
                </a>
            </div>
        </div>
    </div>

    <!-- List Count Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm list-card-gradient">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm bg-white-10 text-white rounded-circle me-3">
                        <i class="fas fa-list"></i>
                    </div>
                    <div>
                        <h3 class="text-white mt-1 mb-0 fw-bold" id="userNamelistTotal">--</h3>
                        <p class="text-white-70 mb-0 small">Your Lists</p>
                    </div>
                </div>
                <div class="progress bg-white-20 mb-3" style="height: 6px;">
                    <div class="progress-bar bg-white" style="width: 65%"></div>
                </div>
                <a href="lists.php#addList" class="btn btn-white btn-sm w-100 rounded-pill">
                    <i class="fas fa-plus me-1"></i> Create New List
                </a>
            </div>
        </div>
    </div>

    <!-- Dispatch Count Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm dispatch-card-gradient">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm bg-white-10 text-white rounded-circle me-3">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div>
                        <h3 class="text-white mt-1 mb-0 fw-bold" id="totalDispatchTotal">--</h3>
                        <p class="text-white-70 mb-0 small">Total Dispatched</p>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-white-50"><i class="fas fa-arrow-up me-1"></i> 12% from last week</small>
                    <small class="text-white-50">Recent activity</small>
                </div>
                <a href="history.php" class="btn btn-white btn-sm w-100 rounded-pill">
                    <i class="fas fa-history me-1"></i> View Dispatch
                </a>
            </div>
        </div>
    </div>

    <!-- Credit Card -->
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm credit-card-gradient">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm bg-white-10 text-white rounded-circle me-3">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h3 class="text-white mt-1 mb-0 fw-bold"><?= $_SESSION['dispatch_credit'] ?></h3>
                        <p class="text-white-70 mb-0 small">Dispatch Credit</p>
                    </div>
                </div>
                <div class="sparkline-container mb-3">
                    <canvas id="creditSparkline" height="30"></canvas>
                </div>
                <a href="wallet.php#topup" class="btn btn-white btn-sm w-100 rounded-pill">
                    <i class="fas fa-credit-card me-1"></i> Top Up Now
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Card Gradients */
    .welcome-card-gradient {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    }
    
    .list-card-gradient {
        background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);
    }
    
    .dispatch-card-gradient {
        background: linear-gradient(135deg, #ef32d9 0%, #89fffd 100%);
    }
    
    .credit-card-gradient {
        background: linear-gradient(135deg, #f46b45 0%, #eea849 100%);
    }
    
    /* General Card Styling */
    .card {
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    /* Avatar Styling */
    .avatar-sm {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-lg {
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-white-10 {
        background-color: rgba(255,255,255,0.1);
    }
    
    .bg-white-20 {
        background-color: rgba(255,255,255,0.2);
    }
    
    /* Text Colors */
    .text-white-70 {
        color: rgba(255,255,255,0.7);
    }
    
    .text-white-50 {
        color: rgba(255,255,255,0.5);
    }
    
    /* Button Styling */
    .btn-white {
        background-color: rgba(255,255,255,0.9);
        color: #333;
        border: none;
        transition: all 0.2s;
    }
    
    .btn-white:hover {
        background-color: white;
        transform: translateY(-1px);
    }
    
    /* Sparkline Container */
    .sparkline-container {
        width: 100%;
        height: 30px;
    }
    
    /* Progress Bar */
    .progress {
        border-radius: 3px;
        overflow: hidden;
    }
    
    .progress-bar {
        transition: width 0.6s ease;
    }
</style>





                <!--end row-->
                <div class="row justify-content-center">

                    <div class="col-lg-6">
                        <div class="card card-h-100 border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title mb-0">
                                            <i class="fas fa-paper-plane me-2 text-purple"></i>Recent Dispatch
                                        </h4>
                                    </div>
                                    <div class="col-auto">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle" type="button" id="dispatchFilter" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-filter me-1"></i> Filter
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dispatchFilter">
                                                <li><a class="dropdown-item" href="#">All</a></li>
                                                <li><a class="dropdown-item" href="#">Completed</a></li>
                                                <li><a class="dropdown-item" href="#">Pending</a></li>
                                                <li><a class="dropdown-item" href="#">Failed</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light-alpha">
                                            <tr>
                                                <th class="border-0 rounded-start">Title</th>
                                                <th class="border-0">Content</th>
                                                <th class="border-0">Status</th>
                                                <th class="border-0">Recipients</th>
                                                <th class="border-0 rounded-end">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentDispatch as $dispatch): ?>
                                                <tr class="position-relative dispatch-row" data-id="<?= $dispatch['dmsg_id'] ?>">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-xs bg-purple bg-opacity-10 text-white rounded me-2 p-2">
                                                                <i class="fa-solid fa-envelope"></i>
                                                            </div>
                                                            <span class="fw-semibold"><?= htmlspecialchars($dispatch['title']) ?></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="#" class="text-decoration-none preview-content" data-bs-toggle="popover" data-content="<?= htmlspecialchars($dispatch['body']) ?>">
                                                            <i class="far fa-eye me-1 text-muted"></i> Preview
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = [
                                                            0 => 'warning',        // Pending
                                                            1 => 'info',           // Dispatching...
                                                            2 => 'success',        // Dispatch Complete
                                                            3 => 'danger',         // Failed
                                                            4 => 'warning'         // Dispatch InComplete
                                                        ][$dispatch['status']] ?? 'secondary';

                                                        $statusText = [
                                                            0 => 'Pending',
                                                            1 => 'Dispatching...',
                                                            2 => 'Dispatch Complete',
                                                            3 => 'Failed',
                                                            4 => 'Dispatch InComplete'
                                                        ][$dispatch['status']] ?? 'Unknown';
                                                        ?>
                                                        <span class="badge bg-<?= $statusClass ?> bg-opacity-10 text-<?= $statusClass ?> rounded-pill px-3 py-1">
                                                            <?php if ($dispatch['status'] == 1): ?>
                                                                <i class="fas fa-spinner fa-spin me-1"></i>
                                                            <?php elseif ($dispatch['status'] == 2): ?>
                                                                <i class="fas fa-check-circle me-1"></i>
                                                            <?php elseif ($dispatch['status'] == 3): ?>
                                                                <i class="fas fa-times-circle me-1"></i>
                                                            <?php elseif ($dispatch['status'] == 4): ?>
                                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                            <?php endif; ?>
                                                            <?= $statusText ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="fw-bold me-2"><?= $dispatch['dispatch_count'] ?></span>
                                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                                <div class="progress-bar bg-<?= $statusClass ?>" role="progressbar"
                                                                    style="width: <?= $dispatch['status'] == 1 ? '100' : ($dispatch['status'] == 2 ? '100' : '60') ?>%"
                                                                    aria-valuenow="<?= $dispatch['status'] == 1 ? '100' : ($dispatch['status'] == 2 ? '100' : '60') ?>"
                                                                    aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-nowrap">
                                                            <small class="d-block text-muted"><?= date('M j', strtotime($dispatch['created_at'])) ?></small>
                                                            <small><?= date('h:i A', strtotime($dispatch['created_at'])) ?></small>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3 px-2">
                                    <small class="text-muted">Showing <?= min(5, count($recentDispatch)) ?> of <?= count($recentDispatch) ?> dispatches</small>
                                    <a href="#" class="text-decoration-none small">
                                        View Analytics <i class="fas fa-chart-line ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!--end col-->
                    <div class="col-lg-6">
                        <div class="card card-h-100 border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 pb-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title mb-0">
                                            <i class="fas fa-exchange-alt me-2 text-primary"></i>Recent Transactions
                                        </h4>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="fas fa-sync-alt me-1"></i> Refresh
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="border-0 rounded-start">Date & Time</th>
                                                <th class="border-0">Currency</th>
                                                <th class="border-0">Amount</th>
                                                <th class="border-0">Reference</th>
                                                <th class="border-0 rounded-end">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentTransactionHistory as $transaction): ?>
                                                <tr class="position-relative">
                                                    <td class="text-nowrap">
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-primary bg-opacity-10 p-2 rounded me-2">
                                                                <i class="far fa-calendar-alt text-primary"></i>
                                                            </div>
                                                            <div>
                                                                <small class="d-block text-muted"><?= date('M j, Y', strtotime($transaction['created_at'])) ?></small>
                                                                <span><?= date('h:i A', strtotime($transaction['created_at'])) ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?= $transaction['currency'] == 'USD' ? 'primary' : 'info' ?> bg-opacity-10 text-<?= $transaction['currency'] == 'USD' ? 'primary' : 'info' ?> p-2">
                                                            <?= $transaction['currency'] ?>
                                                        </span>
                                                    </td>
                                                    <td class="fw-bold">
                                                        <?= ($transaction['currency'] == 'USD' ? '$' : '') ?><?= $transaction['amount'] ?>
                                                    </td>
                                                    <td>
                                                        <a href="#" class="text-decoration-none" data-bs-toggle="tooltip" title="<?= $transaction['reference'] ?>">
                                                            <i class="far fa-copy text-muted"></i> Copy
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = [
                                                            0 => 'warning',
                                                            1 => 'success',
                                                            2 => 'danger'
                                                        ][$transaction['status']] ?? 'secondary';

                                                        $statusText = [
                                                            0 => 'Pending',
                                                            1 => 'Completed',
                                                            2 => 'Failed'
                                                        ][$transaction['status']] ?? 'Unknown';
                                                        ?>
                                                        <span class="badge bg-<?= $statusClass ?> bg-opacity-10 text-<?= $statusClass ?> rounded-pill px-3 py-2">
                                                            <i class="fas fa-circle small me-1"></i> <?= $statusText ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <small class="text-muted">Showing <?= count($recentTransactionHistory) ?> transactions</small>
                                    <a href="#" class="text-decoration-none">View all <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!--end col-->
                </div>
                <!--end row-->


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
    <script src="assets/data/stock-prices.js"></script>
    <script src="assets/libs/jsvectormap/js/jsvectormap.min.js"></script>
    <script src="assets/libs/jsvectormap/maps/world.js"></script>
    <script src="assets/js/pages/index.init.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="https://kingslist.pro/v2/js/getUserNamelist&TotalDispatch.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize popovers for content preview
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
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

            // Add click handler for rows
            document.querySelectorAll('.dispatch-row').forEach(row => {
                row.addEventListener('click', function() {
                    // You can implement what happens when a row is clicked
                    const dispatchId = this.getAttribute('data-id');
                    console.log('Dispatch clicked:', dispatchId);
                    // window.location.href = `/dispatch/details/${dispatchId}`;
                });
            });
        });
    </script>
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sparkline for credit card
    const ctx = document.getElementById('creditSparkline').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['', '', '', '', '', ''],
            datasets: [{
                data: [5, 9, 5, 6, 4, 12],
                borderColor: 'rgba(255,255,255,0.8)',
                borderWidth: 2,
                tension: 0.4,
                fill: false,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { display: false },
                y: { display: false }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
    
    // Simulate loading data
    setTimeout(() => {
        document.getElementById('userNamelistTotal').textContent = '0';
        document.getElementById('totalDispatchTotal').textContent = '0';
        document.getElementById('message').textContent = 'Dashboard loaded successfully';
    }, 1500);
});
</script>
</body>
<!--end body-->

</html>