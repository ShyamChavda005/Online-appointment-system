<?php
session_start();
if (!isset($_SESSION["receptionist"])) {
    header("location:../index.php");
    exit();
}

include_once('../../config.php');
$conn = connection();

$runame = $_SESSION['receptionist'];
$query2 = mysqli_query($conn, "SELECT * FROM receptionist where username='$runame'");
$receptionist = mysqli_fetch_assoc($query2);
$rid = $receptionist["rid"];

// Fetch unique patients and their appointment details
$query3 = mysqli_query($conn, "SELECT p.patient_id, p.patient_name, p.dob, p.contact, p.email, COUNT(a.appoint_id) AS total_appointments 
FROM patients p 
JOIN appointments a ON p.patient_id = a.patient_id 
GROUP BY p.patient_id");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patients</title>
    <link rel="stylesheet" href="./style/viewpatient.css">
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

    <div class="content">
        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Appointments List</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> View Patients
                    </span></p>
            </div>
        </div>

        <div class="container">
            <div class="table-container">
                <table id="myTable" class="nowrap">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Patient Name</th>
                            <th class="text-center">Age</th>
                            <th class="text-center">Contact</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Total Appointments</th>
                            <th class="text-center">More</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($patient = mysqli_fetch_assoc($query3)) {
                            $dob = $patient["dob"];
                            $age = date_diff(date_create($dob), date_create('today'))->y; ?>
                            <tr>
                                <td class="text-center"><?= $patient["patient_id"] ?></td>
                                <td class="text-center"><?= $patient["patient_name"] ?></td>
                                <td class="text-center"><?= $age  ?></td>
                                <td class="text-center"><?= $patient["contact"] ?></td>
                                <td class="text-center"><?= $patient["email"] ?></td>
                                <td class="text-center"><?= $patient["total_appointments"] ?></td>
                                <td class="text-center">
                                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#detailsModal<?= $patient["patient_id"] ?>">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Bootstrap Modal for Receptionist -->
                            <div class="modal fade" id="detailsModal<?= $patient["patient_id"] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content shadow-lg rounded-4">

                                        <!-- Modal Header -->
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-calendar-check"></i> Receptionist - Appointment Details: <?= $patient["patient_name"] ?>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <!-- Modal Body -->
                                        <div class="modal-body p-4">
                                            <div class="container-fluid">

                                                <!-- Patient Information -->
                                                <div class="row mb-3">
                                                    <div class="col-md-4 text-center">
                                                        <i class="bi bi-person-circle text-secondary" style="font-size: 60px;"></i>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="p-3 border rounded-3 bg-light shadow-sm">
                                                            <h6 class="mb-2"><i class="bi bi-person-circle text-primary"></i> <strong> Name:</strong> <?= $patient["patient_name"] ?></h6>
                                                            <h6 class="mb-2"><i class="bi bi-calendar text-primary"></i> <strong> Age:</strong> <?= $age ?> years</h6>
                                                            <h6 class="mb-0"><i class="bi bi-telephone text-primary"></i> <strong> Contact:</strong> <?= $patient["contact"] ?></h6>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Appointment History Section -->
                                                <div class="row">
                                                    <div class="col-12">
                                                        <h5 class="fw-bold text-primary mb-3">
                                                            <i class="bi bi-clock-history"></i> Appointment History (Receptionist)
                                                        </h5>
                                                    </div>
                                                </div>

                                                <!-- Appointment List -->
                                                <div class="row">
                                                    <?php
                                                    $p_id = $patient["patient_id"];
                                                    $appointments = mysqli_query($conn, "SELECT a.*, d.doctor_name FROM appointments a JOIN doctors d ON a.doctor_id = d.doctor_id WHERE a.patient_id = $p_id");

                                                    while ($appt = mysqli_fetch_assoc($appointments)) { ?>
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card border-0 shadow-sm p-3">
                                                                <h6 class="mb-2"><i class="bi bi-person text-primary"></i> <strong> Doctor:</strong> <?= $appt["doctor_name"] ?></h6>
                                                                <h6 class="mb-2"><i class="bi bi-calendar-event text-primary"></i> <strong> Date:</strong> <?= $appt["appointment_date"] ?></h6>
                                                                <h6 class="mb-2"><i class="bi bi-clock text-primary"></i> <strong> Time:</strong> <?= $appt["appointment_time"] ?></h6>
                                                                <h6 class="mb-0"><i class="bi bi-chat-left-dots text-primary"></i> <strong> Reason:</strong> <?= $appt["reason"] ?></h6>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>

                                            </div>
                                        </div>

                                        <!-- Modal Footer -->
                                        <div class="modal-footer bg-primary text-white">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                                <i class="bi bi-x-circle"></i> Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

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