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
$exits = false;
$redirect = false;

$query2 = mysqli_query($conn, "SELECT * FROM doctors where username='$duname'");
$doctor = mysqli_fetch_assoc($query2);

if (isset($_REQUEST["save"])) {
    $did = $doctor["doctor_id"];
    $newUsername = $_REQUEST["username"];

    // Check if the username is changed and exists in the database
    $checkUsernameQuery = mysqli_query($conn, "SELECT * FROM doctors WHERE username='$newUsername' AND doctor_id != $did");

    if (mysqli_num_rows($checkUsernameQuery) > 0) {
        $exits = true; // Username already taken
    } else {
        // If username is unique, proceed with update
        $dname = isset($_REQUEST["dname"]) ? $_REQUEST["dname"] : $doctor["doctor_name"];
        $specilization = isset($_REQUEST["specilization"]) ? $_REQUEST["specilization"] : $doctor["specilization"];
        $fee = isset($_REQUEST["fee"]) ? $_REQUEST["fee"] : $doctor["consultancy_fee"];
        $dob = isset($_REQUEST["date"]) ? $_REQUEST["date"] : $doctor["dob"];
        $address = isset($_REQUEST["address"]) ? $_REQUEST["address"] : $doctor["address"];
        $gender = isset($_REQUEST["gender"]) ? $_REQUEST["gender"] : $doctor["gender"];
        $email = isset($_REQUEST["email"]) ? $_REQUEST["email"] : $doctor["email"];
        $phone = isset($_REQUEST["phone"]) ? $_REQUEST["phone"] : $doctor["contact"];
        $password = $doctor["password"];

        if (!empty($_REQUEST["password"])) {
            $newPassword = $_REQUEST["password"];
            if (strlen($newPassword) < 6) {
                echo "<script>alert('Password must be at least 6 characters long');</script>";
            } else {
                $password = hash("sha256", $newPassword);
            }
        }

        $update = "UPDATE `doctors` SET doctor_name='$dname', specilization='$specilization', consultancy_fee=$fee, dob='$dob', gender='$gender', `address`='$address', email='$email', 
        contact='$phone', username='$newUsername', `password`='$password' WHERE doctor_id = $did";

        mysqli_query($conn, $update);

        if ($newUsername !== $doctor["username"] || $password !== $doctor["password"]) {
            $redirect = true;
        }

        if (
            $_REQUEST["dname"] == $doctor["doctor_name"] && $_REQUEST["specilization"] == $doctor["specilization"]  && $_REQUEST["fee"] == $doctor["consultancy_fee"]  && $_REQUEST["date"] == $doctor["dob"] && $_REQUEST["gender"] == $doctor["gender"]
            &&  $_REQUEST["address"] == $doctor["address"] && $_REQUEST["email"] == $doctor["email"] && $_REQUEST["phone"] == $doctor["contact"] && $_REQUEST["username"] == $doctor["username"] &&
            $password == $doctor["password"]
        ) {
            $Nochange = true;
        }

        $alert = true;
    }
}


if (isset($_REQUEST["save_photo"])) {
    $did = $doctor["doctor_id"];

    if (isset($_FILES["photo"]["name"]) && !empty($_FILES["photo"]["name"])) {
        $photo = $_FILES["photo"]["name"];
        $tmpname = $_FILES["photo"]["tmp_name"];
        $folder = "./../assets/doctorphotos/" . $photo;

        if (move_uploaded_file($tmpname, $folder)) {
            $update = "UPDATE `doctors` SET photo = '$photo' WHERE doctor_id = $did";
            mysqli_query($conn, $update);
            $alert = true;
        }
    }
}

