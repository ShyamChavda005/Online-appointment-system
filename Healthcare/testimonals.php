<?php
include_once('../config.php');
$conn = connection();

if (!$conn) {
  echo "Connection Error !";
}
$query = mysqli_query($conn, "SELECT * FROM feedback where `status`= 'Show'");
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
  <link rel="stylesheet" href="./assets/css/testimonals.css">
  <!-- <link rel="stylesheet" href="./assets/css/media.css"> -->
  <link rel="stylesheet" href="./assets/css/media.css?v=<?php echo time(); ?>">

</head>

<body>
  <!-- strat testimonial-section -->
  <div class="testimonial-section">
    <div class="container">
      <div class="main-testimonial">
        <div class="left-testimonial">
          <div class="testi-thumb">
            <img src="assets/img/tesi_img.png" alt="" />
            <div class="countsingle-box2">
              <div class="countr-inco">
                <img src="assets/img/testi-icon.png" alt="" />
              </div>
              <div class="counttitle">
                <h2>6K+</h2>
              </div>
              <div class="count-info">
                <p>Our All Customers</p>
              </div>
            </div>
            <div class="testi-shpinfo">
              <img src="assets/img/testi-shape.png" alt="" />
            </div>
          </div>
        </div>
        <div class="right-Testimonals">
          <div class="testimonalstitle">
            <h4>FeedBack</h4>
            <h2>Trusted Care and Proven Satisfactions</h2>
          </div>

          <div class="slider-services">
            <div class="owl-carousel owl-theme" id="left-slider">

              <?php
              while ($feedbacks = mysqli_fetch_assoc($query)) {
              ?>

                <div class="swipperslider">
                  <div class="testi-content">
                    <div class="testi-quote">
                      <img src="assets/img/arrow.png" alt="" />
                    </div>
                    <div class="testi-desc">
                      <p>
                        <?= $feedbacks["feedback"] ?>
                      </p>
                    </div>
                    <ul class="testi-rating">
                      <li>
                        <i class="fa-solid fa-star"></i>
                      </li>

                      <li>
                        <i class="fa-solid fa-star"></i>
                      </li>

                      <li>
                        <i class="fa-solid fa-star"></i>
                      </li>

                      <li>
                            <i class="fa-solid fa-star"></i>
                          </li>
      
                          <li>
                            <i class="fa-solid fa-star"></i>
                          </li>
                    </ul>
                  </div>
                  <div class="user-info">
                    <div class="people-pic">
                      <img src="assets/img/feedback.png" alt=""/ >
                      <!-- <i class="fa-solid fa-person"></i> -->
                    </div>
                    <div class="user-name">
                      <h4> <?= ucwords($feedbacks["name"]) ?></h4>
                      <h6> <?= $feedbacks["email"] ?></h6>
                    </div>
                  </div>
                </div>
              <?php
              }
              ?>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
  <!-- end testimonial-section -->
</body>

</html>