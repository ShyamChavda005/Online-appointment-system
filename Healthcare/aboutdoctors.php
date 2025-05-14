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

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Unbounded:wght@200..900&display=swap"
            rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link rel="stylesheet" href="./assets/css/aboutdoctors.css">
        <link rel="stylesheet" href="./assets/css/media.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/brands.min.css" integrity="sha512-58P9Hy7II0YeXLv+iFiLCv1rtLW47xmiRpC1oFafeKNShp8V5bKV/ciVtYqbk2YfxXQMt58DjNfkXFOn62xE+g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>

<body>
    <div class="doctors-about">
        <div class="container">
            <div class="main-aboutdoctors">
                <div class="top-doctors">
                    <div class="doctors-text">
                        <h4>
                            Meet Doctors
                        </h4>
                        <h1>
                            Meet Our  Specialists Doctors
                        </h1>
                    </div>
                </div>
            </div>
            <div class="bottom-doctors">
                <?php
                while ($doctors = mysqli_fetch_assoc($query)) {
                ?>
                    <div class="doctors-box">
                        <div class="inner-doctors">
                            <div class="doctors-team">
                                <img src="../Healthcare_manage/assets/doctorphotos/<?= $doctors["photo"] ?>" alt="doctors">
                            </div>
                            <div class="doctorsteam-content">
                                <h2><?= $doctors["doctor_name"] ?></h2>
                                <h5><?= $doctors["specilization"] ?></h5>
                                <div class="team-btn">
                                    <a href="appointmentform.php">
                                        <i class="fa-solid fa-clipboard-question"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>