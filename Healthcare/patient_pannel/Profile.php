<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: Dashboard.php");
    exit();
}
include_once("../../config.php");
$conn = connection();
$puname = $_SESSION['user'];
$Nochange = false;
$alert = false;
$exits = false;
$redirect = false;

$query2 = mysqli_query($conn, "SELECT * FROM patients where username='$puname'");
$patient = mysqli_fetch_assoc($query2);

// calculating current age
$dob = $patient["dob"];
$dobObject = new DateTime($dob);
$currentDate = new DateTime();
$age = $dobObject->diff($currentDate)->y;

if (isset($_REQUEST["save"])) {
    $pid = $patient["patient_id"];
    $newUsername = $_REQUEST["username"];

    // Check if the username is changed and exists in the database
    $checkUsernameQuery = mysqli_query($conn, "SELECT * FROM patients WHERE username='$newUsername' AND patient_id != $pid");

    if (mysqli_num_rows($checkUsernameQuery) > 0) {
        $exits = true; // Username already taken
    } else {
        $pid = $patient["patient_id"];
        $pname = isset($_REQUEST["pname"]) ? $_REQUEST["pname"] : $patient["fname"];
        $dob = isset($_REQUEST["date"]) ? $_REQUEST["date"] : $patient["dob"];
        $gender = isset($_REQUEST["gender"]) ? $_REQUEST["gender"] : $patient["gender"];
        $email = isset($_REQUEST["email"]) ? $_REQUEST["email"] : $patient["email"];
        $phone = isset($_REQUEST["phone"]) ? $_REQUEST["phone"] : $patient["contact"];
        $username = isset($_REQUEST["username"]) ? $_REQUEST["username"] : $patient["username"];
        $password = $patient["password"];

        if (!empty($_REQUEST["password"])) {
            $newPassword = $_REQUEST["password"];
            if (strlen($newPassword) < 6) {
                echo "<script>alert('Password must be at least 6 characters long');</script>";
            } else {
                $password = hash("sha256", $newPassword);
            }
        }

        $update = "UPDATE `patients` SET patient_name='$pname',dob='$dob',gender='$gender', email='$email', 
        contact='$phone', username='$username', `password`='$password' WHERE patient_id = $pid";
        mysqli_query($conn, $update);

        if ($newUsername !== $patient["username"] || $password !== $patient["password"]) {
            $redirect = true;
        }

        if (
            $_REQUEST["pname"] == $patient["patient_name"] && $_REQUEST["date"] == $patient["dob"] && $_REQUEST["gender"] == $patient["gender"]
            && $_REQUEST["email"] == $patient["email"] && $_REQUEST["phone"] == $patient["contact"] && $_REQUEST["username"] == $patient["username"] &&
            $password == $patient["password"]
        ) {
            $Nochange = true;
        }
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
    <link rel="stylesheet" href="./style/Profile.css">
    <link rel="website icon" href="./image/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <script>
        function validationForm() {
            const patientName = document.getElementById('pname').value.trim();
            const dt = document.getElementById('date').value;
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!patientName) {
                document.getElementById("pval").style.display = "block";
                document.getElementById("pval").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    document.getElementById("pval").style.display = "none";
                }, 1200);

                return false;
            } else if (!dt) {
                document.getElementById("dtval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("dtval").style.display = "none";
                }, 1200);

                return false;
            } else if (!email) {
                document.getElementById("eval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("eval").style.display = "none";
                }, 1200);

                return false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById("eval").style.display = "block";
                document.getElementById("eval").innerHTML = "Invalid Email Format";

                setTimeout(() => {
                    document.getElementById("eval").style.display = "none";
                }, 1200);

                return false;
            }  else if (!phone) {
                document.getElementById("cval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("cval").style.display = "none";
                }, 1200);

                return false;
            } else if (phone.length < 10) {
                document.getElementById("cval").style.display = "block";
                document.getElementById("cval").innerHTML = "Phone NO. must be in 10 digits";

                setTimeout(() => {
                    document.getElementById("cval").style.display = "none";
                }, 1200);

                return false;
            } else if (!username) {
                document.getElementById("uval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("uval").style.display = "none";
                }, 1200);

                return false;
            } else if (!password) {
                document.getElementById("psval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("psval").style.display = "none";
                }, 1200);

                return false;
            }
            if (password.length < 6) {
                document.getElementById("plval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("plval").style.display = "none";
                }, 1200);

                return false;
            } else {
                return true;
            }

        }
    </script>
</head>

