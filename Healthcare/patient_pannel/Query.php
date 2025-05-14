<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("location:../login.php");
}
include_once("../../config.php");
$conn = connection();
$upd = false;
$noChange = false;

//fetching patient email who logged in using session
$puname = $_SESSION['user'];
$Q1 = mysqli_query($conn, "SELECT * FROM patients where username='$puname'");
$patient = mysqli_fetch_assoc($Q1);
$patient_email_id = $patient["email"];

$clientq_id = "";
$clientname = "";
$clientemail = "";
$clientquery = "";

if (isset($_REQUEST["qid"])) {
    $clientq_id = $_REQUEST["qid"];
    $str = mysqli_query($conn, "SELECT * FROM query WHERE qid = $clientq_id");
    $query_data = mysqli_fetch_assoc($str);

    $clientname = $query_data["name"];
    $clientemail = $query_data["email"];
    $clientquery = $query_data["query"];
}

if (isset($_REQUEST["update_query"])) {
    $qid = $_REQUEST["query_id"];
    $qname = $_REQUEST["client_name"];
    $qemail = $_REQUEST["email_address"];
    $qquery = $_REQUEST["query"];

    $C = mysqli_query($conn, "SELECT * FROM query WHERE qid = $qid");
    $O = mysqli_fetch_assoc($C);

    if ($qid == $O["qid"] && $qname == $O["name"] && $qemail == $O["email"] && $qquery == $O["query"]) {
        $noChange = true;
    } else {
        $q = "UPDATE query SET `name` = '$qname',email = '$qemail',query = '$qquery' WHERE qid = $qid";
        mysqli_query($conn, $q);
        $upd = true;
    }
}

