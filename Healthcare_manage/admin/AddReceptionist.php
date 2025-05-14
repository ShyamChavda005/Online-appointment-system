<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}

include_once('../../config.php');
include_once("../../mail_helper.php");
$conn = connection();
$alert = false;
$exits = false;

if (isset($_REQUEST["addRecep"])) {
    $receptionist = $_REQUEST["receptionist_name"];
    $dob = $_REQUEST["dt"];
    $gender = $_REQUEST["gender"];
    $email = $_REQUEST["email"];
    $contact = $_REQUEST["phone"];
    $username = $_REQUEST["username"];
    $original_pass = $_REQUEST["password"];
    $password = hash("sha256", $original_pass);

    $R = mysqli_query($conn, "SELECT * FROM receptionist WHERE username = '$username'");

    if (mysqli_num_rows($R) > 0) {
        $exits = true;
    } else {
        $query5 = "INSERT INTO receptionist (`name`,dob,gender,email,contact,username,`password`)
    VALUES ('$receptionist','$dob','$gender','$email',$contact,'$username','$password')";
        mysqli_query($conn, $query5);

        $subject = "Welcome to HealthCare - Receptionist Portal Access";
        $msg = "
Dear $receptionist,
        
We are pleased to welcome you to HealthCare. Your account has been successfully created on **Receptionist Portal**.
        s
Here are your login credentials:  
        
🔹 **Username:** $username  
🔹 **Password:** $original_pass  
        
For security reasons, we strongly recommend changing your password immediately after logging in.   
        
If you need any assistance, feel free to contact our support team.  
        
Best regards,
**Team HealthCare**  
teamhealthcarehospital@gmail.com | +0261 250 5050  
";

        $result = sendEmail($email, $subject, $msg);

        if ($result === true) {
            $alert = true;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist</title>
    <link rel="stylesheet" href="./style/AddReceptionist.css">
    <link rel="website icon" href="./../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split("T")[0]; // Get today's date in YYYY-MM-DD format
            document.getElementById("dt").setAttribute("max", today);
        });

        function validationForm() {
            const receptionist = document.getElementById('receptionist_name').value.trim();
            const dt = document.getElementById('dt').value;
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            let isValid = true;
            let firstError = null;
            let regex = /^[A-Za-z\s]+$/; // Only allows letters and spaces

            function showError(id, message) {
                let errorMsg = document.getElementById(id);
                errorMsg.innerText = message;
                errorMsg.style.display = "block";

                // Capture first error for scrolling
                if (!firstError) {
                    firstError = errorMsg;
                }

                setTimeout(() => {
                    errorMsg.style.display = "none";
                }, 1500);
            }

            function clearErrorOnInput(inputId, errorId, condition) {
                document.getElementById(inputId).addEventListener("input", function() {
                    if (condition()) {
                        document.getElementById(errorId).style.display = "none";
                    }
                });
            }

            if (!receptionist) {
                showError("rval", "* Receptionist Name is Required");
                isValid = false;
            } else if (!regex.test(receptionist)) {
                showError("rval", "* Only letters and spaces are allowed");
                isValid = false;
            }
            clearErrorOnInput("receptionist_name", "rval", () => regex.test(receptionist));

            if (!dt) {
                showError("dtval", "* Date is required");
                isValid = false;
            }

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showError("eval", "* Enter a valid email address");
                isValid = false;
            }
            clearErrorOnInput("email", "eval", () => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email));

            if (!phone || !/^\d{10}$/.test(phone)) {
                showError("cval", "* Enter a valid 10-digit phone number");
                isValid = false;
            }
            clearErrorOnInput("phone", "cval", () => /^\d{10}$/.test(phone));

            if (!username) {
                showError("uval", "* Username is required");
                isValid = false;
            }
            clearErrorOnInput("username", "uval", () => username.length > 0);

            if (!password) {
                showError("psval", "* Password is required");
                isValid = false;
            } else if (password.length < 6) {
                showError("plval", "* Password must be at least 6 characters");
                isValid = false;
            }
            clearErrorOnInput("password", "plval", () => password.length >= 6);

            // Scroll to first error if validation fails
            if (!isValid && firstError) {
                firstError.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
            }

            return isValid;
        }
    </script>
</head>

<body>
    <?php include_once("./Navbar.php"); ?>

    <?php include_once("./component/admin_header.php"); ?>

    <?php if ($exits) { ?>
        <script>
            Swal.fire({
                title: "Error!",
                text: "Username Already Exits !",
                icon: "error"
            });
        </script>
    <?php } ?>

    <?php if ($alert) { ?>
        <script>
            Swal.fire({
                title: "Receptionist Added Successfully!",
                text: "The receptionist's account has been created, and login details have been sent via email.",
                icon: "success"
            });

            setTimeout(() => {
                window.location.href = "ViewReceptionist.php";
            }, 2000);
        </script>
    <?php } ?>


    <div class="content">

        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Add New Receptionist</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <a
                        href="./ViewReceptionist.php"> Receptionists </a> > <span> Add Receptionist </span></p>
            </div>
        </div>

        <div class="container">
            <div class="form-container">
                <form onsubmit="return validationForm()" method="post">
                    <div class="form-group">
                        <label>Receptionist Name</label>
                        <input type="text" id="receptionist_name" name="receptionist_name">
                        <span id="rval" style="color:red;display:none;"> * Receptionist Name is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="dt">Date Of Birth</label>
                        <input type="date" id="dt" name="dt">
                        <span id="dtval" style="color:red;display:none;"> * Date is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <div class="radiogroup">
                            <label> <input type="radio" name="gender" value="Male" checked> Male </label>
                            <label> <input type="radio" name="gender" value="Female"> Female </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                        <span id="eval" style="color:red;display:none;"> * Email is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone No.</label>
                        <input type="tel" id="phone" name="phone" maxlength="10"
                            oninput="this.value = this.value.replace(/\D/, '');">
                        <span id="cval" style="color:red;display:none;"> * Phone is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username">
                        <span id="uval" style="color:red;display:none;"> * Username is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password">
                        <span id="psval" style="color:red;display:none;"> * Password is Required </span>
                        <span id="plval" style="color:red;display:none;"> * Password Must be 6 digits long </span>
                    </div>
                    <div class="form-group">
                        <div class="btn-group" style="width:100%;">
                            <button type="submit" name="addRecep"> Submit </button>
                            <button type="reset" id="reset"> Reset </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>