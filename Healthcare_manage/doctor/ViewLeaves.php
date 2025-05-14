<?php
session_start();
if (!isset($_SESSION["doctor"])) {
    header("location:../index.php");
}

include_once('../../config.php');
$conn = connection();
$upd = false;

// Fetching doctor ID from session
$duname = $_SESSION['doctor'];
$query2 = mysqli_query($conn, "SELECT * FROM doctors WHERE username='$duname'");
$doctor = mysqli_fetch_assoc($query2);
$did = $doctor["doctor_id"];

// Fetch leave records for the doctor
$query3 = mysqli_query($conn, "SELECT * FROM doctor_leave WHERE doctor_id=$did");

// Handling leave deletion
if (isset($_REQUEST["leaveid"])) {
    $leaveid = $_REQUEST["leaveid"];
    $cu = "UPDATE doctor_leave SET `status` = 'Cancelled' WHERE leave_id = $leaveid";
    mysqli_query($conn, $cu);
    $upd = true;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View - Leaves</title>
    <link rel="stylesheet" href="./style/viewleaves.css">
    <link rel="website icon" href="./../assets/images/logo.png">

    <!-- Datatable JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />

    <!-- Datatable Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.css" />
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.print.min.js"></script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
                text: "Leave Cancel Successfully!",
                icon: "success",
                showConfirmButton: false
            });

            setTimeout(() => {
                window.location.href = "ViewLeaves.php";
            }, 1500);
        </script>
    <?php } ?>

    <div class="content">
        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Leaves List</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> View Leaves </span></p>
            </div>
            <div class="right">
                <div class="text-center mt-3">
                    <form action="./AddLeave.php" method="post">
                        <button type="submit" class="btn btn-primary d-flex align-items-center px-4 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-calendar-check me-2"></i> Apply for Leave
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
                            <th class="text-center">Leave Date</th>
                            <th class="text-center">Start Time</th>
                            <th class="text-center">End Time</th>
                            <th class="text-center">Reason</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($leave = mysqli_fetch_assoc($query3)) { ?>
                            <tr>
                                <td class="text-center"><?php echo $leave["leave_id"]; ?></td>
                                <td class="text-center"><?php echo $leave["leave_date"]; ?></td>
                                <td class="text-center"><?php echo $leave["leave_start"]; ?></td>
                                <td class="text-center"><?php echo $leave["leave_end"]; ?></td>
                                <td class="text-center"><?php echo $leave["reason"]; ?></td>
                                <td class="text-center">
                                    <?php if ($leave["status"] == "Approve") { ?>
                                        <div class="badge bg-success">Approve</div>
                                    <?php } elseif ($leave["status"] == "Cancelled") { ?>
                                        <div class="badge bg-warning text-dark">You Cancelled</div>
                                    <?php } else { ?>
                                        <div class="badge bg-danger">Disapprove</div>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    // Convert dates and times to timestamps for comparison
                                    $LeaveDate = strtotime(date("Y-m-d", strtotime($leave["leave_date"])));
                                    $currentDate = strtotime(date("Y-m-d"));

                                    // Future Date
                                    if ($LeaveDate > $currentDate) {
                                    ?>
                                        <?php if ($leave["status"] == "Cancelled") { ?>
                                            <button type="submit" class="btn btn-danger btn-sm" disabled>
                                                <i class="bi bi-trash"></i> Cancel leave
                                            </button>
                                        <?php } else { ?>
                                            <form method="post">
                                                <input type="hidden" name="leaveid" value="<?= $leave["leave_id"] ?>" />
                                                <button type="submit" class="btn btn-danger btn-sm" title="Cancel this leave request">
                                                    <i class="bi bi-trash"></i> Cancel leave
                                                </button>
                                            </form>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <button type="submit" class="btn btn-danger btn-sm" disabled>
                                            <i class="bi bi-trash"></i> Cancel leave
                                        </button>
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
                "targets": [6]
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
            },
            language: {
                info: "", // Hide default DataTable info text
            },
            drawCallback: function(settings) {
                let api = this.api();
                let pageInfo = api.page.info();

                let customInfo = `
        <span class="text-muted">
            <i class="bi bi-info-circle-fill text-primary"></i> 
            Showing <strong>${pageInfo.start + 1} - ${pageInfo.end}</strong> 
            of <strong>${pageInfo.recordsTotal}</strong> total records.
        </span>`;

                $('#myTable_info').html(customInfo);
            }
        });
    </script>
</body>

</html>