<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}

include_once('../../config.php');
include_once("../../mail_helper.php");
$conn = connection();
$exits = false;
$alert = false;

if (isset($_REQUEST["addPatient"])) {
    $patient = $_REQUEST["patient_name"];
    $gender = $_REQUEST["gender"];
    $dob = $_REQUEST["dt"];
    $email = $_REQUEST["email"];
    $contact = $_REQUEST["phone"];
    $username = $_REQUEST["username"];
    $original_pass = $_REQUEST["password"];
    $password = hash("sha256", $original_pass);

    $P = mysqli_query($conn, "SELECT * FROM patients WHERE username = '$username'");

    if (mysqli_num_rows($P) > 0) {
        $exits = true;
    } else {
        $query = "INSERT INTO patients (patient_name,gender,dob,email,contact,username,`password`)
        VALUES ('$patient','$gender','$dob','$email',$contact,'$username','$password')";
        mysqli_query($conn, $query);

        $subject = "Welcome to HealthCare - Patient Portal Access";
        $msg = "
Dear $patient,

Welcome to HealthCare! Your patient account has been successfully created, allowing you to access your appointments and health information through our **Patient Portal**.

Here are your login credentials:  

🔹 **Username:** $username  
🔹 **Password:** $original_pass  

For security reasons, we strongly recommend changing your password immediately after logging in.  

If you have any questions or need assistance, feel free to reach out to our support team.  

Best regards,  
Team HealthCare  
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
    <title>Patient</title>
    <link rel="stylesheet" href="./style/AddPatient.css">
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
            const patientName = document.getElementById('patient_name').value.trim();
            const dt = document.getElementById('dt').value;
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            let regex = /^[A-Za-z\s]+$/; // Only allows letters and spaces
            let isValid = true;

            if (!patientName) { // Check if the input is empty
                let errorMsg = document.getElementById("pval");
                errorMsg.innerText = "* Patient Name is Required";
                errorMsg.style.display = "block";
                errorMsg.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    errorMsg.style.display = "none";
                }, 1200);

                isValid = false;
            } else if (!regex.test(patientName)) { // Check if input contains invalid characters
                let errorMsg = document.getElementById("pval");
                errorMsg.innerText = "* Only letters and spaces are allowed";
                errorMsg.style.display = "block";
                errorMsg.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    errorMsg.style.display = "none";
                }, 1200);

                isValid = false;
            }
            if (!dt) {
                document.getElementById("dtval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("dtval").style.display = "none";
                }, 1200);

                isValid = false;
            }
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById("eval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("eval").style.display = "none";
                }, 1200);

                isValid = false;
            }
            if (!phone || !/^\d{10}$/.test(phone)) {
                document.getElementById("cval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("cval").style.display = "none";
                }, 1200);

                isValid = false;
            }
            if (!username) {
                document.getElementById("uval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("uval").style.display = "none";
                }, 1200);

                isValid = false;
            }
            if (!password) {
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
    include_once("./component/admin_header.php");
    ?>

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
                title: "Patient Added Successfully!",
                text: "The patient's account has been created, and login details have been sent via email.",
                icon: "success"
            });


            setTimeout(() => {
                window.location.href = "ViewPatient.php";
            }, 2000);
        </script>
    <?php } ?>

    <div class="content">
        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Add New Patient</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <a href="./ViewPatient.php">
                        Patients </a> > <span> Add Patient </span></p>
            </div>
        </div>

        <div class="container">
            <div class="form-container">
                <form onsubmit="return validationForm()" method="post">
                    <div class="form-group">
                        <label>Patient Name</label>
                        <input type="text" id="patient_name" name="patient_name">
                        <span id="pval" style="color:red;display:none;"></span>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <div class="radiogroup">
                            <label> <input type="radio" value="Male" name="gender" checked> Male </label>
                            <label> <input type="radio" value="Female" name="gender"> Female </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dt">Date Of Birth</label>
                        <input type="date" id="dt" name="dt">
                        <span id="dtval" style="color:red;display:none;"> * Date is Required </span>
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
                        <div class="btn-group" style="width: 100%;">
                            <button type="submit" name="addPatient"> Submit </button>
                            <button type="reset" id="reset"> Reset </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>