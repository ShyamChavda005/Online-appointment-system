<?php
session_start();
$Matched = false;
$notMatched = false;

if (isset($_REQUEST["verify"])) {
    $all_otp = $_REQUEST["o1"] . $_REQUEST["o2"] . $_REQUEST["o3"] . $_REQUEST["o4"];

    if ($_SESSION["otp"] == $all_otp) {
        $Matched = true;
    } else {
        $notMatched = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP</title>
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
            text-align: center;
        }

        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.5rem;
            margin: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .otp-input:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn {
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="card">
        <h4 class="mb-3">Enter OTP</h4>
        <p class="text-muted">We have sent a 4-digit OTP to your email.</p>
        <form method="post">
            <div class="d-flex justify-content-center mb-3">
                <input type="text" class="otp-input form-control" name="o1" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="moveToNext(this)">
                <input type="text" class="otp-input form-control" name="o2" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="moveToNext(this)">
                <input type="text" class="otp-input form-control" name="o3" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="moveToNext(this)">
                <input type="text" class="otp-input form-control" name="o4" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="moveToNext(this)">
            </div>
            <button type="submit" name="verify" class="btn btn-primary w-100">Verify OTP</button>
        </form>
    </div>

    <script>
        function moveToNext(input) {
            if (input.value.length === 1 && /[0-9]/.test(input.value)) {
                let nextInput = input.nextElementSibling;
                if (nextInput) nextInput.focus();
            } else {
                input.value = "";
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            <?php if ($Matched) { ?>
                Swal.fire({
                    title: "Success!",
                    text: "OTP Verified Successfully!",
                    icon: "success"
                }).then(() => {
                    window.location.href = "password.php";
                });
            <?php } ?>

            <?php if ($notMatched) { ?>
                Swal.fire({
                    title: "Wrong OTP!",
                    text: "Invalid OTP!",
                    icon: "error"
                });
            <?php } ?>
        });
    </script>

</body>

</html>
