<?php
session_start();
if (!isset($_SESSION['receptionist'])) {
    header("location:../index.php");
}

include_once('../../config.php');
include_once("../../mail_helper.php");

$conn = connection();
$insert = false;
$failed = false;

// Get appointment ID from URL
$task_id = $_GET["task_id"] ?? "";

// Initialize variables
$prefilled_patient_id = "";
$prefilled_doctor_id = "";
$prefilled_dt = "";
$prefilled_time = "";
$prefilled_reason = "";

if (!empty($task_id)) {

    $_SESSION["task"] = $task_id;
    // Fetch appointment details
    $stmt = $conn->prepare("SELECT dr.*, p.patient_name, d.doctor_name 
                            FROM doctor_appointment_requests dr
                            JOIN patients p ON dr.patient_id = p.patient_id
                              JOIN doctors d ON dr.doctor_id = d.doctor_id
                            WHERE dr.id = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $prefilled_patient_id = $row["patient_id"];
        $prefilled_doctor_id = $row["doctor_id"];
        $prefilled_dt = $row["suggested_date"];
        $prefilled_time = $row["suggested_time"];
        $prefilled_reason = $row["reason"];
    }
    $stmt->close();
}

if (isset($_SESSION['task']) && isset($_GET['order_id'])) {
    $order_id = $_REQUEST["order_id"];
    $fetch_paymentid = mysqli_query($conn, "SELECT * FROM payments WHERE order_id='$order_id'");
    $payments = mysqli_fetch_assoc($fetch_paymentid);

    $task_num = $_SESSION['task'] ?? null;
    $patient = $_SESSION['patient_id'] ?? null;
    $doctor = $_SESSION['doctor_id'] ?? null;
    $pay_id = $payments["payment_id"];
    $dt = $_SESSION['dt'] ?? null;
    $time = $_SESSION['time'] ?? null;
    $reason = $_SESSION['reason'] ?? null;

    if ($task_num) {
        $updateQuery = "UPDATE doctor_appointment_requests SET `status` = 'Completed' WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $task_num);

        if ($updateStmt->execute()) {
            unset($_SESSION['task']);
        }

        if ($patient && $doctor && $dt && $reason && $pay_id) {
            $query = "INSERT INTO appointments (patient_id,doctor_id,payment_id,appointment_date,appointment_time,reason) VALUES ($patient,$doctor,$pay_id,'$dt','$time','$reason')";
            mysqli_query($conn, $query);

            $pat = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $patient");
            $allpat = mysqli_fetch_assoc($pat);

            $doc = mysqli_query($conn, "SELECT * FROM doctors WHERE doctor_id = $doctor");
            $alldoc = mysqli_fetch_assoc($doc);

            $email = $allpat["email"];
            $subject = " Appointment Confirmation -- [" . $dt . "] at [" . $time . "]";
            $msg = "Dear " . $allpat["patient_name"] . ",
    
Thank you for scheduling your appointment with Healthcare. We are pleased to confirm that your appointment has been successfully booked.

Appointment Details

    Patient Name: " . $allpat["patient_name"] . "

    Doctor: " . $alldoc["doctor_name"] . "

    Date: " . $dt . "

    Time: " . $time . "

    Reason for Visit: " . $reason . "

    Username: " . $allpat["username"] . "

    Payment Status: Paid

If you need to reschedule or have any questions, please feel free to contact us or reply to this email.

We appreciate your trust in us and look forward to providing you with excellent care.

Best Regards,
Team HealthCare
📧 teamhealthcarehospital@gmail.com
📞 +0261 250 5050
🌐 https://www.HealthCare.com";

            $result3 = sendEmail($email, $subject, $msg);
            if ($result3 === true) {
                $insert = true;
                unset($_SESSION['patient_id'], $_SESSION['doctor_id'], $_SESSION['dt'], $_SESSION['time'], $_SESSION['reason']);
            } else {
                echo "<pre>Mail Error: ";
                print_r($result);
                echo "</pre>";
            }
        }
    }
}


