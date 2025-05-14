<?php
session_start();
if (!isset($_SESSION['receptionist'])) {
    header("location:../index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/dashboard.css">
    <title>Home - Receptionist</title>
    <link rel="website icon" href="./../assets/images/logo.png">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <?php
    include_once("Navbar.php");
    include_once("admin_header.php");
    include_once('../../config.php');
    $conn = connection();
    $runame = $_SESSION['receptionist'];

    // getting particular receptionist record that in session
    $query2 = mysqli_query($conn, "SELECT * FROM receptionist WHERE username='$runame'");
    $receptionist = mysqli_fetch_assoc($query2);
    $rid = $receptionist["rid"];

    // getting the number of appointments
    $Q1 = mysqli_query($conn, "SELECT * FROM appointments");
    $total_appointments = mysqli_num_rows($Q1);

    //upcoming appointments number
    $Q3 = mysqli_query($conn, "SELECT * FROM appointments WHERE appointment_date >= CURDATE() ORDER BY appointment_date ASC");
    $number_of_upcoming = mysqli_num_rows($Q3);

    // Total amount of payment of receptionist
    $Q5 = mysqli_query($conn, "SELECT SUM(amount) AS total_amount FROM payments WHERE `status` = 'PAID'");
    $payment = mysqli_fetch_assoc($Q5);
    $number_of_amount = $payment["total_amount"];

    ?>

    <div class="content">
        <div class="header-list">
            <div class="left">
                <h4 class="fw-bold">Dashboard</h4>
            </div>
            <div class="right">
                <div class="text-center mt-3">
                    <form action="./ViewAppointment.php" method="post">
                        <button type="submit" class="btn btn-primary d-flex align-items-center px-4 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-calendar-check me-2"></i> View Appointment
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="info-container">
            <div class="info-box">
                <div class="info-icon">
                    <i class="bi bi-calendar-check-fill fs-3"></i>
                </div>
                <div class="info-details">
                    <h3><?= $total_appointments ?> </h3>
                    <p>Appointments Summary</p>
                </div>
                <a href="./ViewAppointment.php" class="view-details">View Appointments</a>
            </div>

            <div class="info-box">
                <div class="info-icon">
                    <i class="bi bi-calendar-event-fill fs-3"></i>
                </div>
                <div class="info-details">
                    <h3><?= $number_of_upcoming ?></h3>
                    <p>Upcoming Consultations</p>
                </div>
                <a href="./UpcomingAppointment.php" class="view-details">View Schedule</a>
            </div>

            <div class="info-box">
                <div class="info-icon">
                    <i class="bi bi-cash-coin fs-3"></i>
                </div>
                <div class="info-details">
                    <h3><?= $number_of_amount ?></h3>
                    <p>Billing & Payments</p>
                </div>
                <a href="./Payment.php" class="view-details">Payment History</a>
            </div>
        </div>

        <div id="tables" class="container mt-5">

            <!-- Active Patients -->

            <div class="card shadow-sm border-0 rounded">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-center">Doctor Requests</h5>
                </div>
                <div class="card-body p-0 overflow-auto">
                    <table class="table table-hover mb-0" id="myTable">
                        <thead class="table-light text-center">
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
                        <tbody class="text-center">
                            <?php
                            $query = mysqli_query($conn, "SELECT dr.*, p.patient_name, d.doctor_name 
                           FROM doctor_appointment_requests dr
                           JOIN patients p ON dr.patient_id = p.patient_id
                           JOIN doctors d ON dr.doctor_id = d.doctor_id
                            WHERE dr.status = 'Pending'
                           ORDER BY dr.id DESC");

                            while ($row = mysqli_fetch_assoc($query)) {
                                $appointment_id = $row["id"]; // Only pass appointment ID
                                $isCompleted = ($row["status"] == "Completed"); // Check if status is Completed

                                $statusBadge = ($row["status"] == "Completed") ?
                                    '<span class="badge bg-success">Completed</span>' :
                                    '<span class="badge bg-warning">Pending</span>';

                                $actionButton = $isCompleted
                                    ? '<button class="btn btn-sm btn-secondary" disabled>Completed</button>'
                                    : "<a href='AddAppointment.php?task_id={$appointment_id}' class='btn btn-sm btn-success'>Complete</a>";
                            ?>
                                <tr>
                                    <td><?= $row["id"] ?></td>
                                    <td><?= $row["patient_name"] ?></td>
                                    <td><?= $row["doctor_name"] ?></td>
                                    <td><?= $row["suggested_date"] ?></td>
                                    <td><?= $row["suggested_time"] ?></td>
                                    <td><?= $row["reason"] ?></td>
                                    <td class='text-center'><?= $statusBadge ?></td>
                                    <td class='text-center'><?= $actionButton ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Active Doctors -->

            <div class="card shadow-sm border-0 rounded">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-center">Active Doctor List</h5>
                </div>
                <div class="card-body p-0 overflow-auto">
                    <table class="table table-hover mb-0" id="myTable1">
                        <thead class="table-light text-center">
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Doctor</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Specialization</th>
                                <th class="text-center">Fee</th>
                                <th class="text-center">Phone</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php
                            $Q2 = mysqli_query($conn, "SELECT * FROM doctors WHERE `status` = 'Active'");
                            while ($doctor = mysqli_fetch_assoc($Q2)) {
                            ?>
                                <tr>
                                    <td class="text-center"><?= $doctor["doctor_id"] ?></td>
                                    <td class="text-center"><img src="./../assets/doctorphotos/<?php echo $doctor["photo"] ?>" class="rounded-circle" height="40" /></td>
                                    <td class="text-center"><?php echo $doctor["doctor_name"] ?></td>
                                    <td class="text-center"><?php echo $doctor["specilization"] ?></td>
                                    <td class="text-center"><?php echo $doctor["consultancy_fee"] . ".Rs" ?></td>
                                    <td class="text-center"><?php echo $doctor["contact"] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>


    </div>

    <script>
        function loadAppointments() {
            $.ajax({
                url: "Dashboard.php",
                method: "GET",
                success: function(response) {
                let content = $(response).find('.content').html(); // Extract updated content
                $('.content').html(content);
            }
            });
        }
        // Auto-refresh every 5 seconds
        setInterval(loadAppointments, 5000);
        loadAppointments(); // Load initially

        let table = new DataTable('#myTable', {
            paging: true,
            searching: true,
            ordering: true,
            scrollX: true,
            info: true,
            responsive: true,
            autoWidth: true,
            pageLength: 5,
            lengthChange: false
        });

        let table1 = new DataTable('#myTable1', {
            paging: true,
            searching: true,
            ordering: true,
            scrollX: true,
            info: true,
            responsive: true,
            autoWidth: true,
            pageLength: 5,
            lengthChange: false
        });
    </script>
</body>

</html>