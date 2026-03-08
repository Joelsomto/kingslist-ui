<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require_once('../include/Session.php');
// require_once('../include/Functions.php');
require_once("../include/Controller.php");



$Controller = new Controller();

$user_id =  $_SESSION['user_id'];
$TransactionHistory = $Controller->TransactionHistory($_SESSION['user_id']);

$TotalEspeesFunded = $Controller->TotalEspeesFunded($user_id);
$TotalUsdFunded = $Controller->TotalUsdFunded($user_id);

$bal = intval($_SESSION['dispatch_credit']);
// var_dump($currentBalance);

?>

<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>


    <meta charset="utf-8" />
    <title>Wallet | Kingslist</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Font Awesome (version 6) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Google Fonts - Poppins (sleek modern) + Open Sans (clean readable) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
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
        .topup-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(106, 17, 203, 0.3);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .topup-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(106, 17, 203, 0.4);
            color: white;
        }

        .topup-btn:active {
            transform: translateY(0);
        }

        .topup-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .topup-btn:hover .topup-icon {
            transform: scale(1.1) rotate(15deg);
            background: rgba(255, 255, 255, 0.3);
        }

        .topup-badge {
            margin-left: 10px;
            font-size: 1.2rem;
            font-weight: 700;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .topup-btn:hover .topup-badge {
            transform: scale(1.3);
            opacity: 1;
        }

        /* Pulse animation */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }

        .topup-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50px;
            animation: pulse 2s infinite;
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
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4 class="card-title">Wallet </h4>
                                    </div><!--end col-->
                                    <div class="col-auto">
                                        <a href="#" class="topup-btn"  data-bs-toggle="modal" data-bs-target="#topupModal">
                                            <span class="topup-icon">
                                                <i class="fas fa-bolt"></i>
                                            </span>
                                            <span class="topup-text">Instant Top-Up</span>
                                            <span class="topup-badge">+</span>
                                        </a>
                                    </div><!--end col-->
                                </div> <!--end row-->
                            </div><!--end card-header-->
                            <div class="card-body pt-0">
                                <div class="row g-3">
                                    <!-- Credit Balance Card -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="metric-card bg-gradient-primary">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="metric-icon bg-white-10">
                                                        <i class="fas fa-wallet text-white"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <h2 class="mb-0 text-white"><?= $bal ?></h2>
                                                        <p class="text-white-70 mb-0 text-uppercase small">Credit Balance</p>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="progress bg-white-20" style="height: 4px;">
                                                        <div class="progress-bar bg-white" style="width: 85%"></div>
                                                    </div>
                                                    <small class="text-white-50">85% of limit</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Espees Bought Card -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="metric-card bg-gradient-info">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="metric-icon bg-white-10">
                                                        <i class="fas fa-coins text-white"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <h2 class="mb-0 text-white"><?= number_format($TotalEspeesFunded, 2) ?></h2>
                                                        <p class="text-white-70 mb-0 text-uppercase small">Espees Bought</p>
                                                    </div>
                                                </div>
                                                <div class="mt-3 d-flex justify-content-between">
                                                    <small class="text-white-50">
                                                        <i class="fas fa-arrow-up me-1"></i> 12%
                                                    </small>
                                                    <small class="text-white-50">Last 30 days</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- USD Funded Card -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="metric-card bg-gradient-success">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="metric-icon bg-white-10">
                                                        <i class="fas fa-dollar-sign text-white"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <h2 class="mb-0 text-white">$<?= number_format($TotalUsdFunded, 2) ?></h2>
                                                        <p class="text-white-70 mb-0 text-uppercase small">USD Funded</p>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <div class="sparkline" data-sparkline-type="line" data-sparkline-color="#fff" data-sparkline-width="90%" data-sparkline-height="30px" data-sparkline-data="[5,9,5,6,4,12,18,14,10,15,12,8,10]"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bounce Rate Card -->
                                    <div class="col-md-6 col-lg-3">
                                        <div class="metric-card bg-gradient-warning">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="metric-icon bg-white-10">
                                                        <i class="fas fa-chart-line text-white"></i>
                                                    </div>
                                                    <div class="ms-3">
                                                        <h2 class="mb-0 text-white">1.5%</h2>
                                                        <p class="text-white-70 mb-0 text-uppercase small">Bounce Rate</p>
                                                    </div>
                                                </div>
                                                <div class="mt-3 d-flex justify-content-between">
                                                    <small class="text-white-50">
                                                        <i class="fas fa-arrow-down me-1"></i> 2.3%
                                                    </small>
                                                    <small class="text-white-50">vs last month</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <style>
                                .metric-card {
                                    border: none;
                                    border-radius: 12px;
                                    overflow: hidden;
                                    transition: all 0.3s ease;
                                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                                }

                                .metric-card:hover {
                                    transform: translateY(-3px);
                                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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
                                    background-color: rgba(255, 255, 255, 0.1);
                                }

                                .text-white-70 {
                                    color: rgba(255, 255, 255, 0.7);
                                }

                                .text-white-50 {
                                    color: rgba(255, 255, 255, 0.5);
                                }

                                .sparkline {
                                    width: 100%;
                                    height: 30px;
                                    opacity: 0.7;
                                }
                            </style>
                        </div><!--end card-->
                    </div> <!--end col-->

                    <!--end col-->

                </div><!--end row-->

                <div class="row">
                    <div class="col-12">
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
                                            <?php foreach ($TransactionHistory as $transaction): ?>
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
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->

            </div><!-- container -->