if (isset($_REQUEST["order_id"])) {
    $order_id = $_REQUEST["order_id"];
    $fetch_paymentid = mysqli_query($conn, "SELECT * FROM payments WHERE order_id='$order_id'");
    $payments = mysqli_fetch_assoc($fetch_paymentid);

    $patient = $_SESSION['patient_id'] ?? null;
    $doctor = $_SESSION['doctor_id'] ?? null;
    $pay_id = $payments["payment_id"];
    $dt = $_SESSION['dt'] ?? null;
    $time = $_SESSION['time'] ?? null;
    $reason = $_SESSION['reason'] ?? null;

    if ($patient && $doctor && $dt && $reason && $pay_id) {
        $query = "INSERT INTO appointments (patient_id,doctor_id,payment_id,appointment_date,appointment_time,reason) VALUES ($patient,$doctor,$pay_id,'$dt','$time','$reason')";
        mysqli_query($conn, $query);

        $pat = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $patient");
        $allpat = mysqli_fetch_assoc($pat);

        $doc = mysqli_query($conn, "SELECT * FROM doctors WHERE doctor_id = $doctor");
        $alldoc = mysqli_fetch_assoc($doc);

        $email = $allpat["email"];
        $subject = " Appointment Confirmation -- [" . $dt . "] at [" . $time . "]";
        $msg = "Dear " . $allpat["patient_name"] . ",

 
Thank you for scheduling your appointment with Healthcare. We are pleased to confirm that your appointment has been successfully booked.

Appointment Details

    Patient Name: " . $allpat["patient_name"] . "

    Doctor: " . $alldoc["doctor_name"] . "

    Date: " . $dt . "

    Time: " . $time . "

    Reason for Visit: " . $reason . "

    Username: " . $allpat["username"] . "

    Payment Status: Paid

If you need to reschedule or have any questions, please feel free to contact us or reply to this email.

We appreciate your trust in us and look forward to providing you with excellent care.

Best Regards,
Team HealthCare
📧 teamhealthcarehospital@gmail.com
📞 +0261 250 5050
🌐 https://www.HealthCare.com";

        $result2 = sendEmail($email, $subject, $msg);
        if ($result2 === true) {
            $insert = true;
            unset($_SESSION['patient_id'], $_SESSION['doctor_id'], $_SESSION['dt'], $_SESSION['time'], $_SESSION['reason']);
        } else {
            echo "<pre>Mail Error: ";
            print_r($result);
            echo "</pre>";
        }
    }
}

