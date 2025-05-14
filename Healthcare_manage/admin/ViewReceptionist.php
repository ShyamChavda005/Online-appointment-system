<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}
include_once('../../config.php');
$conn = connection();
$update = false;
$Nochange = false;
$exits = false;

$query5 = mysqli_query($conn, "SELECT * FROM receptionist");

$uprname = "";
$updob = "";
$upgender = "";
$upemail = "";
$upcontact = "";
$upusername = "";

if (isset($_REQUEST["rid"])) {
    $rid = $_REQUEST["rid"];
    $str = mysqli_query($conn, "SELECT * FROM receptionist WHERE rid = $rid");
    $receptionist_data = mysqli_fetch_assoc($str);

    $uprname = $receptionist_data["name"];
    $updob = $receptionist_data["dob"];
    $upgender = $receptionist_data["gender"];
    $upemail = $receptionist_data["email"];
    $upcontact = $receptionist_data["contact"];
    $upusername = $receptionist_data["username"];
}

if (isset($_REQUEST["update_receptionist"])) {
    $rid = $_REQUEST["rid"];
    $receptionist = $_REQUEST["receptionist_name"];
    $dt = $_REQUEST["dt"];
    $gender = $_REQUEST["gender"];
    $email = $_REQUEST["email"];
    $contact = $_REQUEST["phone"];
    $username = $upusername;

    $D = mysqli_query($conn, "SELECT * FROM receptionist WHERE rid = $rid");
    $old = mysqli_fetch_assoc($D);

    $NewUsernameQuery = mysqli_query($conn, "SELECT * FROM receptionist WHERE username = '$username' AND rid != $rid");

    if (
        $receptionist == $old["name"] && $dt == $old["dob"] && $gender == $old["gender"] &&
        $email == $old["email"] && $contact == $old["contact"]
    ) {
        $Nochange = true;
    } else {
        if (mysqli_num_rows($NewUsernameQuery) > 0) {
            $exits = true;
        } else {
            $que = "UPDATE receptionist SET `name`='$receptionist',dob='$dt',gender='$gender',email='$email',contact='$contact' WHERE rid = $rid";
            mysqli_query($conn, $que);
            $update = true;
        }
    }
}

