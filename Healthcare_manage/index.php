<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <link rel="website icon" href="./assets/images/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Unbounded:wght@200..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="login.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  </script>
</head>

<body>
  <div class="appoinment-section">
    <div class="container">
      <div class="mian-appoinmentsection">
        <div class="left-appoinmentselection">
          <div class="contact_thumb">
            <img src="./assets/login_photo.jpg" alt="" />
          </div>
        </div>
        <div class="right-appoinmentselection">
          <div class="contact-form-box">
            <div class="appoinmentstitle">
              <h4>Login</h4>
              <h2>Get an Login</h2>
              <?php
              if (isset($_GET['error'])) {
                echo "<p class='error text-danger'>" . htmlspecialchars($_GET['error']) . "</p>";
              }
              ?>
            </div>
            <form id="dreamit-form" method="post" action="loginvalidation.php">
              <div class="inner-form">
                <div class="singleform">
                  <div class="form-box" id="drop-item">
                    <select id="role" name="role">
                      <option value="Admin">Admin</option>
                      <option value="Doctor">Doctor</option>
                      <option value="Receptionist">Receptionist</option>
                    </select>
                    <i class="fas fa-bookmark"></i>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input
                      type="text"
                      name="username"
                      id="username"
                      placeholder="User Name"
                      required />
                    <i class="fa-solid fa-user"></i>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input
                      type="password"
                      name="password"
                      id="password"
                      placeholder="Password"
                      required />
                    <i class="fa-solid fa-lock"></i>
                  </div>
                </div>

                <div class="booton-from">
                  <!-- <div class="check_bx">
                    <span><a href="#">Forgot Password</a></span>
                  </div> -->
                  <div class="submit-button1">
                    <button class="submit-btn1" name="submit_form">
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