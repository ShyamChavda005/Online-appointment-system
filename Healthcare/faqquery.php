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
  $que = $_REQUEST["query"];

  $insert_query = "INSERT INTO query (`name`,email,query) VALUES ('$name','$email','$que')";
  mysqli_query($conn, $insert_query);

  $newQueryId = mysqli_insert_id($conn);
  $response = "INSERT INTO response (qid,res_name,email,sent_at) VALUES ($newQueryId,'','','')";
  mysqli_query($conn, $response);

  $alert = true;

}
?>
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
  <link rel="stylesheet" href="./assets/css/appoinment.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="./assets/css/media.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  </script>
</head>

<body>

  <?php if ($alert) { ?>
    <script>
      Swal.fire({
        title: "Success!",
        text: "Query Submited Successfully!",
        icon: "success"
      });

      setTimeout(() => {
        window.location.href = "faqindex.php";
      }, 3000);
    </script>
  <?php } ?>

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
              <h4>Query</h4>
              <h2>Get an Query</h2>
            </div>
            <form id="dreamit-form" method="post">
              <div class="inner-form">
                <div class="singleform">
                  <div class="form-box">
                    <input type="text" name="name" id="name" placeholder="Full Name" />
                    <i class="fa-solid fa-user"></i>
                    <span class="error-message" id="name-error"></span>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="email" name="email" id="email" placeholder="E-mail Address" />
                    <i class="fa-regular fa-envelope"></i>
                    <span class="error-message" id="email-error"></span>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="text" name="query" id="query" placeholder="Query" />
                    <i class="fa-solid fa-comments"></i>
                    <span class="error-message" id="query-error"></span>
                  </div>
                </div>

                <div class="booton-from">
                  <div class="check_bx">
                    <input type="checkbox" id="terms" />
                    <span>I agree to the terms and conditions</span>
                    <span class="error-message" id="terms-error"></span>
                  </div>

                  <div class="submit-button1">
                    <button type="submit" name="submit_form" class="submit-btn1">
                      Submit Query
                      <i class="fa-solid fa-square-up-right"></i>
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

                let name = document.getElementById("name").value.trim();
                let email = document.getElementById("email").value.trim();
                let query = document.getElementById("query").value.trim();
                let terms = document.getElementById("terms").checked;

                // Function to display and remove error messages after 1.5 seconds
                function showError(fieldId, message) {
                  let errorElement = document.getElementById(fieldId);
                  errorElement.textContent = message;
                  setTimeout(() => {
                    errorElement.textContent = "";
                  }, 1500);
                }

                let nameRegex = /^[A-Za-z\s]+$/;
                let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                // Name Validation
                if (name === "") {
                  showError("name-error", "Full Name is required.");
                  isValid = false;
                } else if (!nameRegex.test(name)) {
                  showError("name-error", "Full Name must contain only letters.");
                  isValid = false;
                }

                // Email Validation
                if (email === "") {
                  showError("email-error", "Email is required.");
                  isValid = false;
                } else if (!emailRegex.test(email)) {
                  showError("email-error", "Enter a valid email address.");
                  isValid = false;
                }

                // Query Validation
                if (query === "") {
                  showError("query-error", "Query is required.");
                  isValid = false;
                } 

                // Terms & Conditions Validation
                if (!terms) {
                  showError("terms-error", "You must agree to the terms.");
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