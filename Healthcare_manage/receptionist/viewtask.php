<?php
session_start();
if (!isset($_SESSION["receptionist"])) {
    header("location:../index.php");
}

include_once('../../config.php');
$conn = connection();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist - Appointments</title>
    <link rel="stylesheet" href="./style/viewtask.css">
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
                <h4 class="fw-bold">Doctor Appointment Requests</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> ><span> View Tasks
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
                            <th class="text-center">Doctor</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Time</th>
                            <th class="text-center">Reason</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded here by AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function loadAppointments() {
            $.ajax({
                url: "fetch_task.php",
                method: "GET",
                success: function(response) {
                    $("#myTable tbody").html(response);
                    $("#myTable").DataTable().destroy(); // Destroy previous instance
                    initializeDataTable(); // Reinitialize DataTable
                }
            });
        }
        loadAppointments(); // Load initially

        function initializeDataTable() {
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
                                title: 'Doctor Appointment Requests', // Custom print heading
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6]
                                }
                            },
                            {
                                extend: 'excel',
                                title: 'Doctor Appointment Requests', // Custom print heading
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6]
                                }
                            },
                            {
                                extend: 'pdf',
                                title: 'Doctor Appointment Requests', // Custom print heading
                                messageTop: 'List of all doctor appointment requests', // Optional subheading
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6]
                                }
                            },
                            {
                                extend: 'print',
                                title: 'Doctor Appointment Requests', // Custom print heading
                                messageTop: 'List of all doctor appointment requests', // Optional subheading
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6]
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
        }
    </script>
</body>

</html>