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

$user_id =  $_SESSION['user_id'];

$dmsg_id = sanitizeString($_GET['id']);
// $dmsg_id = sanitizeString($secondparam);

$getName = $Controller->getDispName($dmsg_id);

$list_id = $getName[0]['list_id'];

$user_id = $_SESSION['user_id'];


$name_user_id = $getName[0]['user_id'];
if ($name_user_id != $user_id) {
    echo "redirecting...";
    header("refresh:3;url=/dashboard");
    exit();
}
$start_dispatch = 1;
$status = $getName[0]['status'];
if ($status == 2) {
    $start_dispatch = 0;
}
// var_dump($status);
// die();
$_SESSION['dmsg_id'] = $dmsg_id;
//$getList = $Controller->getNamesList($name_id);

// $getDispatchBatch = $Controller->getDispatchBatch($dmsg_id);
// var_dump($getDispatchBatch);
// die();


?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>


    <meta charset="utf-8" />
    <title>Dispatch Messages | Kingslist</title>
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
    /* Custom Styles */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    }
    
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .card-status-bar {
        background-color: rgba(0,0,0,0.03);
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .progress-container {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }
    
    .progress {
        border-radius: 4px;
        overflow: hidden;
    }
    
    .progress-bar {
        transition: width 0.3s ease;
    }
    
    #resultsTable th {
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    
    #resultsTable tbody tr {
        transition: all 0.2s ease;
    }
    
    #resultsTable tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.03);
    }
    
    .rounded-start {
        border-top-left-radius: 12px !important;
        border-bottom-left-radius: 12px !important;
    }
    
    .rounded-end {
        border-top-right-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
    }
    
    .badge {
        font-weight: 500;
        padding: 5px 10px;
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
    <div class="card shadow-lg border-0">
        <!-- Card Header with Gradient Background -->
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title mb-0 text-white">
                    <i class="fas fa-paper-plane me-2"></i> Dispatch Logs
                </h4>
            </div>
            <a href="dispatch-report.php?id=<?= $dmsg_id ?>" class="btn btn-light btn-sm rounded-pill">
                <i class="fas fa-eye me-1"></i> View Detailed Logs
            </a>
        </div>

        <!-- Dispatch Status Indicator -->
        <div class="card-status-bar px-4 pt-3">
            <?php 
            $statusClasses = [
                0 => 'success',
                1 => 'info',
                2 => 'success',
                3 => 'danger',
                4 => 'warning'
            ];
            $statusIcons = [
                0 => 'check-circle',
                1 => 'sync-alt',
                2 => 'check-double',
                3 => 'times-circle',
                4 => 'exclamation-circle'
            ];
            $statusTexts = [
                0 => 'Dispatch completed successfully',
                1 => 'Dispatching in progress',
                2 => 'Dispatch Complete',
                3 => 'Dispatch Failed',
                4 => 'Dispatch Incomplete'
            ];
            $currentStatus = $start_dispatch ?? 5;
            ?>
            <div id="dispatchStatus" class="alert alert-<?= $statusClasses[$currentStatus] ?? 'secondary' ?> d-flex align-items-center mb-0">
                <i class="fas fa-<?= $statusIcons[$currentStatus] ?? 'question-circle' ?> me-2"></i>
                <span><?= $statusTexts[$currentStatus] ?? 'Unknown Status' ?></span>
                <div class="spinner-border spinner-border-sm ms-auto d-none" id="statusSpinner" role="status"></div>
            </div>
        </div>

        <div class="card-body pt-3">
            <!-- Progress Bar -->
            <div class="progress-container mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">Dispatch Progress</small>
                    <small id="progressText" class="fw-bold">0%</small>
                </div>
                <div class="progress" style="height: 8px;">
                    <div id="dispatchProgress" class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 0%" 
                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="table-responsive">
                <table id="resultsTable" class="table table-hover align-middle d-none">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 rounded-start"><i class="fas fa-user me-1"></i> Username</th>
                            <th class="border-0"><i class="fas fa-id-card me-1"></i> Name</th>
                            <th class="border-0 rounded-end"><i class="fas fa-info-circle me-1"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody id="resultsBody" class="border-top-0"></tbody>
                </table>
            </div>

            <!-- Hidden Form -->
            <form id="autoDispatchForm" method="post" class="d-none">
                <input type="hidden" name="dmsg_id" value="<?= htmlspecialchars($dmsg_id) ?>">
                <input type="hidden" name="start_dispatch" value="<?= htmlspecialchars($start_dispatch) ?>">
                <input type="hidden" name="accessToken" value="<?= htmlspecialchars($_SESSION['accessToken']) ?>">
                <input type="hidden" name="refreshToken" value="<?= htmlspecialchars($_SESSION['refreshToken']) ?>">
            </form>
        </div>
    </div>
</div>

<!-- <script>
document.addEventListener('DOMContentLoaded', async function() {
    // DOM Elements
    const form = document.getElementById('autoDispatchForm');
    const statusEl = document.getElementById('dispatchStatus');
    const statusSpinner = document.getElementById('statusSpinner');
    const progressBar = document.getElementById('dispatchProgress');
    const progressText = document.getElementById('progressText');
    const resultsTable = document.getElementById('resultsTable');
    const resultsBody = document.getElementById('resultsBody');
    
    // Form Data
    const dmsgId = form.querySelector('input[name="dmsg_id"]').value;
    const startDispatch = parseInt(form.querySelector('input[name="start_dispatch"]').value);
    let accessToken = form.querySelector('input[name="accessToken"]').value;
    let refreshToken = form.querySelector('input[name="refreshToken"]').value;
    
    // Constants
    const userId = <?= $user_id ?>;
    const listId = <?= $list_id ?>;
    const API_BASE = 'https://kingslist.pro/app/default/api';
    const STORAGE_KEY = `dispatchQueue_${userId}_${dmsgId}`;
    const MAX_QUEUE_AGE = 60 * 60 * 1000; // 1 hour
    
    // State
    let messages = [];
    
    // Initialize
    updateProgress(0);
    sessionStorage.setItem('accessToken', accessToken);
    sessionStorage.setItem('refreshToken', refreshToken);
    
    // Helper Functions
    function updateProgress(percent) {
        progressBar.style.width = `${percent}%`;
        progressBar.setAttribute('aria-valuenow', percent);
        progressText.textContent = `${percent}%`;
        
        if (percent >= 100) {
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.remove('progress-bar-striped');
        }
    }
    
    function showStatus(text, type = 'info') {
        statusEl.className = `alert alert-${type} d-flex align-items-center mb-0`;
        statusEl.innerHTML = `
            <i class="fas fa-${getStatusIcon(type)} me-2"></i>
            <span>${text}</span>
        `;
        
        if (type === 'info') {
            statusSpinner.classList.remove('d-none');
            statusEl.appendChild(statusSpinner);
        } else {
            statusSpinner.classList.add('d-none');
        }
    }
    
    function getStatusIcon(type) {
        const icons = {
            'success': 'check-circle',
            'info': 'sync-alt',
            'warning': 'exclamation-circle',
            'danger': 'times-circle'
        };
        return icons[type] || 'info-circle';
    }
    
    function sanitizeString(str) {
        return str ? str.normalize('NFKD').replace(/[\u0300-\u036f]/g, '') : '';
    }
    
    function saveToLocalQueue(users, baseMessage) {
        const queueData = {
            dmsg_id: dmsgId,
            list_id: listId,
            user_id: userId,
            message: baseMessage,
            users: users,
            dispatched: false,
            timestamp: new Date().toISOString()
        };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(queueData));
    }
    
    function clearLocalQueue() {
        localStorage.removeItem(STORAGE_KEY);
    }
    
    function checkExistingQueue() {
        const queue = localStorage.getItem(STORAGE_KEY);
        if (!queue) return true;
        
        try {
            const data = JSON.parse(queue);
            const age = Date.now() - new Date(data.timestamp).getTime();
            
            if (data.dispatched) {
                showStatus('Dispatch already completed', 'success');
                return false;
            }
            
            if (age < MAX_QUEUE_AGE) {
                return confirm("You have unsent messages queued. Would you like to continue?");
            } else {
                clearLocalQueue();
                return true;
            }
        } catch (err) {
            console.error("Error parsing queue:", err);
            clearLocalQueue();
            return true;
        }
    }
    
    // Event Listeners
    window.addEventListener('beforeunload', function(e) {
        const queue = localStorage.getItem(STORAGE_KEY);
        if (queue && !JSON.parse(queue).dispatched) {
            e.preventDefault();
            e.returnValue = 'You have unsent messages. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
    
    // Main Dispatch Function
    async function executeDispatch() {
        if (startDispatch !== 1) {
            showStatus('Dispatch not in progress', 'info');
            return;
        }
        
        if (!checkExistingQueue()) {
            return;
        }
        
        try {
            showStatus('Loading recipients...', 'info');
            
            // Fetch batch data
            const response = await fetch(`${API_BASE}/getDispatchBatch.php?dmsg_id=${dmsgId}`, {
                headers: { 'Authorization': `Bearer ${accessToken}` }
            });
            
            if (!response.ok) throw new Error(`HTTP error ${response.status}`);
            const data = await response.json();
            
            if (!data?.success || !Array.isArray(data.data?.messages)) {
                throw new Error("Invalid response format");
            }
            
            messages = data.data.messages;
            if (messages.length === 0) {
                showStatus('No recipients found', 'warning');
                return;
            }
            
            const baseMessage = messages[0].body || "";
            const totalUsers = messages.length;
            
            // Prepare users data
            const sanitizedUsers = messages.map(user => ({
                kc_id: user.kc_id,
                fullname: sanitizeString(user.fullname),
                username: sanitizeString(user.username),
                formatted_message: (user.body || '')
                    .replace(/<fullname>/gi, sanitizeString(user.fullname))
                    .replace(/<kc_username>/gi, sanitizeString(user.username))
            }));
            
            // Save to local queue
            saveToLocalQueue(sanitizedUsers, baseMessage);
            showStatus(`Preparing ${totalUsers} messages...`, 'info');
            
            // Add to server queue
            const queueResponse = await fetch(`${API_BASE}/addToDispatchQueue.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${accessToken}`
                },
                body: JSON.stringify({
                    dmsg_id: dmsgId,
                    list_id: listId,
                    user_id: userId,
                    message: baseMessage,
                    access_token: accessToken,
                    refresh_token: refreshToken,
                    users: sanitizedUsers
                })
            });
            
            if (!queueResponse.ok) throw new Error(await queueResponse.text());
            
            // Mark as dispatched
            const queue = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
            queue.dispatched = true;
            localStorage.setItem(STORAGE_KEY, JSON.stringify(queue));
            
            // Update UI
            updateProgress(100);
            showStatus(`Successfully queued ${totalUsers} messages`, 'success');
            resultsTable.classList.remove('d-none');
            
            // Populate results table
            resultsBody.innerHTML = sanitizedUsers.map(user => `
                <tr>
                    <td>@${user.username || user.kc_id}</td>
                    <td>${user.fullname || 'N/A'}</td>
                    <td><span class="badge bg-success">Queued</span></td>
                </tr>
            `).join('');
            
        } catch (error) {
            console.error("Dispatch error:", error);
            showStatus(`Error: ${error.message}`, 'danger');
            progressBar.classList.add('bg-danger');
        }
    }
    
    // Start dispatch if needed
    if (startDispatch === 1) {
        executeDispatch();
    }
});
</script> -->

<script>
    document.addEventListener('DOMContentLoaded', async function() {
    // DOM Elements
    const form = document.getElementById('autoDispatchForm');
    const statusEl = document.getElementById('dispatchStatus');
    const statusSpinner = document.getElementById('statusSpinner');
    const progressBar = document.getElementById('dispatchProgress');
    const progressText = document.getElementById('progressText');
    const resultsTable = document.getElementById('resultsTable');
    const resultsBody = document.getElementById('resultsBody');
    
    // Form Data
    const dmsgId = form.querySelector('input[name="dmsg_id"]').value;
    const startDispatch = parseInt(form.querySelector('input[name="start_dispatch"]').value);
    let accessToken = form.querySelector('input[name="accessToken"]').value;
    let refreshToken = form.querySelector('input[name="refreshToken"]').value;
    
    // Constants
    const userId = <?= $user_id ?>;
    const listId = <?= $list_id ?>;
    const API_BASE = 'https://kingslist.pro/app/default/api';
    const MAX_QUEUE_AGE = 60 * 60 * 1000; // 1 hour
    
    // State
    let messages = [];
    
    // Initialize
    updateProgress(0);
    sessionStorage.setItem('accessToken', accessToken);
    sessionStorage.setItem('refreshToken', refreshToken);
    
    // Helper Functions
    function updateProgress(percent) {
        progressBar.style.width = `${percent}%`;
        progressBar.setAttribute('aria-valuenow', percent);
        progressText.textContent = `${percent}%`;
        
        if (percent >= 100) {
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.remove('progress-bar-striped');
        }
    }
    
    function showStatus(text, type = 'info') {
        statusEl.className = `alert alert-${type} d-flex align-items-center mb-0`;
        statusEl.innerHTML = `
            <i class="fas fa-${getStatusIcon(type)} me-2"></i>
            <span>${text}</span>
        `;
        
        if (type === 'info') {
            statusSpinner.classList.remove('d-none');
            statusEl.appendChild(statusSpinner);
        } else {
            statusSpinner.classList.add('d-none');
        }
    }
    
    function getStatusIcon(type) {
        const icons = {
            'success': 'check-circle',
            'info': 'sync-alt',
            'warning': 'exclamation-circle',
            'danger': 'times-circle'
        };
        return icons[type] || 'info-circle';
    }
    
    function sanitizeString(str) {
        return str ? str.normalize('NFKD').replace(/[\u0300-\u036f]/g, '') : '';
    }
    
    async function checkOrCreateQueue(users, baseMessage) {
        try {
            const response = await fetch(`${API_BASE}/checkDispatchQueue.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${accessToken}`
                },
                body: JSON.stringify({
                    dmsg_id: dmsgId,
                    user_id: userId,
                    list_id: listId,
                    message: baseMessage,
                    users_data: users
                })
            });
            
            if (!response.ok) throw new Error(`HTTP error ${response.status}`);
            
            const data = await response.json();
            
           if (data.exists) {
                const logsUrl = `https://kingslist.pro/v2/dispatch-report.php?id=${dmsgId}`;
                showStatus(`Dispatch already queued, <a href="${logsUrl}" target="_blank">check logs</a>`, 'info');
                return false;
            }
            
            return true;
        } catch (error) {
            console.error("Queue check/create error:", error);
            showStatus(`Error checking queue: ${error.message}`, 'danger');
            return false;
        }
    }
    
    // Event Listeners
    window.addEventListener('beforeunload', function(e) {
        // This check would need to be replaced with an API call if you want to maintain this functionality
        // Currently removed since we can't easily check server queue status without an additional API endpoint
    });
    
    // Main Dispatch Function
    async function executeDispatch() {
        if (startDispatch !== 1) {
            showStatus('Dispatch not in progress', 'info');
            return;
        }
        
        try {
            showStatus('Loading recipients...', 'info');
            
            // Fetch batch data
            const response = await fetch(`${API_BASE}/getDispatchBatch.php?dmsg_id=${dmsgId}`, {
                headers: { 'Authorization': `Bearer ${accessToken}` }
            });
            
            if (!response.ok) throw new Error(`HTTP error ${response.status}`);
            const data = await response.json();
            
            if (!data?.success || !Array.isArray(data.data?.messages)) {
                throw new Error("Invalid response format");
            }
            
            messages = data.data.messages;
            if (messages.length === 0) {
                showStatus('No recipients found', 'warning');
                return;
            }
            
            const baseMessage = messages[0].body || "";
            const totalUsers = messages.length;
            
            // Prepare users data
            const sanitizedUsers = messages.map(user => ({
                kc_id: user.kc_id,
                fullname: sanitizeString(user.fullname),
                username: sanitizeString(user.username),
                formatted_message: (user.body || '')
                    .replace(/<fullname>/gi, sanitizeString(user.fullname))
                    .replace(/<kc_username>/gi, sanitizeString(user.username))
            }));
            
            // Check/create queue via API
            const shouldContinue = await checkOrCreateQueue(sanitizedUsers, baseMessage);
            if (!shouldContinue) return;
            
            showStatus(`Preparing ${totalUsers} messages...`, 'info');
            
            // Add to server queue
            const queueResponse = await fetch(`${API_BASE}/addToDispatchQueue.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${accessToken}`
                },
                body: JSON.stringify({
                    dmsg_id: dmsgId,
                    list_id: listId,
                    user_id: userId,
                    message: baseMessage,
                    access_token: accessToken,
                    refresh_token: refreshToken,
                    users: sanitizedUsers
                })
            });
            
            if (!queueResponse.ok) throw new Error(await queueResponse.text());
            
            // Update UI
            updateProgress(100);
            showStatus(`Successfully queued ${totalUsers} messages`, 'success');
            resultsTable.classList.remove('d-none');
            
            // Populate results table
            resultsBody.innerHTML = sanitizedUsers.map(user => `
                <tr>
                    <td>@${user.username || user.kc_id}</td>
                    <td>${user.fullname || 'N/A'}</td>
                    <td><span class="badge bg-success">Queued</span></td>
                </tr>
            `).join('');
            
        } catch (error) {
            console.error("Dispatch error:", error);
            showStatus(`Error: ${error.message}`, 'danger');
            progressBar.classList.add('bg-danger');
        }
    }
    
    // Start dispatch if needed
    if (startDispatch === 1) {
        executeDispatch();
    }
});
</script>

                </div>



            </div><!-- container -->
            <!--Start Rightbar-->

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

    <script src="assets/js/app.js"></script>
</body>
<!--end body-->

</html>