if (isset($_REQUEST["responseId"])) {
    $quid1 = $_REQUEST["responseId"];
    $str1 = mysqli_query($conn, "SELECT * FROM response WHERE qid = $quid1");
    $response = mysqli_fetch_assoc($str1);

    $res_email = $response["email"];
    $str2 = mysqli_query($conn, "SELECT * FROM query WHERE qid = $quid1");
    $DATA = mysqli_fetch_assoc($str2);
    $res_query = $DATA["query"];
    $res_response = $response["res_name"];
    $res_sent = $response["sent_at"];
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queries</title>

    <link rel="stylesheet" href="./style/query.css">
    <link rel="website icon" href="./image/logo.png">
    <!-- Datable JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <!-- Datable Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.css" />
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.print.min.js"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- this validation code is for edit form -->
    <script>
        function validationForm() {
            const clientname = document.getElementById('client_name').value.trim();
            const email = document.getElementById('email_address').value;
            const query = document.getElementById('query').value.trim();

            if (!clientname) {
                document.getElementById("nameerror").style.display = "block";

                setTimeout(() => {
                    document.getElementById("nameerror").style.display = "none";
                }, 1200);

                return false;
            } else if (!email) {
                document.getElementById("emailerror").style.display = "block";

                setTimeout(() => {
                    document.getElementById("emailerror").style.display = "none";
                }, 1200);

                return false;
            } else if (!query) {
                document.getElementById("queryerror").style.display = "block";

                setTimeout(() => {
                    document.getElementById("queryerror").style.display = "none";
                }, 1200);

                return false;
            } else {
                return true;
            }
        }
    </script>


</head>

<body>
    <?php include_once("./Navbar.php"); ?>
    <?php include_once("./admin_header.php"); ?>

    <?php if ($noChange) { ?>
        <script>
            Swal.fire({
                text: "No Changes!",
                icon: "warning"
            });
        </script>
    <?php } ?>

    <?php if ($upd) { ?>
        <script>
            Swal.fire({
                title: "Success!",
                text: "Update Successfully!",
                icon: "success",
                showConfirmButton: false
            });

            setTimeout(() => {
                window.location.href = "Query.php";
            }, 1500);
        </script>
    <?php } ?>

    <div class="content">

        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Queries List</h4>
                <p style="margin-top:10px;"><a href="./dashboard.php"> HealthCare </a> > <span>Your Queries </span></p>
            </div>
        </div>

        <div class="container">
            <div class="table-container">
                <table id="myTable" class="nowrap">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Email ID</th>
                            <th class="text-center">Query</th>
                            <th class="text-center">Submitted On</th>
                            <th class="text-center">Actions</th>
                            <th class="text-center">HealthCare Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $Q2 = mysqli_query($conn, "SELECT * FROM query where email='$patient_email_id' ");
                        while ($Query = mysqli_fetch_assoc($Q2)) { ?>
                            <tr>
                                <td class="text-center"><?php echo $Query["qid"]; ?></td>
                                <td class="text-center"><?php echo $Query["name"]; ?></td>
                                <td class="text-center"><?php echo $Query["email"]; ?></td>
                                <td class="text-center"><?php echo $Query["query"]; ?></td>
                                <td class="text-center"><?php echo $Query["date&time"]; ?></td>
                                <td class="d-flex justify-content-center">
                                    <?php if ($Query["response"] == "Unsent") { ?>
                                        <a href="?qid=<?php echo $Query["qid"]; ?>" style="text-decoration: none;">
                                            <button type="button" class="btn btn-dark  btn-sm fw-semibold shadow-sm d-flex align-items-center"
                                                data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?responseId=<?php echo $Query['qid']; ?>" style="text-decoration: none;">
                                            <button type="button" class="btn btn-outline-primary btn-sm fw-semibold shadow-sm d-flex align-items-center"
                                                data-bs-toggle="modal" data-bs-target="#exampleModal1">
                                                <i class="bi bi-eye me-1"></i> View
                                            </button>
                                        </a>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($Query["response"] == "Sent") { ?>
                                        <span class="badge bg-success p-2 rounded-pill d-inline-flex align-items-center" data-bs-toggle="tooltip" title="Your query has been reviewed and addressed by the healthcare team">
                                            <i class="bi bi-check-circle-fill me-1"></i> Query Addressed
                                        </span>
                                    <?php } else { ?>
                                        <span class="badge bg-warning text-dark p-2 rounded-pill d-inline-flex align-items-center" data-bs-toggle="tooltip" title="Your query is awaiting a response from the healthcare team">
                                            <i class="bi bi-clock-fill me-1"></i> Awaiting Response
                                        </span>
                                    <?php } ?>
                                </td>

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- this the modal form for edit -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModal" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <!-- Enlarged for better visibility -->
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="editQueryLabel">
                        <i class="bi bi-file-earmark-text"></i> Edit Patient Inquiry
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Form Start -->
                <form method="post" onsubmit="return validationForm()">
                    <div class="modal-body">
                        <div class="row g-3"> <!-- Bootstrap Grid for better spacing -->

                            <!-- Inquiry ID (Read-Only) -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="query_id" id="query_id"
                                        value="<?= $clientq_id ?>" readonly />
                                    <label for="query_id"><i class="bi bi-tag"></i> Inquiry ID</label>
                                </div>
                            </div>

                            <!-- Patient Name -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="client_name" id="client_name"
                                        value="<?= $clientname ?>" />
                                    <label for="client_name"><i class="bi bi-person-circle"></i> Patient Name</label>
                                    <small id="nameerror" class="text-danger" style="display:none;">* Name is required</small>
                                </div>
                            </div>

                            <!-- Patient Email Address -->
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="email" class="form-control" name="email_address" id="email_address"
                                        value="<?= $clientemail ?>" />
                                    <label for="email_address"><i class="bi bi-envelope-at"></i> Patient Email Address</label>
                                    <small id="emailerror" class="text-danger" style="display:none;">* Email is required</small>
                                </div>
                            </div>

                            <!-- Patient Inquiry / Concern -->
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="query" id="query" style="height: 120px"><?= $clientquery ?></textarea>
                                    <label for="query"><i class="bi bi-chat-left-text"></i> Patient Inquiry / Concern</label>
                                    <small id="queryerror" class="text-danger" style="display:none;">* Inquiry is required</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i> Close
                        </button>
                        <button type="submit" class="btn btn-success" name="update_query">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Sent query Modal -->
    <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0 rounded-3">

                <div class="modal-header bg-light border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-question-circle-fill fs-4 text-primary"></i>
                        <h5 class="px-2 my-0 fw-semibold">Support & Queries</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="post">
                    <input type="hidden" name="quid1" value="<?= $quid1 ?>">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="fw-semibold text-secondary d-flex align-items-center">
                                <i class="bi bi-person-fill text-primary me-2"></i> Your Submitted Query
                            </label>
                            <textarea class="form-control border-0 bg-light p-3" id="query1" name="query1" rows="5" readonly></textarea>
                            <script>
                                document.getElementById("query1").value = <?php echo json_encode($res_query); ?>;
                            </script>
                        </div>

                        <div class="mb-3">
                            <label class="fw-semibold text-secondary d-flex align-items-center">
                                <i class="bi bi-hospital-fill text-success me-2"></i> Official Response from Healthcare Team
                            </label>
                            <textarea class="form-control border-0 bg-light p-3" id="reply" name="reply" rows="8" readonly></textarea>
                            <script>
                                document.getElementById("reply").value = <?php echo json_encode($res_response); ?>;
                            </script>
                        </div>

                        <div class="mb-3">
                            <label class="fw-semibold text-secondary d-flex align-items-center">
                                <i class="bi bi-clock-history text-warning me-2"></i> Response Date & Time
                            </label>
                            <input type="text" class="form-control border-0 bg-light p-2 fw-semibold"
                                value="<?= date('F j, Y, g:i A', strtotime($res_sent)) ?>" readonly>
                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-end bg-light border-top">
                        <button type="button" class="btn btn-dark justify-content-center w-100 px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let table = new DataTable('#myTable', {
            paging: true,
            searching: true,
            ordering: true,
            scrollX: true,
            info: true,
            "columnDefs": [{
                "orderable": false,
                "targets": [5]
            }],
            responsive: true,
            // "fixedHeader": false,
            autoWidth: true,
            pageLength: 5,
            lengthChange: false,
            language: {
                info: "", // Hide default DataTable info text
            },
            drawCallback: function(settings) {
                let api = this.api();
                let pageInfo = api.page.info();

                // Custom info message with professional formatting
                let customInfo = `
        <span class="text-muted">
            <i class="bi bi-info-circle-fill text-primary"></i> 
            Showing <strong>${pageInfo.start + 1} - ${pageInfo.end}</strong> 
            of <strong>${pageInfo.recordsTotal}</strong> total records.
        </span>`;

                // Update DataTable info display
                $('#myTable_info').html(customInfo);
            }

        });

        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('qid')) {
                let editModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                editModal.show();
            }

            if (urlParams.has('responseId')) {
                let historyModal = new bootstrap.Modal(document.getElementById('exampleModal1'));
                historyModal.show();
            }
        });
    </script>

</body>

</html>