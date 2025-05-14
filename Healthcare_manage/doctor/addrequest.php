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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Leave</title>
    <link rel="stylesheet" href="./style/addrequest.css">
    <link rel="website icon" href="./../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split("T")[0]; // Get today's date in YYYY-MM-DD format
            document.getElementById('leave_date').setAttribute('min', today);
        });

        function validationForm() {
            let isValid = true;

            const patientName = document.getElementById("patient_name").value;
            const appointmentDate = document.getElementById("dt").value;
            const appointmentTime = document.getElementById("timeSlots").value;
            const reason = document.getElementById("reason").value.trim();

            // Hide all previous errors
            document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
            // Validation for Patient Name
            if (!patientName || patientName === "Registered Patients") {
                document.getElementById("pval").style.display = "block";
                isValid = false;
            }

            // Validation for Appointment Date
            if (!appointmentDate) {
                document.getElementById("dateError").style.display = "block";
                isValid = false;
            }

            // Validation for Time Slot
            if (!appointmentTime || appointmentTime === "select") {
                document.getElementById("timerror").style.display = "block";
                isValid = false;
            }

            // Validation for Reason
            if (!reason) {
                document.getElementById("reasonError").style.display = "block";
                isValid = false;
            }
            // Hide errors after 1.2 seconds
            setTimeout(() => {
                document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
            }, 1500);

            return isValid;
        }
    </script>
</head>

<body>
    <?php include_once("./Navbar.php");
    include_once("./admin_header.php"); ?> 

    <div class="content">
        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Apply for Appointment</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php">HealthCare</a> > <span>Add Request</span></p>
            </div>
        </div>

        <div class="profile-header">
            <div class="profile-content">
                <div class="profile-image">
                    <img src="./../assets/doctorphotos/<?php echo $doctor["photo"] ?>" alt="Admin">
                </div>
                <div class="profile-details">
                    <h2><?php echo $doctor["doctor_name"] ?></h2>
                    <p><?php echo $doctor["specilization"] ?></p>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="form-container">
                <form action="./TaskAlerts.php" onsubmit="return validationForm()" method="post">
                    <div class="form-group">
                        <label>Doctor Name</label>
                        <input type="text" class="form-control" value="<?= $doctor["doctor_name"] ?>" disabled>
                    </div>

                    <div class="form-group pt-3">
                        <label>Patient Name</label>
                        <select class="form-select" name="patient_name" id="patient_name">
                            <option>Registered Patients</option>
                            <?php
                            $st = mysqli_query($conn, "SELECT * FROM patients where `status`='Active'");
                            while ($row = mysqli_fetch_assoc($st)) {
                            ?>
                                <option value="<?= $row["patient_id"] ?>">(<?= $row["patient_id"] ?>) <?= $row["patient_name"] ?></option>
                            <?php } ?>
                        </select>
                        <span id="pval" class="error-message text-danger" style="display:none;"> * Patient Name is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="dt">Appointment Date</label>
                        <input type="date" id="dt" name="dt" class="form-control">
                        <span id="dateError" class="error-message text-danger" style="display:none;">* Date is required</span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="time">Available Time</label>
                        <select class="form-select" name="appointment_time" id="timeSlots">
                            <option value="select">-- Select Time --</option>
                        </select>
                        <span id="timerror" class="error-message text-danger" style="display:none;">* Time is Required</span>
                    </div>

                    <div class="form-group pt-3">
                        <label>Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                        <span id="reasonError" class="error-message text-danger" style="display:none;">* Reason is required</span>
                    </div>

                    <div class="form-group pt-3">
                        <button type="submit" name="save" class="btn btn-primary">Add Request</button>
                    </div>
                </forma>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            loadTimeSlots(); // Load available slots for pre-selected doctor
        });

        function loadTimeSlots() {
            let doctorId = <?= json_encode($did); ?>;
            let appointmentDate = document.getElementById("dt").value;

            if (!appointmentDate) return; // Prevent API call if no date is selected

            fetch(`get_available_slots.php?doctor_id=${doctorId}&appointment_date=${appointmentDate}`)
                .then(response => response.json())
                .then(data => {
                    let timeSelect = document.getElementById("timeSlots");
                    timeSelect.innerHTML = '<option value="select">-- Select Time --</option>';

                    if (data.error) {
                        Swal.fire({
                            title: "Unavailable",
                            text: data.error,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                        return; // Stop further execution
                    }

                    if (data.length === 0) {
                        Swal.fire({
                            title: "Fully Booked",
                            text: "No available slots for this date. Please choose another date.",
                            icon: "warning",
                            confirmButtonText: "OK"
                        });
                        return;
                    }

                    data.forEach(time => {
                        let option = document.createElement("option");
                        option.value = time;
                        option.textContent = time;
                        timeSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        document.getElementById("dt").addEventListener("change", loadTimeSlots);



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