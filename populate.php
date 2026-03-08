<?php
require_once('../include/Session.php');
require_once('../include/Functions.php');
require_once('../include/Crud.php');
require_once("../include/Controller.php");

$dbConnection = new DbConnection();
$conn = $dbConnection->getConnection();

$Controller = new Controller();

$user_id =  $_SESSION['user_id'];

$name_id = $_REQUEST['id'];
// $name_id = sanitizeString($secondparam);

$fullname = $_SESSION['fullname'];

$GetEditList = $Controller->GetEditList($name_id);
$getName = $Controller->getName($name_id);

// var_dump($name_id);
// var_dump($getName);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>


    <meta charset="utf-8" />
    <title>Edit List | Kingslist</title>
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


    <link href="assets/libs/simple-datatables/style.css" rel="stylesheet" type="text/css" />
    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />

    <style>
        .dropzone {
            border-color: #dee2e6;
            transition: all 0.3s;
            cursor: pointer;
        }

        .dropzone:hover {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }

        #usernamePreview {
            max-height: 120px;
            overflow-y: auto;
        }
    </style>
    <style>
        /* Custom Styles */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }

        .text-white-70 {
            color: rgba(255, 255, 255, 0.7);
        }

        .search-section {
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        .contact-row:hover {
            background-color: rgba(13, 110, 253, 0.03);
        }

        .avatar-xs {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .table-container {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-group .btn {
            border-radius: 4px !important;
        }
    </style>
    <style>
        /* Modern Search Results Container */
        #myUL {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 12px;
            margin-top: 15px;
        }

        /* Search Result Item - Card Style */
        .search-result-item {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .search-result-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
            border-color: #dee2e6;
        }

        /* Result Link Container */
        .search-result-item a {
            display: flex;
            align-items: center;
            padding: 12px;
            text-decoration: none;
            color: #212529;
        }

        /* Circular Avatar with Fallback */
        .result-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #6c757d;
            flex-shrink: 0;
            border: 2px solid #e9ecef;
        }

        /* Default avatar when no image */
        .result-avatar:empty::before {
            content: "\f007";
            /* FontAwesome user icon */
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
        }

        /* Text Content */
        .search-result-item span {
            flex-grow: 1;
            font-size: 14px;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-right: 10px;
        }

        /* Username styling */
        .search-result-item span::after {
            content: attr(data-username);
            display: block;
            font-size: 12px;
            color: #6c757d;
            margin-top: 2px;
        }

        /* Add Button */
        .search-result-item .btn {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 20px;
            transition: all 0.2s;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .search-result-item .btn-success {
            background-color: #1abc9c;
            border-color: #1abc9c;
        }

        .search-result-item .btn-success:hover {
            background-color: #16a085;
            border-color: #16a085;
            transform: scale(1.05);
        }

        /* Empty State */
        #searchPrompt {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-size: 15px;
        }

        #searchPrompt i {
            font-size: 24px;
            margin-bottom: 10px;
            color: #adb5bd;
        }

        /* Duplicate Item Styling */
        .duplicate-item {
            opacity: 0.7;
            position: relative;
        }

        .duplicate-item::after {
            content: "Duplicate";
            position: absolute;
            top: 5px;
            right: 5px;
            background: #f39c12;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            #myUL {
                grid-template-columns: 1fr;
            }
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
                        <div class="card shadow-lg border-0 overflow-hidden">
                            <!-- Card Header with Gradient Background -->
                            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title mb-0 text-white">
                                        <i class="fas fa-users me-2"></i><?= htmlspecialchars($getName[0]['title']) ?>
                                    </h4>
                                    <small class="text-white-70">Total: <strong class="text-white"><?= count($GetEditList) ?> Recipients</strong></small>
                                </div>
                                <div>
                                    <a href="dispatch.php?id=<?= $name_id ?>" class="btn btn-light btn-sm rounded-pill">
                                        <i class="fas fa-paper-plane me-1"></i> Dispatch Now
                                    </a>
                                </div>
                            </div><!--end card-header-->

                            <div class="card-body pt-0">
                                <!-- Search Section -->
                                <div class="search-section p-3 mb-4 rounded-3" style="background-color: #f8f9fa;">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                                <input type="text" id="search" class="form-control form-control-lg" placeholder="Search contacts...">
                                                <input type="hidden" id="name_id" value="<?= htmlspecialchars($name_id) ?>">
                                                <input type="hidden" id="user_id" value="<?= htmlspecialchars($user_id) ?>">
                                                <button type="button" onclick="searchList()" class="btn btn-primary">
                                                    <i class="fas fa-search me-1"></i> Search
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#fileUploadModal">
                                                <i class="fas fa-file-import me-1"></i> Bulk Import
                                            </button>
                                        </div>
                                    </div>

                                    <div class="search-results mt-3">
                                        <div class="text-center py-3">
                                            <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                            <h5 id="searchPrompt" class="text-muted">Search for contacts to add to your list</h5>
                                        </div>
                                        <ul id="myUL" class="list-group" style="max-height: 300px; overflow-y: auto;">
                                            <!-- Search results will appear here -->
                                        </ul>
                                    </div>
                                </div>

                                <!-- Contacts Table -->
                                <div class="table-container">
                                    <form id="dataForm" action="" method="post">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle" id="basic-datatable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="50">#</th>
                                                        <th>Contact</th>
                                                        <th width="150" class="text-end">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($GetEditList)) : ?>
                                                        <?php foreach ($GetEditList as $key => $user) : ?>
                                                            <tr class="contact-row">
                                                                <td class="text-muted"><?= $key + 1 ?></td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-xs bg-primary bg-opacity-10 text-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                                            <i class="fas fa-user"></i>
                                                                        </div>
                                                                        <span><?= htmlspecialchars($user['list_name']) ?></span>
                                                                    </div>
                                                                </td>
                                                                <td class="text-end">
                                                                    <div class="btn-group" role="group">
                                                                        <button type="button" class="add-btn btn btn-sm btn-success d-none" data-name="<?= htmlspecialchars($user['list_name']) ?>">
                                                                            <i class="fas fa-plus"></i>
                                                                        </button>
                                                                        <button type="button" class="remove-btn btn btn-sm btn-danger" onclick="removeContact(<?= ($user['list_id']) ?>)" data-name="<?= htmlspecialchars($user['list_id']) ?>">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else : ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center py-4">
                                                                <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                                                                <h5 class="text-muted">No contacts found</h5>
                                                                <p class="text-muted">Add contacts using the search or import feature above</p>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <input type="hidden" id="selectedUsers" name="selectedUsers" value="">
                                        <div class="d-flex justify-content-between mt-4 mb-3">
                                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                                <i class="fas fa-save me-1"></i> Save Changes
                                            </button>
                                            <a href="dispatch.php?id=<?= $name_id ?>" class="btn btn-success rounded-pill px-4">
                                                <i class="fas fa-paper-plane me-1"></i> Dispatch List
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div><!--end card-body-->
                        </div><!--end card-->
                    </div><!--end col-->





                </div><!--end row-->



            </div><!-- container -->

            <!--end Rightbar-->
            <!--Start Footer-->
            <?php include_once('./components/footer.php') ?>

            <!--end footer-->
        </div>
        <!-- end page content -->
        <!-- Modal -->
        <div class="modal fade" id="fileUploadModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title text-primary" id="fileUploadModalLabel">Upload User List</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- File Requirements Box -->
                        <div class="alert alert-warning mb-4">
                            <h5 class="alert-heading fw-bold">📌 File Requirements</h5>
                            <ul class="mb-1">
                                <li><strong>File type:</strong> Must be <span class="badge bg-dark">.csv</span> format</li>
                                <!-- <li><strong>Header:</strong> First row must contain <code>kc_username</code> as column header</li> -->
                                <li>
                                    <strong>Content Rules:</strong>
                                    <ul>
                                        <li>Each subsequent row must contain exactly one username</li>
                                        <li>Only valid KingsChat usernames (e.g. <code>dev_joel</code>, <code>flourishingpfo</code>)</li>
                                        <li class="text-danger">Do NOT include prefixes like <code>https://kingschat.com/</code></li>
                                    </ul>
                                </li>
                            </ul>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                    <span class="ms-2">Please be patient while we process your data</span>
                                </div>
                                <a href="https://kingslist.pro/app/default/template/template.csv" download
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i> Download CSV Template
                                </a>
                            </div>
                        </div>

                        <!-- Upload Section -->
                        <input type="hidden" id="user_id" value="<?= $user_id ?>">
                        <div id="dropzone" class="dropzone border-2 border-dashed rounded p-5 text-center mb-3">
                            <i class="bi bi-cloud-arrow-up fs-1 text-muted"></i>
                            <p class="h5 mt-2">Drag & drop your CSV file here</p>
                            <p class="text-muted">or click to browse your files</p>
                            <input type="file" id="fileInput" class="d-none" accept=".csv">
                        </div>

                        <!-- File Preview -->
                        <div class="file-list card mt-3" id="fileList">
                            <div class="card-body text-center py-4">
                                <i class="bi bi-file-earmark-text fs-1 text-muted"></i>
                                <p class="text-muted mb-0">No files selected</p>
                            </div>
                        </div>

                        <!-- Username Preview -->
                        <div class="alert alert-info mt-3 d-none" id="previewAlert">
                            <strong><i class="bi bi-check-circle"></i> Found <span id="usernameCount" class="fw-bold">0</span> valid usernames</strong>
                            <div id="usernamePreview" class="mt-2 small bg-white p-2 rounded"></div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-primary" id="uploadBtn">
                            <i class="bi bi-upload"></i> Upload to Kingslist
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end page-wrapper -->

    <!-- Javascript  -->
    <!-- vendor js -->

    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>

    <script src="assets/libs/simple-datatables/umd/simple-datatables.js"></script>
    <script src="assets/js/pages/datatable.init.js"></script>
    <script src="/app/default/js/populatewithbot.js"></script>

    <script src="assets/js/app.js"></script>





    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropzone = document.getElementById('dropzone');
            const fileInput = document.getElementById('fileInput');
            const fileList = document.getElementById('fileList');
            const uploadBtn = document.getElementById('uploadBtn');
            const previewAlert = document.getElementById('previewAlert');
            const usernameCount = document.getElementById('usernameCount');
            const usernamePreview = document.getElementById('usernamePreview');
            let selectedFiles = [];
            let extractedUsernames = [];

            // Click to browse
            dropzone.addEventListener('click', () => fileInput.click());

            // Handle file selection
            fileInput.addEventListener('change', handleFiles);

            // Drag and drop
            ['dragover', 'dragleave', 'drop'].forEach(event => {
                dropzone.addEventListener(event, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            dropzone.addEventListener('dragover', () => {
                dropzone.classList.add('border-primary', 'bg-light');
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('border-primary', 'bg-light');
            });

            dropzone.addEventListener('drop', (e) => {
                dropzone.classList.remove('border-primary', 'bg-light');
                fileInput.files = e.dataTransfer.files;
                handleFiles({
                    target: fileInput
                });
            });

            // Process selected files
            async function handleFiles(e) {
                selectedFiles = Array.from(e.target.files);
                updateFileList();

                // Extract and preview usernames
                extractedUsernames = await extractUsernames(selectedFiles);
                updateUsernamePreview();
            }

            // Display selected files
            function updateFileList() {
                if (selectedFiles.length === 0) {
                    fileList.innerHTML = '<p class="text-muted text-center">No files selected</p>';
                    return;
                }

                fileList.innerHTML = `
            <div class="list-group">
                ${selectedFiles.map(file => `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>${file.name}</span>
                            <small class="text-muted">${(file.size / 1024).toFixed(2)} KB</small>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
            }

            // Update username preview
            function updateUsernamePreview() {
                if (extractedUsernames.length === 0) {
                    previewAlert.classList.add('d-none');
                    return;
                }

                usernameCount.textContent = extractedUsernames.length;
                usernamePreview.innerHTML = `
            <div class="d-flex flex-wrap gap-1">
                ${extractedUsernames.slice(0, 20).map(u => `
                    <span class="badge bg-secondary">${u}</span>
                `).join('')}
                ${extractedUsernames.length > 20 ? '<span class="badge bg-light text-dark">+' + (extractedUsernames.length - 20) + ' more</span>' : ''}
            </div>
        `;
                previewAlert.classList.remove('d-none');
            }

            // Upload to API
            // uploadBtn.addEventListener('click', async function() {
            //     if (extractedUsernames.length === 0) {
            //         alert('No valid usernames found in the file');
            //         return;
            //     }

            //     const user_id = document.getElementById("user_id").value;
            //     uploadBtn.disabled = true;
            //     uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

            //     try {
            //         // Process in batches of 100 with delay
            //         const batchSize = 100;
            //         let successfulBatches = 0;
            //         let failedBatches = 0;
            //         let totalInserted = 0;

            //         for (let i = 0; i < extractedUsernames.length; i += batchSize) {
            //             const batch = extractedUsernames.slice(i, i + batchSize);

            //             try {
            //                 const response = await fetch('https://kingslist.pro/app/default/api/add-search.php', {
            //                     method: 'POST',
            //                     headers: {
            //                         'Content-Type': 'application/json',
            //                         'X-Requested-With': 'XMLHttpRequest'
            //                     },
            //                     body: JSON.stringify({
            //                         name_id: '<?= $name_id ?>',
            //                         user_id: user_id,
            //                         search_req: batch
            //                     })
            //                 });

            //                 const result = await response.json();

            //                 if (result.status !== 'success') {
            //                     console.warn(`Batch ${i/batchSize + 1} failed:`, result.message);
            //                     failedBatches++;
            //                     continue;
            //                 }

            //                 successfulBatches++;
            //                 totalInserted += batch.length;

            //                 // Add delay between batches (1 second)
            //                 if (i + batchSize < extractedUsernames.length) {
            //                     await new Promise(resolve => setTimeout(resolve, 1000));
            //                 }

            //             } catch (batchError) {
            //                 console.error(`Batch ${i/batchSize + 1} error:`, batchError);
            //                 failedBatches++;
            //             }
            //         }

            //         // Success message with accurate count
            //         let message = `Successfully processed ${totalInserted} usernames`;

            //         if (failedBatches > 0) {
            //             message += `\n(${failedBatches} batches failed - please check logs)`;
            //         }

            //         alert(message);
            //         $('#fileUploadModal').modal('hide');

            //         // Only reload if everything succeeded
            //         if (failedBatches === 0) {
            //             location.reload();
            //         }

            //     } catch (error) {
            //         console.error('Upload error:', error);
            //         alert('Critical error: ' + error.message);
            //     } finally {
            //         uploadBtn.disabled = false;
            //         uploadBtn.textContent = 'Upload to KingsChat';
            //     }
            // });

            // Upload to API - Modified version
            uploadBtn.addEventListener('click', async function() {
                if (extractedUsernames.length === 0) {
                    alert('No valid usernames found in the file');
                    return;
                }

                const user_id = document.getElementById("user_id").value;
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

                try {
                    // Process in smaller batches to avoid server timeouts
                    const batchSize = 50; // Reduced from 100 to 50
                    let successfulInserts = 0;
                    let failedInserts = 0;
                    let processedCount = 0;

                    for (let i = 0; i < extractedUsernames.length; i += batchSize) {
                        const batch = extractedUsernames.slice(i, i + batchSize);
                        processedCount += batch.length;

                        try {
                            const response = await fetch('https://kingslist.pro/app/default/api/add-search.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    name_id: '<?= $name_id ?>',
                                    user_id: user_id,
                                    search_req: batch
                                })
                            });

                            const result = await response.json();

                            if (result.status === 'success') {
                                successfulInserts += batch.length;
                            } else {
                                console.warn(`Batch ${Math.floor(i/batchSize) + 1} failed:`, result.message);
                                failedInserts += batch.length;

                                // If batch failed, try processing usernames individually
                                for (const username of batch) {
                                    try {
                                        const singleResponse = await fetch('https://kingslist.pro/app/default/api/add-search.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            },
                                            body: JSON.stringify({
                                                name_id: '<?= $name_id ?>',
                                                user_id: user_id,
                                                search_req: [username]
                                            })
                                        });

                                        const singleResult = await singleResponse.json();
                                        if (singleResult.status === 'success') {
                                            successfulInserts++;
                                            failedInserts--;
                                        }
                                    } catch (singleError) {
                                        console.error(`Failed to insert username ${username}:`, singleError);
                                    }
                                }
                            }

                            // Update progress indicator
                            uploadBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing ${processedCount}/${extractedUsernames.length}...`;

                            // Add delay between batches (1.5 seconds)
                            if (i + batchSize < extractedUsernames.length) {
                                await new Promise(resolve => setTimeout(resolve, 1500));
                            }

                        } catch (batchError) {
                            console.error(`Batch ${Math.floor(i/batchSize) + 1} error:`, batchError);
                            failedInserts += batch.length;
                        }
                    }

                    // Success message with accurate count
                    let message = `Successfully processed ${successfulInserts} of ${extractedUsernames.length} usernames`;

                    if (failedInserts > 0) {
                        message += `\n${failedInserts} usernames failed to process`;
                    }

                    alert(message);
                    $('#fileUploadModal').modal('hide');

                    // Only reload if everything succeeded
                    if (failedInserts === 0) {
                        location.reload();
                    }

                } catch (error) {
                    console.error('Upload error:', error);
                    alert('Critical error: ' + error.message);
                } finally {
                    uploadBtn.disabled = false;
                    uploadBtn.textContent = 'Upload to KingsChat';
                }
            });
            // Extract usernames from files
            async function extractUsernames(files) {
                const usernames = new Set();

                for (const file of files) {
                    try {
                        const content = await readFile(file);
                        const lines = content.split('\n');

                        lines.forEach(line => {
                            const username = line.trim()
                                .replace(/^@/, '')
                                .replace(/\s+/g, '')
                                .toLowerCase();

                            if (username && username.length >= 3) {
                                usernames.add(username);
                            }
                        });
                    } catch (error) {
                        console.error(`Error processing ${file.name}:`, error);
                    }
                }

                return Array.from(usernames);
            }

            function readFile(file) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = e => resolve(e.target.result);
                    reader.onerror = () => reject(new Error(`Could not read file: ${file.name}`));
                    reader.readAsText(file);
                });
            }
        });
    </script> -->

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        const uploadBtn = document.getElementById('uploadBtn');
        const previewAlert = document.getElementById('previewAlert');
        const usernameCount = document.getElementById('usernameCount');
        const usernamePreview = document.getElementById('usernamePreview');
        let selectedFiles = [];
        let extractedUsernames = [];

        // Click to browse
        dropzone.addEventListener('click', () => fileInput.click());

        // Handle file selection
        fileInput.addEventListener('change', handleFiles);

        // Drag and drop
        ['dragover', 'dragleave', 'drop'].forEach(event => {
            dropzone.addEventListener(event, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        dropzone.addEventListener('dragover', () => {
            dropzone.classList.add('border-primary', 'bg-light');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-primary', 'bg-light');
        });

        dropzone.addEventListener('drop', (e) => {
            dropzone.classList.remove('border-primary', 'bg-light');
            fileInput.files = e.dataTransfer.files;
            handleFiles({
                target: fileInput
            });
        });

        // Process selected files
        async function handleFiles(e) {
            selectedFiles = Array.from(e.target.files);
            updateFileList();

            // Extract and preview usernames
            extractedUsernames = await extractUsernames(selectedFiles);
            updateUsernamePreview();
        }

        // Display selected files
        function updateFileList() {
            if (selectedFiles.length === 0) {
                fileList.innerHTML = '<p class="text-muted text-center">No files selected</p>';
                return;
            }

            fileList.innerHTML = `
                <div class="list-group">
                    ${selectedFiles.map(file => `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>${file.name}</span>
                                <small class="text-muted">${(file.size / 1024).toFixed(2)} KB</small>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        // Update username preview - FIXED to show exact count clearly
        function updateUsernamePreview() {
            if (extractedUsernames.length === 0) {
                previewAlert.classList.add('d-none');
                return;
            }

            // Create a more prominent display of the total count
            usernameCount.textContent = extractedUsernames.length.toLocaleString();
            
            // Add a subtitle that clearly states the total
            usernamePreview.innerHTML = `
                <div class="mb-2">
                    <strong>Found ${extractedUsernames.length.toLocaleString()} valid usernames in your file</strong>
                </div>
                <div class="d-flex flex-wrap gap-1 mb-2">
                    ${extractedUsernames.slice(0, 20).map(u => `
                        <span class="badge bg-secondary">${u}</span>
                    `).join('')}
                    ${extractedUsernames.length > 20 ? 
                        `<span class="badge bg-info text-dark">+${(extractedUsernames.length - 20).toLocaleString()} more</span>` : 
                        ''}
                </div>
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    ${extractedUsernames.length >= 1000 ? 
                        `Processing ${extractedUsernames.length.toLocaleString()} usernames may take a few moments` : 
                        'Ready to upload'}
                </div>
            `;
            
            previewAlert.classList.remove('d-none');
        }

        // Upload to API - Modified version
        uploadBtn.addEventListener('click', async function() {
            if (extractedUsernames.length === 0) {
                alert('No valid usernames found in the file');
                return;
            }

            const user_id = document.getElementById("user_id").value;
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

            try {
                // Process in smaller batches to avoid server timeouts
                const batchSize = 50;
                let successfulInserts = 0;
                let failedInserts = 0;
                let processedCount = 0;

                for (let i = 0; i < extractedUsernames.length; i += batchSize) {
                    const batch = extractedUsernames.slice(i, i + batchSize);
                    processedCount += batch.length;

                    try {
                        const response = await fetch('https://kingslist.pro/app/default/api/add-search.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                name_id: '<?= $name_id ?>',
                                user_id: user_id,
                                search_req: batch
                            })
                        });

                        const result = await response.json();

                        if (result.status === 'success') {
                            successfulInserts += batch.length;
                        } else {
                            console.warn(`Batch ${Math.floor(i/batchSize) + 1} failed:`, result.message);
                            failedInserts += batch.length;

                            // If batch failed, try processing usernames individually
                            for (const username of batch) {
                                try {
                                    const singleResponse = await fetch('https://kingslist.pro/app/default/api/add-search.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        },
                                        body: JSON.stringify({
                                            name_id: '<?= $name_id ?>',
                                            user_id: user_id,
                                            search_req: [username]
                                        })
                                    });

                                    const singleResult = await singleResponse.json();
                                    if (singleResult.status === 'success') {
                                        successfulInserts++;
                                        failedInserts--;
                                    }
                                } catch (singleError) {
                                    console.error(`Failed to insert username ${username}:`, singleError);
                                }
                            }
                        }

                        // Update progress indicator with exact numbers
                        uploadBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing ${processedCount.toLocaleString()}/${extractedUsernames.length.toLocaleString()}...`;

                        // Add delay between batches
                        if (i + batchSize < extractedUsernames.length) {
                            await new Promise(resolve => setTimeout(resolve, 1500));
                        }

                    } catch (batchError) {
                        console.error(`Batch ${Math.floor(i/batchSize) + 1} error:`, batchError);
                        failedInserts += batch.length;
                    }
                }

                // Success message with accurate count
                let message = `Successfully processed ${successfulInserts.toLocaleString()} of ${extractedUsernames.length.toLocaleString()} usernames`;

                if (failedInserts > 0) {
                    message += `\n${failedInserts.toLocaleString()} usernames failed to process`;
                }

                alert(message);
                $('#fileUploadModal').modal('hide');

                // Only reload if everything succeeded
                if (failedInserts === 0) {
                    location.reload();
                }

            } catch (error) {
                console.error('Upload error:', error);
                alert('Critical error: ' + error.message);
            } finally {
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload to KingsChat';
            }
        });

        // Extract usernames from files
        async function extractUsernames(files) {
            const usernames = new Set();

            for (const file of files) {
                try {
                    const content = await readFile(file);
                    const lines = content.split('\n');

                    lines.forEach(line => {
                        const username = line.trim()
                            .replace(/^@/, '')
                            .replace(/\s+/g, '')
                            .toLowerCase();

                        if (username && username.length >= 3) {
                            usernames.add(username);
                        }
                    });
                } catch (error) {
                    console.error(`Error processing ${file.name}:`, error);
                }
            }

            return Array.from(usernames);
        }

        function readFile(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = e => resolve(e.target.result);
                reader.onerror = () => reject(new Error(`Could not read file: ${file.name}`));
                reader.readAsText(file);
            });
        }
    });
