<?php
session_start();
include_once("../config.php");
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

        $result2 = sendEmail($email, $subject, $msg);
        if ($result2 === true) {
            $_SESSION["otp"] = $otp;
            $_SESSION["email"] = $email;
            header("Location: otp.php");
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
    <title>Forgot Password</title>
    <link rel="website icon" href="./assets/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f4f7fc;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .card {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            background: #ffffff;
        }

        .btn {
            color: white;
            font-weight: bold;
        }

        .form-control {
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="card text-center">
        <h4 class="mb-3">Forgot Password</h4>
        <p class="text-muted">Enter your email to receive an OTP</p>
        <form method="post">
            <div class="mb-3 text-start">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email">
            </div>
            <button type="submit" class="btn btn-primary w-100" name="sendOtp">Send OTP</button>
        </form>
    </div>
</body>

</html>