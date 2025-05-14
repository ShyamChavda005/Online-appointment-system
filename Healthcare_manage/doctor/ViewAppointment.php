<?php
session_start();
if (!isset($_SESSION["doctor"])) {
    header("location:../index.php");
}

date_default_timezone_set("Asia/Kolkata");

include_once('../../config.php');
$conn = connection();
$upd = false;

//fetching patient id who logged in using session
$duname = $_SESSION['doctor'];
$query2 = mysqli_query($conn, "SELECT * FROM doctors where username='$duname'");
$doctor = mysqli_fetch_assoc($query2);
$did = $doctor["doctor_id"];

$query3 = mysqli_query($conn, "SELECT * FROM appointments WHERE doctor_id=$did");

if (isset($_REQUEST["appid"])) {
    $appid = $_REQUEST["appid"];
    $status = isset($_REQUEST["status"]);

    if ($status == "on") {
        $status = "Approve";
        $qu = "UPDATE appointments SET `status` = '$status' WHERE appoint_id = $appid ";
        mysqli_query($conn, $qu);
        $upd = true;
    } else {
        $status = "Disapprove";
        $qu = "UPDATE appointments SET `status` = '$status' WHERE appoint_id = $appid ";
        mysqli_query($conn, $qu);
        $upd = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View - Appointements</title>
    <link rel="stylesheet" href="./style/viewappointment.css">
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
    <?php
    include_once("./Navbar.php");
    include_once("./admin_header.php");
    ?>
    <?php if ($upd) { ?>
        <script>
            Swal.fire({
                title: "Success!",
                text: "Update Successfully!",
                icon: "success",
                showConfirmButton: false
            });

            setTimeout(() => {
                window.location.href = "Viewappointment.php";
            }, 1500);
        </script>
    <?php } ?>

    <div class="content">
        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Appointments List</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> View Appointments
                    </span></p>
            </div>
        </div>

        <div class="container">
            <div class="table-container">
                <table id="myTable" class="nowrap">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Patient</th>
                            <th class="text-center">Doctor</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Time</th>
                            <th class="text-center">Reason</th>
                            <th class="text-center">Payment</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appointment = mysqli_fetch_assoc($query3)) { ?>
                            <tr>
                                <td><?php echo $appointment["appoint_id"]; ?></td>
                                <td><?php
                                    $p_id = $appointment["patient_id"];
                                    $pt = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $p_id");
                                    $pdata = mysqli_fetch_assoc($pt);
                                    echo $pdata["patient_name"];
                                    ?></td>
                                <td><?php
                                    echo $doctor["doctor_name"];
                                    ?></td>
                                </td>
                                <td><?php echo $appointment["appointment_date"]; ?></td>
                                <td><?php echo $appointment["appointment_time"]; ?></td>
                                <td><?php echo $appointment["reason"]; ?></td>
                                <td>
                                    <?php
                                    $payment_id = $appointment["payment_id"];
                                    $query7 = mysqli_query($conn, "SELECT * FROM payments WHERE payment_id=$payment_id");
                                    $payments = mysqli_fetch_assoc($query7);
                                    if ($payments["status"] == "PAID") {
                                        echo ' <span class="badge bg-success">Success</span>';
                                    }
                                    else {
                                        echo ' <span class="badge bg-danger">Failed</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                     $appointmentDateTime = strtotime($appointment["appointment_date"] . ' ' . $appointment["appointment_time"]); // Combine date and time
                                     $currentDateTime = time(); // Get current timestamp
 
                                     $isDisabled = ($appointmentDateTime < $currentDateTime) ? "disabled" : ""; // Disable switch if time has passed
                                     ?>
                                    <form method="post" class="d-flex justify-content-center">
                                        <input type="hidden" name="appid" value="<?= $appointment["appoint_id"] ?>" />
                                        <div class="form-check form-switch">
                                            <input class="form-check-input fs-4 p-1 " type="checkbox" id="switch<?= $appointment["appoint_id"] ?>" onchange="this.form.submit()" name="status"
                                                <?php if ($appointment["status"] == "Approve") { ?> checked <?php } ?><?= $isDisabled ?>>
                                            <label class="form-check-label ms-1 fs-6" for="switch<?= $receptionist["status"] ?>">
                                                <?php echo ($appointment["status"] == "Approve") ? '<span class="text-success">Approve</span>' : '<span class="text-danger">Disapprove</span>'; ?>
                                            </label>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
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
            layout: {
                topStart: {
                    buttons: [{
                            extend: 'csv',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 7) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Approve" : "Disapprove";
                                        }
                                        if (column === 6) { // Payment Column
                                            return node.textContent.trim(); // Extracts text (Success/Failed)
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 7) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Approve" : "Disapprove";
                                        }
                                        if (column === 6) { // Payment Column
                                            return node.textContent.trim(); // Extracts text (Success/Failed)
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'pdf',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 7) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Approve" : "Disapprove";
                                        }
                                        if (column === 6) { // Payment Column
                                            return node.textContent.trim(); // Extracts text (Success/Failed)
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 7) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Approve" : "Disapprove";
                                        }
                                        if (column === 6) { // Payment Column
                                            return node.textContent.trim(); // Extracts text (Success/Failed)
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
    </script>
</body>

</html>