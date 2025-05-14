<?php
session_start();
include_once("../../config.php");
$conn = connection();
$ban = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $q = "SELECT * from patients where username ='$username' and `password`= '$password'";
    $query = mysqli_query($conn, $q);
    $data = mysqli_fetch_assoc($query);

    if (mysqli_num_rows($query) > 0) {
        if ($data["status"] == "Active") {
            $_SESSION['user'] = $data['username'];
            header("Location:Dashboard.php");
            exit();
        } else {
            $ban = true;
        }
    } else {
        header("Location: login.php?error=Invalid Username or Password");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <?php if ($ban) { ?>
        <script>
            Swal.fire({
                text: "You Suspended For Some Reason Try to Contact with Healthcare !",
                icon: "warning"
            });

            setTimeout(() => {
                window.location.href = "Login.php";
            }, 3000);
        </script>
    <?php } ?>
</body>

</html>