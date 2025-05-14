<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Healthcare - Admin</title>
  <link rel="stylesheet" href="./style/Navbar.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
  <?php $current_page = basename($_SERVER['PHP_SELF']); ?>

  <button class="menu-btn" onclick="toggleSidebar()">☰</button>

  <div class="sidebar" id="sidebar">
    <div class="logo">
      <img src="./../assets/images/logo.png" alt="Logo">
      <span>HealthCare</span>
    </div>

    <ul class="menu">
      <li>
        <a href="Dashboard.php" class="<?= ($current_page == 'Dashboard.php') ? 'active' : '' ?>">
          <i class="bi bi-house-door me-2"></i> Home
        </a>
      </li>

      <li>
        <a href="viewpatient.php" class="<?= ($current_page == 'viewpatient.php') ? 'active' : '' ?>">
          <i class="bi bi-person-circle me-2"></i> Patients
        </a>
      </li>

      <li class="has-submenu">
        <a href="#" onclick="toggleSubmenu(event)" class="<?= ($current_page == 'ViewAppointment.php' || $current_page == 'UpcomingAppointment.php') ? 'active' : '' ?>">
          <i class="bi bi-calendar-event me-2"></i> Appointments
          <i class="bi bi-chevron-down arrow"></i>
        </a>
        <ul class="submenu">
          <li><a href="UpcomingAppointment.php"><i class="bi bi-calendar-check me-3"></i>Upcoming</a></li>
          <li><a href="ViewAppointment.php"><i class="bi bi-clock-history me-3"></i>History</a></li>
        </ul>
      </li>

      <li class="has-submenu">
        <a href="#" onclick="toggleSubmenu(event)" class="<?= ($current_page == 'viewdoctor_schedule.php' || $current_page == 'updatedoctor_schedule.php') ? 'active' : '' ?>">
          <i class="bi bi-clock me-2"></i> Doctor Schedule
          <i class="bi bi-chevron-down arrow"></i>
        </a>
        <ul class="submenu">
          <li><a href="viewdoctor_schedule.php"><i class="bi bi-calendar-week me-3"></i>View Schedule</a></li>
          <li><a href="updatedoctor_schedule.php"><i class="bi bi-pencil-square me-3"></i>Update Schedule</a></li>
        </ul>
      </li>

      <li class="has-submenu">
        <a href="#" onclick="toggleSubmenu(event)" class="<?= ($current_page == 'ViewLeaves.php' || $current_page == 'AddLeave.php') ? 'active' : '' ?>">
          <i class="bi bi-person-check me-2"></i> Doctor Leave
          <i class="bi bi-chevron-down arrow"></i>
        </a>
        <ul class="submenu">
          <li><a href="ViewLeaves.php"><i class="bi bi-calendar-x me-3"></i>View Leaves</a></li>
          <li><a href="AddLeave.php"><i class="bi bi-plus-circle me-3"></i>Add Leave</a></li>
        </ul>
      </li>

      <li class="has-submenu">
        <a href="#" onclick="toggleSubmenu(event)" class="<?= ($current_page == 'addrequest.php' || $current_page == 'viewrequest.php') ? 'active' : '' ?>">
          <i class="bi bi-card-checklist me-2"></i> Appointment Request
          <i class="bi bi-chevron-down arrow"></i>
        </a>
        <ul class="submenu">
          <li><a href="viewrequest.php"><i class="bi bi-clipboard-check me-3"></i>View Request</a></li>
          <li><a href="addrequest.php"><i class="bi bi-plus-circle me-3"></i>Add Request</a></li>
        </ul>
      </li>

      <li>
        <a href="Payment.php" class="<?= ($current_page == 'Payment.php') ? 'active' : '' ?>">
          <i class="bi bi-credit-card me-2"></i> Billing
        </a>
      </li>
    </ul>
  </div>
</body>

</html>