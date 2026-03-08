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

// Get year (default: current year)
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Fetch all dispatch messages for the current year
$sql = "SELECT dm.dmsg_id, dm.name_id, dm.title, dm.dispatch_count, dm.created_at, 
               nl.title as list_name, 
               COUNT(DISTINCT mdl.list_id) as received_count
        FROM dispatch_msg dm
        LEFT JOIN namelist nl ON dm.name_id = nl.name_id
        LEFT JOIN message_dispatch_log mdl ON dm.dmsg_id = mdl.dmsg_id AND mdl.status = 'success'
        WHERE dm.user_id = ? AND YEAR(dm.created_at) = ?
        GROUP BY dm.dmsg_id
        ORDER BY dm.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $selectedYear);
$stmt->execute();
$dispatchResult = $stmt->get_result();
$dispatchMessages = $dispatchResult->fetch_all(MYSQLI_ASSOC);

// Calculate summary statistics
$totalCampaigns = count($dispatchMessages);
$totalAttempted = 0;
$totalReceived = 0;
$totalNotReceived = 0;

foreach ($dispatchMessages as $msg) {
    $totalAttempted += $msg['dispatch_count'] ?? 0;
    $totalReceived += $msg['received_count'] ?? 0;
}
$totalNotReceived = max(0, $totalAttempted - $totalReceived);

// Get monthly trend
$sqlTrend = "SELECT DATE_FORMAT(dm.created_at, '%Y-%m') as month, 
             COUNT(DISTINCT dm.dmsg_id) as campaigns,
             SUM(dm.dispatch_count) as total_sent,
             COUNT(DISTINCT mdl.list_id) as total_delivered
             FROM dispatch_msg dm
             LEFT JOIN message_dispatch_log mdl ON dm.dmsg_id = mdl.dmsg_id AND mdl.status = 'success'
             WHERE dm.user_id = ? AND YEAR(dm.created_at) = ?
             GROUP BY DATE_FORMAT(dm.created_at, '%Y-%m')
             ORDER BY month DESC";

$stmt = $conn->prepare($sqlTrend);
$stmt->bind_param("ii", $user_id, $selectedYear);
$stmt->execute();
$trendResult = $stmt->get_result();
$monthlyTrend = $trendResult->fetch_all(MYSQLI_ASSOC);

// Get year list for dropdown
$sqlYears = "SELECT DISTINCT YEAR(created_at) as year FROM dispatch_msg WHERE user_id = ? ORDER BY year DESC";
$stmt = $conn->prepare($sqlYears);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$yearsResult = $stmt->get_result();
$availableYears = $yearsResult->fetch_all(MYSQLI_ASSOC);

