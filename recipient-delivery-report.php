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

// Get filter parameters
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$selectedCampaign = isset($_GET['campaign']) ? (int)$_GET['campaign'] : 0;
$filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all'; // all, received, not_received

// Get campaign list
$sqlCampaigns = "SELECT DISTINCT dm.dmsg_id, dm.title, dm.created_at
                 FROM dispatch_msg dm
                 WHERE dm.user_id = ? AND YEAR(dm.created_at) = ?
                 ORDER BY dm.created_at DESC";
$stmt = $conn->prepare($sqlCampaigns);
$stmt->bind_param("ii", $user_id, $selectedYear);
$stmt->execute();
$campaignResult = $stmt->get_result();
$campaigns = $campaignResult->fetch_all(MYSQLI_ASSOC);

// If no campaign selected, use the first one
if ($selectedCampaign == 0 && count($campaigns) > 0) {
    $selectedCampaign = $campaigns[0]['dmsg_id'];
}

// Get recipient details for selected campaign
$recipients = [];
$campaignInfo = [];

if ($selectedCampaign > 0) {
    // Get campaign info
    $sqlCampaignInfo = "SELECT dm.dmsg_id, dm.title, dm.dispatch_count, dm.created_at, nl.title as list_name
                       FROM dispatch_msg dm
                       LEFT JOIN namelist nl ON dm.name_id = nl.name_id
                       WHERE dm.dmsg_id = ? AND dm.user_id = ?";
    $stmt = $conn->prepare($sqlCampaignInfo);
    $stmt->bind_param("ii", $selectedCampaign, $user_id);
    $stmt->execute();
    $campaignInfo = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get all recipients for the list used in this campaign
    $sqlRecipients = "SELECT l.list_id, l.kc_username, l.list_name, 
                     mdl.dmsg_id, mdl.status, mdl.created_at as delivered_at,
                     CASE 
                        WHEN mdl.status = 'success' THEN 1
                        WHEN mdl.status = 'failed' THEN 2
                        ELSE 3
                      END as delivery_status
             FROM list l
             INNER JOIN (
                SELECT DISTINCT name_id FROM dispatch_msg WHERE dmsg_id = ? AND user_id = ?
             ) dm_filter ON l.name_id = dm_filter.name_id
             LEFT JOIN message_dispatch_log mdl ON l.list_id = mdl.list_id AND mdl.dmsg_id = ?
             ORDER BY delivery_status ASC, l.kc_username ASC";

    $stmt = $conn->prepare($sqlRecipients);
    $stmt->bind_param("iii", $selectedCampaign, $user_id, $selectedCampaign);
    $stmt->execute();
    $recipientResult = $stmt->get_result();
    
    $allRecipients = $recipientResult->fetch_all(MYSQLI_ASSOC);
    
    // Filter by status
    foreach ($allRecipients as $recipient) {
        if ($filterStatus === 'received' && $recipient['status'] !== 'success') {
            continue;
        }
        if ($filterStatus === 'not_received' && $recipient['status'] === 'success') {
            continue;
        }
        $recipients[] = $recipient;
    }
}

// Calculate statistics for selected campaign
$totalRecipients = count($recipients);
$receivedCount = 0;
$notReceivedCount = 0;
$failedCount = 0;

foreach ($recipients as $r) {
    if ($r['status'] === 'success') {
        $receivedCount++;
    } else if ($r['status'] === 'failed') {
        $failedCount++;
    } else {
        $notReceivedCount++;
    }
}

$deliveryRate = $totalRecipients > 0 ? round(($receivedCount / $totalRecipients) * 100, 2) : 0;

