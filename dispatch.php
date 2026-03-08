<?php


ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once('../include/Session.php');
require_once('../include/Functions.php');
require_once('../include/Crud.php');
require_once("../include/Controller.php");

// var_dump($_SESSION['fullname']);
// die();
$dbConnection = new DbConnection();
$conn = $dbConnection->getConnection();

$Controller = new Controller();

$user_id =  $_SESSION['user_id'];

$name_id = sanitizeString($_GET['id'] ?? '');
// $name_id = sanitizeString($secondparam);

$getName = $Controller->getName($name_id);
$getList = $Controller->getNamesList($name_id);

$name_user_id = $getName[0]['user_id'];

if ($name_user_id != $user_id) {
    echo "redirecting...";
    header("refresh:3;url=/dashboard");
    exit();
}

//$getList = $Controller->getNamesList($name_id);

$showform = true;

$bal = intval($_SESSION['dispatch_credit']);
//echo "name id: $name_id, userid: ".$_SESSION['user_id'];

$getList = $Controller->getDispCountNamesList($name_id);

if ($getList[0]['list_count'] < 1) {
    $_SESSION['errorMsg'] = "No names found in this list - " . $getList[0]['list_count'];
    $showform = false;
} else if ($bal <= 0) {
    $_SESSION['errorMsg'] = "Kindly fund your wallet.";
    $showform = false;
} else {
    $totalRecipients = $getList[0]['list_count'];
}

//var_dump($totalRecipients);
//die();



// var_dump($getList);
// Fetch the current balance
if (isset($_POST['send'])) {
    $name_id = sanitizeString($_POST['name_id']);
    $body = sanitizeString(htmlspecialchars($_POST['messageBody'], ENT_NOQUOTES));
    $user_id = $_SESSION['user_id'];
    $title = isset($_POST['title']) ? sanitizeString($_POST['title']) : 'Default Title';  // Get the title from the form input


    //echo "$name_id, $body, $user_id, $title";
    if (empty($name_id) || empty($body) || empty($user_id) || empty($title)) {
        $_SESSION['errorMsg'] = "All fields are required.";
    } else {
        // Get the list of names

        $listAmt = $totalRecipients;
        $disBalance = $bal - $listAmt;

        if ($disBalance < 0) {
            $_SESSION['errorMsg'] = "Insufficient balance.";
        } else {

            try {
                $data = [
                    "name_id" => $name_id,
                    "title" => $title,
                    "body" => $body,
                    "status" => 0,
                    "user_id" => $user_id,
                ];

                $result = $Controller->dispatchMsg($data);

                if ($result) {
                    // Log the dispatch for this recipient
                    // Update the balance in the database after all messages are dispatched
                    if ($Controller->updateCreditBal($user_id, $disBalance)) {
                        $_SESSION['dispatch_credit'] = $disBalance;
                    } else {
                        $_SESSION['errorMsg'] = "Unable to update Credit";
                    }
                    $start_dispatch = 1;
                    $_SESSION['successMsg'] = "Message queued successfully.";
                    $showform = false;
                    header("refresh:1;url=dispatchMessage.php?id=$result");
                } else {
                    $_SESSION['errorMsg'] = "Error occurred while dispatching.";
                }
            } catch (Exception $e) {
                // Handle unexpected exceptions
                $_SESSION['errorMsg'] = "An unexpected error occurred: " . $e->getMessage();
            }
        }
    }
}