</script>
</body>
<!--end body-->
<script>
    // Function to filter duplicates before displaying results
    function displaySearchResults(results) {
        const myUL = document.getElementById('myUL');
        myUL.innerHTML = '';

        if (!results || results.length === 0) {
            document.getElementById('searchPrompt').textContent = 'No contacts found ';
            return;
        }

        // Filter duplicates by username (case insensitive)
        const uniqueResults = [];
        const usernames = new Set();

        results.forEach(result => {
            // Extract username from the span text (assuming format "Name (username)")
            const usernameMatch = result.name.match(/\(([^)]+)\)/);
            const username = usernameMatch ? usernameMatch[1].toLowerCase() : result.name.toLowerCase();

            if (!usernames.has(username)) {
                usernames.add(username);
                uniqueResults.push(result);
            }
        });

        // Display unique results
        uniqueResults.forEach(result => {
            const li = document.createElement('li');
            li.className = 'search-result-item';

            // Extract name and username
            const nameMatch = result.name.match(/^([^(]+)/);
            const usernameMatch = result.name.match(/\(([^)]+)\)/);
            const name = nameMatch ? nameMatch[0].trim() : result.name;
            const username = usernameMatch ? usernameMatch[1] : '';

            li.innerHTML = `
            <a href="#">
                ${result.avatar ? 
                    `<img src="${result.avatar}" class="result-avatar">` : 
                    `<div class="result-avatar"></div>`
                }
                <span data-username="${username}">${name}</span>
                <button class="btn btn-success btn-sm">Add</button>
            </a>
        `;
            myUL.appendChild(li);
        });

        // Show the results
        myUL.style.display = 'grid';
        document.getElementById('searchPrompt').style.display = 'none';
    }

    function removeContact(listId) {
        if (confirm('Are you sure you want to remove this contact?')) {
            // Add your removal logic here
            console.log('Removing contact:', listId);
            // You might want to refresh the table after removal
        }
    }
</script>

</html>