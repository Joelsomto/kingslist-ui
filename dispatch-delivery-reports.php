<?php
require_once('../include/Session.php');
require_once('../include/Functions.php');
require_once('../include/Crud.php');
require_once("../include/Controller.php");

$dbConnection = new DbConnection();
$conn = $dbConnection->getConnection();
$Controller = new Controller();

$user_id = $_SESSION['user_id'] ?? 0;
$username = $_SESSION['username'] ?? '';
$fullname = $_SESSION['fullname'] ?? '';

$currentYear = date('Y');

// Get summary statistics for current year
$sqlYearStats = "SELECT 
                   COUNT(DISTINCT dm.dmsg_id) as total_campaigns,
                   SUM(dm.dispatch_count) as total_dispatched,
                   COUNT(DISTINCT CASE WHEN mdl.status = 'success' THEN mdl.list_id END) as total_delivered,
                   COUNT(DISTINCT CASE WHEN mdl.status = 'failed' THEN mdl.list_id END) as total_failed
                 FROM dispatch_msg dm
                 LEFT JOIN message_dispatch_log mdl ON dm.dmsg_id = mdl.dmsg_id
                 WHERE dm.user_id = ? AND YEAR(dm.created_at) = ?";

$stmt = $conn->prepare($sqlYearStats);
$stmt->bind_param("ii", $user_id, $currentYear);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

$totalCampaigns = $stats['total_campaigns'] ?? 0;
$totalDispatched = $stats['total_dispatched'] ?? 0;
$totalDelivered = $stats['total_delivered'] ?? 0;
$totalFailed = $stats['total_failed'] ?? 0;
$notDelivered = max(0, ($totalDispatched ?? 0) - ($totalDelivered ?? 0));
$deliveryRate = $totalDispatched > 0 ? round(($totalDelivered / $totalDispatched) * 100, 2) : 0;

// Get recent campaigns
$sqlRecent = "SELECT dm.dmsg_id, dm.title, dm.dispatch_count, dm.created_at,
                     COUNT(DISTINCT mdl.list_id) as delivered
              FROM dispatch_msg dm
              LEFT JOIN message_dispatch_log mdl ON dm.dmsg_id = mdl.dmsg_id AND mdl.status = 'success'
              WHERE dm.user_id = ? AND YEAR(dm.created_at) = ?
              GROUP BY dm.dmsg_id
              ORDER BY dm.created_at DESC
              LIMIT 5";

