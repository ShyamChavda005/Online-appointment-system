<?php
session_start();
unset($_SESSION["user"],$_SESSION["pass"]);
// session_destroy();  
header("Location:index.php");
exit();
?>