<body>
    <?php include_once("./Navbar.php"); ?>

    <?php include_once("./admin_header.php"); ?>

    <?php if ($exits) { ?>
        <script>
            Swal.fire({
                title: "Error!",
                text: "Username Already Exists !",
                icon: "error"
            });
        </script>
    <?php } ?>

    <?php if ($alert) { ?>
        <script>
            Swal.fire({
                title: "Updated!",
                text: "Changes saved successfully!",
                icon: "success"
            });
        </script>
    <?php } ?>

    <?php if ($Nochange) { ?>
        <script>
            Swal.fire({
                text: "No Changes!",
                icon: "warning"
            });
        </script>
    <?php } ?>

    <?php if ($redirect) { ?>
        <script>
            Swal.fire({
                title: "Updated!",
                text: "Credentials changed successfully! Please log in again!",
                icon: "success"
            })

            setTimeout(() => {
                window.location.href = "../login.php";
            }, 1500);
        </script>
    <?php unset($_SESSION["user"]); // Destroy session only after the alert is shown 
    } ?>

    <div class="content">
        <div class="header-list">
            <div class="left">
                <h4 class="fw-bold">Profile Settings</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Profile </span></p>
            </div>
            <div class="right">

            </div>
        </div>

        <div class="container">
            <?php if ($patient["status"] == 'Active') { ?>
                <span class="badge bg-success p-2 fs-6 mb-2 mx-3 rounded-pill d-inline-flex align-items-center"
                    data-bs-toggle="tooltip" title="Your query has been reviewed and addressed by the healthcare team">
                    <i class="bi bi-check-circle-fill me-2"></i> Activated
                </span>
            <?php } ?>
            <div class="form-container">
                <form onsubmit="return validationForm()" method="post">
                    <div class="form-group pt-3">
                        <label>Your Reference ID </label>
                        <input type="text" class="form-control text-primary fw-bold" id="pid" name="pid"
                            value="<?= $patient["patient_id"] ?>" disabled>
                    </div>

                    <div class="form-group pt-3 pt-3">
                        <label>Your Full Name</label>
                        <input type="text" class="form-control shadow-sm" id="pname" name="pname"
                            value="<?= $patient["patient_name"] ?>" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').replace(/\s+/g, ' ')">
                        <span id="pval" style="color:red;display:none;"> * Patient Name is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="dob">Your Birth Date</label>
                        <input type="date" class="form-control shadow-sm" id="date" name="date"
                            value="<?= $patient["dob"] ?>">
                        <span id="dtval" style="color:red;display:none;"> * Date is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="age">Your Age</label>
                        <input type="number" class="form-control shadow-sm" id="age" name="age" value="<?= $age ?>"
                            disabled>
                    </div>

                    <div class="form-group pt-3">
                        <label for="gender">Your Gender</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender" value="Male" <?php if ($patient["gender"] == "Male") { ?> checked <?php } ?>>
                            <label class="form-check-label" for="inlineRadio1">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender" value="Female" <?php if ($patient["gender"] == "Female") { ?> checked <?php } ?>>
                            <label class="form-check-label" for="inlineRadio2">Female</label>
                        </div>
                    </div>

                    <div class="form-group pt-3">
                        <label for="email">Your Email</label>
                        <input type="email" class="form-control shadow-sm" id="email" name="email"
                            value="<?= $patient["email"] ?>">
                        <span id="eval" style="color:red;display:none;"> * Email is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="phone">Your Phone</label>
                        <input type="tel" class="form-control shadow-sm" id="phone" name="phone" maxlength="10"
                            value="<?= $patient["contact"] ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <span id="cval" style="color:red;display:none;"> * Phone is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="bio">Username</label>
                        <input type="text" class="form-control shadow-sm" id="username" name="username"
                            value="<?= $patient["username"] ?>" oninput="this.value = this.value.replace(/\s/g, '');">
                        <span id="uval" style="color:red;display:none;"> * Username is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="bio">Enter New Password</label>
                        <input type="password" class="form-control shadow-sm" id="password" name="password" placeholder="Old Password : <?php if (isset($_SESSION["pass"])) {
                                                                                                                                            echo $_SESSION["pass"];
                                                                                                                                        } ?>" />
                        <!-- <span id="psval" style="color:red;display:none;"> * Password is Required </span> -->
                        <span id="plval" style="color:red;display:none;"> * Password Must be 6 digits long </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="tmp">Account Registered at</label>
                        <input class="form-control shadow-sm" id="tmp" name="tmp" value="<?= date("d M Y, h:i A", strtotime($patient["create_at"])); ?>" disabled>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split("T")[0]; // Get today's date in YYYY-MM-DD format
            let dateInput = document.getElementById("date");
            if (dateInput) {
                dateInput.setAttribute("max", today);
            }
        });
    </script>
</body>

</html>