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
$getList = $Controller->getDispNamesList($name_id);
$totalRecipients = count($getList);
// var_dump($name_id);
// var_dump($getName);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>


    <meta charset="utf-8" />
    <title>View List | Kingslist</title>
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
                                    /* Improved table styling */
                                    .table-container {
                                        border-radius: 8px;
                                        overflow: hidden;
                                        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
                                    }

                                    #namesListTable th {
                                        font-weight: 600;
                                        text-transform: uppercase;
                                        font-size: 0.75rem;
                                        letter-spacing: 0.5px;
                                    }

                                    #namesListTable tbody tr {
                                        transition: all 0.2s ease;
                                    }

                                    #namesListTable tbody tr:hover {
                                        background-color: rgba(13, 110, 253, 0.03);
                                    }

                                    .avatar-xs {
                                        width: 32px;
                                        height: 32px;
                                    }

                                    /* Search section improvements */
                                    .search-section {
                                        border: 1px solid rgba(0, 0, 0, 0.05);
                                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
                                    }

                                    #searchInput {
                                        border-left: none;
                                    }

                                    .input-group-text {
                                        border-right: none;
                                    }

                                    /* Button styling */
                                    .btn-outline-danger {
                                        border-radius: 50px;
                                        padding: 5px 10px;
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
                                    <small class="text-white-70">Total: <strong class="text-white"><?= $totalRecipients ?> Recipients</strong></small>
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
                                                <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Search by name or username..." onkeyup="filterTable()">
                                                <input type="hidden" id="name_id" value="<?= htmlspecialchars($name_id) ?>">
                                                <input type="hidden" id="user_id" value="<?= htmlspecialchars($user_id) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <a href="populate.php?id=<?= $getName[0]['name_id'] ?>" class="btn btn-outline-info">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contacts Table -->
                                <div class="table-container">
                                    <form id="dataForm" action="" method="post">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle" id="namesListTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="50">#</th>
                                                        <th>Contact</th>
                                                        <th>Username</th>
                                                        <th width="150" class="text-end">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Data will be loaded here -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <input type="hidden" id="selectedUsers" name="selectedUsers" value="">
                                        <div class="d-flex justify-content-between mt- mb-3">
                                            <button class="btn btn-primary rounded-pill px-4" type="button" id="loadMoreBtn">
                                                <span class="spinner-grow spinner-grow-sm me-1 d-none" id="loadSpinner" role="status" aria-hidden="true"></span>
                                                Load More
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

    </div>
    <!-- end page-wrapper -->

    <!-- Javascript  -->
    <!-- vendor js -->

    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>

    <script src="assets/libs/simple-datatables/umd/simple-datatables.js"></script>
    <script src="assets/js/pages/datatable.init.js"></script>

    <script src="assets/js/app.js"></script>




                                <script>
// Define these variables at the top level
let allData = [];
let offset = 0;
const limit = 20;
const name_id = "<?php echo $name_id; ?>";

document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality
    document.getElementById('searchInput').addEventListener('keyup', filterTable);
    
    // Initial data load
    loadMoreData();
    
    // Load more button event
    document.getElementById('loadMoreBtn').addEventListener('click', loadMoreData);
});

// Now properly define filterTable in the global scope
function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    if (!searchTerm) {
        renderTable(allData);
        return;
    }

    const filteredData = allData.filter(user => 
        user.list_name.toLowerCase().includes(searchTerm) || 
        (user.kc_username && user.kc_username.toLowerCase().includes(searchTerm))
    );
    
    renderTable(filteredData);
}

async function loadMoreData() {
    const loadSpinner = document.getElementById('loadSpinner');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    
    loadSpinner.classList.remove('d-none');
    loadMoreBtn.disabled = true;

    try {
        const response = await fetch(`/app/default/api/loadMoreNamesList.php?name_id=${name_id}&offset=${offset}&limit=${limit}`);
        const data = await response.json();

        if (data.error) {
            console.error(data.error);
            return;
        }

        allData = [...allData, ...data];
        renderTable(allData);
        offset += limit;
        
        if (data.length < limit) {
            loadMoreBtn.style.display = 'none';
        }
    } catch (error) {
        console.error('Error:', error);
    } finally {
        loadSpinner.classList.add('d-none');
        loadMoreBtn.disabled = false;
    }
}

function renderTable(data) {
    const tableBody = document.querySelector('#namesListTable tbody');
    tableBody.innerHTML = '';
    
    data.forEach((user, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-xs bg-primary bg-opacity-10 text-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user"></i>
                    </div>
                    <span>${user.list_name}</span>
                </div>
            </td>
            <td>${user.kc_username || 'N/A'}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-danger" onclick="removeContact(${user.list_id})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

function removeContact(listId) {
    if (confirm('Are you sure you want to remove this contact?')) {
        // Add your removal logic here
        console.log('Removing contact:', listId);
    }
}
</script>

</body>
<!--end body-->


</html>