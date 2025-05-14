<?php
session_start();
if (!isset($_SESSION['doctor'])) {
    header("location:../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/dashboard.css">
    <title>Home - Doctor </title>
    <link rel="website icon" href="./../assets/images/logo.png">
</head>

<body>
    <?php
    include_once("Navbar.php");
    include_once("admin_header.php");
    include_once('../../config.php');
    $conn = connection();
    $duname = $_SESSION['doctor'];

    // getting particular docotor record that in session
    $query2 = mysqli_query($conn, "SELECT * FROM doctors where username='$duname'");
    $doctor = mysqli_fetch_assoc($query2);
    $did = $doctor["doctor_id"];

    // getting the number of appointments
    $Q1 = mysqli_query($conn, "SELECT * FROM appointments where doctor_id=$did");
    $total_appointments = mysqli_num_rows($Q1);

    //upcoming appointments number
    $Q3 = mysqli_query($conn, "
    SELECT * FROM appointments 
    WHERE (appointment_date > CURDATE() 
        OR (appointment_date = CURDATE() AND appointment_time > CURTIME())) 
    AND doctor_id = $did 
    ORDER BY appointment_date ASC, appointment_time ASC
");
    $number_of_upcoming = mysqli_num_rows($Q3);

    //calculating current age
    $dob = $doctor["dob"];
    $dobObject = new DateTime($dob);
    $currentDate = new DateTime();
    $age = $dobObject->diff($currentDate)->y;

    //total patient treated number
    $Q4 = mysqli_query($conn, "SELECT COUNT(DISTINCT patient_id) AS unique_patient FROM appointments WHERE  doctor_id=$did ");
    $patient_treats = mysqli_fetch_assoc($Q4);
    $number_of_treated = $patient_treats["unique_patient"];

    $fetch_total = mysqli_query($conn, "
    SELECT SUM(p.amount) AS total_amount 
    FROM payments p
    JOIN appointments a ON p.payment_id = a.payment_id
    WHERE a.doctor_id = $did");
    $amount = mysqli_fetch_assoc($fetch_total);
    $totalAmount = $amount['total_amount'];

    ?>


    <div class="content">
        <div class="header-list">
            <div class="left">
                <h4 class="fw-bold">Dashboard</h4>
            </div>
        </div>

        <div class="info-container">
            <div class="info-box">
                <div class="info-icon">
                    <i class="bi bi-calendar-check-fill fs-3"></i>
                </div>
                <div class="info-details">
                    <h3><?= $total_appointments ?></h3>
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
                    <h3><?php echo $totalAmount; ?></h3>
                    <p>Billing & Payments</p>
                </div>
                <a href="./Payment.php" class="view-details">Payment History</a>
            </div>

            <div class="info-box">
                <div class="info-icon">
                    <i class="bi bi-clipboard-check-fill fs-3"></i>
                    <!-- <i class="bi bi-heart-pulse-fill fs-3"></i> -->
                </div>
                <div class="info-details">
                    <h3><?php echo $number_of_treated; ?></h3>
                    <p>Total Patients Treated</p>
                </div>
                <a href="./viewpatient.php" class="view-details">View List</a>
            </div>
        </div>

        <div class="container mt-4">
            <div class="row">
                <!-- Left Side - Chart Section -->
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0 rounded p-3">
                        <h5 class="fw-bold text-center">Appointment Statistics</h5>
                        <div class="chart-container" id="chart">
                            <?php include_once("./chart.php") ?>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Upcoming Appointments -->
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 rounded">
                        <div class="card-header bg-primary text-white text-center">
                            <h5 class="mb-0">Upcoming Appointments</h5>
                        </div>
                        <div class="card-body p-0 overflow-auto" style="max-height: 300px;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Patient</th>
                                        <th>Patient Name</th>
                                        <th>Appointment Date</th>
                                        <th>Appointment Time</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php
                                    $Q2 = mysqli_query($conn, "SELECT * FROM appointments 
                            WHERE (appointment_date > CURDATE() 
                                OR (appointment_date = CURDATE() AND appointment_time > CURTIME())) 
                            AND doctor_id = $did 
                            ORDER BY appointment_date ASC, appointment_time ASC");

                                    while ($upcoming = mysqli_fetch_assoc($Q2)) {
                                        $pid = $upcoming["patient_id"];
                                        $Q3 = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id=$pid");
                                        $patient = mysqli_fetch_assoc($Q3);
                                    ?>
                                        <tr>
                                            <td><i class="bi bi-person-circle"></i></td>
                                            <td><?php echo $patient["patient_name"] ?></td>
                                            <td><?php echo date("d F Y", strtotime($upcoming["appointment_date"])) ; ?></td>
                                            <td><?php echo $upcoming["appointment_time"]; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center bg-light">
                            <a href="./ViewAppointment.php" class="btn btn-sm btn-primary">View All Appointments</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

</body>

</html>