<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}

date_default_timezone_set("Asia/Kolkata");

include_once('../../config.php');
$conn = connection();
$upd = false;
$noChange = false;

$query3 = mysqli_query($conn, "SELECT * FROM appointments");

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

    $st = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $uppatient");
    $row = mysqli_fetch_assoc($st);

    $updoctor = $appoint_data["doctor_id"];

    $st1 = mysqli_query($conn, "SELECT * FROM doctors WHERE doctor_id = $updoctor");
    $row1 = mysqli_fetch_assoc($st1);

    $updt = $appoint_data["appointment_date"];
    $uptime = $appoint_data["appointment_time"];
    $upreason = $appoint_data["reason"];
}

if (isset($_REQUEST["update_appointment"])) {
    $aid = $_REQUEST["aid"];
    $ptid = $_REQUEST["patient_id"];
    $dtid = $_REQUEST["doctor_id"];
    $date = $_REQUEST["ap_date"];
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
        $status = isset($_REQUEST["status"]);
    }
}

if (isset($_REQUEST["appid"])) {
    $appid = $_REQUEST["appid"];
    $status = isset($_REQUEST["status"]);

    if ($status == "on") {
        $status = "Approve";
        $qu = "UPDATE appointments SET `status` = '$status' WHERE appoint_id = $appid ";
        mysqli_query($conn, $qu);
    } else {
        $status = "Disapprove";
        $qu = "UPDATE appointments SET `status` = '$status' WHERE appoint_id = $appid ";
        mysqli_query($conn, $qu);
    }
    header("Location:ViewAppointment.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View - Appointements</title>
    <link rel="stylesheet" href="./style/ViewAppointment.css">
    <link rel="website icon" href="./../assets/images/logo.png">
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
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split("T")[0]; // Get today's date in YYYY-MM-DD format
            document.getElementById('dte').setAttribute('min', today);

            let dateInput = document.getElementById("dte"); // Ensure this ID matches your date input field
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

        function validationForm() {
            const doctorName = document.getElementById('doctor_id').value.trim();
            const dt = document.getElementById('dte').value; // Fixed id reference
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
    <?php
    include_once("./Navbar.php");
    include_once("./component/admin_header.php");
    ?>

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
                window.location.href = "ViewAppointment.php";
            }, 1500);
        </script>
    <?php } ?>

    <div class="content">
        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Appointments List</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> View Appointments
                    </span></p>
            </div>
            <div class="right">
                <div class="input-search">
                    <form action="./AddAppointment.php" method="post">
                        <button> <i class="bi bi-calendar"></i> <span class="px-1"> Add Appointement </span></button>
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
                            <th class="text-center">Edit</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appointment = mysqli_fetch_assoc($query3)) { ?>
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
                                <td>
                                    <?php
                                    // Convert dates and times to timestamps for comparison
                                    $appointmentDate = strtotime(date("Y-m-d", strtotime($appointment["appointment_date"])));
                                    $currentDate = strtotime(date("Y-m-d"));

                                    $appointmentTime = strtotime($appointment["appointment_time"]);
                                    $currentTime = strtotime(date("H:i:s"));

                                    // Future Date
                                    if ($appointmentDate > $currentDate) {
                                    ?>
                                        <a href="?aid=<?php echo $appointment["appoint_id"]; ?>" name="edit">
                                            <button type="button" class="btn" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal">
                                                <img src="../assets/images/edit.svg" alt="Edit" height="20">
                                            </button>
                                        </a>
                                    <?php
                                        // Today but Future Time
                                    } else if ($appointmentDate == $currentDate && $appointmentTime > $currentTime) {
                                    ?>
                                        <a href="?aid=<?php echo $appointment["appoint_id"]; ?>" name="edit">
                                            <button type="button" class="btn" id="editAppointmentBtn" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal">
                                                <img src="../assets/images/edit.svg" alt="Edit" height="20">
                                            </button>
                                        </a>
                                    <?php
                                        // Today but Time Passed OR Past Date
                                    } else {
                                    ?>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" disabled
                                            title="Editing Disabled - Time Passed">
                                            <i class="bi bi-lock-fill"></i>
                                        </button>
                                    <?php } ?>
                                </td>

                                <td class="text-center">
                                    <?php
                                    $appointmentDateTime = strtotime($appointment["appointment_date"] . ' ' . $appointment["appointment_time"]); // Combine date and time
                                    $currentDateTime = time(); // Get current timestamp

                                    $isDisabled = ($appointmentDateTime < $currentDateTime) ? "disabled" : ""; // Disable switch if time has passed
                                    ?>
                                    <form method="post" class="d-flex justify-content-center">
                                        <input type="hidden" name="appid" value="<?= $appointment["appoint_id"] ?>" />
                                        <div class="form-check form-switch">
                                            <!-- Increased switch size -->
                                            <input class="form-check-input fs-4 p-1 " type="checkbox"
                                                id="switch<?= $appointment["appoint_id"] ?>" onchange="this.form.submit()"
                                                name="status" <?php if ($appointment["status"] == "Approve") { ?> checked
                                                class="bg-success" <?php } ?> <?= $isDisabled ?>>
                                            <label class="form-check-label ms-1 fs-6"
                                                for="switch<?= $appointment["appoint_id"] ?>">
                                                <?php echo ($appointment["status"] == "Approve") ? '<span class="text-success">Approved</span>' : '<span class="text-danger">Cancel</span>'; ?>
                                            </label>
                                        </div>
                                    </form>
                                </td>

                                <td class="text-center">
                                    <?php
                                    $payment_id = $appointment["payment_id"];
                                    $query7 = mysqli_query($conn, "SELECT * FROM payments WHERE payment_id=$payment_id");
                                    $payments = mysqli_fetch_assoc($query7);
                                    if ($payments["status"] == "PAID") {
                                        echo '<div class="badge bg-success">Success</div>';
                                    } else {
                                        echo '<div class="badge bg-danger">Failed</div>';
                                    } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="my-1">Edit Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" onsubmit="return validationForm()">
                    <div class="modal-body">
                        <div class="form-group my-2">
                            <label for="patient_id">Patient Details</label>
                            <div class="input-group">
                                <span class="input-group-text">ID</span>
                                <input type="text" class="form-control" name="patient_id" id="patient_id" value="<?= $uppatient ?>" readonly>
                                <span class="input-group-text">Name</span>
                                <input type="text" class="form-control" value="<?= $row['patient_name'] ?>" readonly>
                            </div>
                        </div>

                        <div class="form-group my-2">
                            <label>Doctor Name</label>
                            <select class="form-select" id="doctor_id" name="doctor_id">
                                <option value="<?= $row1["doctor_id"] ?>" <?php if ($row1["doctor_id"] == $updoctor) { ?>
                                    selected <?php } ?>> <?= $row1["doctor_name"] ?> </option>
                            </select>
                            <span id="dval" style="color:red;display:none;"> * Select Doctor First </span>
                        </div>
                        <div class="form-group my-2">
                            <label for="dte">Appointment Date</label>
                            <input type="date" class="form-control" value="<?= $updt ?>" id="dte" name="ap_date">
                            <span id="dtval" style="color:red;display:none;"> * Date is Required </span>
                        </div>
                        <div class="form-group">
                            <label for="time">Available Time</label>
                            <select class="form-select" name="appointment_time" id="timeSlots">
                                <!-- <option value="">-- Select Time --</option> -->
                            </select>
                            <span id="ttval" style="color:red;display:none;"> * Time is Required </span>
                        </div>
                        <div class="form-group my-2">
                            <label for="reason">Reason</label>
                            <input id="reason" class="form-control" value="<?= $upreason ?>" name="reason">
                            <span id="rval" style="color:red;display:none;"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="close"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_appointment">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let doctorId = document.getElementById("doctor_id").value;
            let appointmentDate = document.getElementById("dte").value;
            let selectedTime = "<?= $uptime ?>";

            if (doctorId && appointmentDate) {
                loadTimeSlots(doctorId, appointmentDate, selectedTime);
            }
        });

        document.getElementById("doctor_id").addEventListener("change", function() {
            let doctorId = this.value;
            let appointmentDate = document.getElementById("dte").value;
            loadTimeSlots(doctorId, appointmentDate);
        });

        document.getElementById("dte").addEventListener("change", function() {
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

        // This is for datatable

        let table = new DataTable('#myTable', {
            paging: true,
            searching: true,
            ordering: true,
            scrollX: true,
            info: false,
            "columnDefs": [{
                "orderable": false,
                "targets": [6]
            }],
            responsive: true,
            // "fixedHeader": false,
            autoWidth: true,
            pageLength: 5,
            lengthChange: false,
            layout: {
                topStart: {
                    buttons: [{
                            extend: 'csv',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 7, 8],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 7) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Approve" : "Disapprove";
                                        }
                                        if (column === 8) { // Payment Column
                                            return node.textContent.trim(); // Extracts text (Success/Failed)
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 7, 8],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 7) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Approve" : "Disapprove";
                                        }
                                        if (column === 8) { // Payment Column
                                            return node.textContent.trim(); // Extracts text (Success/Failed)
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'pdf',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 7, 8],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 7) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Approve" : "Disapprove";
                                        }
                                        if (column === 8) { // Payment Column
                                            return node.textContent.trim(); // Extracts text (Success/Failed)
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 7, 8],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 7) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Approve" : "Disapprove";
                                        }
                                        if (column === 8) { // Payment Column
                                            return node.textContent.trim(); // Extracts text (Success/Failed)
                                        }
                                        return data;
                                    }
                                }
                            }
                        }
                    ]
                }
            }

        });

        // this is for showing edit model
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('aid')) {
                let editModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                editModal.show();
            }
        });
    </script>
</body>

</html>