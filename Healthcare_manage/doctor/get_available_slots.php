<?php
session_start();
if (!isset($_SESSION["doctor"])) {
    header("location:../index.php");
    exit;
}

// Set your timezone to match your local requirements
date_default_timezone_set("Asia/Kolkata"); // Change to your appropriate timezone

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include_once('../../config.php'); // Ensure this file exists

$doctor_id = $_REQUEST['doctor_id'] ?? null;
$appointment_date = $_REQUEST['appointment_date'] ?? null;

// Validate required parameters and date format
if (!$doctor_id || !$appointment_date) {
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

// Optionally, check that $appointment_date is in the correct format (Y-m-d)
if (DateTime::createFromFormat('Y-m-d', $appointment_date) === false) {
    echo json_encode(["error" => "Invalid date format"]);
    exit;
}

// Database Connection
$conn = connection();

// Fetch Doctor's Schedule
$scheduleQuery = "SELECT available_from, available_to, appointment_duration FROM doctor_schedule WHERE doctor_id = ?";
$stmt = $conn->prepare($scheduleQuery);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$scheduleResult = $stmt->get_result();

if ($scheduleResult->num_rows == 0) {
    echo json_encode(["error" => "Doctor schedule not found"]);
    exit;
}

$schedule = $scheduleResult->fetch_assoc();
// Combine the appointment_date with the doctor's available times
$available_from = strtotime("$appointment_date {$schedule['available_from']}");
$available_to = strtotime("$appointment_date {$schedule['available_to']}");
$appointment_duration = $schedule['appointment_duration'] * 60; // Convert minutes to seconds

// Get current time (timestamp) and current date string
$current_datetime = time();
$current_date = date("Y-m-d");

// Get Booked Slots
$booked_slots = [];
$bookedSlotsQuery = "SELECT appointment_time FROM appointments WHERE doctor_id = ? AND appointment_date = ?";
$stmt = $conn->prepare($bookedSlotsQuery);
$stmt->bind_param("is", $doctor_id, $appointment_date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) { 
    // Convert each booked slot time to a timestamp for the same appointment date
    $booked_slots[] = strtotime("$appointment_date {$row['appointment_time']}");
}

// Generate Available Time Slots
$available_slots = [];
for ($time = $available_from; $time + $appointment_duration <= $available_to; $time += $appointment_duration) {
    
    // If booking for today, skip slots that are already passed
    if ($appointment_date === $current_date && $time < $current_datetime) {
        continue; 
    }
    
    // If this slot time is not in the booked slots array, add it
    if (!in_array($time, $booked_slots)) {
        $available_slots[] = date("H:i", $time);
    }
}

// Output JSON Response
echo json_encode($available_slots);
?>
