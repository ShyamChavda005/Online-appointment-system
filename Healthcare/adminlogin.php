
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Unbounded:wght@200..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/adminlogin.css?v=<?php echo time(); ?>">
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
      <div class="form-box" id="drop-item">
        <select id="role">
          <option value="">Select Role</option>
          <option value="admin">Admin</option>
          <option value="doctor">Doctor</option>
          <option value="receptionist">Receptionist</option>
        </select>
        <i class="fas fa-bookmark"></i>
        <span class="error-message" id="role-error"></span>
      </div>
    </div>

    <div class="singleform">
      <div class="form-box">
        <input type="text" name="name" id="name" placeholder="User Name" />
        <i class="fa-solid fa-user"></i>
        <span class="error-message" id="name-error"></span>
      </div>
    </div>

    <div class="singleform">
      <div class="form-box">
        <input type="password" name="password" id="password" placeholder="Password" />
        <i class="fa-solid fa-lock"></i>
        <span class="error-message" id="password-error"></span>
      </div>
    </div>

    <!-- <div class="booton-from">
      <div class="check_bx">
        <span><a href="#">Forgot Password</a></span>
      </div> -->

      <div class="submit-button1">
        <button type="submit" class="submit-btn1">
          Login
          <i class="fa-solid fa-lock-open"></i>
        </button>
      </div>
    </div>
  </div>
</form>

<style>
  .error-message {
    color: red;
    font-size: 12px;
    display: block;
    margin-top: 5px;
  }
</style>

<script>
  document.getElementById("dreamit-form").addEventListener("submit", function (event) {
    let isValid = true;

    let role = document.getElementById("role").value;
    let username = document.getElementById("name").value.trim();
    let password = document.getElementById("password").value.trim();

    document.getElementById("role-error").textContent = "";
    document.getElementById("name-error").textContent = "";
    document.getElementById("password-error").textContent = "";

    if (role === "") {
      document.getElementById("role-error").textContent = "Please select a role.";
      isValid = false;
    }
    if (username === "") {
      document.getElementById("name-error").textContent = "Username is required.";
      isValid = false;
    }
    if (password === "") {
      document.getElementById("password-error").textContent = "Password is required.";
      isValid = false;
    }

    if (!isValid) {
      event.preventDefault();
    }
  });
</script>

          </div>
        </div>
      </div>
    </div>
  </div>
 
  
</body>

</html> 