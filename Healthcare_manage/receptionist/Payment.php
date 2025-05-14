<?php
session_start();
if (!isset($_SESSION["receptionist"])) {
    header("location:../index.php");
}
include_once('../../config.php');
$conn = connection();

$runame = $_SESSION['receptionist'];

$query2 = mysqli_query($conn, "SELECT * FROM receptionist where username='$runame'");
$receptionist = mysqli_fetch_assoc($query2);
$rid = $receptionist["rid"];

$query1 = mysqli_query($conn, "SELECT * FROM payments");

$appointment_dt = "";
$appointment_id = "";

if (isset($_REQUEST["payid"])) {
    $pay_id = $_REQUEST["payid"];
    $query3 = mysqli_query($conn, "SELECT * FROM payments WHERE payment_id=$pay_id");
    $payment = mysqli_fetch_assoc($query3);

    $payment_id = $payment["payment_id"];
    $patient_id = $payment["patient_id"];

    $Q6 = mysqli_query($conn, "SELECT * FROM appointments WHERE payment_id = $payment_id");
    $DATA = mysqli_fetch_assoc($Q6);
    $appointment_id = $DATA["appoint_id"];
    $appointment_dt = $DATA["appointment_date"];

    $Q7 = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $patient_id");
    $patient = mysqli_fetch_assoc($Q7);
}

$Q = mysqli_query($conn, "SELECT SUM(amount) AS total_amount FROM payments WHERE `status` = 'PAID'");
$amount = mysqli_fetch_assoc($Q);
$totalAmount = $amount['total_amount'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="./style/Payment.css">
    <link rel="website icon" href="./../assets/images/logo.png">
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
    <!-- Correct jsPDF Import -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include_once("./Navbar.php");
    ?>

    <?php include_once("./admin_header.php"); ?>

    <div class="content">

        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold"> Billing & Invoices </h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Billing & Invoices </span></p>
            </div>
            <div class="right">
                <div class="alert text-white text-center fw-bold fs-6 shadow-sm rounded-3" role="alert" style="background-color: #1976D2; letter-spacing: 1px;">
                    <i class="bi bi-cash-stack me-2"></i> TOTAL AMOUNT: <strong>₹ <?php echo number_format($totalAmount, 2); ?></strong>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="table-container">
                <table id="myTable" class="nowrap">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Order_id</th>
                            <th class="text-center">Patient</th>
                            <th class="text-center">Amount (&#8377;)</th>
                            <th class="text-center">Payment Status</th>
                            <th class="text-center">More</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($payments = mysqli_fetch_assoc($query1)) {
                        ?>
                            <tr>
                                <td><?php echo $payments['payment_id'] ?></td>
                                <td><?php echo $payments['order_id'] ?></td>
                                <td>
                                    <?php
                                    $patient_id = $payments['patient_id'];
                                    $query2 = mysqli_query($conn, "SELECT patient_name FROM patients WHERE patient_id=$patient_id");
                                    $patients = mysqli_fetch_assoc($query2);
                                    echo $patients["patient_name"];
                                    ?>
                                </td>
                                <td><?php echo $payments['amount'] ?></td>
                                <td class="text-center">
                                    <?php if ($payments["status"] == "PAID") { ?>
                                        <span class="badge bg-success p-2 rounded-pill d-inline-flex align-items-center" data-bs-toggle="tooltip" title="payment Success">
                                            <i class="bi bi-check-circle-fill me-1"></i> Completed
                                        </span>
                                    <?php } else { ?>
                                        <span class="badge bg-danger text-white p-2 rounded-pill d-inline-flex align-items-center" data-bs-toggle="tooltip" title="Something Wrong">
                                            <i class="bi bi-x-circle me-1"></i> Failed
                                        </span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href="?payid=<?php echo $payments["payment_id"]; ?>" name="edit">
                                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-receipt"></i> <strong>Payment Reference:</strong> <?php echo $payment["payment_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-cart-check-fill"></i> <strong>Order Reference:</strong> <?php echo $payment["order_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-arrow-left-right"></i> <strong>Transaction ID:</strong> <?php echo $payment["transection_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-person-fill"></i> <strong>Patient Name:</strong> <?php echo $patient["patient_name"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-telephone-fill"></i> <strong>Contact Number:</strong> <?php echo $patient["contact"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-envelope-at-fill"></i> <strong>Email Address:</strong> <?php echo $patient["email"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-calendar-check-fill"></i> <strong>Appointment Reference:</strong> <?php echo $appointment_id; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-calendar-date-fill"></i> <strong>Appointment Date:</strong> <?php echo $appointment_dt; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-clock-fill"></i> <strong>Payment Date & Time:</strong> <?php echo $payment["create_at"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-info-circle-fill"></i> <strong>Payment Status:</strong>
                                    <span class="badge bg-<?php echo ($payment["status"] == 'PAID') ? 'success' : 'danger'; ?>">
                                        <?php if ($payment["status"] != "PAID") { ?>
                                            <span> Failed </span>
                                        <?php } else { ?>
                                            <span> Success </span>
                                        <?php } ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let table = new DataTable("#myTable", {
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
                layout: {
                    topStart: {
                        buttons: [{
                                extend: 'csv',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4],
                                    format: {
                                        body: function(data, row, column, node) {
                                            if (column === 4) {
                                                return node.textContent.trim();
                                            }
                                            return data;
                                        }
                                    }
                                }
                            },
                            {
                                extend: 'excel',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4],
                                    format: {
                                        body: function(data, row, column, node) {
                                            if (column === 4) {
                                                return node.textContent.trim();
                                            }
                                            return data;
                                        }
                                    }
                                }
                            },
                            {
                                extend: 'pdf',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4],
                                    format: {
                                        body: function(data, row, column, node) {
                                            if (column === 4) {
                                                return node.textContent.trim();
                                            }
                                            return data;
                                        }
                                    }
                                }
                            },
                            {
                                extend: 'print',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4],
                                    format: {
                                        body: function(data, row, column, node) {
                                            if (column === 4) {
                                                return node.textContent.trim();
                                            }
                                            return data;
                                        }
                                    }
                                }
                            }
                        ]
                    }
                },
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
        });


        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('payid')) {
                let editModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                editModal.show();
            }
        });
    </script>
</body>

</html>