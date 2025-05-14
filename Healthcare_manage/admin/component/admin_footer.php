<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/YOUR_FONT_AWESOME_KIT.js" crossorigin="anonymous"></script>
    <style>
        .footer {
            background-color: rgb(3, 95, 194);
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        .footer a {
            color: #F8F9FA;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: #00D4FF;
            text-decoration: underline;
        }


        .social-icons a {
            color: white;
            font-size: 20px;
            margin: 0 10px;
            transition: transform 0.3s ease;
        }

        .social-icons a:hover {
            transform: scale(1.2);
            color: #FFD700;
        }

        @media (max-width: 768px) {
            .footer {
                text-align: center;
            }

            .footer-links {
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>

    <footer class="footer">
        <img src="./../assets/images/logo.png" class="img-fluid d-block mx-auto mb-3 shadow-sm" style="max-height: 50px;" alt="Company Logo">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><strong>HealthCare</strong></h5>
                    <p>Your trusted healthcare partner.</p>
                </div>

                <div class="col-md-4 footer-links">
                    <h5>Quick Links</h5>
                    <a href="../../HealthCare/index.php">Go to Home</a> |
                    <a href="../index.php">Doctors</a> |
                    <a href="../index.php">Receptionist</a>
                </div>

                <div class="col-md-4 footer-links">
                    <h5>Follow Us</h5>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <hr class="bg-light">
            <p>2025 &copy; <strong>HealthCare</strong>. Designed & Built by <a class="text-warning" href="./Dashboard.php">Team PSDK</a>.</p>
        </div>
    </footer>

    <script src="https://kit.fontawesome.com/YOUR_FONT_AWESOME_KIT.js" crossorigin="anonymous"></script>
</body>

</html>