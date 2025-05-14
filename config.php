<?php
// config_sample.php

// === DATABASE SETTINGS ===
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'healthcare');

function connection()
{
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}

// === SMTP SETTINGS ===
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your_email@gmail.com');
define('SMTP_PASS', 'your_app_password');  // Use App Password for Gmail not Email password
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
?>
