<?php
session_start();
if (!isset($_SESSION["doctor"])) {
    header("location:../index.php");
}

include_once('../../config.php');
$conn = connection();
$duname = $_SESSION['doctor'];

$alert = false;

$query2 = mysqli_query($conn, "SELECT * FROM doctors WHERE username='$duname'");
$doctor = mysqli_fetch_assoc($query2);
$did = $doctor["doctor_id"];

if (isset($_REQUEST["save"])) {
    $leave_date = $_REQUEST["leave_date"];
    $leave_start = $_REQUEST["leave_start"];
    $leave_end = $_REQUEST["leave_end"];
    $reason = $_REQUEST["reason"];

    // Check if the selected leave date is within the doctor's schedule
    $checkScheduleQuery = "SELECT * FROM doctor_schedule WHERE doctor_id = '$did'";
    $scheduleResult = mysqli_query($conn, $checkScheduleQuery);

    $isOutOfSchedule = true; // Assume initially that the date is out of schedule
    while ($schedule = mysqli_fetch_assoc($scheduleResult)) {
        $availableDays = json_decode($schedule['available_days']); // Decode JSON stored days
        $selectedDay = date('l', strtotime($leave_date)); // Convert date to day (Monday, Tuesday, etc.)

        if (in_array($selectedDay, $availableDays)) {
            $isOutOfSchedule = false; // Doctor is available on this day
            break;
        }
    }

    if ($isOutOfSchedule) {
        // If the doctor is not available on the selected day, show an error alert
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
                title: 'Error!',
                text: 'You are not Scheduled on that day.',
                icon: 'error'
            }).then(() => {
                window.location.href = 'AddLeave.php';
            });
        </script>
    </body>
    </html>";
        exit;
    }


    // Check if any appointment is booked on that date between the leave start and end times
    $checkQuery = "SELECT * FROM appointments 
                   WHERE doctor_id = '$did' 
                     AND appointment_date = '$leave_date' 
                     AND appointment_time BETWEEN '$leave_start' AND '$leave_end' 
                     AND `status` = 'Approve'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Appointment already exists during the requested leave time.
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
                title: 'Error!',
                text: 'Cannot apply leave. There is already an appointment booked during the selected time period.',
                icon: 'error'
            }).then(() => {
                window.location.href = 'AddLeave.php';
            });
           </script>
        </body>
        </html>";
        exit;
    } else {
        // No appointment booked during the leave period, so insert the leave request.
        $insert = "INSERT INTO doctor_leave (doctor_id, leave_date, leave_start, leave_end, reason) 
                   VALUES ('$did', '$leave_date', '$leave_start', '$leave_end', '$reason')";
        mysqli_query($conn, $insert);
        $alert = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Leave</title>
    <link rel="stylesheet" href="./style/AddLeave.css">
    <link rel="website icon" href="./../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split("T")[0]; // Get today's date in YYYY-MM-DD format
            document.getElementById('leave_date').setAttribute('min', today);
        });

        function validationForm() {
            let isValid = true;

            const leaveDate = document.getElementById("leave_date").value.trim();
            const leaveStart = document.getElementById("leave_start").value.trim();
            const leaveEnd = document.getElementById("leave_end").value.trim();
            const reason = document.getElementById("reason").value.trim();

            // Hide all previous errors
            document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');

            if (!leaveDate) {
                document.getElementById("dateError").style.display = "block";
                isValid = false;
            }
            if (!leaveStart) {
                document.getElementById("startError").style.display = "block";
                isValid = false;
            }
            if (!leaveEnd) {
                document.getElementById("endError").style.display = "block";
                isValid = false;
            }
            if (leaveStart && leaveEnd) {
                const startTime = new Date(`1970-01-01T${leaveStart}:00`);
                const endTime = new Date(`1970-01-01T${leaveEnd}:00`);
                const diffMinutes = (endTime - startTime) / (1000 * 60);

                if (startTime >= endTime) {
                    document.getElementById("timeError").textContent = "* Start time must be before end time";
                    document.getElementById("timeError").style.display = "block";
                    isValid = false;
                } else if (diffMinutes < 15) {
                    document.getElementById("timeError").textContent = "* Leave duration must be at least 15 minutes";
                    document.getElementById("timeError").style.display = "block";
                    isValid = false;
                }
            }
            if (leaveStart && leaveEnd && leaveStart >= leaveEnd) {
                document.getElementById("timeError").style.display = "block";
                isValid = false;
            }
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

    <?php if ($alert) { ?>
        <script>
            Swal.fire({
                title: "Success!",
                text: "Leave Applied Successfully!",
                icon: "success"
            });
            setTimeout(() => {
                window.location.href = "ViewLeaves.php";
            }, 1500);
        </script>
    <?php } ?>

    <div class="content">
        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Apply for Leave</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php">HealthCare</a> > <span>Apply Leave</span></p>
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
                <form onsubmit="return validationForm()" method="post">
                    <div class="form-group">
                        <label>Doctor Name</label>
                        <input type="text" class="form-control" value="<?= $doctor["doctor_name"] ?>" disabled>
                    </div>

                    <div class="form-group pt-3">
                        <label>Leave Date</label>
                        <input type="date" class="form-control" id="leave_date" name="leave_date">
                        <span id="dateError" class="error-message text-danger" style="display:none;">* Leave date is required</span>
                    </div>

                    <div class="form-group pt-3">
                        <label>Leave Start Time</label>
                        <input type="time" class="form-control" id="leave_start" name="leave_start">
                        <span id="startError" class="error-message text-danger" style="display:none;">* Start time is required</span>
                    </div>

                    <div class="form-group pt-3">
                        <label>Leave End Time</label>
                        <input type="time" class="form-control" id="leave_end" name="leave_end">
                        <span id="endError" class="error-message text-danger" style="display:none;">* End time is required</span>
                        <span id="timeError" class="error-message text-danger" style="display:none;">* Start time must be before end time</span>
                    </div>

                    <div class="form-group pt-3">
                        <label>Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                        <span id="reasonError" class="error-message text-danger" style="display:none;">* Reason is required</span>
                    </div>

                    <div class="form-group pt-3">
                        <button type="submit" name="save" class="btn btn-primary">Apply Leave</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>