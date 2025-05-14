<?php
define("CASHFREE_APP_ID", "your_api_key_here");
define("CASHFREE_SECRET_KEY", "your_secret_key_here");
define("CASHFREE_API_URL", "https://sandbox.cashfree.com/pg/orders"); // Use production URL when live

// Database Connection
$conn = new mysqli("localhost", "root", "", "healthcare");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>