$stmt = $conn->prepare($sqlRecent);
$stmt->bind_param("ii", $user_id, $currentYear);
$stmt->execute();
$recentCampaigns = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get available years
$sqlYears = "SELECT DISTINCT YEAR(created_at) as year FROM dispatch_msg WHERE user_id = ? ORDER BY year DESC LIMIT 5";
$stmt = $conn->prepare($sqlYears);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$availableYears = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>Dispatch & Delivery Reports | Kingslist</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Comprehensive Dispatch and Delivery Reports" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/libs/simple-datatables/style.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <style>
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0563e0 100%);
            color: white;
            padding: 3rem 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .hero-section p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }        .hero-section .btn-light {
            background-color: rgba(255, 255, 255, 0.9);
            border-color: rgba(255, 255, 255, 0.9);
            color: #0d6efd;
            font-weight: 600;
            transition: all 0.2s;
        }
        .hero-section .btn-light:hover {
            background-color: white;
            border-color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }        .stat-card {
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: none;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #0d6efd;
        }
        .stat-label {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
        }
        .report-btn {
            border-radius: 10px;
            padding: 1.5rem;
            text-decoration: none;
            color: inherit;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            min-height: 200px;
            justify-content: center;
        }
        .report-btn:hover {
            border-color: #0d6efd;
            background-color: #f0f6ff;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.15);
            text-decoration: none;
            color: inherit;
        }
        .report-btn-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #0d6efd;
        }
        .report-btn-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #212529;
        }
        .report-btn-desc {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .recent-campaigns {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }
        .quick-action {
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 1.5rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        .quick-action h6 {
            color: #0d6efd;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .year-badge {
            display: inline-block;
            background: #0d6efd;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .year-badge:hover {
            background: #0b5ed7;
            transform: scale(1.05);
        }
        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #0d6efd;
            display: inline-block;
        }
    </style>
</head>

<body>
    <!-- Top Bar -->
    <div id="topBar-placeholder"></div>
    <script>
        fetch('components/topbar.php')
            .then(res => res.text())
            .then(html => document.getElementById('topBar-placeholder').innerHTML = html)
            .catch(() => {
                fetch('components/topbar.html')
                    .then(res => res.text())
                    .then(html => document.getElementById('topBar-placeholder').innerHTML = html);
            });
    </script>

    <!-- Sidebar -->
    <div id="leftbar-placeholder"></div>
    <script>
        fetch('components/leftbar.php')
            .then(res => res.text())
            .then(html => document.getElementById('leftbar-placeholder').innerHTML = html)
            .catch(() => {
                fetch('components/leftbar.html')
                    .then(res => res.text())
                    .then(html => document.getElementById('leftbar-placeholder').innerHTML = html);
            });
    </script>

    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-xxl">
                <!-- Hero Section -->
                <div class="hero-section d-flex justify-content-between align-items-center">
                    <div>
                        <h1>📊 Dispatch & Delivery Reports</h1>
                        <p>Comprehensive analytics on who received and who didn't receive your dispatches</p>
                    </div>
                    <div>
                        <a href="yearly-delivery-report.php" class="btn btn-light me-2"><i class="icofont-chart-line"></i> View Analytics</a>
                        <a href="recipient-delivery-report.php" class="btn btn-light"><i class="icofont-users"></i> View Details</a>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-icon">📤</div>
                                <div class="stat-value"><?php echo $totalCampaigns; ?></div>
                                <div class="stat-label">Campaigns <?php echo $currentYear; ?></div>
                                <small class="text-muted">Total dispatched</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-icon">✅</div>
                                <div class="stat-value" style="color: #28a745;"><?php echo number_format($totalDelivered); ?></div>
                                <div class="stat-label">Delivered</div>
                                <small class="text-muted">successfully received</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-icon">⏳</div>
                                <div class="stat-value" style="color: #ffc107;"><?php echo number_format($notDelivered); ?></div>
                                <div class="stat-label">Not Delivered</div>
                                <small class="text-muted">pending/failed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <div class="stat-icon">📈</div>
                                <div class="stat-value"><?php echo $deliveryRate; ?>%</div>
                                <div class="stat-label">Success Rate</div>
                                <small class="text-muted">delivery rate</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Types -->
                <div class="mb-4">
                    <div class="section-title">Choose Report Type</div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <a href="yearly-delivery-report.php" class="report-btn">
                            <div class="report-btn-icon">📊</div>
                            <div class="report-btn-title">Yearly Analytics</div>
                            <div class="report-btn-desc">
                                Overall dispatch and delivery statistics for the entire year with monthly trends
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="recipient-delivery-report.php" class="report-btn">
                            <div class="report-btn-icon">👥</div>
                            <div class="report-btn-title">Recipient Details</div>
                            <div class="report-btn-desc">
                                Detailed list of who received and who didn't receive specific messages
                            </div>
                        </a>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <a href="dispatch-report.php" class="report-btn">
                            <div class="report-btn-icon">📋</div>
                            <div class="report-btn-title">Campaign Dispatch Log</div>
                            <div class="report-btn-desc">
                                View details of individual dispatch campaigns and message delivery logs
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Quick Navigation -->
                <div class="row mb-4">
                    <div class="col-lg-8">
                        <div class="section-title mb-3">Available Years</div>
                        <div class="quick-action">
                            <h6>📅 Filter by Year</h6>
                            <div>
                                <?php foreach ($availableYears as $year): ?>
                                    <span class="year-badge" onclick="navigateYear(<?php echo $year['year']; ?>)">
                                        <?php echo $year['year']; ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                            <small class="text-muted">Click on a year to view reports for that specific year</small>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="section-title mb-3">Export Data</div>
                        <div class="quick-action">
                            <h6>📥 Export Options</h6>
                            <button class="btn btn-sm btn-primary me-2" onclick="quickExportCSV()">
                                <i class="icofont-download"></i> CSV
                            </button>
                            <button class="btn btn-sm btn-info" onclick="window.print()">
                                <i class="icofont-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent Campaigns -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title mb-3">Recent Campaigns (<?php echo $currentYear; ?>)</div>
                        <div class="card recent-campaigns">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Latest Dispatch Activities</h5>
                            </div>
                            <div class="card-body">
                                <?php if (count($recentCampaigns) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Campaign Title</th>
                                                    <th>Total Sent</th>
                                                    <th>Delivered</th>
                                                    <th>Rate</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentCampaigns as $campaign): 
                                                    $rate = $campaign['dispatch_count'] > 0 ? round(($campaign['delivered'] / $campaign['dispatch_count']) * 100, 2) : 0;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($campaign['title']); ?></strong>
                                                        </td>
                                                        <td><?php echo number_format($campaign['dispatch_count']); ?></td>
                                                        <td><span class="badge bg-success"><?php echo number_format($campaign['delivered']); ?></span></td>
                                                        <td>
                                                            <div class="progress" style="height: 20px; width: 80px;">
                                                                <div class="progress-bar" role="progressbar" 
                                                                     style="width: <?php echo $rate; ?>%; background-color: <?php echo $rate >= 80 ? '#28a745' : ($rate >= 50 ? '#ffc107' : '#dc3545'); ?>" 
                                                                     aria-valuenow="<?php echo $rate; ?>" 
                                                                     aria-valuemin="0" aria-valuemax="100">
                                                                    <small><?php echo $rate; ?>%</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?php echo date('M d, Y', strtotime($campaign['created_at'])); ?></td>
                                                        <td>
                                                            <a href="recipient-delivery-report.php?campaign=<?php echo $campaign['dmsg_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info mb-0" role="alert">
                                        <strong>No campaigns found</strong> for <?php echo $currentYear; ?>. 
                                        <a href="dispatch.html">Start dispatching</a> to generate reports.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div id="footer-placeholder"></div>
        <script>
            fetch('components/footer.php')
                .then(res => res.text())
                .then(html => document.getElementById('footer-placeholder').innerHTML = html)
                .catch(() => {
                    fetch('components/footer.html')
                        .then(res => res.text())
                        .then(html => document.getElementById('footer-placeholder').innerHTML = html);
                });
        </script>
    </div>

    <!-- Scripts -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
        function navigateYear(year) {
            window.location.href = 'yearly-delivery-report.php?year=' + year;
        }

        function quickExportCSV() {
            const data = [
                ['Year', '<?php echo $currentYear; ?>'],
                ['Total Campaigns', '<?php echo $totalCampaigns; ?>'],
                ['Total Dispatched', '<?php echo $totalDispatched; ?>'],
                ['Total Delivered', '<?php echo $totalDelivered; ?>'],
                ['Delivery Rate', '<?php echo $deliveryRate; ?>%'],
                ['Generated', new Date().toLocaleString()]
            ];

            let csv = [];
            data.forEach(row => {
                csv.push(row.map(cell => '"' + String(cell).replace(/"/g, '""') + '"').join(','));
            });

            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'dispatch-summary-<?php echo $currentYear; ?>.csv';
            a.click();
        }
    </script>
</body>

</html>
