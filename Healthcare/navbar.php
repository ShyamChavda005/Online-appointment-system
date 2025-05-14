<?php
session_start();
include_once('../config.php');
$conn = connection();

if (isset($_SESSION["user"])) {
    $uname = $_SESSION["user"];
    $Q1 = mysqli_query($conn, "SELECT * FROM patients WHERE username = '$uname'");
    $patients = mysqli_fetch_assoc($Q1);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="website icon" href="./assets/images/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Unbounded:wght@200..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./assets/css/navbar.css">

    <link rel="stylesheet" href="./assets/css/media.css?v=<?php echo time(); ?>">

    <style>
        /* Hide menu items on smaller screens (less than 768px) */
        @media (max-width: 768px) {
            .menu-inner {
                display: none;
                /* Hides the original navbar items */
            }

            /* Show the offcanvas menu button on mobile */
            .navbar-toggler {
                display: block;
            }
        }

        /* Show menu items on larger screens */
        @media (min-width: 768px) {
            .menu-inner {
                display: block;
                /* Show the original navbar items */
            }

            .navbar-toggler {
                display: none;
                /* Hide the toggle button on large screens */
            }
        }

        @media (min-width: 768px) {
            .menu-inner {
                display: block;
                display: flex;
            }
        }

        .offcanvas.offcanvas-end {
            width: 380px !important;
        }

        .offcanvas-body {
            background-color: #082340;
        }
    </style>
</head>

<body>
    <!-- start toper header -->
    <div id="content" id="allcolor">
        <div class="toper_area">
            <div class="container">
                <div class="main-rowtoper">
                    <div class="innertop-row">
                        <div class="toper-textleft">
                            <p>
                                <img src="assets/img/top-shape.png" alt="logtoper" />
                                Welcome Healthcare hospital and doctors appointment services
                            </p>
                        </div>
                    </div>
                    <div class="innertop-row2">
                        <div class="toper-textright">
                            <p>
                                <span>
                                    <i class="fa-solid fa-location-dot"></i>
                                </span>
                                HealthcareHospital ,Surat
                                <span class="right-info">
                                    <i class="fa-regular fa-envelope"></i>
                                </span>
                                <a class="email-link"
                                    href="mailto: teamhealthcarehospital@gmail.com">teamhealthcarehospital@gmail.com</a>

                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end toper header -->

        <!-- start main header -->
        <div class="main-header">
            <div class="secound-header">
                <div class="right-header">
                    <div class="log-header">
                        <a href="#" class="loge_img">
                            <img src="assets/img/logo (2).png" width="100%" height="50px" alt="logimg" />
                        </a>
                    </div>
                </div>
                <div class="left-header">
                    <nav class="meedy_menu">
                        <div class="main-nemu_nav">
                            <ul class="menu-inner" id="tako">
                                <li><a href="index.php">Home</a></li>
                                <li><a href="mainservice.php">Services</a></li>
                                <li><a href="aboutindex.php">About</a></li>
                                <li><a href="faqindex.php">FAQ</a></li>
                                <li><a href="contactindex.php">ContactUs</a></li>
                                <div class="header-button">
                                    <a href="./appointmentform.php">Book Appointment
                                        <div class="mediket-hover-btn hover-btn"></div>
                                        <div class="mediket-hover-btn hover-btn2"></div>
                                        <div class="mediket-hover-btn hover-btn3"></div>
                                        <div class="mediket-hover-btn hover-btn4"></div>
                                    </a>
                                </div>
                                <?php
                                if (!isset($_SESSION["user"])) {
                                    ?>
                                    <div class="header-button1">
                                        <a href="login.php">LOGIN
                                            <div class="mediket-hover-btn hover-btn"></div>
                                            <div class="mediket-hover-btn hover-btn2"></div>
                                            <div class="mediket-hover-btn hover-btn3"></div>
                                            <div class="mediket-hover-btn hover-btn4"></div>
                                        </a>
                                    </div>
                                <?php } ?>
                                <?php
                                if (isset($_SESSION["user"])) {
                                    ?>
                                    <!-- <div class=""> -->
                                    <div class="header-button2" id="btnapp">
                                        <a href="./patient_pannel/Dashboard.php"> <?= $patients["patient_name"]; ?>
                                            <div class="mediket-hover-btn hover-btn"></div>
                                            <div class="mediket-hover-btn hover-btn2"></div>
                                            <div class="mediket-hover-btn hover-btn3"></div>
                                            <div class="mediket-hover-btn hover-btn4"></div>
                                        </a>
                                    </div>


                                    <?php
                                } ?>
                                <!-- </div> -->
                                <?php
                                if (isset($_SESSION["user"])) {
                                    ?>
                                    <div class="header-button1">
                                        <a href="logout.php">LOGOUT
                                            <div class="mediket-hover-btn hover-btn"></div>
                                            <div class="mediket-hover-btn hover-btn2"></div>
                                            <div class="mediket-hover-btn hover-btn3"></div>
                                            <div class="mediket-hover-btn hover-btn4"></div>
                                        </a>
                                    </div>
                                <?php } ?>
                            </ul>

                            <!-- Navbar toggle button -->
                            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                                data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar"
                                aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon">
                                    <i class="fa-solid fa-align-left"></i>
                                </span>
                            </button>

                            <!-- Offcanvas -->
                            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                                aria-labelledby="offcanvasNavbarLabel">
                                <div class="offcanvas-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body">
                                    <ul class="tako">
                                        <li><a href="index.php">Home</a></li>
                                        <li><a href="mainservice.php">Services</a></li>
                                        <li><a href="aboutindex.php">About</a></li>
                                        <li><a href="faqindex.php">FAQ</a></li>
                                        <li><a href="contactindex.php">ContactUs</a></li>
                                        <div class="header-button">
                                            <div class="header-button" id="btn-tog">
                                                <a href="./appointmentform.php">Book Appointment
                                                    <div class="mediket-hover-btn hover-btn"></div>
                                                    <div class="mediket-hover-btn hover-btn2"></div>
                                                    <div class="mediket-hover-btn hover-btn3"></div>
                                                    <div class="mediket-hover-btn hover-btn4"></div>
                                                </a>
                                            </div>
                                            <?php
                                            if (!isset($_SESSION["user"])) {
                                                ?>
                                                <div class="header-button1" id="btn-tog1">
                                                    <a href="login.php" class="logbtn">LOGIN
                                                        <div class="mediket-hover-btn hover-btn"></div>
                                                        <div class="mediket-hover-btn hover-btn2"></div>
                                                        <div class="mediket-hover-btn hover-btn3"></div>
                                                        <div class="mediket-hover-btn hover-btn4"></div>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                            <?php
                                            if (isset($_SESSION["user"])) {
                                                ?>
                                                <!-- <div class=""> -->
                                                <div class="header-button2" id="btnapp" id="btn-tog1">
                                                    <a href="./patient_pannel/Dashboard.php" class="logbtn">
                                                        <?= $patients["patient_name"]; ?>
                                                        <div class="mediket-hover-btn hover-btn"></div>
                                                        <div class="mediket-hover-btn hover-btn2"></div>
                                                        <div class="mediket-hover-btn hover-btn3"></div>
                                                        <div class="mediket-hover-btn hover-btn4"></div>
                                                    </a>
                                                </div>


                                                <?php
                                            } ?>
                                            <!-- </div> -->
                                            <?php
                                            if (isset($_SESSION["user"])) {
                                                ?>
                                                <div class="header-button1" id="btn-tog2">
                                                    <a href="logout.php" class="logbtn">LOGOUT
                                                        <div class="mediket-hover-btn hover-btn"></div>
                                                        <div class="mediket-hover-btn hover-btn2"></div>
                                                        <div class="mediket-hover-btn hover-btn3"></div>
                                                        <div class="mediket-hover-btn hover-btn4"></div>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                    </ul>
                                </div>
                            </div>


                        </div>
                    </nav>
                </div>
            </div>
        </div>
        <!-- end main header -->
    </div>
</body>

</html>