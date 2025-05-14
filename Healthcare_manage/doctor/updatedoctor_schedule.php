<?php
session_start();
if (!isset($_SESSION["doctor"])) {
    header("location:../index.php");
}

include_once('../../config.php');
$conn = connection();
$duname = $_SESSION['doctor'];

$Nochange = false;
$alert = false;

$query2 = mysqli_query($conn, "SELECT * FROM doctors where username='$duname'");
$doctor = mysqli_fetch_assoc($query2);
$did = $doctor["doctor_id"];

$query3 = mysqli_query($conn, "SELECT * FROM doctor_schedule WHERE doctor_id=$did");
$schedule = mysqli_fetch_assoc($query3);

$available_days = isset($schedule['available_days']) ? json_decode($schedule['available_days'], true) : [];

if (isset($_REQUEST["save"])) {
    $schedule_id = $schedule["schedule_id"];
    $did = $schedule["doctor_id"];
    $days = isset($_REQUEST['available_days']) ? json_encode($_REQUEST['available_days']) : json_encode($schedule['available_days']);
    $from = isset($_REQUEST["available_from"]) ? $_REQUEST["available_from"] : $schedule["available_from"];
    $to = isset($_REQUEST["available_to"]) ? $_REQUEST["available_to"] : $schedule["available_to"];
    $duration = isset($_REQUEST["duration"]) ? $_REQUEST["duration"] : $schedule["duration"];

    if (
        json_encode($_REQUEST["available_days"]) == $schedule["available_days"]
        && $_REQUEST["available_from"] == $schedule["available_from"] 
        && $_REQUEST["available_to"] == $schedule["available_to"] 
        && $_REQUEST["duration"] == $schedule["appointment_duration"]
    ) {
        $Nochange = true;
    } else {
        $update = "UPDATE `doctor_schedule` SET available_days='$days',available_from='$from',available_to='$to',appointment_duration='$duration'
        WHERE schedule_id = $schedule_id";
        mysqli_query($conn, $update);
        $alert = true;
    }
    
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="./style/updatedoctor_schedule.css">
    <link rel="website icon" href="./../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function validationForm() {
            const availableDays = document.getElementsByName("available_days[]")[0];
            const availableFrom = document.getElementById("available_from").value.trim();
            const availableTo = document.getElementById("available_to").value.trim();
            const duration = document.getElementById("duration").value.trim();
            let isValid = true;

            // Validate Available Days
            if (availableDays.selectedOptions.length === 0) {
                document.getElementById("sval").style.display = "block";
                availableDays.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    document.getElementById("sval").style.display = "none";
                }, 1200);

                return false;
            }

            // Validate Available From
            if (!availableFrom) {
                document.getElementById("dtval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("dtval").style.display = "none";
                }, 1200);

                return false;
            }

            // Validate Available To
            if (!availableTo) {
                document.getElementById("eval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("eval").style.display = "none";
                }, 1200);

                return false;
            }

            // Ensure Available From is earlier than Available To
            if (availableFrom && availableTo && availableFrom >= availableTo) {
                alert("Available From time must be earlier than Available To time.");
                return false;
            }

            // Validate Appointment Duration
            if (!duration || duration < 5) {
                document.getElementById("cval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("cval").style.display = "none";
                }, 1200);

                return false;
            }

            return true;
        }
    </script>
</head>

<body>
    <?php
    include_once("./Navbar.php");
    include_once("./admin_header.php");
    ?>


    <?php if ($alert) { ?>
        <script>
            Swal.fire({
                title: "Updated!",
                text: "Changes saved successfully!",
                icon: "success"
            });
            setTimeout(() => {
                window.location.href = "viewdoctor_schedule.php";
            }, 1500);
        </script>
    <?php } ?>

    <?php if ($Nochange) { ?>
        <script>
            Swal.fire({
                text: "No Changes!",
                icon: "warning"
            });
            setTimeout(() => {
                window.location.href = "updatedoctor_schedule.php";
            }, 1500);
        </script>
    <?php } ?>

    <div class="content">
        <div class="header-list">
            <div class="left">
                <h4 class="fw-bold">Update Schedule</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Update Schedule </span></p>
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
                    <div class="form-group pt-3">
                        <label>Your Reference ID</label>
                        <input type="text" class="form-control shadow-sm text-primary fw-bold" id="pid" name="pid" value="<?= $schedule["schedule_id"] ?>" disabled>
                    </div>

                    <div class="form-group pt-3 pt-3">
                        <label>Doctor Name</label>
                        <input type="text" class="form-control shadow-sm" id="dname" name="dname" value="<?= $doctor["doctor_name"] ?>" disabled>
                    </div>

                    <div class="form-group pt-3 pt-3">
                        <label>Available Days</label>
                        <select name="available_days[]" class="form-select" size="7" multiple>
                            <?php
                            $week_days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

                            foreach ($week_days as $day) {
                                $selected = in_array($day, $available_days) ? "selected" : ""; // Ensure pre-selection
                                echo "<option value='$day' $selected>$day</option>";
                            }
                            ?>
                        </select>
                        <small class="text-muted">Hold <kbd>Ctrl</kbd> (Windows) or <kbd>Cmd</kbd> (Mac) to select multiple days.</small>
                        <span id="sval" style="color:red;display:none;"> * Specilization is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="dob">Available From</label>
                        <input type="time" class="form-control shadow-sm" id="available_from" name="available_from" value="<?= $schedule["available_from"] ?>">
                        <span id="dtval" style="color:red;display:none;"> * Available Time is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="email">Available To</label>
                        <input type="time" class="form-control shadow-sm" id="available_to" name="available_to" value="<?= $schedule["available_to"] ?>">
                        <span id="eval" style="color:red;display:none;"> * Available Time is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="phone">Appointment Duration</label>
                        <input type="number" class="form-control shadow-sm" id="duration" name="duration"
                            value="<?= isset($schedule["appointment_duration"]) ? $schedule["appointment_duration"] : 20 ?>"
                            min="5" step="5">
                        <span id="cval" style="color:red;display:none;"> * Appointment Duration is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <div class="btn-group" style="width:100%;">
                            <button type="submit" name="save">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>