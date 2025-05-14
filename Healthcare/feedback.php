<?php
include_once('../config.php');
$conn = connection();

if (!$conn) {
  echo "Connection Error !";
}

$alert = false;
if (isset($_REQUEST["submit_form"])) {
  $name = $_REQUEST["name"];
  $email = $_REQUEST["email"];
  $feedback = $_REQUEST["feedback"];

  $insert_query = "INSERT INTO feedback (`name`,email,feedback) VALUES ('$name','$email','$feedback')";
  $query = mysqli_query($conn, $insert_query);

  $newFeedbackId = mysqli_insert_id(mysql: $conn);
  $reply = "INSERT INTO reply (fid,feedback_reply,email,sent_at) VALUES ($newFeedbackId,'','','')";
  mysqli_query($conn, $reply);

  $alert = true;

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback</title>
  <link rel="website icon" href="./assets/images/logo.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Unbounded:wght@200..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/appoinment.css">
  <link rel="stylesheet" href="./assets/css/media.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

</head>

<body>
  <?php if ($alert) { ?>
    <script>
      Swal.fire({
        title: "Success!",
        text: "Feedback added successfully!",
        icon: "success"
      });

      setTimeout(() => {
        window.location.href = "index.php";
      }, 3000);

    </script>
  <?php } ?>
  <!-- start appoinment-section -->
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
              <h4>FeedBack</h4>
              <h2>Get an FeedBack</h2>
            </div>
            <form id="dreamit-form" method="post">
              <div class="inner-form">
                <div class="singleform">
                  <div class="form-box">
                    <input type="text" name="name" id="name" placeholder="Full Name" />
                    <i class="fa-solid fa-user"></i>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="email" name="email" id="email" placeholder="Email Address" />
                    <i class="fa-regular fa-envelope"></i>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="text" name="feedback" id="feedback" placeholder="Feedback" />
                    <i class="fa-solid fa-comments"></i>
                  </div>
                </div>

                <div class="booton-from">
                  <div class="check_bx">
                    <input type="checkbox" />
                    <span>I agree terms and conditions</span>
                  </div>
                  <div class="submit-button1">
                    <button class="submit-btn1" name="submit_form">
                      FeedBack
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

  <script>
    document.getElementById("dreamit-form").addEventListener("submit", function (event) {
      let valid = true;

      // Get form elements
      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("email").value.trim();
      const feedback = document.getElementById("feedback").value.trim();
      const checkbox = document.querySelector(".check_bx input");

      // Regular expressions
      const textOnlyRegex = /^[A-Za-z\s]+$/;  // Only letters and spaces
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      // Clear previous errors
      document.querySelectorAll(".error").forEach(el => el.remove());

      // Validate Name (No numbers allowed)
      if (!textOnlyRegex.test(name)) {
        showError("name", "Name cannot contain numbers or special characters.");
        valid = false;
      }

      // Validate Email
      if (!emailRegex.test(email)) {
        showError("email", "Enter a valid email address.");
        valid = false;
      }

      // Validate Feedback (No numbers allowed)
      if (!feedback) {
        showError("feedback", " Feedback required.");
        valid = false;
      }

      // Validate Checkbox
      if (!checkbox.checked) {
        showError("dreamit-form", " You must agree to the terms and conditions.");
        valid = false;
      }

      if (!valid) {
        event.preventDefault(); // Prevent form submission if invalid
      }
    });

    // Function to show error message with timeout
    function showError(inputId, message) {
      const inputField = document.getElementById(inputId);
      const errorDiv = document.createElement("div");
      errorDiv.className = "error";
      errorDiv.style.color = "red";
      errorDiv.style.fontSize = "12px";
      errorDiv.style.marginTop = "5px";
      errorDiv.textContent = message;
      inputField.parentElement.appendChild(errorDiv);

      // Remove error message after 5 seconds
      setTimeout(() => {
        errorDiv.remove();
      }, 1500);
    }
  </script>



  <!-- end appoinment-section -->
</body>

</html>