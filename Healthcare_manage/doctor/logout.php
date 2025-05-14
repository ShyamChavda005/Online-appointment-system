<?php
session_start();
unset($_SESSION["docotor"],$_SESSION["doctor_pass"]);
header("location:../index.php");
exit();
?>