// Get year list for dropdown
$sqlYears = "SELECT DISTINCT YEAR(created_at) as year FROM dispatch_msg WHERE user_id = ? ORDER BY year DESC";
$stmt = $conn->prepare($sqlYears);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$yearsResult = $stmt->get_result();
$availableYears = $yearsResult->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>Recipient Delivery Report | Kingslist</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Detailed Recipient Delivery Report" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <link href="assets/libs/simple-datatables/style.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <style>
        .stat-card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
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
        .report-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0563e0 100%);
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
        .badge-delivered {
            background-color: #28a745;
            color: white;
        }
        .badge-failed {
            background-color: #dc3545;
            color: white;
        }
        .badge-pending {
            background-color: #ffc107;
            color: #333;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border-left: 4px solid #0d6efd;
        }
        .table-container {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }
        .status-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
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
                <!-- Header -->
                <div class="report-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1>👥 Recipient Delivery Details</h1>
                        <p>Who received vs who didn't receive messages</p>
                    </div>
                    <div>
                        <a href="dispatch-delivery-reports.php" class="btn btn-light me-2"><i class="icofont-arrow-left"></i> Dashboard</a>
                        <a href="yearly-delivery-report.php" class="btn btn-light"><i class="icofont-arrow-left"></i> Analytics</a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-section">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="yearSelect" class="form-label fw-semibold">Year:</label>
                            <select class="form-select" id="yearSelect" onchange="updateFilters()">
                                <option value="<?php echo date('Y'); ?>" <?php echo $selectedYear == date('Y') ? 'selected' : ''; ?>>
                                    <?php echo date('Y'); ?>
                                </option>
                                <?php foreach ($availableYears as $year): ?>
                                    <option value="<?php echo $year['year']; ?>" <?php echo $selectedYear == $year['year'] ? 'selected' : ''; ?>>
                                        <?php echo $year['year']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="campaignSelect" class="form-label fw-semibold">Campaign:</label>
                            <select class="form-select" id="campaignSelect" onchange="updateFilters()">
                                <option value="">-- Select Campaign --</option>
                                <?php foreach ($campaigns as $campaign): ?>
                                    <option value="<?php echo $campaign['dmsg_id']; ?>" 
                                            <?php echo $selectedCampaign == $campaign['dmsg_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($campaign['title']); ?> 
                                        (<?php echo date('M d, Y', strtotime($campaign['created_at'])); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="statusFilter" class="form-label fw-semibold">Status:</label>
                            <select class="form-select" id="statusFilter" onchange="updateFilters()">
                                <option value="all" <?php echo $filterStatus === 'all' ? 'selected' : ''; ?>>All Recipients</option>
                                <option value="received" <?php echo $filterStatus === 'received' ? 'selected' : ''; ?>>Delivered Only</option>
                                <option value="not_received" <?php echo $filterStatus === 'not_received' ? 'selected' : ''; ?>>Not Delivered</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100" onclick="exportRecipientData()">
                                <i class="icofont-download"></i> Export
                            </button>
                        </div>
                    </div>
                </div>

                <?php if ($selectedCampaign > 0 && count($campaignInfo) > 0): 
                    $campaign = $campaignInfo[0];
                ?>
                <!-- Campaign Details -->
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted text-uppercase">Campaign Details</h6>
                                        <p class="mb-1"><strong>Campaign:</strong> <?php echo htmlspecialchars($campaign['title']); ?></p>
                                        <p class="mb-1"><strong>List:</strong> <?php echo htmlspecialchars($campaign['list_name'] ?? 'N/A'); ?></p>
                                        <p class="mb-0"><strong>Sent Date:</strong> <?php echo date('M d, Y H:i', strtotime($campaign['created_at'])); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted text-uppercase">Summary</h6>
                                        <p class="mb-1"><strong>Total Recipients:</strong> <?php echo number_format($totalRecipients); ?></p>
                                        <p class="mb-1"><strong>Delivered:</strong> <span class="badge badge-delivered"><?php echo number_format($receivedCount); ?></span></p>
                                        <p class="mb-0"><strong>Failed/Pending:</strong> <span class="badge badge-pending"><?php echo number_format($notReceivedCount + $failedCount); ?></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-value" style="color: #0d6efd;"><?php echo number_format($totalRecipients); ?></div>
                                <div class="stat-label">Total Recipients</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-value" style="color: #28a745;"><?php echo number_format($receivedCount); ?></div>
                                <div class="stat-label">Delivered</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-value" style="color: #ffc107;"><?php echo number_format($failedCount); ?></div>
                                <div class="stat-label">Failed</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="stat-value"><?php echo $deliveryRate; ?>%</div>
                                <div class="stat-label">Success Rate</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recipients Table -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card table-container">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Recipients List</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="recipientTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Username/Handle</th>
                                                <th>Recipient Name</th>
                                                <th>Status</th>
                                                <th>Delivery Date</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($recipients) > 0): ?>
                                                <?php $counter = 1; foreach ($recipients as $recipient): ?>
                                                    <tr>
                                                        <td><?php echo $counter++; ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($recipient['kc_username']); ?></strong>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($recipient['list_name'] ?? 'N/A'); ?></td>
                                                        <td>
                                                            <?php if ($recipient['status'] === 'success'): ?>
                                                                <span class="badge badge-delivered">
                                                                    <span class="status-icon">✓</span> Delivered
                                                                </span>
                                                            <?php elseif ($recipient['status'] === 'failed'): ?>
                                                                <span class="badge badge-failed">
                                                                    <span class="status-icon">✗</span> Failed
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge badge-pending">
                                                                    <span class="status-icon">⏳</span> Pending
                                                                </span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($recipient['delivered_at']): ?>
                                                                <?php echo date('M d, Y H:i', strtotime($recipient['delivered_at'])); ?>
                                                            <?php else: ?>
                                                                <span class="text-muted">N/A</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?php if ($recipient['status'] === 'success'): ?>
                                                                    Message delivered successfully
                                                                <?php elseif ($recipient['status'] === 'failed'): ?>
                                                                    Delivery failed
                                                                <?php else: ?>
                                                                    Awaiting delivery
                                                                <?php endif; ?>
                                                            </small>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        No recipients found for the selected filters.
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
                <?php else: ?>
                <div class="alert alert-info" role="alert">
                    <strong>No Campaign Selected:</strong> Please select a campaign from the filters above to view recipient details.
                </div>
                <?php endif; ?>
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
    <script src="assets/libs/simple-datatables/umd/simple-datatables.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
        function updateFilters() {
            const year = document.getElementById('yearSelect').value;
            const campaign = document.getElementById('campaignSelect').value;
            const status = document.getElementById('statusFilter').value;
            
            let url = '?year=' + year;
            if (campaign) url += '&campaign=' + campaign;
            if (status !== 'all') url += '&status=' + status;
            
            window.location.href = url;
        }

        function exportRecipientData() {
            const table = document.getElementById('recipientTable');
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
            a.download = 'recipient-delivery-report-<?php echo $selectedYear; ?>.csv';
            a.click();
        }
    </script>
</body>

</html>
