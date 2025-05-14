<?php
session_start();
unset($_SESSION['receptionist'],$_SESSION["receptionist_pass"]);
header("location:../index.php");
exit();
?>