<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}
include_once('../../config.php');
$conn = connection();
$Nochange = false;
$alert = false;
$exits = false;
$redirect = false;

$query = mysqli_query($conn, "SELECT * FROM `admin`");
$admin = mysqli_fetch_assoc($query);

if (isset($_REQUEST["save"])) {
    $aid = $admin["admin_id"];
    $newUsername = $_REQUEST["username"];

    // Check if the username is changed and exists in the database
    $checkUsernameQuery = mysqli_query($conn, "SELECT * FROM `admin` WHERE username='$newUsername' AND admin_id != $aid");

    if (mysqli_num_rows($checkUsernameQuery) > 0) {
        $exits = true; // Username already taken
    } else {
        $id = $admin["admin_id"];
        $fname = isset($_REQUEST["fname"]) ? $_REQUEST["fname"] : $admin["fname"];
        $email = isset($_REQUEST["email"]) ? $_REQUEST["email"] : $admin["email"];
        $phone = isset($_REQUEST["phone"]) ? $_REQUEST["phone"] : $admin["phone"];
        $role = isset($_REQUEST["role"]) ? $_REQUEST["role"] : $admin["role"];
        $username = isset($_REQUEST["username"]) ? $_REQUEST["username"] : $admin["username"];
        $password = $admin["password"];

        if (!empty($_REQUEST["password"])) {
            $newPassword = $_REQUEST["password"];
            if (strlen($newPassword) < 6) {
                echo "<script>alert('Password must be at least 6 characters long');</script>";
            } else {
                $password = hash("sha256", $newPassword);
            }
        }

        $update = "UPDATE `admin` SET fname='$fname', email='$email', phone='$phone', `role`='$role', username='$username', `password`='$password' WHERE admin_id = $id";
        mysqli_query($conn, $update);

        if ($newUsername !== $admin["username"] || $password !== $admin["password"]) {
            $redirect = true;
        }

        if (
            $_REQUEST["fname"] == $admin["fname"] && $_REQUEST["email"] == $admin["email"] && $_REQUEST["phone"] == $admin["phone"]
            && $_REQUEST["role"] == $admin["role"] && $_REQUEST["username"] == $admin["username"] &&
            $admin["password"] == $password
        ) {
            $Nochange = true;
        }

        $alert = true;
    }
}

if (isset($_REQUEST["save_photo"])) {
    $id = $admin["admin_id"];
    if (isset($_FILES["photo"]["name"]) && !empty($_FILES["photo"]["name"])) {
        $photo = $_FILES["photo"]["name"];
        $tmpname = $_FILES["photo"]["tmp_name"];
        $folder = "./../assets/adminprofilephotos/" . $photo;

        if (move_uploaded_file($tmpname, $folder)) {
            $update = "UPDATE `admin` SET photo='$photo' WHERE admin_id = $id";
            mysqli_query($conn, $update);
            $alert = true;
        }
    }
}

if (isset($_REQUEST["remove"])) {
    $id = $admin["admin_id"];
    $update = "UPDATE `admin` SET photo='logo.png' WHERE admin_id = $id";
    mysqli_query($conn, $update);
    $alert = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="./style/Profile.css">
    <link rel="website icon" href="./../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        function validationForm() {
            const fname = document.getElementById('fname').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const role = document.getElementById('role').value.trim();
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!fname) {
                document.getElementById("fval").style.display = "block";
                document.getElementById("fname").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    document.getElementById("fval").style.display = "none";
                }, 1200);

                return false;
            } else if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById("eval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("eval").style.display = "none";
                }, 1200);

                return false;
            } else if (!phone) {
                document.getElementById("cval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("cval").style.display = "none";
                }, 1200);

                return false;
            } else if (!role) {
                document.getElementById("rval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("rval").style.display = "none";
                }, 1200);

                return false;
            } else if (!username) {
                document.getElementById("uval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("uval").style.display = "none";
                }, 1200);

                return false;
            } else if (!password) {
                document.getElementById("psval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("psval").style.display = "none";
                }, 1200);

                return false;
            }
            if (password.length < 6) {
                document.getElementById("plval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("plval").style.display = "none";
                }, 1200);

                return false;
            } else {
                return true;
            }

        }
    </script>
