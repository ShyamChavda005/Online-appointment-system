<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("location:../login.php");
}

include_once("../../config.php");
$conn = connection();

//fetching patient id who logged in using session
$puname = $_SESSION['user'];
$query2 = mysqli_query($conn, "SELECT * FROM patients WHERE username='$puname'");
$patient = mysqli_fetch_assoc($query2);
$pid = $patient["patient_id"];

$upd = false;
$noChange = false;

$uppatient = "";
$updoctor = "";
$updt = "";
$uptime = "";
$upreason = "";

if (isset($_REQUEST["aid"])) {
    $aid = $_REQUEST["aid"];
    $str = mysqli_query($conn, "SELECT * FROM appointments WHERE appoint_id = $aid");
    $appoint_data = mysqli_fetch_assoc($str);

    $uppatient = $appoint_data["patient_id"];
    $updoctor = $appoint_data["doctor_id"];

    $st1 = mysqli_query($conn, "SELECT * FROM doctors WHERE `status` = 'Active' AND doctor_id = $updoctor");
    $row1 = mysqli_fetch_assoc($st1);

    $updt = $appoint_data["appointment_date"];
    $uptime = $appoint_data["appointment_time"];
    $upreason = $appoint_data["reason"];
}

if (isset($_REQUEST["update_appointment"])) {
    $aid = $_REQUEST["aid"];
    $ptid = $_REQUEST["patient_id"];
    $dtid = $_REQUEST["doctor_id"];
    $date = $_REQUEST["dt"];
    $time = $_REQUEST["appointment_time"];
    $reason = $_REQUEST["reason"];

    // Convert the appointment date to a weekday name (e.g., "Monday")
    $dayOfWeek = date('l', strtotime($date));

    // Query to check if the doctor is available on the selected date
    $scheduleQuery = "SELECT * FROM doctor_schedule WHERE doctor_id = ? AND JSON_CONTAINS(available_days, '\"$dayOfWeek\"')";

    $stmt = $conn->prepare($scheduleQuery);
    $stmt->bind_param("i", $dtid);
    $stmt->execute();
    $result = $stmt->get_result();

    // If no matching schedule found, show SweetAlert error
    if ($result->num_rows === 0) {
        echo "<html>
        <head>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head> 
        <body>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Unavailable Date',
                        text: 'The doctor is not available on this date. Please select another date!',
                    }).then(() => {
                        window.history.back(); // Go back to the previous page after alert
                    });
                });
            </script>
        </body>
    </html>";
        exit;
    }

    $C = mysqli_query($conn, "SELECT * FROM appointments WHERE appoint_id = $aid");
    $O = mysqli_fetch_assoc($C);

    if ($ptid == $O["patient_id"] && $dtid == $O["doctor_id"] && $date == $O["appointment_date"] && $time == $O["appointment_time"] && $reason == $O["reason"]) {
        $noChange = true;
    } else {
        $q = "UPDATE appointments SET doctor_id = $dtid , appointment_date = '$date',appointment_time= '$time', reason = '$reason' WHERE appoint_id = $aid";
        mysqli_query($conn, $q);
        $upd = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming - Appointements</title>
    <link rel="stylesheet" href="./style/upcominappointment.css">
    <link rel="website icon" href="./image/logo.png">
    <!-- Datable JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <!-- Datable Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.css" />
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.print.min.js"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function validationForm() {
            const doctorName = document.getElementById('doctor_id').value.trim();
            const dt = document.getElementById('dt').value; // Fixed id reference
            const timeSlots = document.getElementById('timeSlots').value.trim();
            const reason = document.getElementById('reason').value.trim();
            const today = new Date().toISOString().split("T")[0]; // Get today's date
            let isValid = true;

            // Regular expression to allow only letters and spaces
            const regex = /^[A-Za-z\s]+$/;

            // Doctor selection validation
            if (doctorName === "Select Doctors") {
                document.getElementById("dval").style.display = "block";
                setTimeout(() => document.getElementById("dval").style.display = "none", 1200);
                isValid = false;
            }

            // Date validation
            if (!dt) {
                document.getElementById("dtval").innerText = "* Date is Required";
                document.getElementById("dtval").style.display = "block";
                setTimeout(() => document.getElementById("dtval").style.display = "none", 1200);
                isValid = false;
            }

            // Time slot validation
            if (timeSlots == "-- Select Time --") {
                document.getElementById("ttval").style.display = "block";
                setTimeout(() => document.getElementById("ttval").style.display = "none", 1200);
                isValid = false;
            }

            // Reason validation
            if (!reason) {
                document.getElementById("rval").innerText = "* Reason is required";
                document.getElementById("rval").style.display = "block";
                setTimeout(() => document.getElementById("rval").style.display = "none", 1200);
                isValid = false;
            } 
            return isValid;
        }
    </script>
