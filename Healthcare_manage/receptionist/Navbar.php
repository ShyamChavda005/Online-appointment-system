<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Panel - Healthcare</title>
    <link rel="stylesheet" href="./style/Navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
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

    <!-- Toggle Button for Small Screens -->
    <button class="btn btn-primary" id="menu-btn" onclick="toggleSidebar()">☰ Menu</button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <img src="./../assets/images/logo.png" alt="Logo">
            <span>HealthCare</span>
        </div>

        <ul class="menu">
            <li>
                <a href="Dashboard.php" class="<?= ($current_page == 'Dashboard.php') ? 'active' : '' ?>">
                    <i class="bi bi-house-door me-2"></i> Dashboard
                </a>
            </li>

            <li>
                <a href="viewpatients.php" class="<?= ($current_page == 'viewpatients.php') ? 'active' : '' ?>">
                    <i class="bi bi-person-circle me-2"></i> Patients
                </a>
            </li>

            <li class="has-submenu">
                <a href="#" onclick="toggleSubmenu(event)" class="<?= ($current_page == 'ViewAppointment.php' || $current_page == 'UpcomingAppointment.php' || $current_page == 'AddAppointment.php') ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check me-2"></i> Appointments
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="UpcomingAppointment.php"><i class="bi bi-calendar-event me-2"></i> Upcoming </a></li>
                    <li><a href="ViewAppointment.php"><i class="bi bi-clock-history me-2"></i> History</a></li>
                    <li><a href="AddAppointment.php"><i class="bi bi-plus-circle me-2"></i> New Appointment</a></li>
                </ul>
            </li>

            <li class="has-submenu">
                <a href="#" onclick="toggleSubmenu(event)" class="<?= ($current_page == 'Viewdoctor_schedule.php' || $current_page == 'ViewLeaves.php') ? 'active' : '' ?>">
                    <i class="bi bi-hospital me-2"></i> Doctor Scheduling
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <ul class="submenu">
                    <li><a href="./Viewdoctor_schedule.php"><i class="bi bi-calendar-range me-2"></i> Schedules</a></li>
                    <li><a href="./ViewLeaves.php"><i class="bi bi-calendar-x me-2"></i> Leave Requests</a></li>
                </ul>
            </li>

            <li>
                <a href="viewtask.php" class="<?= ($current_page == 'viewtask.php') ? 'active' : '' ?>">
                <i class="bi bi-list-check me-2"></i> Tasks
                </a>
            </li>

            <li>
                <a href="Payment.php" class="<?= ($current_page == 'Payment.php') ? 'active' : '' ?>">
                    <i class="bi bi-credit-card me-2"></i> Billing & Invoices
                </a>
            </li>

        </ul>
    </div>
</body>

</html>