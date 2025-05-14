<?php
define("CASHFREE_APP_ID", "TEST106129236bd3ef181dec8b45968132921601");
define("CASHFREE_SECRET_KEY", "cfsk_ma_test_5399214271b6ea45201df6ed7112065f_cac7c49e");
define("CASHFREE_API_URL", "https://sandbox.cashfree.com/pg/orders"); // Use production URL when live

// Database Connection
$conn = new mysqli("localhost", "root", "", "healthcare");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>