<?php
session_start();
unset($_SESSION["admin"],$_SESSION["admin_pass"]);
// session_destroy();
header("location:../index.php");
exit();
?>