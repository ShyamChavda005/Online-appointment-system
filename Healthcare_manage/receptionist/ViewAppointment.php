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

$query3 = mysqli_query($conn, "SELECT * FROM appointments");

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
    <script>
        function validationForm() {
            const doctorName = document.getElementById('doctor_id').value.trim();
            const dt = document.getElementById('dt').value;
            const reason = document.getElementById('reason').value.trim();

            if (doctorName == "Select Doctor") {
                document.getElementById("dval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("dval").style.display = "none";
                }, 1200);

                return false;
            } else if (!dt) {
                document.getElementById("dtval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("dtval").style.display = "none";
                }, 1200);

                return false;
            } else if (!reason) {
                document.getElementById("rval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("rval").style.display = "none";
                }, 1200);

                return false;
            } else {
                return true;
            }
        }
    </script>

</head>

<body>
    <?php
    include_once("./Navbar.php");
    include_once("./admin_header.php");
    ?>

    <div class="content">

        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Appointments List</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > Appointments > <span> View Appointments
                    </span></p>
            </div>
            <div class="right">
                <div class="text-center mt-3">
                    <form action="./AddAppointment.php" method="post">
                        <button type="submit" class="btn btn-primary d-flex align-items-center px-4 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-calendar-check me-2"></i> Add Appointment
                        </button>
                    </form>
                </div>
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
                            <th class="text-center">Appointment Date</th>
                            <th class="text-center">Appointment Time</th>
                            <th class="text-center">Reason</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appointment = mysqli_fetch_assoc($query3)) { ?>
                            <tr>
                                <td class="text-center"><?php echo $appointment["appoint_id"]; ?></td>
                                <td class="text-center"><?php
                                                        $p_id = $appointment["patient_id"];
                                                        $pt = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $p_id");
                                                        $pdata = mysqli_fetch_assoc($pt);
                                                        echo $pdata["patient_name"];
                                                        ?></td>
                                <td class="text-center"><?php
                                                        $d_id = $appointment["doctor_id"];
                                                        $doct = mysqli_query($conn, "SELECT * FROM doctors WHERE doctor_id = $d_id");
                                                        $ddata = mysqli_fetch_assoc($doct);
                                                        echo $ddata["doctor_name"];
                                                        ?></td>
                                </td>
                                <td class="text-center"><?php echo date("d F Y", strtotime($appointment["appointment_date"])); ?></td>
                                <td class="text-center"><?php echo $appointment["appointment_time"]; ?></td>
                                <td class="text-center"><?php echo $appointment["reason"]; ?></td>
                                <td class="text-center">
                                    <?php if ($appointment["status"] == "Approve") { ?>
                                        <span class="badge bg-success p-2 rounded-pill d-inline-flex align-items-center" data-bs-toggle="tooltip" title="Your query has been reviewed and addressed by the healthcare team">
                                            <i class="bi bi-check-circle-fill me-1"></i> Approved
                                        </span>
                                    <?php } else { ?>
                                        <span class="badge bg-danger text-light p-2 rounded-pill d-inline-flex align-items-center" data-bs-toggle="tooltip" title="Your query is awaiting a response from the healthcare team">
                                            <i class="bi bi-clock-fill me-1"></i> Disapproved
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
                                columns: [0, 1, 2, 3, 4, 5, 6],
                                format: {
                                    body: function(data, row, column, node) {
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
                                columns: [0, 1, 2, 3, 4, 5, 6],
                                format: {
                                    body: function(data, row, column, node) {
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
                                columns: [0, 1, 2, 3, 4, 5, 6],
                                format: {
                                    body: function(data, row, column, node) {
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
                                columns: [0, 1, 2, 3, 4, 5, 6],
                                format: {
                                    body: function(data, row, column, node) {
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