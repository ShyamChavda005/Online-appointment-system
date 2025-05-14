<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "healthcare");
$alert = false;

if (!$conn) {
    echo "<script>Swal.fire('Error', 'Database connection failed!', 'error');</script>";
    exit();
}

if (isset($_REQUEST["reset"])) {
    $password = $_REQUEST["password"];
    $confirm = $_REQUEST["confirmPassword"];

    if ($password == $confirm) {
        $em = $_SESSION["email"];
        $Q1 = "UPDATE patients SET `password` = '$confirm' WHERE email = '$em'";
        mysqli_query($conn, $Q1);
        $alert = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            text-align: center;
        }

        .btn {
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="card">
        <h4 class="mb-3">Reset Password</h4>
        <p class="text-muted">Enter your new password below.</p>
        <form method="post" onsubmit="return validatePasswords()">
            <div class="mb-3 text-start">
                <label class="form-label">New Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
            </div>
            <div class="mb-3 text-start">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password">
            </div>
            <button type="submit" name="reset" class="btn btn-primary w-100">Reset Password</button>
        </form>
        <p id="error-message" class="text-danger mt-2" style="display: none;">Passwords do not match!</p>
    </div>

    <script>
        function validatePasswords() {
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirmPassword").value;
            let errorMessage = document.getElementById("error-message");
            
            if (password !== confirmPassword) {
                errorMessage.style.display = "block";
                return false;
            }
            return true;
        }
    </script>
</body>

</html>