if (isset($_REQUEST["paymentfailed"])) {
    $failed = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment</title>
    <link rel="stylesheet" href="./style/AddAppointment.css">
    <link rel="website icon" href="./../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script>
        function validationForm() {
            let isValid = true;

            const patientName = document.getElementById('patient_name').value.trim();
            const doctorName = document.getElementById('doctor_name').value.trim();
            const dt = document.getElementById('dt').value;
            const reason = document.getElementById('reason').value.trim();
            const timeSlot = document.getElementById("timeSlots").value;

            // Validate Patient Name
            if (patientName == "Registered Patients") {
                document.getElementById("pval").style.display = "block";
                setTimeout(() => document.getElementById("pval").style.display = "none", 1200);
                isValid = false;
            }

            // Validate Doctor Name
            if (doctorName == "Select Active Doctors") {
                document.getElementById("dval").style.display = "block";
                setTimeout(() => document.getElementById("dval").style.display = "none", 1200);
                isValid = false;
            }

            // Validate Date
            if (!dt) {
                document.getElementById("dtval").style.display = "block";
                setTimeout(() => document.getElementById("dtval").style.display = "none", 1200);
                isValid = false;
            }

            // Validate Available Time
            if (timeSlot === "" || timeSlot === "select") {
                document.getElementById("timeval").style.display = "block";
                setTimeout(() => document.getElementById("timeval").style.display = "none", 1200);
                isValid = false;
            }

            // Validate Reason
            if (!reason) {
                document.getElementById("rval").style.display = "block";
                setTimeout(() => document.getElementById("rval").style.display = "none", 1200);
                isValid = false;
            }

            return isValid; // Prevent form submission if any validation fails
        }
    </script>
</head>

<body>
    <?php
    include_once("./Navbar.php");
    include_once("./admin_header.php");
    ?>

    <?php if ($insert) { ?>
        <script>
            Swal.fire({
                title: "Success!",
                text: "Appointment add successfully!",
                icon: "success"
            });

            setTimeout(() => {
                window.location.href = "ViewAppointment.php";
            }, 2000);
        </script>
    <?php } ?>

    <?php if ($failed) { ?>
        <script>
            Swal.fire({
                title: "Payment Failed !",
                text: "Appointment can only placed after successfull payment",
                icon: "error"
            });

            setTimeout(() => {
                window.location.href = "AddAppointment.php";
            }, 4000);
        </script>
    <?php } ?>

    <div class="content">

        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Add New Appointment</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <a
                        href="./ViewAppointment.php"> Appointments </a> > <span> Add Appointment </span></p>
            </div>
        </div>

        <div class="container">
            <div class="form-container">
                <form action="cashfree_payment/pay.php" onsubmit="return validationForm()" method="post">
                    <div class="form-group">
                        <label>Patient Name</label>
                        <select name="patient_name" id="patient_name">
                            <option>Registered Patients</option>
                            <?php
                            $st = mysqli_query($conn, "SELECT * FROM patients where `status`='Active'");
                            while ($row = mysqli_fetch_assoc($st)) {
                                $selected = ($row["patient_id"] == $prefilled_patient_id) ? "selected" : "";
                                echo "<option value='{$row["patient_id"]}' $selected>({$row["patient_id"]}) {$row["patient_name"]} </option>";
                            }
                            ?>
                        </select>
                        <span id="pval" style="color:red;display:none;"> * Patient Name is Required </span>
                    </div>
                    <div class="form-group">
                        <label>Doctor Name</label>
                        <select id="doctor_name" name="doctor_name">
                            <option>Select Active Doctors</option>
                            <?php
                            $st1 = mysqli_query($conn, "SELECT * FROM doctors WHERE `status` = 'Active'");
                            while ($row1 = mysqli_fetch_assoc($st1)) {
                                $selected = ($row1["doctor_id"] == $prefilled_doctor_id) ? "selected" : "";
                                echo "<option value='{$row1["doctor_id"]}' data-fee='{$row1["consultancy_fee"]}' $selected>
                {$row1["doctor_name"]} ( {$row1["specilization"]} )
              </option>";
                            }
                            ?>
                        </select>
                        <span id="dval" style="color:red;display:none;"> * Select Active Doctor First </span>
                    </div>
                    <div class="form-group">
                        <label for="dt">Appointment Date</label>
                        <input class="form-control" type="date" id="dt" name="dt" value="<?= $prefilled_dt ?>">
                        <span id="dtval" style="color:red;display:none;"> * Date is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="time">Available Time</label>
                        <select class="form-select" name="appointment_time" id="timeSlots">
                            <option value="<?= $prefilled_time ?>" selected><?= $prefilled_time ?: '-- Select Time --' ?></option>
                        </select>
                        <span id="timeval" style="color:red;display:none;"> * Time is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason</label>
                        <textarea id="reason" name="reason"><?= $prefilled_reason ?></textarea>
                        <span id="rval" style="color:red;display:none;"> * Reason Required </span>
                    </div>
                    <!-- Section to Display Consultancy Fee -->
                    <div class="form-group">
                        <label>Consultancy Fee</label>
                        <input type="text" id="consultancy_fee" name="consultancy_fee" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <div class="btn-group" style="width:100%;">
                            <button type="submit" name="addAppointment"> Submit </button>
                            <button type="reset" id="reset"> Reset </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", setMinDate);

        function setMinDate() {
            let dtInput = document.getElementById("dt");
            let now = new Date();

            let year = now.getFullYear();
            let month = String(now.getMonth() + 1).padStart(2, '0');
            let day = String(now.getDate()).padStart(2, '0');

            let minDate = `${year}-${month}-${day}`; // Format as YYYY-MM-DD
            dtInput.setAttribute("min", minDate); // Set the min attribute
        }
        document.addEventListener("DOMContentLoaded", function() {
            let doctorDropdown = document.getElementById("doctor_name");
            let selectedDoctor = doctorDropdown.options[doctorDropdown.selectedIndex];
            let fee = selectedDoctor.getAttribute("data-fee") || "N/A";
            document.getElementById("consultancy_fee").value = fee;
        });
        document.getElementById("doctor_name").addEventListener("change", function() {
            let selectedDoctor = this.options[this.selectedIndex];
            let fee = selectedDoctor.getAttribute("data-fee") || "N/A"; // Get fee or show "N/A"
            document.getElementById("consultancy_fee").value = fee;
        });
        document.getElementById("doctor_name").addEventListener("change", loadTimeSlots);
        document.getElementById("dt").addEventListener("change", loadTimeSlots);

        function loadTimeSlots() {
            let doctorId = document.getElementById("doctor_name").value;
            let appointmentDate = document.getElementById("dt").value;

            if (doctorId && appointmentDate) {
                fetch(`get_available_slots.php?doctor_id=${doctorId}&appointment_date=${appointmentDate}`)
                    .then(response => response.json())
                    .then(data => {
                        let timeSelect = document.getElementById("timeSlots");
                        timeSelect.innerHTML = '<option value="">-- Select Time --</option>';

                        if (data.error) {
                            alert(data.error);
                        } else {
                            data.forEach(time => {
                                let option = document.createElement("option");
                                option.value = time;
                                option.textContent = time;
                                timeSelect.appendChild(option);
                            });

                            if (data.length === 0) {
                                Swal.fire({
                                    title: "Available Time not found",
                                    text: ` Please select another date.`,
                                    icon: "warning",
                                    confirmButtonText: "Understand"
                                });
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Get today's date
            let today = new Date();

            // Set the maximum allowed date (exactly 1 month from today)
            let maxAllowedDate = new Date(today);
            maxAllowedDate.setMonth(today.getMonth() + 1); // Add 1 month

            // Convert dates to the required format (YYYY-MM-DD)
            let minDateStr = today.toISOString().split("T")[0];
            let maxDateStr = maxAllowedDate.toISOString().split("T")[0];

            // Format maxAllowedDate to DD/MM/YYYY for the alert
            let formattedMaxDate = maxAllowedDate.toLocaleDateString("en-GB", {
                day: "2-digit",
                month: "2-digit",
                year: "numeric"
            });

            // Set the minimum selectable date to today
            let dateInput = document.getElementById("dt");
            dateInput.setAttribute("min", minDateStr);

            // Add event listener to validate the selected date
            dateInput.addEventListener("change", function() {
                let selectedDate = new Date(dateInput.value);

                // Check if the selected date is beyond the allowed range
                if (selectedDate > maxAllowedDate) {
                    Swal.fire({
                        title: "Invalid Date Selection",
                        text: `Appointments can only be scheduled up to ${formattedMaxDate}. Please select a valid date within this range.`,
                        icon: "warning",
                        confirmButtonText: "Got it"
                    });

                    // Reset the date input field
                    dateInput.value = "";
                }
            });
        });
    </script>
</body>

</html>