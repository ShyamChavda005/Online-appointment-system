<?php
include_once('../config.php');
$conn = connection();

if (isset($_REQUEST["register"])) {
  $patient = $_REQUEST["pname"];
  $gender = $_REQUEST["gender"];
  $dob = $_REQUEST["dob"];
  $email = $_REQUEST["email"];
  $contact = $_REQUEST["contact"];
  $username = $_REQUEST["username"];
  $original_pass = $_REQUEST["password"];
  $hash = hash("sha256", $original_pass);

  $res12 = mysqli_query($conn, "SELECT * FROM patients WHERE username = '$username'");

  if (mysqli_num_rows($res12) == 0) {
    $query = "INSERT INTO patients (patient_name,gender,dob,email,contact,username,`password`) VALUES 
    ('$patient','$gender','$dob','$email','$contact','$username','$hash')";
    $result = mysqli_query($conn, $query);

    if ($result) {
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

      if (mail($email, $subject, message: $msg)) {

        header("location: login.php");
      }
      exit();
    }
  } else {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script> 
          document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                  title: 'Error!',
                  text: 'Username Already exist!',
                  icon: 'error'
              });
          }); 
      </script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration</title>
  <link rel="website icon" href="./assets/images/logo.png">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Unbounded:wght@200..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/registration.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="./assets/css/media.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />


  <script>
    document.addEventListener("DOMContentLoaded", function () {
      let today = new Date().toISOString().split("T")[0]; // Get today's date in YYYY-MM-DD format
      document.getElementById("dob").setAttribute("max", today);
    });

    function validateForm() {
      let isValid = true; // Flag to track validation status

      // Function to show error message with delay
      function showError(fieldId, message) {
        let errorElement = document.getElementById(fieldId);
        errorElement.innerText = message;

        // Remove error after 1.5 seconds
        setTimeout(() => {
          errorElement.innerText = "";
        }, 1500);
      }
      // Patient Name Validation (No Numbers Allowed)
      let pname = document.getElementById("pname").value.trim();
      if (!pname.match(/^[A-Za-z\s]+$/)) {
        showError("pname-error", "Name cannot contain numbers.");
        isValid = false;
      } else {
        document.getElementById("pname-error").innerText = "";
      }

      // DOB Validation (Required)
      let dob = document.getElementById("dob").value;
      if (dob === "") {
        showError("dob-error", "Date of Birth is required.");
        isValid = false;
      } else {
        document.getElementById("dob-error").innerText = "";
      }

      // Email Validation
      let email = document.getElementById("email").value.trim();
      let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
      if (!emailPattern.test(email)) {
        showError("email-error", "Enter a valid email.");
        isValid = false;
      } else {
        document.getElementById("email-error").innerText = "";
      }

      // Contact Validation (Only Numbers, Exactly 10 Digits)
      let contact = document.getElementById("contact").value.trim();
      if (!contact.match(/^\d{10}$/)) {
        showError("contact-error", "Enter a valid 10-digit contact number.");
        isValid = false;
      } else {
        document.getElementById("contact-error").innerText = "";
      }

      // Username Validation (Required)
      let username = document.getElementById("username").value.trim();
      if (username === "") {
        showError("username-error", "Username is required.");
        isValid = false;
      } else {
        document.getElementById("username-error").innerText = "";
      }

      // Password Validation (Min 6 characters)
      let password = document.getElementById("password").value.trim();
      if (password.length < 6) {
        showError("password-error", "Password must be at least 6 characters.");
        isValid = false;
      } else {
        document.getElementById("password-error").innerText = "";
      }

      return isValid; // If false, form submission will be blocked
    }

  </script> 

  <style>
    .error-message {
      color: red;
      font-size: 14px;
      display: block;
      margin-top: 5px;
    }
  </style>
</head>

<body>
  <div class="appoinment-section">
    <div class="container">
      <div class="mian-appoinmentsection">
        <div class="left-appoinmentselection">
          <div class="contact_thumb">
            <img src="assets/img/servicenew.jpg" alt="" />
          </div>
        </div>
        <div class="right-appoinmentselection">
          <div class="contact-form-box">
            <div class="appoinmentstitle">
              <h4>Registration</h4>
              <h2>Get an Registration</h2>
            </div>
            <form onsubmit="return validateForm()" id="dreamit-form" method="post">
              <div class="inner-form">
                <div class="singleform">
                  <div class="form-box">
                    <input type="text" name="pname" id="pname" placeholder="Patient Name*" />
                    <i class="fa-solid fa-user"></i>
                  </div>
                  <span class="error-message" id="pname-error"></span>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="date" name="dob" id="dob" placeholder="DOB" />
                    <i class="fa-solid fa-cake-candles"></i>
                  </div>
                  <span class="error-message" id="dob-error"></span>
                </div>

                <div class="singleform">
                  <div class="form-box" id="newbtn">
                    <input type="radio" name="gender" value="Male" checked>
                    <label>Male</label>
                  </div>
                </div>


                <div class="singleform">
                  <div class="form-box" id="newbtn">
                    <input type="radio" name="gender" value="Female">
                    <label>Female</label>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="email" name="email" id="email" placeholder="Email" />
                    <i class="fa-solid fa-envelope"></i></i>
                  </div>
                  <span class="error-message" id="email-error"></span>
                </div>


                <div class="singleform">
                  <div class="form-box">
                    <input type="tel" name="contact" id="contact" placeholder="Contact" maxlength="10" pattern="\d{10}" oninput="this.value = this.value.replace(/\D/, '');" />
                    <i class="fa-solid fa-phone"></i>
                  </div>
                  <span class="error-message" id="contact-error"></span>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="text" name="username" id="username" placeholder="User Name" />
                    <i class="fa-solid fa-circle-user"></i>
                  </div>
                  <span class="error-message" id="username-error"></span>
                </div>


                <div class="singleform">
                  <div class="form-box">
                    <input type="password" name="password" id="password" placeholder="password" />
                    <i class="fa-solid fa-lock"></i>
                  </div>
                  <span class="error-message" id="password-error"></span>
                </div>

                <div class="booton-from">
                  <div class="check_bx">
                    <span><a href="login.php">login</a></span>
                  </div>

                  <div class="booton-from">
                    <div class="submit-button1">
                      <button class="submit-btn1" style="cursor:pointer" name="register">
                        Registration
                        <i class="fa-solid fa-square-up-right">
                          <div></div>
                        </i>
                      </button>
                    </div>
                  </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>