</head>

<body>
    <?php
    include_once("./Navbar.php");
    include_once("./component/admin_header.php");
    ?>

    <?php if ($exits) { ?>
        <script>
            Swal.fire({
                title: "Error!",
                text: "Username Already Exists !",
                icon: "error"
            });
        </script>
    <?php } ?>

    <?php if ($alert) { ?>
        <script>
            Swal.fire({
                title: "Updated!",
                text: "Changes saved successfully!",
                icon: "success"
            });
        </script>
    <?php } ?>

    <?php if ($Nochange) { ?>
        <script>
            Swal.fire({
                text: "No Changes!",
                icon: "warning"
            });
        </script>
    <?php } ?>

    <?php if ($redirect) { ?>
        <script>
            Swal.fire({
                title: "Updated!",
                text: "Credentials changed successfully! Please log in again!",
                icon: "success"
            })

            setTimeout(() => {
                window.location.href = "../index.php";
            }, 1500);
        </script>
    <?php unset($_SESSION["admin"]);
    } ?>

    <div class="content">
        <div class="header-list">
            <div class="left">
                <h4 class="fw-bold">Profile Settings</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Profile </span></p>
            </div>
        </div>

        <div class="profile-header">
            <div class="profile-content">
                <div class="profile-image">
                    <img src="./../assets/adminprofilephotos/<?php echo $admin["photo"] ?>" alt="Admin">
                </div>
                <div class="profile-details">
                    <h2>Mr. <?php echo $admin["fname"] ?></h2>
                    <p><?php echo $admin["role"] ?></p>
                </div>
                <div class="image-btn">
                    <form enctype="multipart/form-data" method="post">
                        <input type="file" class="form-control" id="file-upload" name="photo" />
                        <button type="submit" id="btn" name="save_photo">Update Image</button>
                        <button type="submit" id="btn remove-btn" class="btn btn-danger" name="remove">Remove</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="adminId">
                <label>Your Identification Number :</label>
                <h5 class="fw-bold text-primary"><?php echo $admin["admin_id"]; ?></h5>
            </div>

            <div class="form-container">
                <form method="post" enctype="multipart/form-data" onsubmit="return validationForm()">
                    <div class="form-group">
                        <label>Your Full Name</label>
                        <input type="text" id="fname" name="fname" value="<?php echo $admin["fname"] ?>" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').replace(/\s+/g, ' ')"">
                        <span id="fval" style="color:red;display:none;"> * Name is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="email">Your Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $admin["email"] ?>">
                        <span id="eval" style="color:red;display:none;"> * Email is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="phone">Your Phone</label>
                        <input type="tel" id="phone" name="phone" maxlength="10" value="<?php echo $admin["phone"] ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <span id="cval" style="color:red;display:none;"> * Phone is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="role">Your Role</label>
                        <input type="text" id="role" name="role" value="<?php echo $admin["role"] ?>" readonly>
                        <span id="rval" style="color:red;display:none;"> * Role is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="bio">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo $admin["username"] ?>" oninput="this.value = this.value.replace(/\s/g, '');">
                        <span id="uval" style="color:red;display:none;"> * Username is Required </span>
                    </div>
                    <div class="form-group">
                        <label for="bio">Enter New Password</label>
                        <input type="password" id="password" name="password" placeholder="Old Password : <?php if (isset($_SESSION["admin_pass"])) { echo $_SESSION["admin_pass"]; } ?>"/>
                        <!-- <span id="psval" style="color:red;display:none;"> * Password is Required </span> -->
                        <span id="plval" style="color:red;display:none;"> * Password Must be 6 digits long </span>
                    </div>

                    <div class="form-group">
                        <label for="tmp">Account Created at</label>
                        <input class="form-control shadow-sm" id="tmp" name="tmp" value="<?= date("d M Y, h:i A", strtotime($admin["create_at"])); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <div class="btn-group" style="width:100%;">
                            <button type="submit" name="save">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>