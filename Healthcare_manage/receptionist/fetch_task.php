<?php
include_once('../../config.php');
$conn = connection();

$query = mysqli_query($conn, "SELECT dr.*, p.patient_name, d.doctor_name 
                              FROM doctor_appointment_requests dr
                              JOIN patients p ON dr.patient_id = p.patient_id
                              JOIN doctors d ON dr.doctor_id = d.doctor_id
                              ORDER BY dr.id DESC");

$output = "";
while ($row = mysqli_fetch_assoc($query)) {
    $appointment_id = $row["id"]; // Only pass appointment ID
    $isCompleted = ($row["status"] == "Completed"); // Check if status is Completed

    $statusBadge = ($row["status"] == "Completed") ?
        '<span class="badge bg-success">Completed</span>' :
        '<span class="badge bg-warning">Pending</span>';

    $actionButton = $isCompleted
        ? '<button class="btn btn-sm btn-secondary" disabled>Completed</button>'
        : "<a href='AddAppointment.php?task_id={$appointment_id}' class='btn btn-sm btn-success'>Complete</a>";

    $output .= "<tr>
                    <td>{$row["id"]}</td>
                    <td>{$row["patient_name"]}</td>
                    <td>{$row["doctor_name"]}</td>
                    <td>{$row["suggested_date"]}</td>
                    <td>{$row["suggested_time"]}</td>
                    <td>{$row["reason"]}</td>
                    <td class='text-center'>{$statusBadge}</td>
                    <td class='text-center'>{$actionButton}</td>
                </tr>";
}

echo $output;
