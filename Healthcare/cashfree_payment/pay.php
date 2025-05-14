<?php
session_start();
include_once('../../config.php');

$conn = connection();

if (isset($_REQUEST['addAppointment'])) {
    //setting sessions foe insert data when payment successfull
    $patient_username = $_REQUEST["username"];
    $patient_pwd = hash("sha256",$_REQUEST["password"]);
    $DATA = mysqli_query($conn, "SELECT * FROM patients WHERE username='$patient_username' AND `password`='$patient_pwd'");
    $patient_record =  mysqli_fetch_assoc($DATA);

    $_SESSION["patient_name"] = $_REQUEST["patient_name"];
    $_SESSION['patient_id'] = $patient_record['patient_id'];
    $_SESSION['doctor_id'] = $_REQUEST['doctor_id'];
    $_SESSION['dt'] = $_REQUEST['date'];
    $_SESSION['time'] = $_REQUEST['appointment_time'];
    $_SESSION['reason'] = $_REQUEST['reason'];
    $fee = $_REQUEST["consultancy_fee"];

    //check if doctor available on day or not
    $doctor_id = $_POST['doctor_id'];
    $patient_name = $patient_record['patient_id'];
    $appointment_date = $_POST['date'];
    $appointment_time = $_POST['appointment_time'];

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
}

include 'config.php';

$username = $_REQUEST["username"];
$password = hash("sha256",$_REQUEST["password"]);
$records = mysqli_query($conn, "SELECT * FROM patients WHERE username='$username' AND `password` = '$password'");

if (mysqli_num_rows($records) > 0) {
    $patients = mysqli_fetch_assoc($records);

    if ($patients["status"] == 'Suspend') {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script> 
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Account Suspended!',
                        text: 'You are suspended for some reason. Please contact healthcare support!',
                        icon: 'warning',
                        allowOutsideClick: false,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '../appointmentform.php';
                    });
                }); 
            </script>";
        exit(); // Stop further execution
    }

    $patient_id = $patients["patient_id"];
    $patient_mno = $patients["contact"];
    $patient_name = $patients["patient_name"];
    $patient_email = $patients["email"];

    $amount = $fee; // Set order amount
    $order_id = "ORD_" . uniqid(); // Unique Order ID

    // Prepare Payment Request
    $data = [
        "order_id" => $order_id,
        "order_amount" => $amount,
        "order_currency" => "INR",
        "customer_details" => [
            "customer_id" => $patient_id,
            "customer_email" => $patient_email,
            "customer_phone" => $patient_mno,
            "customer_name" => $patient_name
        ],
        "order_meta" => [
            "return_url" => "http://localhost/Online_Healthcare_Appointment_System/Healthcare/Healthcare/cashfree_payment/callback.php?order_id={$order_id}&patient_id={$patient_id}"
        ]
    ];

    // cURL Request to Cashfree
    $ch = curl_init(CASHFREE_API_URL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "x-api-version: 2022-01-01",
        "x-client-id: " . CASHFREE_APP_ID,
        "x-client-secret: " . CASHFREE_SECRET_KEY
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    // Redirect to Payment Link
    if (isset($response['payment_link'])) {
        header("Location: " . $response['payment_link']);
        exit();
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Payment Error!',
                text: 'There was an issue creating the order. Please try again.',
                confirmButtonText: 'Retry'
            }).then(() => { window.location.href = '../appointmentform.php'; });
        </script>";
        exit();;
    }
} else {
    error_reporting(0);
    echo '
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script> 
        Swal.fire({
            title: "Oops...",
            text: "Username Or Password Not Exist!",
            icon: "error"
        });

        setTimeout(()=> {
            window.location.href = "../appointmentform.php";
        },1000)
    </script>';
}
