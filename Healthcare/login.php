<?php
session_start();
include_once('../config.php');
$conn = connection();

if (isset($_REQUEST["login"])) {
  $username = $_REQUEST["username"];
  $password = $_REQUEST["password"];
  $_SESSION["pass"] = $password;
  $hash = hash("sha256", $password);

  $Q = mysqli_query($conn, "SELECT * FROM patients WHERE username = '$username'");
  if (mysqli_num_rows($Q) > 0) {
    $row = mysqli_fetch_array($Q);

    if ($row["status"] == 'Suspend') {
      echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
      echo "<script> 
              document.addEventListener('DOMContentLoaded', function() {
                  Swal.fire({
                      title: 'Account Suspended!',
                      text: 'You are suspended for some reason. Please contact healthcare support!',
                      icon: 'warning',
                      allowOutsideClick: false,
                      confirmButtonText: 'OK'
                  }).then(() => {
                      window.location.href = 'login.php';
                  });
              }); 
          </script>";
      exit(); // Stop further execution
    }

    if ($row["password"] == $hash) {
      $_SESSION["user"] = $row["username"];
      echo "<script> window.location.href = './index.php'; </script>";
    } else {
      echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
      echo "<script> 
              document.addEventListener('DOMContentLoaded', function() {
                  Swal.fire({
                      title: 'Error!',
                      text: 'Invalid password!',
                      icon: 'error'
                  });
              }); 
          </script>";
    }
  } else {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script> 
          document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                  title: 'Error!',
                  text: 'Username does not exist! Please register.',
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
  <title>Login</title>
  <link rel="website icon" href="./assets/images/logo.png">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Unbounded:wght@200..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/login.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="./assets/css/media.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>

<body>
  <div class="appoinment-section">
    <div class="container">
      <div class="mian-appoinmentsection">
        <div class="left-appoinmentselection">
          <div class="contact_thumb">
            <img src="assets/img/testi-monial.png" alt="" />
          </div>
        </div>
        <div class="right-appoinmentselection">
          <div class="contact-form-box">
            <div class="appoinmentstitle">
              <h4>Login</h4>
              <h2>Get an Login</h2>
            </div>
            <form id="dreamit-form" method="post">

              <div class="inner-form">
                <div class="singleform">
                  <div class="form-box">
                    <input type="text" name="username" id="name" placeholder="User Name" />
                    <i class="fa-solid fa-user"></i>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="password" name="password" id="" placeholder="Password" />
                    <i class="fa-solid fa-lock"></i>
                  </div>
                </div>

                <div class="booton-from">
                  <div class="check_bx">
                    <!-- <input type="checkbox" /> -->
                    <span><a href="registration.php">Registration</a></span>
                    <span><a href="forgotfrom.php">Forgot Password</a></span>
                  </div>

                  <!-- <div class="check_bx">
                    <input type="checkbox" />
                    <span><a href="#">Forgot Password</a></span>
                  </div> -->


                  <div class="submit-button1">
                    <button class="submit-btn1" name="login">
                      Login
                      <i class="fa-solid fa-lock-open"></i>
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