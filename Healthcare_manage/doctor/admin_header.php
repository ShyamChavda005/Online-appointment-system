<?php
if (!isset($_SESSION['doctor'])) {
    header("location:../index.php");
    exit();
}

include_once('../../config.php');
$conn = connection();
$duname = $_SESSION['doctor'];

$query2 = mysqli_query($conn, "SELECT * FROM doctors where username='$duname'");
$doctor = mysqli_fetch_assoc($query2);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .box {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 20px;
            background-color: #fff;
            height: 10vh;
            box-shadow: 2px 2px 3px rgb(206, 206, 206);
        }

        .search-container {
            height: 100%;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1;
            gap: 10px;
            min-width: 280px;
        }

        .search-container #search-button {
            border: none;
            cursor: pointer;
        }

        .search-bar {
            width: 50%;
            padding: 8px 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            outline: none;
        }

        .icons {
            height: 100%;
            display: flex;
            justify-content: space-around;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .icon {
            width: 30px;
            height: 30px;
            cursor: pointer;
        }

        /* Notification and Settings Containers */
        #setting-container,
        #notification-container {
            position: absolute;
            /* z-index: 999999; */
            top: 100%;
            right: 10px;
            background-color: #ffffff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            display: none;
            border-radius: 5px;
            max-width: 300px;
            overflow: hidden;
        }

        #setting-container {
            padding: 0;
        }

        #notification-container {
            padding: 10px;
        }

        #notification-container h3 {
            display: flex;
            justify-content: center;
            color: #555;
            font-size: 14px;
        }

        #setting-container .header {
            display: flex;
            align-items: center;
            gap: 15px;
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .header img {
            height: 50px;
            width: 50px;
            border-radius: 50%;
            border: 1px solid #333;
        }

        .header .text h3 {
            font-size: 18px;
            margin: 0;
            color: #333;
            font-weight: bold;
        }

        .header .text p {
            font-size: 12px;
            color: #666;
            margin: 0;
        }

        #setting-container .body {
            padding: 10px;
        }

        .body ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .body li {
            margin-bottom: 8px;
        }

        .body li a {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            padding: 10px;
            border-radius: 8px;
            transition: background-color 0.3s, color 0.3s;
        }

        .body li a:hover {
            background-color: #007bff;
            color: #fff;
        }

        .body li a img {
            transition: filter 0.3s;
        }

        .body li a:hover img {
            filter: invert(1);
        }

        .box {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        @media (max-width: 1000px) {

            #setting-container,
            #notification-container {
                top: 100%;
                width: 80%;
                max-width: 200px;
                right: 10%;
                padding: 10px;
                z-index: 99999;
            }

            #notification-container h3 {
                margin-top: 5px;
            }
        }

        @media (max-width: 480px) {

            #setting-container,
            #notification-container {
                width: 90%;
                top: 100%;
                right: 5%;
                padding: 10px;
                z-index: 99999;
            }

            #setting-container .header {
                flex-direction: column;
                align-items: center;
                padding: 15px;
            }

            .header img {
                height: 40px;
                margin-bottom: 5px;
            }

            #notification-container h3 {
                font-size: 1rem;
            }

            /* .icon {
            width: 25px;
            height: 30px;
            } */
        }
    </style>
</head>

<body>
    <div class="box">
        <div class="search-container">
            <input type="search" class="search-bar" id="search-bar" placeholder="Search here...">
            <img src="./../assets/images/search.svg" height="25" id="search-button">
        </div>

        <div class="d-flex align-items-center">
            <a class="btn btn-outline-primary d-flex align-items-center px-4 py-2 rounded-pill shadow-sm fw-bold">
                <i class="bi bi-hospital me-2"></i>
                <span><?= $doctor["doctor_name"] ?></span>
            </a>
        </div>



        <div class="icons">
            <img src="./../assets/images/notification.svg" alt="Notification" class="icon" id="notification">
            <img src="./../assets/images/setting.svg" alt="Settings" class="icon" id="setting">
        </div>
        <div id="notification-container">
            <h3> No message yet ! </h3>
        </div>

        <div id="setting-container">
            <div class="header">
                <img src="./../assets/doctorphotos/<?php echo $doctor["photo"] ?>" alt="Patient">
                <div class="text">
                    <h3><?= $doctor["doctor_name"] ?></h3>
                </div>
            </div>
            <div class="body">
                <ul>
                    <li>
                        <a href="./Profile.php">
                            <img src="./../assets/images/admin.svg" alt="Profile" height="20">
                            Profile Settings
                        </a>
                    </li>
                    <li>
                        <a href="./logout.php">
                            <img src="./../assets/images/logout.svg" alt="Logout" height="20">
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <script>
        const settingIcon = document.getElementById("setting");
        const settingContainer = document.getElementById("setting-container");

        settingIcon.addEventListener("click", () => {
            if (settingContainer.style.display === "block") {
                settingContainer.style.display = "none";
            } else {
                settingContainer.style.display = "block";
                document.getElementById("notification-container").style.display = "none";
            }
        });

        const notificationIcon = document.getElementById("notification");
        const notificationContainer = document.getElementById("notification-container");

        notificationIcon.addEventListener("click", () => {
            if (notificationContainer.style.display === "block") {
                notificationContainer.style.display = "none";
            } else {
                notificationContainer.style.display = "block";
                settingContainer.style.display = "none";
            }
        });

        const searchbtn = document.getElementById("search-button");

        searchbtn.addEventListener("click", () => {
            let search = document.getElementById("search-bar").value.trim();

            if (search) {
                window.location.href = `${search}.php`;
            } else {
                alert("page not found");
            }
        })

        const searchBar = document.getElementById("search-bar");

        searchBar.addEventListener("keydown", (event) => {
            if (event.key === "Enter") {
                let search = searchBar.value.trim();
                window.location.href = `${search}.php`;
            }
        });
    </script>
</body>

</html>