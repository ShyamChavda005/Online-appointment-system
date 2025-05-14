<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location:../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/dashboard.css">
    <title>Home - Patient</title>
    <link rel="website icon" href="./image/logo.png">
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
    include_once("../../config.php");
    $conn = connection();
    $puname = $_SESSION['user'];

    // getting particular patients record that in session
    $query2 = mysqli_query($conn, "SELECT * FROM patients where username='$puname'");
    $patient = mysqli_fetch_assoc($query2);
    $pid = $patient["patient_id"];

    // getting the number of appointments
    $Q1 = mysqli_query($conn, "SELECT * FROM appointments where patient_id=$pid");
    $total_appointments = mysqli_num_rows($Q1);

    //upcoming appointments number
    $Q3 = mysqli_query($conn, "SELECT * FROM appointments 
    WHERE (appointment_date > CURDATE() 
        OR (appointment_date = CURDATE() AND appointment_time > CURTIME())) 
    AND patient_id=$pid ORDER BY appointment_date ASC");
    $number_of_upcoming = mysqli_num_rows($Q3);

    //total query number
    $email = $patient["email"];
    $Q4 = mysqli_query($conn, "SELECT * FROM query WHERE email='$email'");
    $number_of_query = mysqli_num_rows($Q4);

    // total feedback number
    $Q5 = mysqli_query($conn, "SELECT * FROM feedback WHERE email='$email'");
    $number_of_feedback = mysqli_num_rows($Q5);

    // Total amount of payment of patient
    $Q5 = mysqli_query($conn, "SELECT SUM(amount) AS total_amount FROM payments WHERE patient_id = $pid");
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
                    <form action="./AddAppointment.php" method="post">
                        <button type="submit"
                            class="btn btn-primary d-flex align-items-center px-4 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-calendar-check me-2"></i> Book Appointment
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
                    <h3><?= $number_of_amount ?></h3>
                    <p>Billing & Payments</p>
                </div>
                <a href="./Payment.php" class="view-details">Payment History</a>
            </div>

            <div class="info-box">
                <div class="info-icon">
                    <i class="bi bi-question-octagon-fill fs-3"></i>
                </div>
                <div class="info-details">
                    <h3><?php echo $number_of_query; ?></h3>
                    <p>Support & Inquiries</p>
                </div>
                <a href="./Query.php" class="view-details">Help Desk</a>
            </div>

            <div class="info-box">
                <div class="info-icon">
                    <i class="bi bi-star-fill fs-3"></i>
                </div>
                <div class="info-details">
                    <h3><?php echo $number_of_feedback; ?></h3>
                    <p>Patient Feedback</p>
                </div>
                <a href="./Feedback.php" class="view-details">Read Reviews</a>
            </div>
        </div>


        <div class="container mt-4">
            <div class="row g-4">
                <!-- Patient Profile Card -->
                <div class="col-lg-7">
                    <div class="chart-container" id="chart">
                        <?php include_once("./chart.php") ?>
                    </div>
                </div>

                <!-- Upcoming Appointments -->
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 rounded">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-center">Upcoming Appointments</h5>
                        </div>
                        <div class="card-body p-0 overflow-auto" style="max-height: 270px;">
                            <table class="table table-hover mb-0">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Doctor Name</th>
                                        <th>Date & Time</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php
                                    $Q2 = mysqli_query($conn, "SELECT * FROM appointments WHERE appointment_date > CURDATE() AND patient_id=$pid ORDER BY appointment_date ASC");
                                    while ($upcoming = mysqli_fetch_assoc($Q2)) {
                                        $did = $upcoming["doctor_id"];
                                        $Q3 = mysqli_query($conn, "SELECT * FROM doctors WHERE doctor_id=$did");
                                        $doctor = mysqli_fetch_assoc($Q3);
                                        ?>
                                        <tr>
                                            <td><img src="./../../healthcare_manage/assets/doctorphotos/<?php echo $doctor["photo"] ?>"
                                                    class="rounded-circle" height="40" /></td>
                                            <td><?php echo $doctor["doctor_name"] ?></td>
                                            <td>
                                                <?php echo date("d M Y", strtotime($upcoming["appointment_date"])); ?>

                                                <?php echo date("H:i:s", strtotime($upcoming["appointment_time"])); ?>
                                            </td>
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