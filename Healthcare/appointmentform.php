<?php
error_reporting(error_level: 0);
session_start();
include_once('../config.php');
include_once("../mail_helper.php");

$conn = connection();
$alert = false;

if (!$conn) {
  echo "Connection Error !";
}

if (isset($_REQUEST["order_id"])) {
  $order_id = $_REQUEST["order_id"];
  $fetch_paymentid = mysqli_query($conn, "SELECT * FROM payments WHERE order_id='$order_id'");
  $payments = mysqli_fetch_assoc($fetch_paymentid);

  $patient = $_SESSION['patient_id'] ?? null;
  $doctor = $_SESSION['doctor_id'] ?? null;
  $pay_id = $payments["payment_id"];
  $dt = $_SESSION['dt'] ?? null;
  $time = $_SESSION['time'] ?? null;
  $reason = $_SESSION['reason'] ?? null;

  if ($patient && $doctor && $dt && $time && $reason && $pay_id) {
    $query = "INSERT INTO appointments (patient_id,doctor_id,payment_id,appointment_date,appointment_time,reason) VALUES ($patient,$doctor,$pay_id,'$dt','$time','$reason')";
    mysqli_query($conn, $query);

    $pat = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $patient");
    $allpat = mysqli_fetch_assoc($pat);

    $doc = mysqli_query($conn, "SELECT * FROM doctors WHERE doctor_id = $doctor");
    $alldoc = mysqli_fetch_assoc($doc);

    $email = $allpat["email"];
    $subject = " Appointment Confirmation -- [" . $dt . "] at [" . $time . "]";
    $msg = "Dear " . $allpat["patient_name"] . ",

Thank you for scheduling your appointment with Healthcare. We are pleased to confirm that your appointment has been successfully booked.

Appointment Details

    Patient Name: " . $_SESSION["patient_name"] . "

    Doctor: " . $alldoc["doctor_name"] . "

    Date: " . $dt . "

    Time: " . $time . "

    Reason for Visit: " . $reason . "

    Username: " . $allpat["username"] . "

    Payment Status: Paid

If you need to reschedule or have any questions, please feel free to contact us or reply to this email.

We appreciate your trust in us and look forward to providing you with excellent care.

Best Regards,
Team HealthCare
📧 teamhealthcarehospital@gmail.com
📞 +0261 250 5050
🌐 https://www.HealthCare.com";

    $result2 = sendEmail($email, $subject, $msg);
    if ($result2 === true) {
      $alert = true;
      unset($_SESSION['patient_id'], $_SESSION['doctor_id'], $_SESSION['dt'], $_SESSION['time'], $_SESSION['reason']);
    } else {
      echo "<pre>Mail Error: ";
      print_r($result);
      echo "</pre>";
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointment Booking</title>
  <link rel="website icon" href="./assets/images/logo.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Unbounded:wght@200..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <link rel="stylesheet" href="./assets/css/faqquery.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- <link rel="stylesheet" href="./assets/css/media.css"> -->
  <link rel="stylesheet" href="./assets/css/media.css?v=<?php echo time(); ?>">
</head>


<body>
  <?php if ($alert) { ?>
    <script>
      Swal.fire({
        title: "Success!",
        text: "Appointment successfully booked! The appointment details have been sent to your email.",
        icon: "success"
      });
    </script>
  <?php } ?>

  <?php include_once("./appointmentnavbar.php"); ?>

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
              <h4>Book Appointment</h4>
              <h2>Get an Book Appointment</h2>
            </div>
            <form action="./cashfree_payment/pay.php" id="dreamit-form" method="post">
              <div class="inner-form " id="inner-subfrom">
                <div class="singleform">
                  <div class="form-box">
                    <input type="text" name="patient_name" id="patient_name" placeholder="Patient Name*" />
                    <i class="fa-solid fa-user"></i>
                    <span class="error-message" id="patient-error"></span>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <select id="doctor_id" name="doctor_id">
                      <option value="">Select Active Doctors</option>
                      <?php
                      $st1 = mysqli_query($conn, "SELECT * FROM doctors WHERE `status` = 'Active'");
                      while ($row1 = mysqli_fetch_assoc($st1)) {
                      ?>
                        <option value="<?= $row1["doctor_id"] ?>" data-fee="<?= $row1["consultancy_fee"] ?>">
                          <?= $row1["doctor_name"] ?> ( <?= $row1["specilization"] ?> )
                        </option>
                      <?php } ?>
                    </select>
                    <i class="fa-solid fa-user-doctor"></i>
                    <span class="error-message" id="doctor-error"></span>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="date" name="date" id="date" />
                    <i class="fa-solid fa-calendar-days"></i>
                    <span class="error-message" id="date-error"></span>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <select class="form-select" name="appointment_time" id="timeSlots">
                      <option value="">-- Select Time --</option>
                    </select>
                    <i class="fa-solid fa-calendar-days"></i>
                    <span class="error-message" id="time-error"></span>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="text" name="reason" id="reason" placeholder="Your Reason*" />
                    <i class="fa-solid fa-note-sticky"></i>
                    <span class="error-message" id="reason-error"></span>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="text" name="consultancy_fee" id="consultancy_fee" placeholder="Consultancy Fee"
                      readonly />
                    <i class="fa-solid fa-sack-dollar"></i>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="text" name="username" id="username" placeholder="User Name" />
                    <i class="fa-solid fa-user"></i>
                    <span class="error-message" id="username-error"></span>
                  </div>
                </div>

                <div class="singleform">
                  <div class="form-box">
                    <input type="password" name="password" id="password" placeholder="Password" />
                    <i class="fa-solid fa-lock"></i>
                    <span class="error-message" id="password-error"></span>
                  </div>
                </div>

                <div class="booton-from">
                  <div class="check_bx">
                    <input type="checkbox" id="terms" />
                    <span>I agree to the terms and conditions</span>
                    <span class="error-message" id="terms-error"></span>
                  </div>
                  <div class="submit-button1">
                    <button type="submit" name="addAppointment" class="submit-btn1">
                      Book Appointment <i class="fa-solid fa-square-up-right"></i>
                    </button>
                    <button type="reset" value="Reset" class="submit-btn1">
                      Reset <i class="fa-solid fa-square-up-right"></i>
                    </button>
                  </div>
                </div>
              </div>
            </form>

            <style>
              .error-message {
                color: red;
                font-size: 15px;
                display: block;
                margin-top: 5px;
              }
            </style>

            <!-- <script>
  document.getElementById("dreamit-form").addEventListener("submit", function (event) {
    let isValid = true;

    let patientName = document.getElementById("patient_name").value.trim();
    let doctor = document.getElementById("doctor_id").value;
    let date = document.getElementById("date").value;
    let time = document.getElementById("timeSlots").value;
    let reason = document.getElementById("reason").value.trim();
    let username = document.getElementById("username").value.trim();
    let password = document.getElementById("password").value.trim();
    let terms = document.getElementById("terms").checked;

    document.getElementById("patient-error").textContent = "";
    document.getElementById("doctor-error").textContent = "";
    document.getElementById("date-error").textContent = "";
    document.getElementById("time-error").textContent = "";
    document.getElementById("reason-error").textContent = "";
    document.getElementById("username-error").textContent = "";
    document.getElementById("password-error").textContent = "";
    document.getElementById("terms-error").textContent = "";

    let nameRegex = /^[A-Za-z\s]+$/;

    if (patientName === "") {
      document.getElementById("patient-error").textContent = "Patient name is required.";
      isValid = false;
    } else if (!nameRegex.test(patientName)) {
      document.getElementById("patient-error").textContent = "Patient name must contain only letters.";
      isValid = false;
    }

    if (doctor === "") {
      document.getElementById("doctor-error").textContent = "Please select a doctor.";
      isValid = false;
    }

    if (date === "") {
      document.getElementById("date-error").textContent = "Please select a date.";
      isValid = false;
    }

    if (time === "") {
      document.getElementById("time-error").textContent = "Please select an appointment time.";
      isValid = false;
    }

    if (reason === "") {
      document.getElementById("reason-error").textContent = "Reason is required.";
      isValid = false;
    } else if (!nameRegex.test(reason)) {
      document.getElementById("reason-error").textContent = "Reason must contain only letters.";
      isValid = false;
    }

    if (username === "") {
      document.getElementById("username-error").textContent = "Username is required.";
      isValid = false;
    }

    if (password === "") {
      document.getElementById("password-error").textContent = "Password is required.";
      isValid = false;
    }

    if (!terms) {
      document.getElementById("terms-error").textContent = "You must agree to the terms.";
      isValid = false;
    }

    if (!isValid) {
      event.preventDefault();
    }
  });

  document.getElementById("doctor_id").addEventListener("change", function () {
    let selectedOption = this.options[this.selectedIndex];
    let fee = selectedOption.getAttribute("data-fee");
    document.getElementById("consultancy_fee").value = fee ? fee : "";
  });

  setTimeout(() => {
        errorDiv.remove();
    }, 5000);
</script> -->

            <script>
              document.getElementById("dreamit-form").addEventListener("submit", function(event) {
                let isValid = true;

                let patientName = document.getElementById("patient_name").value.trim();
                let doctor = document.getElementById("doctor_id").value;
                let date = document.getElementById("date").value;
                let time = document.getElementById("timeSlots").value;
                let reason = document.getElementById("reason").value.trim();
                let username = document.getElementById("username").value.trim();
                let password = document.getElementById("password").value.trim();
                let terms = document.getElementById("terms").checked;

                let nameRegex = /^[A-Za-z\s]+$/; // Only letters and spaces

                // Function to show errors and auto-hide them
                function showError(fieldId, message) {
                  let errorElement = document.getElementById(fieldId);
                  errorElement.textContent = message;
                  setTimeout(() => {
                    errorElement.textContent = ""; // Clear after 1.5 seconds
                  }, 1500);
                }

                // Clear all previous errors
                document.querySelectorAll(".error-message").forEach(el => el.textContent = "");

                // Patient Name Validation
                if (patientName === "") {
                  showError("patient-error", "Patient name is required.");
                  isValid = false;
                } else if (!nameRegex.test(patientName)) {
                  showError("patient-error", "Patient name must contain only letters.");
                  isValid = false;
                }

                // Doctor Selection Validation
                if (doctor === "") {
                  showError("doctor-error", "Please select a doctor.");
                  isValid = false;
                }

                // Date Validation
                if (date === "") {
                  showError("date-error", "Please select a date.");
                  isValid = false;
                }

                // Time Slot Validation
                if (time === "") {
                  showError("time-error", "Please select an appointment time.");
                  isValid = false;
                }

                // Reason Validation (Only Letters Allowed)
                if (reason === "") {
                  showError("reason-error", "Reason is required.");
                  isValid = false;
                } else if (!nameRegex.test(reason)) {
                  showError("reason-error", "Reason must contain only letters.");
                  isValid = false;
                }

                // Username Validation
                if (username === "") {
                  showError("username-error", "Username is required.");
                  isValid = false;
                }

                // Password Validation
                if (password === "") {
                  showError("password-error", "Password is required.");
                  isValid = false;
                }

                // Terms & Conditions Validation
                if (!terms) {
                  showError("terms-error", "You must agree to the terms.");
                  isValid = false;
                }

                // Prevent form submission if validation fails
                if (!isValid) {
                  event.preventDefault();
                }
              });

              // Update consultancy fee when doctor is selected
              document.getElementById("doctor_id").addEventListener("change", function() {
                let selectedOption = this.options[this.selectedIndex];
                let fee = selectedOption.getAttribute("data-fee");
                document.getElementById("consultancy_fee").value = fee ? fee : "";
              });
            </script>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end appoinment-section -->

  <?php include_once("./servicefooter.php"); ?>

  <script>
    document.addEventListener("DOMContentLoaded", setMinDate);

    function setMinDate() {
      let dtInput = document.getElementById("date");
      let now = new Date();

      let year = now.getFullYear();
      let month = String(now.getMonth() + 1).padStart(2, '0');
      let day = String(now.getDate()).padStart(2, '0');

      let minDate = `${year}-${month}-${day}`; // Format as YYYY-MM-DD
      dtInput.setAttribute("min", minDate); // Set the min attribute
    }

    document.getElementById("doctor_id").addEventListener("change", function() {
      let selectedDoctor = this.options[this.selectedIndex];
      let fee = selectedDoctor.getAttribute("data-fee") || "N/A"; // Get fee or show "N/A"
      document.getElementById("consultancy_fee").value = fee;
    });
    document.getElementById("doctor_id").addEventListener("change", loadTimeSlots);
    document.getElementById("date").addEventListener("change", loadTimeSlots);

    function loadTimeSlots() {
      let doctorId = document.getElementById("doctor_id").value;
      let appointmentDate = document.getElementById("date").value;

      if (doctorId && appointmentDate) {
        fetch(`get_available_slots.php?doctor_id=${doctorId}&appointment_date=${appointmentDate}`)
          .then(response => response.json())
          .then(data => {
            let timeSelect = document.getElementById("timeSlots");
            timeSelect.innerHTML = '<option value="">-- Select Time --</option>';

            if (data.error) {
              alert(data.error);
            } else {
              data.forEach(time => {
                let option = document.createElement("option");
                option.value = time;
                option.textContent = time;
                timeSelect.appendChild(option);
              });

              if (data.length === 0) {
                Swal.fire({
                  title: "Available Time not found",
                  text: ` Please select another date.`,
                  icon: "warning",
                  confirmButtonText: "Understand"
                });
              }
            }
          })
          .catch(error => console.error('Error:', error));
      }
    }

    document.addEventListener("DOMContentLoaded", function() {
      // Get today's date
      let today = new Date();

      // Set the maximum allowed date (exactly 1 month from today)
      let maxAllowedDate = new Date(today);
      maxAllowedDate.setMonth(today.getMonth() + 1); // Add 1 month

      // Convert dates to the required format (YYYY-MM-DD)
      let minDateStr = today.toISOString().split("T")[0];
      let maxDateStr = maxAllowedDate.toISOString().split("T")[0];

      // Format maxAllowedDate to DD/MM/YYYY for the alert
      let formattedMaxDate = maxAllowedDate.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric"
      });

      // Set the minimum selectable date to today
      let dateInput = document.getElementById("date");
      dateInput.setAttribute("min", minDateStr);

      // Add event listener to validate the selected date
      dateInput.addEventListener("change", function() {
        let selectedDate = new Date(dateInput.value);

        // Check if the selected date is beyond the allowed range
        if (selectedDate > maxAllowedDate) {
          Swal.fire({
            title: "Invalid Date Selection",
            text: `Appointments can only be scheduled up to ${formattedMaxDate}. Please select a valid date within this range.`,
            icon: "warning",
            confirmButtonText: "Got it"
          });

          // Reset the date input field
          dateInput.value = "";
        }
      });
    });
  </script>

</body>

</html>