<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}
include_once('../../config.php');
include_once("../../mail_helper.php");
$conn = connection();
$alert = false;
$exist = false;

if (isset($_REQUEST["addDoctor"])) {
    $doctor = $_REQUEST["doctor_name"];
    $sp = $_REQUEST["specilization"];
    $fee = $_REQUEST["fee"];
    $dob = $_REQUEST["dob"];
    $gender = $_REQUEST["gender"];
    $address = $_REQUEST["address"];
    $email = $_REQUEST["email"];
    $contact = $_REQUEST["contact"];
    $username = $_REQUEST["username"];
    $original_pass = $_REQUEST["password"];
    $password = hash("sha256", $original_pass);
    // $hash = password_hash($password, PASSWORD_DEFAULT);

    $photo = $_FILES["photo"]["name"];
    $tmpname = $_FILES["photo"]["tmp_name"];
    $folder = "./../assets/doctorphotos/" . $photo;

    $D = mysqli_query($conn, "SELECT * FROM doctors WHERE username = '$username'");
    if (mysqli_num_rows($D) > 0) {
        $exist = true;
    } else {
        $query = "INSERT INTO doctors (doctor_name,specilization,consultancy_fee,dob,gender,`address`,email,contact,username,`password`,photo)
    VALUES ('$doctor','$sp',$fee,'$dob','$gender','$address','$email',$contact,'$username','$password','$photo')";
        mysqli_query($conn, $query);
        move_uploaded_file($tmpname, $folder);

        // Get the newly inserted doctor's ID
        $newdoctor_id = mysqli_insert_id($conn);
        // Insert default schedule for the doctor
        $insertSchedule = "INSERT INTO doctor_schedule (doctor_id, available_days, available_from, available_to, appointment_duration) 
                   VALUES ($newdoctor_id, '[]', '00:00:00', '00:00:00', 20)";
        mysqli_query($conn, $insertSchedule);

        $subject = "Welcome to HealthCare - Doctor Portal Access";
        $msg = "
Dear $doctor,

We are pleased to welcome you to HealthCare. Your account has been successfully created on our **Doctor Portal**.

Here are your login details:

🔹 **Username:** $username  
🔹 **Password:** $original_pass

For security reasons, we **recommend changing your password** immediately after logging in. If you need any assistance, feel free to contact our support team.

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
    <title>Doctor</title>
    <link rel="stylesheet" href="./style/AddDoctor.css">
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
            // console.log(today);
            document.getElementById("dob").setAttribute("max", today);
        });

        function validationForm() {
            const doctorName = document.getElementById('doctor_name').value.trim();
            const specilization = document.getElementById('specilization').value;
            const fee = document.getElementById('fee').value.trim();
            const dob = document.getElementById('dob').value;
            const address = document.getElementById('address').value.trim();
            const email = document.getElementById('email').value.trim();
            const contact = document.getElementById('contact').value.trim();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const photo = document.getElementById('photo').value;

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

            if (!doctorName) {
                showError("nval", "* Doctor Name is Required");
                isValid = false;
            } else if (!regex.test(doctorName)) {
                showError("nval", "* Only letters and spaces are allowed");
                isValid = false;
            }
            clearErrorOnInput("doctor_name", "nval", () => regex.test(doctorName));

            if (specilization === "Select Specilization") {
                showError("sval", "* Please select a specialization");
                isValid = false;
            }

            if (!fee || isNaN(fee) || fee <= 0) {
                showError("feeval", "* Enter a valid fee amount");
                isValid = false;
            }
            clearErrorOnInput("fee", "feeval", () => !isNaN(fee) && fee > 0);

            if (!dob) {
                showError("dtval", "* Date of birth is required");
                isValid = false;
            }

            if (!address) {
                showError("aval", "* Address is required");
                isValid = false;
            }
            clearErrorOnInput("address", "aval", () => address.length > 0);

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showError("eval", "* Enter a valid email address");
                isValid = false;
            }
            clearErrorOnInput("email", "eval", () => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email));

            if (!contact || !/^\d{10}$/.test(contact)) {
                showError("cval", "* Enter a valid 10-digit contact number");
                isValid = false;
            }
            clearErrorOnInput("contact", "cval", () => /^\d{10}$/.test(contact));

            if (!username) {
                showError("uval", "* Username is required");
                isValid = false;
            }
            clearErrorOnInput("username", "uval", () => username.length > 0);

            if (!password) {
                showError("pval", "* Password is required");
                isValid = false;
            } else if (password.length < 6) {
                showError("plval", "* Password must be at least 6 characters");
                isValid = false;
            }
            clearErrorOnInput("password", "plval", () => password.length >= 6);

            if (!photo) {
                showError("ptval", "* Please upload a photo");
                isValid = false;
            }

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
    <?php include_once("./Navbar.php");
    include_once("./component/admin_header.php");
    ?>

    <?php if ($exist) { ?>
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
                title: "Doctor Added Successfully!",
                text: "The doctor's account has been created, and login details have been sent via email.",
                icon: "success"
            });


            setTimeout(() => {
                window.location.href = "ViewDoctor.php";
            }, 2000);
        </script>
    <?php } ?>

    <div class="content">

        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold">Add New Doctor</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <a href="./ViewDoctor.php">
                        Doctors </a> > <span> Add Doctor </span></p>
            </div>
        </div>

        <div class="container">
            <div class="form-container">
                <form onsubmit="return validationForm()" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="doctor_name">Doctor Name</label>
                        <input type="text" id="doctor_name" name="doctor_name">
                        <span id="nval" style="color:red;display:none;"></span>
                    </div>
                    <div class="form-group">
                        <label>Specialization</label>
                        <select id="specilization" name="specilization">
                            <option>Select Specilization</option>
                            <option value="Cardiologist">Cardiologist</option>
                            <option value="Dentist">Dentist</option>
                            <option value="Dermatologist">Dermatologist</option>
                            <option value="Gynecologist">Gynecologist</option>
                            <option value="Neurologist">Neurologist</option>
                            <option value="Orthopedic">Orthopedic</option>
                            <option value="Psychiatrist">Psychiatrist</option>
                            <option value="Radiologist">Radiologist</option>
                        </select>
                        <span id="sval" style="color:red;display:none;"> * Specilization is Required </span>
                    </div>
                    <div class="form-group my-2">
                        <label for="fee">Consultancy Fee</label>
                        <input type="number" class="form-control" id="fee" name="fee">
                        <span id="feeval" style="color:red;display:none;"> * Consultancy Fee is Required </span>
                    </div>

                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob">
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
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address">
                        <span id="aval" style="color:red;display:none;"> * Address is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" />
                        <span id="eval" style="color:red;display:none;"> * Email is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact</label>
                        <input type="tel" id="contact" name="contact" maxlength="10"
                            oninput="this.value = this.value.replace(/\D/, '');">
                        <span id="cval" style="color:red;display:none;"> * Contact is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username">
                        <span id="uval" style="color:red;display:none;"> * Username Required </span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password">
                        <span id="pval" style="color:red;display:none;"> * Password Required </span>
                        <span id="plval" style="color:red;display:none;"> * Password Must be 6 digits long </span>
                    </div>

                    <div class="form-group">
                        <label for="photo">Photo</label>
                        <input type="file" id="photo" name="photo" accept="image/*">
                        <span id="ptval" style="color:red;display:none;"> * Photo Required </span>
                    </div>

                    <div class="form-group">
                        <div class="btn-group" style="width:100%;">
                            <button type="submit" name="addDoctor"> Submit </button>
                            <button type="reset" id="reset"> Clear </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>