if (isset($_REQUEST["receptid"])) {
    $receptid = $_REQUEST["receptid"];
    $status = isset($_REQUEST["status"]);

    if ($status == "on") {
        $status = "Active";
        $qu = "UPDATE receptionist SET `status` = '$status' WHERE rid = $receptid ";
        mysqli_query($conn, $qu);
    } else {
        $status = "Deactive";
        $qu = "UPDATE receptionist SET `status` = '$status' WHERE rid = $receptid ";
        mysqli_query($conn, $qu);
    }
    header("Location:ViewReceptionist.php");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View - Receptionist</title>
    <link rel="stylesheet" href="./style/ViewReceptionist.css">
    <link rel="website icon" href="./../assets/images/logo.png">
    <!-- Datable JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <!-- Datable Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.css" />
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.print.min.js"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split("T")[0]; // Get today's date in YYYY-MM-DD format
            document.getElementById("dt").setAttribute("max", today);
        });

        function validationForm() {
            const receptionist = document.getElementById('receptionist_name').value.trim();
            const dt = document.getElementById('dt').value;
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();

            if (!receptionist) {
                document.getElementById("rval").style.display = "block";
                document.getElementById("rval").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    document.getElementById("rval").style.display = "none";
                }, 1200);

                return false;
            } else if (!dt) {
                document.getElementById("dtval").style.display = "block";
                document.getElementById("dtval").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    document.getElementById("dtval").style.display = "none";
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
            } else if (phone.length < 10) {
                document.getElementById("cval").style.display = "block";
                document.getElementById("cval").innerHTML = "Phone NO. must be in 10 digits";

                setTimeout(() => {
                    document.getElementById("cval").style.display = "none";
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
                text: "Username Already Exits !",
                icon: "error"
            });
        </script>
    <?php } ?>

    <?php if ($update) { ?>
        <script>
            Swal.fire({
                title: "Success!",
                text: "Update Successfully!",
                icon: "success",
                showConfirmButton: false
            });

            setTimeout(() => {
                window.location.href = "ViewReceptionist.php";
            }, 1500);
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

    <div class="content">

        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold"> Receptionist's List</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> View Receptionist </span></p>
            </div>
            <div class="right">
                <div class="input-search">
                    <form action="./AddReceptionist.php" method="post">
                        <button> <i class="bi bi-person-badge"></i> <span class="px-1"> Add Receptionist </span></button>
                    </form>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="table-container">
                <table id="myTable" class="nowrap">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">DOB</th>
                            <th class="text-center">Gender</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Phone</th>
                            <th class="text-center">Username</th>
                            <th class="text-center">Hiring Date</th>
                            <th class="text-center">Update</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($receptionist = mysqli_fetch_assoc($query5)) { ?>
                            <tr>
                                <td class="text-center"><?php echo $receptionist["rid"]; ?></td>
                                <td class="text-center"><?php echo $receptionist["name"]; ?></td>
                                <td class="text-center"><?php echo $receptionist["dob"]; ?></td>
                                <td class="text-center"><?php echo $receptionist["gender"]; ?></td>
                                <td class="text-center"><?php echo $receptionist["email"]; ?></td>
                                <td class="text-center"><?php echo $receptionist["contact"]; ?></td>
                                <td class="text-center"><?php echo $receptionist["username"]; ?></td>
                                <td class="text-center"><?php echo $receptionist["hire_dt"]; ?></td>
                                <td class="text-center">
                                    <a href="?rid=<?php echo $receptionist["rid"]; ?>" name="edit">
                                        <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                            <img src="./../assets/images/edit.svg" alt="Edit" height="20">
                                        </button>
                                    </a>
                                </td>

                                <td class="text-center">
                                    <form method="post" class="d-flex justify-content-center">
                                        <input type="hidden" name="receptid" value="<?= $receptionist["rid"] ?>" />
                                        <div class="form-check form-switch">
                                            <input class="form-check-input fs-4 p-1 " type="checkbox" id="switch<?= $receptionist["rid"]  ?>" onchange="this.form.submit()" name="status"
                                                <?php if ($receptionist["status"] == "Active") { ?> checked <?php } ?>>
                                            <label class="form-check-label ms-1 fs-6" for="switch<?= $receptionist["status"] ?>">
                                                <?php echo ($receptionist["status"] == "Active") ? '<span class="text-success">Active</span>' : '<span class="text-danger">Deactive</span>'; ?>
                                            </label>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="my-1">Edit Receptionist</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form onsubmit="return validationForm()" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Receptionist Name</label>
                            <input type="text" class="form-control" id="receptionist_name" name="receptionist_name" value="<?= $uprname ?>" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').replace(/\s+/g, ' ')">
                            <span id="rval" style="color:red;display:none;"> * Receptionist Name is Required </span>
                        </div>
                        <div class="form-group my-2">
                            <label for="dt">Date Of Birth</label>
                            <input type="date" class="form-control" id="dt" name="dt" value="<?= $updob ?>">
                            <span id="dtval" style="color:red;display:none;"> * Date is Required </span>
                        </div>
                        <div class="form-group my-2">
                            <label for="gender">Gender</label>
                            <div class="radiogroup">
                                <label> <input type="radio" class="form-check-input" name="gender" value="Male" <?php if ($upgender == "Male") { ?> checked <?php } ?>> Male </label>
                                <label> <input type="radio" class="form-check-input" name="gender" value="Female" <?php if ($upgender == "Female") { ?> checked <?php } ?>> Female </label>
                            </div>
                        </div>
                        <div class="form-group my-2">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= $upemail ?>">
                            <span id="eval" style="color:red;display:none;"> * Email is Required </span>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone No.</label>
                            <input type="tel" class="form-control" id="phone" name="phone" maxlength="10" value="<?= $upcontact ?>" oninput="this.value = this.value.replace(/\D/, '');">
                            <span id="cval" style="color:red;display:none;"> * Phone is Required </span>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= $upusername ?>" disabled>
                            <span id="uval" style="color:red;display:none;"> * Username is Required </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="close" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_receptionist">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let table = new DataTable('#myTable', {
            paging: true,
            searching: true,
            ordering: true,
            scrollX: true,
            info: false,
            "columnDefs": [{
                "orderable": false,
                "targets": [8]
            }],
            responsive: true,
            // "fixedHeader": false,
            autoWidth: true,
            pageLength: 5,
            lengthChange: false,
            layout: {
                topStart: {
                    buttons: [{
                            extend: 'csv',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 9],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 9) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Active" : "Disactive";
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 9],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 9) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Active" : "Deactive";
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'pdf',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 9],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 9) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Active" : "Deactive";
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 9],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 9) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Active" : "Deactive";
                                        }
                                        return data;
                                    }
                                }
                            }
                        }
                    ]
                }
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('rid')) {
                let editModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                editModal.show();
            }
        });
    </script>
</body>

</html>