?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>


    <meta charset="utf-8" />
    <title>Dispatch | Kingslist</title>
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


    <link rel="stylesheet" href="assets/libs/quill/quill.snow.css">
    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }

        .editor-container {
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .editor-container:focus-within {
            background-color: white;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        #messageBody {
            resize: none;
            background: transparent;
            min-height: 200px;
        }

        #messageBody:focus {
            box-shadow: none;
        }

        #charCount {
            font-weight: bold;
            color: #0d6efd;
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn-success {
            background-color: #1abc9c;
            border-color: #1abc9c;
        }

        .btn-success:hover {
            background-color: #16a085;
            border-color: #16a085;
            transform: translateY(-2px);
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .invalid-feedback {
            font-size: 0.85rem;
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
                <?php
                    echo errorMsg();
                    echo successMsg();
                    echo infoMsg();
                    ?>
                <div class="row justify-content-center">
                    <!-- Include Quill Editor CDN (if not already included) -->
                    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

                    <div class="col-12">
                        <div class="card shadow-lg border-0">
                            <!-- Card Header with Gradient Background -->
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="row align-items-center justify-content-between">
                                    <div class="col">
                                        <h4 class="card-title mb-0 text-white">
                                            <i class="fas fa-paper-plane me-2"></i>Compose Dispatch Message
                                        </h4>
                                    </div>
                                    <div class="col-auto">
                                        <a href="wallet.php#topup" class="topup-btn">
                                            <span class="topup-icon">
                                                <i class="fas fa-bolt"></i>
                                            </span>
                                            <span class="topup-text">Instant Top-Up</span>
                                            <span class="topup-badge">+</span>
                                        </a>
                                        <div class="badge bg-white text-primary fs-6 p-2 rounded-pill">
                                            <i class="fas fa-coins me-1"></i>
                                            <strong>Dispatch Credit:</strong>
                                            <span class="text-success"><?= $_SESSION['dispatch_credit'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body pt-4">
                                <!-- Template Tags -->
                                <div class="mb-4">
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="text-muted small">Insert tags:</span>
                                        <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="insertTag('<fullname>')">
                                            <i class="fas fa-user-tag me-1"></i> &lt;fullname&gt;
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="insertTag('<kc_username>')">
                                            <i class="fas fa-at me-1"></i> &lt;kc_username&gt;
                                        </button>
                                    </div>
                                </div>

                                <?php if ($showform): ?>
                                    <form id="editorForm" action="" method="post" class="needs-validation" novalidate>
                                        <input type="hidden" name="name_id" value="<?= htmlspecialchars($getName[0]['name_id'] ?? '') ?>">

                                        <!-- Recipient Info -->
                                        <div class="alert alert-info mb-4">
                                            <i class="fas fa-users me-2"></i>
                                            <strong>Dispatch to <?= ucfirst(htmlspecialchars($getName[0]['name'] ?? '')) ?></strong>
                                            <span class="badge bg-primary ms-2"><?= $totalRecipients ?> Recipients</span>
                                        </div>

                                        <!-- Title Input -->
                                        <div class="mb-4">
                                            <label for="messageTitle" class="form-label">Message Title</label>
                                            <input type="text" id="messageTitle" name="title" class="form-control form-control-lg" placeholder="Enter message title..." required>
                                            <div class="invalid-feedback">Please provide a message title</div>
                                        </div>

                                        <!-- Message Editor -->
                                        <div class="mb-4">
                                            <label for="messageBody" class="form-label">Message Content</label>
                                            <div class="editor-container border rounded-3 p-3" style="min-height: 200px;">
                                                <textarea id="messageBody" name="messageBody" class="form-control border-0"
                                                    placeholder="E.g. Dear <fullname>, ..."
                                                    rows="8" maxlength="1200" required></textarea>
                                            </div>
                                            <div class="text-end text-muted small mt-1">
                                                <span id="charCount">0</span>/1200 characters
                                            </div>
                                        </div>
                                        <input type="hidden" name="body" id="bodyInput">

                                        <!-- Action Buttons -->
                                        <div class="d-flex justify-content-between mt-4">
                                            <div>
                                                <button type="button" onclick="dispatchTest()" id="dispatch_test" class="btn btn-info rounded-pill px-4">
                                                    <i class="fas fa-paper-plane me-1"></i> Send Test
                                                </button>
                                                <button type="reset" class="btn btn-outline-danger rounded-pill px-4">
                                                    <i class="fas fa-trash-alt me-1"></i> Clear
                                                </button>
                                            </div>
                                            <button type="submit"  name="send" id="startDispatch" class="btn btn-success rounded-pill px-4">
                                                <i class="fas fa-bolt me-1"></i> Dispatch Now
                                            </button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-history fa-2x me-3"></i>
                                            <div>
                                                <!-- <h5>Dispatch Queued!</h5> -->
                                                <p class="mb-1">Redirecting to dispatch page...</p>
                                                <a href="history.php" class="btn btn-sm btn-outline-primary mt-2">
                                                    <i class="fas fa-list me-1"></i> View Dispatch History
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="get_name_id" id="get_name_id" value="<?= htmlspecialchars($name_id) ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <script>
                        // Character counter
                        document.getElementById('messageBody').addEventListener('input', function() {
                            document.getElementById('charCount').textContent = this.value.length;
                        });

                        // Form validation
                        document.addEventListener('DOMContentLoaded', function() {
                            var forms = document.querySelectorAll('.needs-validation');
                            Array.prototype.slice.call(forms).forEach(function(form) {
                                form.addEventListener('submit', function(event) {
                                    if (!form.checkValidity()) {
                                        event.preventDefault();
                                        event.stopPropagation();
                                    }
                                    form.classList.add('was-validated');
                                }, false);
                            });
                        });

                        // Enhanced dispatchTest function with loading state
                        async function dispatchTest() {
                            const messageBody = document.getElementById("messageBody").value;
                            const testButton = document.getElementById("dispatch_test");

                            if (!messageBody) {
                                alert("Please enter a message first");
                                return;
                            }

                            const originalText = testButton.innerHTML;
                            testButton.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Sending...`;
                            testButton.disabled = true;

                            try {
                                const response = await fetch('https://kingslist.pro/app/default/api/dispTestMsg.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        msg: messageBody
                                    })
                                });

                                const data = await response.json();
                                alert(data.responseData || "Test message sent successfully!");
                            } catch (err) {
                                console.error(err);
                                alert("Error sending test message");
                            } finally {
                                testButton.innerHTML = originalText;
                                testButton.disabled = false;
                            }
                        }

                        // Insert tag at cursor position
                        function insertTag(tag) {
                            const textarea = document.getElementById('messageBody');
                            const startPos = textarea.selectionStart;
                            const endPos = textarea.selectionEnd;
                            const currentValue = textarea.value;

                            textarea.value = currentValue.substring(0, startPos) + tag + currentValue.substring(endPos);
                            textarea.focus();
                            textarea.selectionStart = textarea.selectionEnd = startPos + tag.length;

                            // Trigger character count update
                            const event = new Event('input');
                            textarea.dispatchEvent(event);
                        }
                    </script>



                </div><!--end row-->
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
    <script src="assets/js/pages/form-editor.init.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="/app/default/js/dispatchtest.js"></script>

</body>
<!--end body-->

</html>