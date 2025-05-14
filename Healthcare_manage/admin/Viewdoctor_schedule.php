<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}

include_once('../../config.php');
$conn = connection();

$query3 = mysqli_query($conn, "SELECT * FROM doctor_schedule");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View - DoctorSchedule</title>
    <link rel="stylesheet" href="./style/Viewdoctor_schedule.css">
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
    include_once("./component/admin_header.php");
    ?>

    <div class="content">
        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Doctor Schedule List</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> View Schedule</span></p>
            </div>
            <div class="right">
                <!-- <div class="text-center mt-3">
                    <form action="./updatedoctor_schedule.php" method="post">
                        <button type="submit" class="btn btn-primary d-flex align-items-center px-4 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-calendar-check me-2"></i> Update Schedule
                        </button>
                    </form>
                </div> -->
            </div>
        </div>

        <div class="container">
            <div class="table-container">
                <table id="myTable" class="nowrap">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Doctor</th>
                            <th class="text-center">Available Days</th>
                            <th class="text-center">Available From</th>
                            <th class="text-center">Available To</th>
                            <th class="text-center">Appointment Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($schedule = mysqli_fetch_assoc($query3)) { ?>
                            <tr>
                                <td class="text-center"><?php echo $schedule["schedule_id"]; ?></td>
                                <td class="text-center"><?php
                                    $data = mysqli_query($conn, "SELECT * FROM doctors WHERE doctor_id = " . $schedule["doctor_id"] . "");
                                    $doctor =  mysqli_fetch_assoc($data);
                                    echo $doctor["doctor_name"]."<br> (";
                                    echo $doctor["specilization"].")";
                                    ?>
                                </td>
                                <td class="text-center"><?php echo implode(", ", json_decode($schedule['available_days'])) ?></td>
                                <td class="text-center"><?php echo $schedule["available_from"]; ?></td>
                                <td class="text-center"><?php echo $schedule["available_to"]; ?></td>
                                <td class="text-center"><?php echo $schedule["appointment_duration"]; ?></td>
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