if (isset($_REQUEST["remove"])) {
    $did = $doctor["doctor_id"];
    $update = "UPDATE doctors SET photo='doctor.svg' WHERE doctor_id = $did";
    mysqli_query($conn, $update);
    $alert = true;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split("T")[0]; // Get today's date in YYYY-MM-DD format
            document.getElementById("date").setAttribute("max", today);
        });

        function validationForm() {
            const doctorName = document.getElementById('dname').value.trim();
            const specilization = document.getElementById('specilization').value.trim();
            const fee = document.getElementById('fee').value.trim();
            const dt = document.getElementById('date').value;
            const address = document.getElementById('address').value;
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            let isValid = true;
            let regex = /^[A-Za-z\s]+$/; // Only allows letters and spaces

            if (!doctorName) {
                let errorMsg = document.getElementById("dval");
                document.getElementById("dname").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
                errorMsg.innerText = "* Doctor Name Required";
                errorMsg.style.display = "block";
                setTimeout(() => errorMsg.style.display = "none", 1200);
                isValid = false;
            } else if (!specilization) {
                document.getElementById("sval").style.display = "block";
                document.getElementById("dname").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    document.getElementById("sval").style.display = "none";
                }, 1200);

                isValid = false;
            } else if (!fee || fee === "" || isNaN(fee) || fee <= 0) {
                document.getElementById("feeval").style.display = "block";
                document.getElementById("feeval").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    document.getElementById("feeval").style.display = "none";
                }, 1200);

                isValid = false;
            } else if (!dt) {
                document.getElementById("dtval").style.display = "block";
                document.getElementById("dtval").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    document.getElementById("dtval").style.display = "none";
                }, 1200);

                isValid = false;
            } else if (!address) {
                document.getElementById("aval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("aval").style.display = "none";
                }, 1200);

                isValid = false;
            } else if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById("eval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("eval").style.display = "none";
                }, 1200);

                isValid = false;
            } else if (!phone || !/^\d{10}$/.test(phone)) {
                document.getElementById("cval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("cval").style.display = "none";
                }, 1200);

                isValid = false;
            } else if (!username) {
                document.getElementById("uval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("uval").style.display = "none";
                }, 1200);

                isValid = false;
            } else if (!password) {
                document.getElementById("psval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("psval").style.display = "none";
                }, 1200);

                isValid = false;
            } else if (password.length < 6) {
                document.getElementById("plval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("plval").style.display = "none";
                }, 1200);

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
    <?php unset($_SESSION["doctor"]); // Destroy session only after the alert is shown 
    } ?>

    <div class="content">
        <div class="header-list">
            <div class="left">
                <h4 class="fw-bold">Profile Settings</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Profile </span></p>
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
                <div class="image-btn">
                    <form enctype="multipart/form-data" method="post">
                        <input type="file" class="form-control" id="file-upload" name="photo" />
                        <button type="submit" id="btn" name="save_photo">Update Image</button>
                        <button type="submit" id="btn remove-btn" class="btn btn-danger" name="remove">Remove</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="form-container">
                <form onsubmit="return validationForm()" method="post">
                    <div class="form-group pt-3">
                        <label>Your Reference ID </label>
                        <input type="text" class="form-control text-primary fw-bold" id="pid" name="pid" value="<?= "REF9350Av5-" . $doctor["doctor_id"] ?>" disabled>
                    </div>

                    <div class="form-group pt-3 pt-3">
                        <label>Your Full Name</label>
                        <input type="text" class="form-control shadow-sm" id="dname" name="dname" value="<?= $doctor["doctor_name"] ?>" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').replace(/\s+/g, ' ')">
                        <span id="dval" style="color:red;display:none;"> </span>
                    </div>

                    <div class="form-group pt-3 pt-3">
                        <label>Your Specilization</label>
                        <input type="text" class="form-control shadow-sm" id="specilization" name="specilization" value="<?= $doctor["specilization"] ?>" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')">
                        <span id="sval" style="color:red;display:none;"> * specilization is Required </span>
                    </div>

                    <div class="form-group pt-3 pt-3">
                        <label>Your Consultancy Fees</label>
                        <input type="number" class="form-control shadow-sm" id="fee" name="fee" value="<?= $doctor["consultancy_fee"] ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <span id="feeval" style="color:red;display:none;"> * Fee is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="dob">Your Birth Date</label>
                        <input type="date" class="form-control shadow-sm" id="date" name="date" value="<?= $doctor["dob"] ?>">
                        <span id="dtval" style="color:red;display:none;"> * Date Of Birth is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="gender">Your Gender</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender" value="Male" <?php if ($doctor["gender"] == "Male") { ?> checked <?php } ?>>
                            <label class="form-check-label" for="inlineRadio1">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="gender" value="Female" <?php if ($doctor["gender"] == "Female") { ?> checked <?php } ?>>
                            <label class="form-check-label" for="inlineRadio2">Female</label>
                        </div>
                    </div>

                    <div class="form-group pt-3">
                        <label for="email">Your Address</label>
                        <textarea class="form-control my-1" id="address" name="address" rows="1" cols="10"></textarea>
                        <script>
                            document.getElementById("address").value = <?php echo json_encode($doctor["address"]); ?>;
                        </script>
                        <span id="aval" style="color:red;display:none;"> * Address is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="email">Your Email</label>
                        <input type="email" class="form-control shadow-sm" id="email" name="email" value="<?= $doctor["email"] ?>">
                        <span id="eval" style="color:red;display:none;"> * Email is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="phone">Your Phone</label>
                        <input type="tel" class="form-control shadow-sm" id="phone" name="phone" maxlength="10" value="<?= $doctor["contact"] ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <span id="cval" style="color:red;display:none;"> * Phone is Required </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="bio">Username</label>
                        <input type="text" class="form-control shadow-sm" id="username" name="username" value="<?php echo $doctor["username"] ?>" oninput="this.value = this.value.replace(/\s/g, '');">
                        <span id="uval" style="color:red;display:none;"> * Username is Required </span>
                    </div>

                    <!-- Password -->
                    <div class="form-group pt-3">
                        <label for="bio">Enter New Password</label>
                        <input type="password" class="form-control shadow-sm" id="password" name="password" placeholder="Old Password : <?php if (isset($_SESSION["doctor_pass"])) {
                                                                                                                                            echo $_SESSION["doctor_pass"];
                                                                                                                                        } ?>" />
                        <!-- <span id="psval" style="color:red;display:none;"> * Password is Required </span> -->
                        <span id="plval" style="color:red;display:none;"> * Password Must be 6 digits long </span>
                    </div>

                    <div class="form-group pt-3">
                        <label for="tmp">Account Created at</label>
                        <input class="form-control shadow-sm" id="tmp" name="tmp" value="<?= date("d M Y, h:i A", strtotime($doctor["create_at"])); ?>" disabled>
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