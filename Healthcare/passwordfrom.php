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

  $passHash = hash("sha256", $password);
  $confirmHash = hash("sha256", $confirm);

  $result = mysqli_query($conn, "SELECT * FROM patients WHERE `password` = '$confirmHash'");

  if (mysqli_num_rows($result) > 0) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script> 
          document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                  title: 'Same Password',
                  text: 'Password Exits!',
                  icon: 'info'
              });
          }); 
      </script>";
  } else {
    if ($passHash === $confirmHash) {
      $em = $_SESSION["email"];
      $Q1 = "UPDATE patients SET `password` = '$confirmHash' WHERE email = '$em'";
      mysqli_query($conn, $Q1);
      $alert = true;
      echo "<script> setTimeout(()=> { window.location.href = 'index.php'; },1500) </script>";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset</title>
  <link rel="website icon" href="./assets/images/logo.png">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Unbounded:wght@200..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/passwordfrom.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="./assets/css/media.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <script>
    function validatePasswords() {
      let password = document.getElementById("password").value.trim();
      let confirmPassword = document.getElementById("confirm").value.trim();
      let passwordError = document.getElementById("password-error");
      let confirmError = document.getElementById("confirm-error");

      // Reset previous errors
      passwordError.style.display = "none";
      confirmError.style.display = "none";

      // Password validation
      if (!password) {
        showError(passwordError, "Password is required");
        return false;
      }

      if (password.length < 6) {
        showError(passwordError, "Password must be at least 6 characters");
        return false;
      }

      if (!/[0-9]/.test(password)) {
        showError(passwordError, "Password must contain at least one number");
        return false;
      }

      if (!/[@$!%*?&]/.test(password)) {
        showError(passwordError, "Password must contain at least one special character (@$!%*?&)");
        return false;
      }

      // Confirm Password Validation
      if (!confirmPassword) {
        showError(confirmError, "Please confirm your password");
        return false;
      }

      if (password !== confirmPassword) {
        showError(confirmError, "Passwords do not match");
        return false;
      }

      return true;
    }

    function showError(element, message) {
      element.innerHTML = message;
      element.style.display = "block";
      setTimeout(() => {
        element.style.display = "none";
      }, 1200);
    }
  </script>

</head>

<body>

  <?php if ($alert) { ?>
    <script>
      Swal.fire({
        title: 'Success!',
        text: 'Password Reset Successfully.',
        icon: 'success'
      });
    </script>
  <?php } ?>

  <div class="appoinment-section">
    <div class="container">
      <div class="mian-appoinmentsection">
        <div class="left-appoinmentselection">
          <div class="contact_thumb">
            <img src="assets/img/contact_thumb.png" alt="" class="passwordimg" />
          </div>
        </div>
        <div class="right-appoinmentselection">
          <div class="contact-form-box">
            <div class="appoinmentstitle">
              <h4>Reset Password</h4>
              <h2>Enter your new password below.</h2>
            </div>
            <form onsubmit="return validatePasswords()" id="dreamit-form" method="post">
              <div class="inner-form">


                <div class="singleform">
                  <div class="form-box">
                    <input type="password" name="password" id="password" placeholder="New Password" />
                    <i class="fa-solid fa-lock"></i>
                    <span class="error-message" id="password-error" style="display: none;color:red;"></span>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="password" name="confirmPassword" id="confirm" placeholder="Confirm Password" />
                    <i class="fa-solid fa-lock"></i>
                    <span class="error-message" id="confirm-error" style="display: none;color:red;"></span>
                  </div>
                </div>

                <div class="booton-from">


                  <div class="submit-button1">
                    <button type="submit" name="reset" class="submit-btn1">
                      Reset Password
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