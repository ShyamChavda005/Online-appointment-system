<?php 
if (!isset($_SESSION["admin"])) {
  header("location:../index.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Healthcare - Admin</title>
  <link rel="stylesheet" href="./style/Navbar.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('open');
    }

    function toggleSubmenu(event) {
            event.preventDefault();
            const parentLi = event.currentTarget.closest(".has-submenu");
            const submenu = parentLi.querySelector(".submenu");
            const arrow = parentLi.querySelector(".arrow");

            submenu.classList.toggle("open");

            // Rotate arrow only when submenu is open
            if (submenu.classList.contains("open")) {
                arrow.style.transform = "rotate(180deg)";
            } else {
                arrow.style.transform = "rotate(0deg)";
            }
        }
  </script>
</head>

<body>
  <?php
  // Get the current page name
  $current_page = basename($_SERVER['PHP_SELF']);
  ?>

  <button class="menu-btn" onclick="toggleSidebar()">☰</button>

  <div class="sidebar" id="sidebar">
    <div class="logo">
      <img src="./../assets/images/logo.png" alt="HealthCare Logo" class="logo-img" />
      <a href="./Dashboard.php" style="text-decoration: none;"> <span class="logo-text">HealthCare</span></a>
    </div>

    <ul class="menu">
      <li><a href="./Dashboard.php" class="<?= ($current_page == 'Dashboard.php') ? 'active' : '' ?>">
          <img src="./../assets/images/dashboard.svg" alt=""> Dashboard </a></li>

      <li class="has-submenu">
        <a href="#" onclick="toggleSubmenu(event)" class="<?= ($current_page == 'ViewPatient.php' || $current_page == 'AddPatient.php') ? 'active' : '' ?>">
          <img src="./../assets/images/patient.svg" alt="" height="25"> Patients
          <i class="bi bi-chevron-down arrow"></i>
        </a>
        <ul class="submenu">
          <li><a href="./ViewPatient.php"><i class="bi bi-clock-history me-3"></i>View Patients</a></li>
          <li><a href="./AddPatient.php"><i class="bi bi-plus-circle me-3"></i>Add Patient</a></li>
        </ul>
      </li>

      <li class="has-submenu">
        <a href="#" onclick="toggleSubmenu(event)" class="<?= ($current_page == 'ViewDoctor.php' || $current_page == 'AddDoctor.php' || $current_page == 'Viewdoctor_schedule.php' || $current_page == 'ViewLeaves.php') ? 'active' : '' ?>">
          <img src="./../assets/images/doctor.svg" alt="" height="25"> Doctors
          <i class="bi bi-chevron-down arrow"></i>
        </a>
        <ul class="submenu">
          <li><a href="./ViewDoctor.php"><i class="bi bi-clock-history me-3"></i>View Doctors</a></li>
          <li><a href="./AddDoctor.php"><i class="bi bi-plus-circle me-3"></i>Add Doctor</a></li>
          <li><a href="./Viewdoctor_schedule.php"><i class="bi bi-calendar-check me-3"></i>Scheduled Doctor</a></li>
          <li><a href="./ViewLeaves.php"><i class="bi bi-calendar-check me-3"></i>Leaved Doctor</a></li>
        </ul>
      </li>

      <li class="has-submenu">
        <a href="#" onclick="toggleSubmenu(event)" class="<?= ($current_page == 'ViewAppointment.php' || $current_page == 'UpcomingAppointment.php' || $current_page == 'AddAppointment.php' || $current_page == 'TodayAppointment.php') ? 'active' : '' ?>">
          <img src="./../assets/images/appointment.svg" alt=""> Appointments
          <i class="bi bi-chevron-down arrow"></i>
        </a>
        <ul class="submenu">
          <li><a href="./TodayAppointment.php"><i class="bi bi-clock-history me-3"></i>Today's Appointments</a></li>
          <li><a href="./ViewAppointment.php"><i class="bi bi-clock-history me-3"></i>All Appointments</a></li>
          <li><a href="./AddAppointment.php"><i class="bi bi-plus-circle me-3"></i>Add Appointment</a></li>
        </ul>
      </li>

      <li class="has-submenu">
        <a href="#" onclick="toggleSubmenu(event)" class="<?= ($current_page == 'ViewReceptionist.php' || $current_page == 'AddReceptionist.php') ? 'active' : '' ?>">
          <img src="./../assets/images/reception.svg" alt="" height="25"> Receptionist
          <i class="bi bi-chevron-down arrow"></i>
        </a>
        <ul class="submenu">
          <li><a href="./ViewReceptionist.php"><i class="bi bi-clock-history me-3"></i>View Receptionist</a></li>
          <li><a href="./AddReceptionist.php"><i class="bi bi-plus-circle me-3"></i>Add Receptionist</a></li>
        </ul>
      </li>

      <li>
        <a href="./Query.php" class="<?= ($current_page == 'Query.php') ? 'active' : '' ?>">
          <img src="./../assets/images/query.svg" alt=""> Query
        </a>
      </li>

      <li>
        <a href="./Feedback.php" class="<?= ($current_page == 'Feedback.php') ? 'active' : '' ?>">
          <img src="./../assets/images/feedback.svg" alt=""> Feedback
        </a>
      </li>

      <li>
        <a href="./Payment.php" class="<?= ($current_page == 'Payment.php') ? 'active' : '' ?>">
          <img src="./../assets/images/payment.svg" alt=""> Payment
        </a>
      </li>

      <li>
        <a href="./Activity.php" class="<?= ($current_page == 'Activity.php') ? 'active' : '' ?>">
          <img src="./../assets/images/history.svg" alt=""> Daily Activity
        </a>
      </li>
    </ul>
  </div>
</body>

</html>