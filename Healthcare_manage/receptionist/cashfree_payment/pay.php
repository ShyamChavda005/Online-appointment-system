<?php
session_start();
include '../../../config.php';

$conn = connection();

if (isset($_REQUEST['addAppointment'])) {
    ob_start();
    //setting sessions foe insert data when payment successfull
    $_SESSION['patient_id'] = $_REQUEST['patient_name'];
    $_SESSION['doctor_id'] = $_REQUEST['doctor_name'];
    $_SESSION['dt'] = $_REQUEST['dt'];
    $_SESSION['time'] = $_REQUEST['appointment_time'];
    $_SESSION['reason'] = $_REQUEST['reason'];
    $fee = $_REQUEST["consultancy_fee"];

    //check if doctor available on day or not
    $doctor_id = $_POST['doctor_name'];
    $patient_name = $_POST['patient_name'];
    $appointment_date = $_POST['dt'];
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
    ob_end_flush();
}
include 'config.php';

$patient_id = $_REQUEST["patient_name"];
$records = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id=$patient_id");
$patients = mysqli_fetch_assoc($records);

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
        "return_url" => "http://localhost/Online_Healthcare_Appointment_System/Healthcare/Healthcare_manage/receptionist/cashfree_payment/callback.php?order_id={$order_id}&patient_id={$patient_id}"
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
        }).then(() => { window.location.href = 'your_payment_page.php'; });
    </script>";
    exit();
}
