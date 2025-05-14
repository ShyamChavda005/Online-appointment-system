<?php
session_start();
if (!isset($_SESSION['receptionist'])) {
    header("location:../index.php");
}
include_once('../../config.php');
$conn = connection();
$runame = $_SESSION['receptionist'];

$Nochange = false;
$alert = false;
$exits = false;
$redirect = false;

$query2 = mysqli_query($conn, "SELECT * FROM receptionist where username='$runame'");
$receptionist = mysqli_fetch_assoc($query2);
$rid = $receptionist["rid"];

if (isset($_REQUEST["save"])) {
    $rid = $receptionist["rid"];
    $newUsername = $_REQUEST["username"];

    // Check if the username is changed and exists in the database
    $checkUsernameQuery = mysqli_query($conn, "SELECT * FROM receptionist WHERE username='$newUsername' AND rid != $rid");

    if (mysqli_num_rows($checkUsernameQuery) > 0) {
        $exits = true; // Username already taken
    } else {
        $rid = $receptionist["rid"];
        $rname = isset($_REQUEST["receptionist_name"]) ? $_REQUEST["receptionist_name"] : $receptionist["name"];
        $dob = isset($_REQUEST["dt"]) ? $_REQUEST["dt"] : $receptionist["dob"];
        $gender = isset($_REQUEST["gender"]) ? $_REQUEST["gender"] : $receptionist["gender"];
        $email = isset($_REQUEST["email"]) ? $_REQUEST["email"] : $receptionist["email"];
        $phone = isset($_REQUEST["phone"]) ? $_REQUEST["phone"] : $receptionist["contact"];
        $username = isset($_REQUEST["username"]) ? $_REQUEST["username"] : $receptionist["username"];
        $password = $receptionist["password"];

        if (!empty($_REQUEST["password"])) {
            $newPassword = $_REQUEST["password"];
            if (strlen($newPassword) < 6) {
                echo "<script>alert('Password must be at least 6 characters long');</script>";
            } else {
                $password = hash("sha256", $newPassword);
            }
        }

        $update = "UPDATE receptionist SET `name`='$rname',dob='$dob',gender='$gender', email='$email', 
        contact='$phone', username='$newUsername', `password`='$password' WHERE rid = $rid";
        mysqli_query($conn, $update);

        if ($newUsername !== $receptionist["username"] || $password !== $receptionist["password"]) {
            $redirect = true;
        }

        if (
            $_REQUEST["receptionist_name"] == $receptionist["name"] && $_REQUEST["dt"] == $receptionist["dob"] && $_REQUEST["gender"] == $receptionist["gender"]
            && $_REQUEST["email"] == $receptionist["email"] && $_REQUEST["phone"] == $receptionist["contact"] && $_REQUEST["username"] == $receptionist["username"] &&
            $password == $receptionist["password"]
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
            document.getElementById('dt').setAttribute('max', today);
        });

        function validationForm() {
            const receptionist = document.getElementById('receptionist_name').value.trim();
            const dt = document.getElementById('dt').value;
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            let regex = /^[A-Za-z\s]+$/; // Only allows letters and spaces
            let isValid = true;

            // Receptionist Name Validation
            if (!receptionist) {
                document.getElementById("rval").innerText = "* Receptionist Name Required";
                document.getElementById("rval").style.display = "block";
                document.getElementById("receptionist_name").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
                setTimeout(() => document.getElementById("rval").style.display = "none", 1200);
                isValid = false;
            }

            // Date Validation
            else if (!dt) {
                document.getElementById("dtval").style.display = "block";
                document.getElementById("dt").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
                setTimeout(() => document.getElementById("dtval").style.display = "none", 1200);
                isValid = false;
            }

            // Email Validation
            else if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById("eval").style.display = "block";
                setTimeout(() => document.getElementById("eval").style.display = "none", 1200);
                isValid = false;
            }

            // Phone Validation
            else if (!phone) {
                document.getElementById("cval").style.display = "block";
                setTimeout(() => document.getElementById("cval").style.display = "none", 1200);
                isValid = false;
            }

            else if (phone.length < 10) {
                document.getElementById("cval").style.display = "block";
                document.getElementById("cval").innerHTML = "Phone NO must be in 10 digits";
                setTimeout(() => document.getElementById("cval").style.display = "none", 1200);
                isValid = false;
            }

            // Username Validation
            else if (!username) {
                document.getElementById("uval").style.display = "block";
                setTimeout(() => document.getElementById("uval").style.display = "none", 1200);
                isValid = false;
            }

            // Password Validation
            else if (!password) {
                document.getElementById("psval").style.display = "block";
                setTimeout(() => document.getElementById("psval").style.display = "none", 1200);
                isValid = false;
            } else if (password.length < 6) {
                document.getElementById("plval").style.display = "block";
                setTimeout(() => document.getElementById("plval").style.display = "none", 1200);
                isValid = false;
            }

            return isValid;
        }
    </script>