<!-- Top-Up Modal -->
<div class="modal fade" id="topupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="modal-icon bg-white-10 rounded-circle me-3">
                        <i class="fas fa-coins text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0">Boost Your Dispatch Power</h5>
                        <small class="text-white-70">Get more reach for your messages</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="pricing-badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 d-inline-block mb-3">
                        <i class="fas fa-percentage me-1"></i> Best Value: 1 Espee = 100 Credits
                    </div>
                    <h4 class="fw-bold">Buy <span id="dynamicCredits">100</span> Dispatch Credits</h4>
                    <p class="text-muted">Deliver to <span id="dynamicRecipients">100</span> recipients</p>
                </div>

                <form class="needs-validation" method="post" action="/topup3" novalidate>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Select Credit Package</label>
                        <select class="form-select form-select-lg shadow-sm" name="dispatch_credits" id="dispatch_credits" required>
                            <option value="100">100 Credits - 1 Espee</option>
                            <option value="200">200 Credits - 2 Espees</option>
                            <option value="300">300 Credits - 3 Espees</option>
                            <option value="400">400 Credits - 4 Espees</option>
                            <option value="500">500 Credits - 5 Espees</option>
                            <option value="1000">1,000 Credits - 10 Espees</option>
                            <option value="2000">2,000 Credits - 20 Espees</option>
                            <option value="5000">5,000 Credits - 50 Espees</option>
                            <option value="10000">10,000 Credits - 100 Espees</option>
                            <option value="20000">20,000 Credits - 200 Espees</option>
                            <option value="50000">50,000 Credits - 500 Espees</option>
                            <option value="100000">100,000 Credits - 1,000 Espees</option>
                            <option value="200000">200,000 Credits - 2,000 Espees</option>
                            <option value="500000">500,000 Credits - 5,000 Espees</option>
                            <option value="1000000">1,000,000 Credits - 10,000 Espees</option>
                        </select>
                    </div>

                    <div class="mb-4 p-3 bg-light rounded-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Total Cost:</span>
                            <h4 class="mb-0 text-primary"><span id="amount_val">1</span> Espees</h4>
                            <input type="hidden" name="amount" id="amount" value="1">
                        </div>
                        <small class="text-muted d-block">≈ <span id="nairaEquivalent">1,450</span> Naira</small>
                    </div>

                    <div class="d-grid gap-3">
                        <button class="btn btn-primary btn-lg rounded-pill py-1" id="espees" name="espees" type="submit">
                            <i class="fas fa-wallet me-2"></i> Pay with Espees Wallet
                        </button>
                        <button class="btn btn-outline-primary btn-lg rounded-pill py-1" id="dollar" name="dollar" type="submit">
                            <i class="fas fa-credit-card me-2"></i> Pay with Card
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="modal-footer bg-light">
                <div class="w-100 text-center">
                    <p class="small text-muted mb-0">
                        <i class="fas fa-lock me-1"></i> Secure payment processing
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dispatchCredits = document.getElementById('dispatch_credits');
    const amountVal = document.getElementById('amount_val');
    const amount = document.getElementById('amount');
    const dynamicCredits = document.getElementById('dynamicCredits');
    const dynamicRecipients = document.getElementById('dynamicRecipients');
    const nairaEquivalent = document.getElementById('nairaEquivalent');

    dispatchCredits.addEventListener('change', function() {
        const credits = parseInt(this.value);
        const espees = credits / 100;
        const naira = espees * 1650;
        
        amountVal.textContent = espees;
        amount.value = espees;
        dynamicCredits.textContent = credits.toLocaleString();
        dynamicRecipients.textContent = credits.toLocaleString();
        nairaEquivalent.textContent = naira.toLocaleString();
    });

    // Initialize with correct values
    dispatchCredits.dispatchEvent(new Event('change'));
});
</script>

<style>


/* Modal Styling */
.modal-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
}

.bg-white-10 {
    background-color: rgba(255,255,255,0.1);
}

.text-white-70 {
    color: rgba(255,255,255,0.7);
}

.pricing-badge {
    font-size: 0.8rem;
    font-weight: 600;
}

.form-select-lg {
    padding: 0.75rem 1rem;
    font-size: 1rem;
}

/* Animation for modal entrance */
@keyframes modalEnter {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-content {
    animation: modalEnter 0.3s ease-out;
}
</style>
            <!--end Rightbar-->
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
    <script src="assets/js/pages/analytics-customers.init.js"></script>
    <script src="assets/js/app.js"></script>
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
    document.addEventListener("DOMContentLoaded", function() {
      if (window.location.hash === "#topup") {
        const modal = new bootstrap.Modal(document.getElementById('topupModal'));
        modal.show();
      }
    });
  </script>
</body>
<!--end body-->

</html>