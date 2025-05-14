<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "healthcare");

if (!$conn) {
    echo "<script>Swal.fire('Error', 'Database connection failed!', 'error');</script>";
    exit();
}

$otp = rand(1000, 9999);

if (isset($_REQUEST["sendOtp"])) {
    $email = $_REQUEST["email"];
    $Q = mysqli_query($conn, "SELECT email FROM patients WHERE email = '$email'");

    if (mysqli_num_rows($Q) > 0) {
        $subject = "Reset Your Password - OTP Verification";
        $msg = "We received a request to reset your password for your online appointment system account. To proceed, 
please use the One-Time Password (OTP) below:

Your OTP: " . $otp . "

Please do not share it with anyone.
If you did not request a password reset, please ignore this email or contact our support team immediately.
Best regards,
Team HealthCare

Customer Support Team
teamhealthcarehospital@gmail.com";

        if (mail($email, $subject, $msg)) {
            $_SESSION["otp"] = $otp;
            $_SESSION["email"] = $email;
            header("Location: otpfrom.php");
            // exit();
        } else {
            echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to send OTP. Please try again.',
                    icon: 'error'
                });
            </script>";
        }
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Oops!',
                    text: 'Email does not exist!',
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
  <title>Forgot</title>
  <link rel="website icon" href="./assets/images/logo.png">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Unbounded:wght@200..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/forgotfrom.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="./assets/css/media.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  </script>
</head>

<body>

 

  <div class="appoinment-section">
    <div class="container">
      <div class="mian-appoinmentsection">
        <div class="left-appoinmentselection">
          <div class="contact_thumb">
            <img src="assets/img/contact_thumb.png" alt="" />
          </div>
        </div>
        <div class="right-appoinmentselection">
          <div class="contact-form-box">
            <div class="appoinmentstitle">
              <h4>ForgotFrom</h4>
              <h2>Enter your email to receive an OTP</h2>
            </div>
            <form id="dreamit-form" method="post">
  <div class="inner-form">
    <div class="singleform">
      <div class="form-box">
        <input type="text" name="email" id="name" placeholder="Email Address" />
        <i class="fa-solid fa-user"></i>
        <span class="error-message" id="name-error"></span>
      </div>
    </div>

    
      <div class="submit-button1">
        <button type="submit" name="sendOtp" class="submit-btn1">
        Send OTP
          <i class="fa-solid fa-square-up-right"></i>
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