</head>

<body>
    <?php include_once("./Navbar.php"); ?>
    <?php include_once("./admin_header.php"); ?>
    <?php if ($noChange) { ?>
        <script>
            Swal.fire({
                text: "No Changes!",
                icon: "warning"
            });
        </script>
    <?php } ?>

    <?php if ($upd) { ?>
        <script>
            Swal.fire({
                title: "Success!",
                text: "Update Successfully!",
                icon: "success",
                showConfirmButton: false
            });

            setTimeout(() => {
                window.location.href = "UpcomingAppointment.php";
            }, 1500);
        </script>
    <?php } ?>


    <div class="content">

        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Upcoming Appointments List</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Upcoming Appointments
                    </span></p>
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

        <div class="container">
            <div class="table-container">
                <table id="myTable" class="nowrap">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Patient</th>
                            <th class="text-center">Doctor</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Time</th>
                            <th class="text-center">Reason</th>
                            <th class="text-center">Update</th>
                            <th class="text-center">Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query3 = mysqli_query($conn, "SELECT * FROM appointments 
    WHERE (appointment_date > CURDATE() 
        OR (appointment_date = CURDATE() AND appointment_time > CURTIME())) 
    AND patient_id = $pid 
    ORDER BY appointment_date ASC, appointment_time ASC");
                        while ($appointment = mysqli_fetch_assoc($query3)) { ?>
                            <tr>
                                <td class="text-center"><?php echo $appointment["appoint_id"]; ?></td>
                                <td class="text-center"><?php
                                    $p_id = $appointment["patient_id"];
                                    $pt = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $p_id");
                                    $pdata = mysqli_fetch_assoc($pt);
                                    echo $pdata["patient_name"];
                                    ?></td>
                                <td class="text-center"><?php
                                    $d_id = $appointment["doctor_id"];
                                    $doct = mysqli_query($conn, "SELECT * FROM doctors WHERE doctor_id = $d_id");
                                    $ddata = mysqli_fetch_assoc($doct);
                                    echo $ddata["doctor_name"];
                                    ?></td>
                                </td>
                                <td class="text-center"><?php echo $appointment["appointment_date"]; ?></td>
                                <td class="text-center"><?php echo $appointment["appointment_time"]; ?></td>
                                <td class="text-center"><?php echo $appointment["reason"]; ?></td>
                                <td class="text-center">
                                    <a href="?aid=<?php echo $appointment["appoint_id"]; ?>" name="edit">
                                        <button type="button" class="btn" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal">
                                            <img src="./image/edit.svg" alt="Edit" height="20">
                                        </button>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $payment_id = $appointment["payment_id"];
                                    $query7 = mysqli_query($conn, "SELECT * FROM payments WHERE payment_id=$payment_id");
                                    $payments = mysqli_fetch_assoc($query7);
                                    if ($payments["status"] == "PAID") {
                                        echo '<span class="badge bg-success">Success</span>';
                                    } else {
                                        echo '<span class="badge bg-danger">Failed</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg">

                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-calendar-check"></i> Modify Appointment Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Form Start -->
                <form method="post" onsubmit="return validationForm()">
                    <div class="modal-body">

                        <!-- Patient ID -->
                        <div class="form-group mb-3">
                            <label class="fw-semibold"><i class="bi bi-person-badge"></i> Patient ID (Read-Only)</label>
                            <input class="form-control" name="patient_id" id="patient_id" value="<?= $uppatient ?>"
                                readonly />
                        </div>

                        <!-- Doctor Selection -->
                        <div class="form-group mb-3">
                            <label class="fw-semibold"><i class="bi bi-person-plus"></i> Select Consulting
                                Doctor</label>
                            <select class="form-select" id="doctor_id" name="doctor_id">
                                <!-- <option>Select Doctor</option> -->
                                <option value="<?= $row1["doctor_id"] ?>" <?php if ($row1["doctor_id"] == $updoctor) { ?>
                                    selected <?php } ?>>
                                    <?= $row1["doctor_name"] ?>
                                </option>
                            </select>
                            <span id="dval" class="text-danger" style="display: none;"> * Please select a doctor.
                            </span>
                        </div>

                        <!-- Appointment Date -->
                        <div class="form-group mb-3">
                            <label class="fw-semibold" for="dt"><i class="bi bi-calendar-event"></i> Select Appointment
                                Date</label>
                            <input type="date" class="form-control" value="<?= $updt ?>" id="dt" name="dt">
                            <span id="dtval" class="text-danger" style="display: none;"> * Date & time are required.
                            </span>
                        </div>

                        <!-- Appointment Time -->
                        <div class="form-group mb-3">
                            <label class="fw-semibold" for="time"> <i class="bi bi-clock"></i> Available Time</label>
                            <select class="form-select" name="appointment_time" id="timeSlots">
                                <option value="">-- Select Time --</option>
                            </select>
                            <span id="ttval" style="color:red;display:none;"> * Time is Required </span>
                        </div>

                        <!-- Reason -->
                        <div class="form-group mb-3">
                            <label class="fw-semibold" for="reason"><i class="bi bi-chat-left-text"></i> Appointment
                                Purpose / Concern</label>
                            <input id="reason" type="text" class="form-control" value="<?= $upreason ?>" name="reason">
                            <span id="rval" class="text-danger" style="display: none;"> * Please provide a reason for
                                the appointment. </span>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success" name="update_appointment">
                            <i class="bi bi-check-circle-fill"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split("T")[0]; // Get today's date in YYYY-MM-DD format
            document.getElementById('dt').setAttribute('min', today);

            let dateInput = document.getElementById("dt"); // Ensure this ID matches your date input field
            if (dateInput) {
                dateInput.addEventListener("change", function() {
                    let today = new Date();
                    let selectedDate = new Date(dateInput.value);
                    let maxAllowedDate = new Date(today);
                    maxAllowedDate.setMonth(today.getMonth() + 1); // Set max limit to 1 month

                    if (selectedDate > maxAllowedDate) {
                        Swal.fire({
                            icon: "warning",
                            title: "Date Selection Out of Range",
                            text: "Appointments can only be scheduled within 1 month from today. Please select a valid date.",
                            confirmButtonText: "OK, I Understand"
                        });

                        // Reset the date field to empty
                        dateInput.value = "";
                    }
                });
            } else {
                console.log("Date input field not found! Check the ID.");
            }
        });

        let table = new DataTable('#myTable', {
            paging: true,
            searching: true,
            ordering: true,
            scrollX: true,
            info: true,
            "columnDefs": [{
                "orderable": false,
                "targets": [6]
            }],
            responsive: true,
            // "fixedHeader": false,
            autoWidth: true,
            pageLength: 5,
            lengthChange: false,
            language: {
                info: "", // Hide default DataTable info text
            },
            drawCallback: function(settings) {
                let api = this.api();
                let pageInfo = api.page.info();

                // Custom info message with professional formatting
                let customInfo = `
            <span class="text-muted">
                <i class="bi bi-info-circle-fill text-primary"></i>
                Showing <strong>${pageInfo.start + 1} - ${pageInfo.end}</strong>
                of <strong>${pageInfo.recordsTotal}</strong> total records.
            </span>`;

                // Update DataTable info display
                $('#myTable_info').html(customInfo);
            }

        });

        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('aid')) {
                let editModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                editModal.show();
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let doctorId = document.getElementById("doctor_id").value;
            let appointmentDate = document.getElementById("dt").value;
            let selectedTime = "<?= $uptime ?>";

            if (doctorId && appointmentDate) {
                loadTimeSlots(doctorId, appointmentDate, selectedTime);
            }
        });

        document.getElementById("doctor_id").addEventListener("change", function() {
            let doctorId = this.value;
            let appointmentDate = document.getElementById("dt").value;
            loadTimeSlots(doctorId, appointmentDate);
        });

        document.getElementById("dt").addEventListener("change", function() {
            let doctorId = document.getElementById("doctor_id").value;
            let appointmentDate = this.value;
            loadTimeSlots(doctorId, appointmentDate);
        });

        function loadTimeSlots(doctorId, appointmentDate, selectedTime = "") {
            if (doctorId && appointmentDate) {
                fetch(`get_available_slots.php?doctor_id=${doctorId}&appointment_date=${appointmentDate}`)
                    .then(response => response.json())
                    .then(data => {
                        let timeSelect = document.getElementById("timeSlots");
                        timeSelect.innerHTML = `<option> -- Select Time -- </option>
                        <option value="<?= $uptime ?>" selected>Current Selected Time : <?= date("H:i", strtotime($uptime)); ?></option>`;

                        data.forEach(time => {
                            let option = document.createElement("option");
                            option.value = time;
                            option.textContent = time;
                            timeSelect.appendChild(option);
                        });

                        // Now, set the selected time after options are added
                        if (selectedTime) {
                            for (let option of timeSelect.options) {
                                if (option.value === selectedTime) {
                                    option.selected = true;
                                }
                            }
                        }

                        if (data.length === 0) {
                            alert("No available slots on this day!");
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>
</body>

</html>