<?php
session_start();
if (!isset($_SESSION["doctor"])) {
    header("location:../index.php");
}

include_once('../../config.php');
$conn = connection();
$duname = $_SESSION['doctor'];

$query2 = mysqli_query($conn, "SELECT * FROM doctors WHERE username='$duname'");
$doctor = mysqli_fetch_assoc($query2);
$did = $doctor["doctor_id"];

if (isset($_REQUEST['save'])) {

    //check if doctor available on day or not
    $doctor_id = $did;
    $patient_id = $_POST['patient_name'];
    $appointment_date = $_POST['dt'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];

    // Get Doctor's Schedule
    $scheduleQuery = "SELECT available_days, available_from, available_to, appointment_duration 
   FROM doctor_schedule WHERE doctor_id = ?";
    $stmt = $conn->prepare($scheduleQuery);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $scheduleResult = $stmt->get_result();
    $stmt->close();

    if ($scheduleResult->num_rows == 0) {
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Schedule Not Found!',
                    text: 'Doctor schedule is not available.',
                    confirmButtonText: 'Go Back'
                }).then(() => { window.history.back(); });
            </script>
        </body>
        </html>";
        exit;
    }

    $schedule = $scheduleResult->fetch_assoc();
    $available_days = json_decode($schedule['available_days'], true);
    $available_from = $schedule['available_from'];
    $available_to = $schedule['available_to'];
    $appointment_duration = $schedule['appointment_duration']; // Get appointment duration

    // Get the day of the week for the appointment date
    $day_of_week = date('l', strtotime($appointment_date));

    // Check if Doctor is Available on this day
    if (!in_array($day_of_week, $available_days)) {
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Doctor Not Available!',
                    text: 'The doctor is not available on this day. Please select another date.',
                    confirmButtonText: 'Try Again'
                }).then(() => { window.history.back(); });
            </script>
        </body>
        </html>";
        exit;
    }

    // Calculate appointment end time based on duration
    $appointment_end_time = date("H:i:s", strtotime($appointment_time) + ($appointment_duration * 60));

    // Check if Doctor is on Leave
    $leaveQuery = "SELECT * FROM doctor_leave 
    WHERE doctor_id = ? AND leave_date = ? 
    AND (leave_start <= ? AND leave_end >= ?) AND `status` = 'Approve'";
    $stmt = $conn->prepare($leaveQuery);
    $stmt->bind_param("isss", $doctor_id, $appointment_date, $appointment_time, $appointment_end_time);
    $stmt->execute();
    $leaveResult = $stmt->get_result();
    $stmt->close();

    if ($leaveResult->num_rows > 0) {
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
            Swal.fire({
                icon: 'info',
                title: 'Doctor on Leave!',
                text: 'The doctor is on leave at this time. Please choose another time.',
                confirmButtonText: 'Pick Another Time'
            }).then(() => { window.history.back(); });
        </script>
        </body>
        </html>";
        exit;
    }
    ob_end_flush();

    // Insert into doctor_appointment_request table
    $insertQuery = "INSERT INTO doctor_appointment_requests(doctor_id, patient_id, suggested_date, suggested_time, reason, status) 
     VALUES ('$did', '$patient_id', '$appointment_date', '$appointment_time', '$reason', 'Pending')";

    if (mysqli_query($conn, $insertQuery)) {
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
           Swal.fire({
                title: 'Success!',
                text: 'Appointment Requested Successfully!',
                icon: 'success'
            });
            setTimeout(() => {
                window.location.href = 'viewrequest.php';
            }, 1500);
        </script>
        </body>
        </html>";
        exit;
    } else {
        $falsealert = true;
    }
}