</head>

<body>
    <?php
    include_once("./Navbar.php");
    include_once("./admin_header.php");
    ?>

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
                window.location.href = "../index.php";
            }, 1500);
        </script>
    <?php unset($_SESSION["receptionist"]); // Destroy session only after the alert is shown 
    } ?>

    <div class="content">
        <div class="header-list">
            <div class="left">
                <h4 class="fw-bold">Profile Settings</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Profile </span></p>
            </div>

        </div>

        <div class="container">
            <?php if ($receptionist["status"] == 'Active') { ?>
                <span class="badge bg-success p-2 fs-6 mb-2 mx-3 rounded-pill d-inline-flex align-items-center" data-bs-toggle="tooltip" title="Your query has been reviewed and addressed by the healthcare team">
                    <i class="bi bi-check-circle-fill me-2"></i> Activated
                </span>
            <?php } ?>
            <div class="form-container">
                <form onsubmit="return validationForm()" method="post">
                    <div class="form-group pt-3 pt-3">
                        <label>Full Name</label>
                        <input type="text" class="form-control shadow-sm" id="receptionist_name" name="receptionist_name" value="<?= $receptionist["name"] ?>" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').replace(/\s+/g, ' ')">
                        <span id="rval" style="color:red;display:none;"> </span>
                    </div>

                    <!-- Date of Birth -->
                    <div class="form-group pt-3">
                        <label for="dob">Birth Date</label>
                        <input type="date" class="form-control shadow-sm" id="dt" name="dt" value="<?= $receptionist["dob"] ?>">
                        <span id="dtval" style="color:red;display:none;"> * Date is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="gender">Your Gender</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender" value="Male" <?php if ($receptionist["gender"] == "Male") { ?> checked <?php } ?>>
                            <label class="form-check-label" for="inlineRadio1">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender" value="Female" <?php if ($receptionist["gender"] == "Female") { ?> checked <?php } ?>>
                            <label class="form-check-label" for="inlineRadio2">Female</label>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group pt-3">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control shadow-sm" id="email" name="email" value="<?= $receptionist["email"] ?>">
                        <span id="eval" style="color:red;display:none;"> * Email is Required </span>
                    </div>

                    <!-- Phone -->
                    <div class="form-group pt-3">
                        <label for="phone">Phone Number</label>
                        <input type="tel" class="form-control shadow-sm" id="phone" name="phone" maxlength="10" value="<?= $receptionist["contact"] ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <span id="cval" style="color:red;display:none;"> * Phone is Required </span>
                    </div>

                    <!-- Username -->
                    <div class="form-group pt-3">
                        <label for="bio">Username</label>
                        <input type="text" class="form-control shadow-sm" id="username" name="username" value="<?php echo $receptionist["username"] ?>" oninput="this.value = this.value.replace(/\s/g, '');">
                        <span id="uval" style="color:red;display:none;"> * Username is Required </span>
                    </div>

                    <!-- Password -->
                    <div class="form-group pt-3">
                        <label for="bio">Enter New Password</label>
                        <input type="password" class="form-control shadow-sm" id="password" name="password" placeholder="Old Password : <?php if (isset($_SESSION["receptionist_pass"])) { echo $_SESSION["receptionist_pass"]; } ?>" />
                        <!-- <span id="psval" style="color:red;display:none;"> * Password is Required </span> -->
                        <span id="plval" style="color:red;display:none;"> * Password Must be 6 digits long </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="tmp">Joing Date And Time</label>
                        <input class="form-control shadow-sm" id="tmp" name="tmp" value="<?= date("d M Y, h:i A", strtotime($receptionist["hire_dt"])); ?>" disabled>
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