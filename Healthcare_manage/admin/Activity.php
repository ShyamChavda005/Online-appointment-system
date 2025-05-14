<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}

include_once('../../config.php');
$conn = connection();    

$date_today = date("Y-m-d");

$appointments = mysqli_query($conn, "SELECT * FROM appointments WHERE DATE(create_at) = '$date_today'");
$doctors = mysqli_query($conn, "SELECT * FROM doctors WHERE DATE(create_at) = '$date_today'");
$patients = mysqli_query($conn, "SELECT * FROM patients WHERE DATE(create_at) = '$date_today'");
$receptionists = mysqli_query($conn, "SELECT * FROM receptionist WHERE DATE(`hire_dt`) = '$date_today'");
$queries = mysqli_query($conn, "SELECT * FROM query WHERE DATE(`date&time`) = '$date_today'");
$feedbacks = mysqli_query($conn, "SELECT * FROM feedback WHERE DATE(`date&time`) = '$date_today'");
$payment = mysqli_query($conn, "SELECT * FROM payments WHERE DATE(`create_at`) = '$date_today'");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Activity Overview</title>
    <link rel="stylesheet" type="text/css" href="./style/Activity.css">
    <link rel="website icon" href="./../assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
</head>

<body>

    <?php include_once("./Navbar.php"); ?>
    <?php include_once("./component/admin_header.php"); ?>

    <div class="content" id="content">
        <div class="header-list">
            <div class="left">
                <h4 class="fw-bold"> Daily Activity Overview </h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Daily Activity Overview
                    </span></p>
            </div>
        </div>

        <div class="container pt-3">
            <div class="timeline-container">
                <div class="timeline">

                    <?php if (mysqli_num_rows($appointments) > 0) { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-calendar-check"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-primary fw-bold">New Appointment Booked</h5>
                                <?php while ($row = mysqli_fetch_assoc($appointments)) { ?>
                                    <?php
                                    $i = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = " . $row["patient_id"]);
                                    $pt = mysqli_fetch_assoc($i);

                                    $j = mysqli_query($conn, "SELECT * FROM doctors WHERE doctor_id = " . $row["doctor_id"]);
                                    $doct = mysqli_fetch_assoc($j);
                                    ?>
                                    <p>Patient <?= htmlspecialchars($pt["patient_name"]) ?> has booked an appointment with
                                        <?= htmlspecialchars($doct["doctor_name"]) ?> on
                                        <?= date("F j, Y", strtotime($row['appointment_date'])) ?> at
                                        <?= date("h:i A", strtotime($row['appointment_time'])) ?>.
                                        <br>

                                        <span class="time"><?= date("F j, Y, g:i A", strtotime($row['create_at'])) ?></span>

                                    </p>
                                <?php } ?>
                                <a href="./TodayAppointment.php" class="btn btn-primary btn-sm mt-2">
                                    View Details <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-calendar-check"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-warning-emphasis fw-bold">No New Appointments Booked</h5>
                                <p>There are no new appointments booked today. Stay tuned for upcoming schedules.</p>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (mysqli_num_rows($patients) > 0) { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-person-lines-fill"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-primary fw-bold">New Patient Registered</h5>
                                <?php while ($row = mysqli_fetch_assoc($patients)) { ?>
                                    <p>Welcome, <?= $row['patient_name'] ?>! You are now connected with our healthcare services.
                                        <br>
                                        <span class="time"><?= date("F j, Y, g:i A", strtotime($row['create_at'])) ?></span>
                                    </p>
                                <?php } ?>
                                <a href="./ViewPatient.php" class="btn btn-primary btn-sm mt-2">
                                    View Details <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-person-lines-fill"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-warning-emphasis fw-bold">No New Patient Registrations</h5>
                                <p>No new patients have signed up today. Check back later for updates.</p>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (mysqli_num_rows($receptionists) > 0) { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-person-badge"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-primary fw-bold">New Receptionists Registered</h5>
                                <?php while ($row = mysqli_fetch_assoc($receptionists)) { ?>
                                    <p>Welcome, <?= $row['name'] ?>! You are now connected with our healthcare services.
                                        <br>
                                        <span class="time"><?= date("F j, Y, g:i A", strtotime($row['hire_dt'])) ?></span>
                                    </p>
                                <?php } ?>
                                <a href="./ViewReceptionist.php" class="btn btn-primary btn-sm mt-2">
                                    View Details <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-person-badge"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-warning-emphasis fw-bold">No New Receptionist Registrations</h5>
                                <p>No new receptionist have signed up today. Check back later for updates.</p>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (mysqli_num_rows($doctors) > 0) { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-hospital"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-primary fw-bold">New Doctor Joined</h5>
                                <?php while ($row = mysqli_fetch_assoc($doctors)) { ?>
                                    <p>We are pleased to welcome Dr. <?= $row['doctor_name'] ?> to our healthcare team.
                                        <br>
                                        <span class="time"><?= date("F j, Y, g:i A", strtotime($row['create_at'])) ?></span>

                                    </p>
                                <?php } ?>
                                <a href="./ViewDoctor.php" class="btn btn-primary btn-sm mt-2">
                                    View Details <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-hospital"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-warning-emphasis fw-bold">No New Doctors Registered</h5>
                                <p>There have been no new doctor registrations today. Stay updated for more additions.</p>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (mysqli_num_rows($queries) > 0) { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-question-circle"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-primary fw-bold">New Patient Inquiry</h5>
                                <?php while ($row = mysqli_fetch_assoc($queries)) { ?>
                                    <p>A query has been submitted by <?= $row['email'] ?>.
                                        <br>
                                        <span class="time"><?= date("F j, Y, g:i A", strtotime($row['date&time'])) ?></span>

                                    </p>
                                <?php } ?>
                                <a href="./Query.php" class="btn btn-primary btn-sm mt-2">
                                    View Details <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-question-circle"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-warning-emphasis fw-bold">No New Inquiries</h5>
                                <p>No patient inquiries have been recorded today.</p>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (mysqli_num_rows($feedbacks) > 0) { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-chat-dots"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-primary fw-bold">New Patient Feedback</h5>
                                <?php while ($row = mysqli_fetch_assoc($feedbacks)) { ?>
                                    <p>Feedback received from <?= $row['email'] ?>.
                                        <br>
                                        <span class="time"><?= date("F j, Y, g:i A", strtotime($row['date&time'])) ?></span>

                                    </p>
                                <?php } ?>
                                <a href="./Feedback.php" class="btn btn-primary btn-sm mt-2">
                                    View Details <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-chat-dots"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-warning-emphasis fw-bold">No New Feedback</h5>
                                <p>No patient feedback has been submitted today.</p>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (mysqli_num_rows($payment) > 0) { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-chat-dots"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-primary fw-bold">New Payment</h5>
                                <?php while ($row = mysqli_fetch_assoc($payment)) { ?>
                                    <p>
                                        Amount Of <?php echo $row['amount']; ?>
                                        is Received from
                                        <?php $i = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = " . $row["patient_id"]);
                                        $pt = mysqli_fetch_assoc($i);
                                        echo $pt["patient_name"]
                                            ?>
                                        <br>
                                        <span class="time"><?= date("F j, Y, g:i A", strtotime($row['create_at'])) ?></span>
                                    </p>
                                <?php } ?>
                                <a href="./Payment.php" class="btn btn-primary btn-sm mt-2">
                                    View Details <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="bi bi-chat-dots"></i></div>
                            <div class="timeline-content">
                                <h5 class="text-warning-emphasis fw-bold">No New Payments</h5>
                                <p>No Payments has been collected today.</p>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</body>

</html>