<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "healthcare");

if (!$conn) {
    echo 'Database Not Connecting';
}
$alert = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hash = hash("sha256",$password);

    if ($role == "Admin") {
        $Q1 = "SELECT * FROM `admin` WHERE username ='$username' AND `password`= '$hash'";
        $query1 = mysqli_query($conn, $Q1);

        if (mysqli_num_rows($query1) > 0) {
            $_SESSION['admin'] = $username;
            $_SESSION['admin_pass'] = $password;
            header("Location: ./admin/Dashboard.php");
            exit();
        } else {
            header("Location: index.php?error=Invalid Username or Password");
            exit();
        }
    } elseif ($role == "Doctor") {
        $Q2 = "SELECT * from doctors WHERE username ='$username' AND `password`= '$hash'";
        $query2 = mysqli_query($conn, $Q2);
        $doctor = mysqli_fetch_assoc($query2);

        if (mysqli_num_rows($query2) > 0) {
            if ($doctor["status"] == "Active") {
                $_SESSION['doctor'] = $username;
                $_SESSION["doctor_pass"] = $password;
                header("Location:./doctor/Dashboard.php");
                exit();
            } else {
                $alert = true;
            }
        } else {
            header("Location: index.php?error=Invalid Username or Password");
            exit();
        }
    } elseif ($role == "Receptionist") {
        $Q3 = "SELECT * from receptionist where username ='$username' and `password`= '$hash'";
        $query3 = mysqli_query($conn, $Q3);
        $receptionist = mysqli_fetch_assoc($query3);

        if (mysqli_num_rows($query3) > 0) {
            if ($receptionist["status"] == "Active") {
                $_SESSION['receptionist'] = $username;
                $_SESSION['receptionist_pass'] = $password;
                header("Location:./receptionist/Dashboard.php");
                exit();
            } else {
                $alert = true;
            }
        } else {
            header("Location: index.php?error=Invalid Username or Password");
            exit();
        }
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
</head>

<body>
    <?php if ($alert) { ?>
        <script>
            Swal.fire({
                text: "You Suspended For Some Reason Try to Contact with Healthcare !",
                icon: "warning"
            });
            setTimeout(() => {
                window.location.href = "index.php";
            }, 3000);
        </script>
    <?php } ?>
</body>

</html>