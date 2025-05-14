<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}

include_once('../../config.php');
$conn = connection();
$Q1 = mysqli_query($conn, "SELECT * FROM doctors");
$total_doctors = mysqli_num_rows($Q1);

$Q2 = mysqli_query($conn, "SELECT * FROM patients");
$total_patients = mysqli_num_rows($Q2);

$Q3 = mysqli_query($conn, "SELECT * FROM appointments");
$total_appointments = mysqli_num_rows($Q3);

$Q4 = mysqli_query($conn, "SELECT * FROM appointments WHERE DATE(appointment_date) = CURDATE()");
$today_appointments = mysqli_num_rows($Q4);

$Q5 = mysqli_query($conn, "SELECT * FROM receptionist");
$total_receptionist = mysqli_num_rows($Q5);

$Q6 = mysqli_query($conn, "SELECT SUM(amount) AS total_amount FROM payments WHERE `status`= 'PAID'");
$amount = mysqli_fetch_assoc($Q6);
$totalAmount = $amount['total_amount']; // Fetching the sum value
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <link rel="stylesheet" href="./style/Dashboard.css">
    <link rel="website icon" href="./../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</head>

<body>
    <?php 
    include_once("./Navbar.php"); 
    include_once("./component/admin_header.php"); 
    ?>

    <div class="content" id="content">

        <div class="header-list">
            <div class="left">
                <h4 class="fw-bold">Dashboard</h4>
            </div>
        </div>

        <div class="info-container">
            <div class="info-box">
                <div class="info-icon">
                    <i class="fa-solid fa-user-injured fa-2x"></i> <!-- Patients Icon -->
                </div>
                <div class="info-details">
                    <h3><?= $total_patients ?></h3>
                    <p>Patients</p>
                </div>
                <a href="./ViewPatient.php" class="view-details">View Details</a>
            </div>

            <div class="info-box">
                <div class="info-icon">
                    <i class="fa-solid fa-user-md fa-2x"></i> <!-- Doctors Icon -->
                </div>
                <div class="info-details">
                    <h3><?= $total_doctors ?></h3>
                    <p>Doctors</p>
                </div>
                <a href="./ViewDoctor.php" class="view-details">View Details</a>
            </div>

            <div class="info-box">
                <div class="info-icon">
                    <i class="fa-solid fa-calendar-check fa-2x"></i> <!-- Appointments Icon -->
                </div>
                <div class="info-details">
                    <h3><?= $total_appointments ?></h3>
                    <p>Appointments</p>
                </div>
                <a href="./ViewAppointment.php" class="view-details">View Details</a>
            </div>
            <div class="info-box">
                <div class="info-icon">
                    <i class="fa-solid fa-headset fa-2x"></i> <!-- Receptionist Icon -->
                </div>
                <div class="info-details">
                    <h3><?= $total_receptionist ?></h3>
                    <p>Receptionist</p>
                </div>
                <a href="./ViewReceptionist.php" class="view-details">View Details</a>
            </div>

            <div class="info-box">
                <div class="info-icon">
                    <i class="fa-solid fa-calendar-day fa-2x"></i> <!-- Today Appointments Icon -->
                </div>
                <div class="info-details">
                    <h3><?php echo $today_appointments ?></h3>
                    <p>Today Appointments</p>
                </div>
                <a href="./TodayAppointment.php" class="view-details">View Details</a>
            </div>

            <div class="info-box">
                <div class="info-icon">
                    <i class="fa-solid fa-money-bill-wave fa-2x"></i> <!-- Revenue Icon -->
                </div>
                <div class="info-details">
                    <h3><?php echo $totalAmount ?> &#8377;</h3>
                    <p>Revenue</p>
                </div>
                <a href="./Payment.php" class="view-details">View Details</a>
            </div>
        </div>

        <div class="d-flex gap-3" id="section">
            <div class="chart-container my-4" id="chart">
                <?php include_once("./component/chart.php") ?>
            </div>

            <div class="doctor-container my-4">
                <h3 class="fw-bold mt-3">Active Doctor</h3>
                <table class="doctor-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Doctor</th>
                            <th>Specialization</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $doc = mysqli_query($conn, "SELECT * FROM doctors WHERE `status` = 'Active'");
                        while ($active = mysqli_fetch_assoc($doc)) {
                        ?>
                            <tr>
                                <td> <img src="./../assets/doctorphotos/<?= $active["photo"] ?>" height="150" /> </td>
                                <td> <?= $active["doctor_name"] ?> </td>
                                <td> <?= $active["specilization"] ?> </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-3" id="section">
            <div class="receptionist-container my-4">
                <h3 class="fw-bold mt-3">Active Receptionist</h3>
                <table class="receptionist-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $doc = mysqli_query($conn, "SELECT * FROM receptionist WHERE `status` = 'Active'");
                        while ($receptionist = mysqli_fetch_assoc($doc)) {
                        ?>
                            <tr>
                                <td> <?= $receptionist["rid"] ?> </td>
                                <td> <?= $receptionist["name"] ?> </td>
                                <td> <?= $receptionist["contact"] ?> </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="chart-container2 my-4" id="chart2">
                <?php include_once("./component/paymentchart.php"); ?>
            </div>
        </div>

        <?php include_once("./component/admin_footer.php"); ?>
        
    </div>

</body>

</html>