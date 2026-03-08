<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

require_once('../include/Session.php');
require_once('../include/Functions.php');
require_once('../include/Crud.php');
require_once("../include/Controller.php");


$Controller = new Controller();

$user_id =  $_SESSION['user_id'];

$getUserList = $Controller->getUserList();

$countSavedList = $Controller->countSavedList();
// var_dump( $countSavedList);
// var_dump( $getUserList);
// die();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $user_id = $_SESSION['user_id'];
    $title = isset($_POST['title']) ? sanitizeString($_POST['title']) : '';
    $description = isset($_POST['description']) ? sanitizeString($_POST['description']) : '';
    try {
        // Prepare data array
        $data_array = [
            "title" => $title,
            "description" => $description,
            "user_id" => $user_id,
        ];
        // Insert user data into database
        $lastInsertedId = $Controller->createNameList($data_array);

        if ($lastInsertedId) {
            $_SESSION['name_id'] = $lastInsertedId;
            $_SESSION['successMsg'] = "Registration successful";
            header("Location: populate.php?id=$lastInsertedId");
            // header("Location: editlist/$lastInsertedId");
            exit();
        } else {
            $_SESSION['errorMsg'] = "Error occurred while registering.";
        }
    } catch (PDOException $e) {
        // Handle database errors
        $_SESSION['errorMsg'] = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        // Handle unexpected exceptions
        $_SESSION['errorMsg'] = "An unexpected error occurred: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>


  <meta charset="utf-8" />
  <title>Lists | Kingslist</title>
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
    /* Custom Styles */
    .bg-gradient-primary {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    }
    
    .text-white-70 {
        color: rgba(255,255,255,0.7);
    }
    
    .bg-white-10 {
        background-color: rgba(255,255,255,0.1);
    }
    
    .modal-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .form-control-lg {
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }
    
    .input-group-text {
        transition: all 0.2s ease;
    }
    
    .input-group:focus-within .input-group-text {
        background-color: #e9ecef;
    }
    
    .rounded-pill {
        border-radius: 50px !important;
    }
    
    /* Animation for modal */
    @keyframes modalSlideIn {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .modal-content {
        animation: modalSlideIn 0.3s ease-out;
        border: none;
    }
    
    /* Floating label focus state */
    .form-floating label {
        color: #6c757d;
    }
    
    .form-control:focus ~ label {
        color: #0d6efd;
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
          <div class="col-lg-12">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-transparent border-0">
                <div class="row align-items-center">
                  <div class="col">
                    <h4 class="card-title mb-0">
                      <i class="fas fa-layer-group me-2 text-primary"></i>Your Contact Lists
                    </h4>
                  </div>
                  <div class="col-auto">
                    <div class="d-flex gap-2">
                      <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle rounded-pill" type="button" id="listFilter" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="listFilter">
                          <li><a class="dropdown-item" href="#">All Lists</a></li>
                          <li><a class="dropdown-item" href="#">Most Contacts</a></li>
                          <li><a class="dropdown-item" href="#">Recently Updated</a></li>
                        </ul>
                      </div>
                      <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addList">
                        <i class="fas fa-plus me-1"></i> New List
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body pt-0">
                <div class="row g-3">
                  <?php
                  // Combine both arrays for display
                  $listsWithCounts = [];
                  foreach ($getUserList as $list) {
                    foreach ($countSavedList as $count) {
                      if ($list['name_id'] == $count['name_id']) {
                        $list['row_count'] = $count['row_count'];
                        $list['last_updated'] = $count['last_updated'];
                        break;
                      }
                    }
                    $listsWithCounts[] = $list;
                  }

                  foreach ($listsWithCounts as $list):
                    $hasDescription = !empty($list['description']);
                  ?>
                    <div class="col-md-6 col-lg-4">
                      <div class="card list-card h-100 border-0 hover-lift">
                        <div class="card-body p-3">
                          <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                              <h5 class="mb-1 text-truncate"><?= htmlspecialchars($list['title']) ?></h5>
                              <?php if ($hasDescription): ?>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($list['description']) ?></p>
                              <?php endif; ?>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">
                              <?= $list['row_count'] ?? 0 ?> contacts
                            </span>
                          </div>

                          <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                              style="width: <?= min(100, ($list['row_count'] ?? 0) / 1000 * 100) ?>%"
                              aria-valuenow="<?= $list['row_count'] ?? 0 ?>"
                              aria-valuemin="0" aria-valuemax="1000"></div>
                          </div>

                          <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                              <i class="far fa-clock me-1"></i>
                              <?= date('M j, Y', strtotime($list['last_updated'] ?? $list['updated_at'])) ?>
                            </small>
                            <div class="dropdown">
                              <button class="btn btn-sm btn-link text-muted dropdown-toggle arrow-none" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-h" style="font-size: 30px;"></i>

                              </button>
                              <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="populate.php?id=<?=$list['name_id']?>"><i class="fas fa-pencil-alt me-2"></i> Edit</a></li>
                                <li><a class="dropdown-item" href="list.php?id=<?=$list['name_id']?>"><i class="fas fa-users me-2"></i> View Contacts</a></li>
                                <li>
                                  <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-trash-alt me-2"></i> Delete</a></li>
                              </ul>
                            </div>
                          </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0">
                          <a href="dispatch.php?id=<?=$list['name_id']?>" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                            <i class="fas fa-paper-plane me-1"></i> Dispatch
                          </a>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>

                  <!-- Empty state (if no lists) -->
                  <?php if (empty($listsWithCounts)): ?>
                    <div class="col-12 text-center py-5">
                      <div class="icon-lg text-muted mb-3">
                        <i class="fas fa-inbox"></i>
                      </div>
                      <h5 class="text-muted">No lists created yet</h5>
                      <p class="text-muted mb-4">Get started by creating your first contact list</p>
                      <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addList">
                        <i class="fas fa-plus me-1"></i> Create List
                      </button>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <style>
            .list-card {
              border-radius: 12px;
              transition: all 0.3s ease;
              border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .hover-lift:hover {
              transform: translateY(-5px);
              box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }

            .progress {
              background-color: rgba(0, 0, 0, 0.05);
            }

            .text-truncate {
              max-width: 150px;
              white-space: nowrap;
              overflow: hidden;
              text-overflow: ellipsis;
            }

            .icon-lg {
              font-size: 3rem;
              opacity: 0.5;
            }
          </style>
        </div> <!-- end row -->
      </div><!-- container -->


      <!--Start Footer-->
      <?php include_once('./components/footer.php') ?>


      <!--end footer-->
    </div>
    <!-- end page content -->
    <div class="modal fade" id="addList" tabindex="-1" aria-labelledby="addListLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <!-- Modal Header with Gradient Background -->
            <div class="modal-header bg-gradient-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="modal-icon bg-white-10 rounded-circle me-3">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="addListLabel">Create New  List</h5>
                        <small class="text-white-70">Organize your recipients efficiently</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        <p class="mb-0">Provide details for your new contact list</p>
                    </div>
                    
                    <form id="addListForm" method="post">
                        <div class="mb-4">
                            <label for="listTitle" class="form-label fw-semibold">
                                <i class="fas fa-heading me-1 text-muted"></i> List Title
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-tag text-muted"></i>
                                </span>
                                <input type="text" name="title" class="form-control form-control-lg" id="listTitle" 
                                       placeholder="e.g. VIP Clients, Newsletter Subscribers" required>
                            </div>
                            <small class="text-muted">Choose a descriptive name for easy identification</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="listDescription" class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1 text-muted"></i> Description
                            </label>
                            <div class="form-floating">
                                <textarea class="form-control" name="description" id="listDescription" 
                                          style="height: 100px" 
                                          placeholder="Enter description"></textarea>
                                <label for="listDescription">Briefly describe this list's purpose</label>
                            </div>
                            <small class="text-muted">Optional but helpful for organization</small>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="submit" form="addListForm" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-save me-1"></i> Create List
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
  <script src="assets/js/app.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      if (window.location.hash === "#addList") {
        const modal = new bootstrap.Modal(document.getElementById('addList'));
        modal.show();
      }
    });
  </script>

</body>
<!--end body-->

</html>