<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}

include_once('../../config.php');
$conn = connection();
$amt = mysqli_query($conn, "SELECT SUM(amount) AS total_amount FROM payments");
$amount = mysqli_fetch_assoc($amt);
$totalAmount = $amount['total_amount']; // Fetching the sum value

$query1 = mysqli_query($conn, "SELECT * FROM payments");

if (isset($_REQUEST["payid"])) {
    $pay_id = $_REQUEST["payid"];
    $query3 = mysqli_query($conn, "SELECT * FROM payments WHERE payment_id=$pay_id");
    $payment = mysqli_fetch_assoc($query3);

    $payment_id = $payment["payment_id"];
    $patient_id = $payment["patient_id"];

    $Q6 = mysqli_query($conn, "SELECT * FROM appointments WHERE payment_id = $payment_id");
    $DATA2 = mysqli_fetch_assoc($Q6);
    $appointment_id = $DATA2["appoint_id"];
    $appointment_dt = $DATA2["appointment_date"];
    $appointment_time = $DATA2["appointment_time"];

    $Q7 = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $patient_id");
    $patient = mysqli_fetch_assoc($Q7);
}
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
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php
    include_once("./Navbar.php");
    include_once("./component/admin_header.php");
    ?>

    <div class="content">

        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold"> Payments </h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Payment </span></p>
            </div>
            <div class="right">
                <div class="alert alert-primary text-center fw-bold fs-5" role="alert"></div>
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
                            <th class="text-center">Payment Date & Time</th>
                            <th class="text-center">More</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        while ($payments = mysqli_fetch_assoc($query1)) {
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $payments['payment_id'] ?></td>
                                <td class="text-center"><?php echo $payments['order_id'] ?></td>
                                <td class="text-center">
                                    <?php
                                    $patient_id = $payments['patient_id'];
                                    $query2 = mysqli_query($conn, "SELECT patient_name FROM patients WHERE patient_id=$patient_id");
                                    $patients = mysqli_fetch_assoc($query2);
                                    echo $patients["patient_name"];
                                    ?>
                                </td>
                                <td class="text-center"><?php echo $payments['amount'] ?></td>
                                <td class="text-center"><?php if ($payments['status'] == "PAID") { ?>
                                   <div class="badge bg-success">Success</div>
                                <?php } ?></td>
                                <td class="text-center"><?php echo $payments['create_at'] ?></td>
                                <td class="text-center">
                                    <a href="?payid=<?php echo $payments["payment_id"]; ?>" name="edit">
                                        <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                            <i class="bi bi-info-circle-fill text-primary fs-5"></i>
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
                            <!-- Left Column -->
                            <div class="col-md-6 mb-2">
                                <p><i class="fas fa-receipt"></i> <strong>Payment Id:</strong> <?php echo $payment["payment_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="fas fa-box"></i> <strong>Order Id:</strong> <?php echo $payment["order_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="fas fa-exchange-alt"></i> <strong>Transaction Id:</strong> <?php echo $payment["transection_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="fas fa-user"></i> <strong>Patient:</strong> <?php echo $patient["patient_name"]; ?></p>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6 mb-2">
                                <p><i class="fas fa-phone"></i> <strong>Contact:</strong> <?php echo $patient["contact"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo $patient["email"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="fas fa-calendar-check"></i> <strong>Appointment Id:</strong> <?php echo $appointment_id; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="fas fa-calendar-alt"></i> <strong>Appointment Date:</strong> <?php echo $appointment_dt; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="fas fa-calendar-alt"></i> <strong>Appointment Time:</strong> <?php echo $appointment_time; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="fas fa-clock"></i> <strong>Payment Date-Time:</strong> <?php echo $payment["create_at"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="fas fa-info-circle"></i> <strong>Status:</strong>
                                    <span class="badge bg-<?php echo ($payment["status"] == 'PAID') ? 'success' : 'danger'; ?>">
                                        <?php echo $payment["status"]; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Include Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let table = $('#myTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                scrollX: true,
                info: false,
                columnDefs: [{
                    orderable: false,
                    targets: [6]
                }],
                responsive: true,
                autoWidth: true,
                pageLength: 5,
                lengthChange: false,
                layout: {
                    topStart: {
                        buttons: [{
                                extend: 'csv',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5]
                                }
                            },
                            {
                                extend: 'excel',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5]
                                }
                            },
                            {
                                extend: 'pdf',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5]
                                }
                            },
                            {
                                extend: 'print',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5]
                                }
                            }
                        ]
                    }
                }
            });


            // Create Filter Dropdown
            let filterContainer = $('<div class="mb-3 d-flex gap-3"></div>');
            let dateFilter = $(`
        <select class="form-control">
            <option value="today">Today</option>
            <option value="this_week">This Week</option>
            <option value="this_month">This Month</option>
            <option value="this_year">This Year</option>
            <option value="">All Time</option>
        </select>
    `);
            filterContainer.append(dateFilter);
            $('.dt-buttons').prepend(filterContainer);

            // Function to Calculate Total Amount Based on Filtered Data
            function calculateTotalAmount() {
                let total = 0;
                table.rows({
                    search: 'applied'
                }).every(function() {
                    let rowData = this.data();
                    let amount = parseFloat(rowData[3]); // Column Index 3 = 'Amount'
                    if (!isNaN(amount)) {
                        total += amount;
                    }
                });
                $(".alert-primary").html(`<i class="bi bi-currency-rupee"></i> Total: ${total.toFixed(2)}`);
            }

            // Apply Date Filter
            function applyDateFilter(filterValue) {
                let today = new Date();
                let startDate, endDate;

                if (filterValue === "today") {
                    startDate = new Date();
                    startDate.setHours(0, 0, 0, 0);
                    endDate = new Date();
                    endDate.setHours(23, 59, 59, 999);
                } else if (filterValue === "this_week") {
                    startDate = new Date();
                    startDate.setDate(today.getDate() - today.getDay());
                    startDate.setHours(0, 0, 0, 0);
                    endDate = new Date();
                    endDate.setDate(startDate.getDate() + 6);
                    endDate.setHours(23, 59, 59, 999);
                } else if (filterValue === "this_month") {
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    endDate.setHours(23, 59, 59, 999);
                } else if (filterValue === "this_year") {
                    startDate = new Date(today.getFullYear(), 0, 1);
                    endDate = new Date(today.getFullYear(), 11, 31);
                    endDate.setHours(23, 59, 59, 999);
                }

                // Apply Filter
                $.fn.dataTable.ext.search.pop();
                if (filterValue) {
                    $.fn.dataTable.ext.search.push((settings, data) => {
                        let dateStr = data[5]; // Column Index 5 = 'create_at' (Date-Time)
                        let dateObj = new Date(dateStr);
                        return dateObj >= startDate && dateObj <= endDate;
                    });
                }

                table.draw();
                calculateTotalAmount(); // Recalculate Total
            }

            // Apply Default Filter (Today)
            applyDateFilter("today");

            // Change Event for Filter
            dateFilter.on('change', function() {
                applyDateFilter($(this).val());
            });

            // Show Modal if PayID is Present in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('payid')) {
                let editModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                editModal.show();
            }
        });
    </script>
</body>

</html>