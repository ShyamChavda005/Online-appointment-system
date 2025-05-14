<?php
include_once('../config.php');
$conn = connection();

if (!$conn) {
  echo "Connection Error !";
}

$query = mysqli_query($conn, "SELECT * FROM doctors where `status`= 'Active'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Unbounded:wght@200..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/doctors.css">
  <!-- <link rel="stylesheet" href="./assets/css/media.css"> -->
  <link rel="stylesheet" href="./assets/css/media.css?v=<?php echo time(); ?>">
</head>

<body>
  <!-- strat Doctors team-section -->
  <div class="team-section">
    <div class="container">
      <div class="maintop-tem">
        <div class="left-tem">
          <div class="doctorsselection">
            <h4>Meet Doctors</h4>
            <h2>Delivering Quality Health’s Meet Our Doctors</h2>
          </div>
        </div>
        <div class="right-doctors">
          <div class="mediket-btn6">
            <a href="aboutindex.php">
              All Doctors
              <i class="fa-solid fa-square-up-right">
                <div></div>
              </i>
            </a>
          </div>
        </div>
      </div>
      <div class="botoon-doctor">

        <?php
        while ($doctors = mysqli_fetch_assoc($query)) {
        ?>
          <div class="doctor-part">
            <div class="team-single-box">
              <div class="team-thumb">
                <img src="../Healthcare_manage/assets/doctorphotos/<?= $doctors["photo"] ?>" alt="" />
              </div>
              <div class="team-content">
                <h2><?= $doctors["doctor_name"] ?></h2>
                <h5 class="dtilte"><?= $doctors["specilization"] ?></h5>
                <div class="social-icon">
                  <ul>
                    <li>
                      <a href="#">
                        <i class="fa-brands fa-facebook"></i>
                      </a>
                    </li>

                    <li>
                      <a href="#">
                        <i class="fa-brands fa-linkedin"></i>
                      </a>
                    </li>

                    <li>
                      <a href="#">
                        <i class="fa-brands fa-behance"></i>
                      </a>
                    </li>

                    <li>
                      <a href="#">
                        <i class="fa-brands fa-x-twitter"></i>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        <?php
        }
        ?>

      </div>
    </div>
  </div>
  <!-- end Doctors team-section -->
</body>

</html>