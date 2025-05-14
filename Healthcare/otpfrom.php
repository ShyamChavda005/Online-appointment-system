<?php
session_start();
$Matched = false;
$notMatched = false;

// echo $_SESSION["otp"];

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
  <link rel="website icon" href="./assets/images/logo.png">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Unbounded:wght@200..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/otpfrom.css?v=<?php echo time(); ?>">
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
              <h4>Enter OTP</h4>
              <h2>We have sent a 4-digit OTP to your email </h2>
              <h3 style="font-size: 11px;color:white;font-weight:900;letter-spacing:1px;"><?= $_SESSION["email"] ?> </h3>
            </div>
            <form id="dreamit-form" method="post">
  <div class="inner-form">
    <!-- <div class="singleform"> -->
      <div class="form-box">
      <div class="d-flex justify-content-center mb-3">
                <input type="text" class="otp-input form-control" name="o1" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="moveToNext(this)">
                <input type="text" class="otp-input form-control" name="o2" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="moveToNext(this)">
                <input type="text" class="otp-input form-control" name="o3" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="moveToNext(this)">
                <input type="text" class="otp-input form-control" name="o4" maxlength="1" pattern="[0-9]" inputmode="numeric" oninput="moveToNext(this)">
            </div>
        <span class="error-message" id="name-error"></span>
      </div>
    <!-- </div> -->

    
      <div class="submit-button1">
        <button type="submit" name="verify" class="submit-btn1">
        Verify OTP
          <i class="fa-solid fa-square-up-right"></i>
        </button>
      </div>
    </div>
  </div>
</form>



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
                    window.location.href = "passwordfrom.php";
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

          </div>
        </div>
      </div>
    </div>
  </div>
 
</body>

</html> 