$deliveryRate = $totalAttempted > 0 ? round(($totalReceived / $totalAttempted) * 100, 2) : 0;
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>Yearly Delivery Report | Kingslist</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Yearly Dispatch & Delivery Report" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/libs/simple-datatables/style.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stat-card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
        .delivery-badge-success {
            background-color: #28a745;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-weight: 600;
        }
        .delivery-badge-pending {
            background-color: #ffc107;
            color: #333;
            padding: 8px 12px;
            border-radius: 5px;
            font-weight: 600;
        }
        .progress-bar-success {
            background-color: #28a745;
        }
        .progress-bar-pending {
            background-color: #ffc107;
        }
        .table-container {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }
        .report-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0d6efd 100%);
            color: white;
            padding: 2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .report-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
        }
        .report-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }
        .report-header .btn-light {
            background-color: rgba(255, 255, 255, 0.9);
            border-color: rgba(255, 255, 255, 0.9);
            color: #0d6efd;
            font-weight: 600;
            transition: all 0.2s;
        }
        .report-header .btn-light:hover {
            background-color: white;
            border-color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .export-btn {
            margin-right: 0.5rem;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border-left: 4px solid #0d6efd;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 2rem;
        }
    </style>
</head>

<body>
    <!-- Top Bar -->
    <div id="topBar-placeholder"></div>
    <script>
        fetch('components/topbar.php')
            .then(res => res.text())
            .then(html => {
                document.getElementById('topBar-placeholder').innerHTML = html;
            })
            .catch(() => {
                fetch('components/topbar.html')
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('topBar-placeholder').innerHTML = html;
                    });
            });
    </script>

    <!-- Sidebar -->
    <div id="leftbar-placeholder"></div>
    <script>
        fetch('components/leftbar.php')
            .then(res => res.text())
            .then(html => {
                document.getElementById('leftbar-placeholder').innerHTML = html;
            })
            .catch(() => {
                fetch('components/leftbar.html')
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('leftbar-placeholder').innerHTML = html;
                    });
            });
    </script>

    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-xxl">
                <!-- Header -->
                <div class="report-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1>📊 Yearly Delivery Report</h1>
                        <p>Comprehensive dispatch and delivery analytics for <?php echo $selectedYear; ?></p>
                    </div>
                    <div>
                        <a href="dispatch-delivery-reports.php" class="btn btn-light me-2"><i class="icofont-arrow-left"></i> Dashboard</a>
                        <a href="recipient-delivery-report.php" class="btn btn-light"><i class="icofont-arrow-right"></i> Recipients</a>
                    </div>
                </div>

                <!-- Year Filter -->
                <div class="filter-section">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label for="yearSelect" class="form-label fw-semibold mb-0">Select Year:</label>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="yearSelect" onchange="filterByYear(this.value)">
                                <option value="<?php echo date('Y'); ?>" <?php echo $selectedYear == date('Y') ? 'selected' : ''; ?>>
                                    Current Year (<?php echo date('Y'); ?>)
                                </option>
                                <?php foreach ($availableYears as $year): ?>
                                    <option value="<?php echo $year['year']; ?>" <?php echo $selectedYear == $year['year'] ? 'selected' : ''; ?>>
                                        <?php echo $year['year']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Summary Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-value"><?php echo $totalCampaigns; ?></div>
                                <div class="stat-label">Total Campaigns</div>
                                <small class="text-muted">dispatched this year</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-value" style="color: #28a745;"><?php echo number_format($totalReceived); ?></div>
                                <div class="stat-label">Delivered</div>
                                <small class="text-muted">successfully received</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-value" style="color: #ffc107;"><?php echo number_format($totalNotReceived); ?></div>
                                <div class="stat-label">Not Delivered</div>
                                <small class="text-muted">pending or failed</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-value" style="color: #0d6efd;"><?php echo $deliveryRate; ?>%</div>
                                <div class="stat-label">Delivery Rate</div>
                                <small class="text-muted">success ratio</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trend Chart -->
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <div class="card table-container">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">📈 Monthly Trend</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="monthlyChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Campaign Report -->
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <div class="card table-container">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">📋 Campaign Details</h5>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary export-btn" onclick="exportToCSV()">
                                        <i class="icofont-download"></i> CSV
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary export-btn" onclick="exportToJSON()">
                                        <i class="icofont-download"></i> JSON
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                                        <i class="icofont-print"></i> Print
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="campaignTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Campaign Name</th>
                                                <th>Total Recipients</th>
                                                <th>Delivered</th>
                                                <th>Not Delivered</th>
                                                <th>Delivery %</th>
                                                <th>Sent Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($dispatchMessages) > 0): ?>
                                                <?php foreach ($dispatchMessages as $msg): 
                                                    $notDelivered = max(0, ($msg['dispatch_count'] ?? 0) - ($msg['received_count'] ?? 0));
                                                    $rate = ($msg['dispatch_count'] ?? 0) > 0 ? round((($msg['received_count'] ?? 0) / $msg['dispatch_count']) * 100, 2) : 0;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <a href="dispatch-report.php?id=<?php echo $msg['dmsg_id']; ?>" class="text-primary fw-semibold">
                                                                <?php echo htmlspecialchars($msg['title'] ?? 'Untitled Campaign'); ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo number_format($msg['dispatch_count'] ?? 0); ?></td>
                                                        <td><span class="delivery-badge-success"><?php echo number_format($msg['received_count'] ?? 0); ?></span></td>
                                                        <td><span class="delivery-badge-pending"><?php echo number_format($notDelivered); ?></span></td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar progress-bar-success" role="progressbar" 
                                                                     style="width: <?php echo $rate; ?>%" 
                                                                     aria-valuenow="<?php echo $rate; ?>" 
                                                                     aria-valuemin="0" aria-valuemax="100">
                                                                    <?php echo $rate; ?>%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
                                                        <td>
                                                            <?php if ($rate == 100): ?>
                                                                <span class="badge bg-success">Completed</span>
                                                            <?php elseif ($rate > 0): ?>
                                                                <span class="badge bg-info">Partial</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning">Pending</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-4">
                                                        No dispatch campaigns found for the selected year.
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Summary -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card table-container">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">📊 Summary Statistics</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6>Dispatch Overview</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><strong>Year:</strong> <?php echo $selectedYear; ?></li>
                                            <li class="mb-2"><strong>Total Campaigns:</strong> <?php echo $totalCampaigns; ?></li>
                                            <li class="mb-2"><strong>Total Attempts:</strong> <?php echo number_format($totalAttempted); ?></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Delivery Status</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><strong>Successfully Delivered:</strong> <span class="text-success">✓ <?php echo number_format($totalReceived); ?></span></li>
                                            <li class="mb-2"><strong>Not Delivered:</strong> <span class="text-warning">✗ <?php echo number_format($totalNotReceived); ?></span></li>
                                            <li class="mb-2"><strong>Success Rate:</strong> <strong><?php echo $deliveryRate; ?>%</strong></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Period Info</h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><strong>Report Generated:</strong> <?php echo date('M d, Y H:i'); ?></li>
                                            <li class="mb-2"><strong>Generated By:</strong> <?php echo htmlspecialchars($fullname); ?></li>
                                            <li class="mb-2"><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></li>
                                        </ul>
                                    </div>
                                </div>
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
                .then(html => {
                    document.getElementById('footer-placeholder').innerHTML = html;
                })
                .catch(() => {
                    fetch('components/footer.html')
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('footer-placeholder').innerHTML = html;
                        });
                });
        </script>
    </div>

    <!-- Scripts -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/simple-datatables/umd/simple-datatables.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
        // Monthly trend chart data
        const monthlyData = <?php echo json_encode($monthlyTrend); ?>;
        
        if (monthlyData.length > 0) {
            const labels = monthlyData.map(d => {
                const [year, month] = d.month.split('-');
                const date = new Date(year, parseInt(month) - 1);
                return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            });
            const sent = monthlyData.map(d => parseInt(d.total_sent) || 0);
            const delivered = monthlyData.map(d => parseInt(d.total_delivered) || 0);

            const ctx = document.getElementById('monthlyChart')?.getContext('2d');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels.reverse(),
                        datasets: [
                            {
                                label: 'Sent',
                                data: sent.reverse(),
                                backgroundColor: '#0d6efd',
                                borderColor: '#0d6efd',
                                borderWidth: 1
                            },
                            {
                                label: 'Delivered',
                                data: delivered.reverse(),
                                backgroundColor: '#28a745',
                                borderColor: '#28a745',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        }

        function filterByYear(year) {
            window.location.href = '?year=' + year;
        }

        function exportToCSV() {
            const table = document.getElementById('campaignTable');
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            rows.forEach(row => {
                const cols = row.querySelectorAll('td, th');
                let csvRow = [];
                cols.forEach(col => {
                    csvRow.push('"' + col.innerText.replace(/"/g, '""') + '"');
                });
                csv.push(csvRow.join(','));
            });

            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'yearly-delivery-report-<?php echo $selectedYear; ?>.csv';
            a.click();
        }

        function exportToJSON() {
            const table = document.getElementById('campaignTable');
            const rows = table.querySelectorAll('tbody tr');
            const data = [];

            rows.forEach(row => {
                const cols = row.querySelectorAll('td');
                if (cols.length > 0) {
                    data.push({
                        campaign: cols[0].innerText,
                        total_recipients: cols[1].innerText,
                        delivered: cols[2].innerText,
                        not_delivered: cols[3].innerText,
                        delivery_rate: cols[4].innerText,
                        sent_date: cols[5].innerText,
                        status: cols[6].innerText
                    });
                }
            });

            const json = JSON.stringify(data, null, 2);
            const blob = new Blob([json], { type: 'application/json' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'yearly-delivery-report-<?php echo $selectedYear; ?>.json';
            a.click();
        }
    </script>
</body>

</html>
