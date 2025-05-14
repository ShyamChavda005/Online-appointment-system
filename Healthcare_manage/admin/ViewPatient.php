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

$query2 = mysqli_query($conn, "SELECT * FROM patients");

$uppname = "";
$upgender = "";
$updob = "";
$upemail = "";
$upcontact = "";
$upusername = "";

if (isset($_REQUEST["pid"])) {
    $pid = $_REQUEST["pid"];
    $str = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $pid");
    $patient_data = mysqli_fetch_assoc($str);

    $uppname = $patient_data["patient_name"];
    $upgender = $patient_data["gender"];
    $updob = $patient_data["dob"];
    $upemail = $patient_data["email"];
    $upcontact = $patient_data["contact"];
    $upusername = $patient_data["username"];
}

if (isset($_REQUEST["update_patient"])) {
    $pid = $_REQUEST["pid"];
    $patient = $_REQUEST["patient_name"];
    $gender = $_REQUEST["gender"];
    $dob = $_REQUEST["dt"];
    $email = $_REQUEST["email"];
    $contact = $_REQUEST["phone"];
    // $username = $_REQUEST["username"];

    $A = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $pid");
    $old = mysqli_fetch_assoc($A);

    if (
        $patient == $old["patient_name"] && $gender == $old["gender"] && $dob == $old["dob"] &&
        $email == $old["email"] && $contact == $old["contact"]
    ) {
        $Nochange = true;
    } else {
        $q = "UPDATE patients SET patient_name='$patient',gender='$gender',dob='$dob',email='$email',contact='$contact' WHERE patient_id = $pid";
        mysqli_query($conn, $q);
        $update = true;
    }
}

if (isset($_REQUEST["patid"])) {
    $patid = $_REQUEST["patid"];
    $status = isset($_REQUEST["status"]);

    if ($status == "on") {
        $status = "Active";
        $q = "UPDATE patients SET `status` = '$status' WHERE patient_id = $patid ";
        mysqli_query($conn, $q);
    } else {
        $status = "Suspend";
        $q = "UPDATE patients SET `status` = '$status' WHERE patient_id = $patid ";
        mysqli_query($conn, $q);
    }
    header("Location:ViewPatient.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View - Patients</title>
    <link rel="stylesheet" href="./style/ViewPatient.css">
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let today = new Date().toISOString().split("T")[0]; // Get today's date in YYYY-MM-DD format
            document.getElementById("dt").setAttribute("max", today);
        });

        function validationForm() {
            const patientName = document.getElementById('patient_name').value.trim();
            const dt = document.getElementById('dt').value;
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();
            let regex = /^[A-Za-z\s]+$/; // Only allows letters and spaces

            if (!patientName) { // Check if the input is empty
                let errorMsg = document.getElementById("pval");
                errorMsg.innerText = "* Patient Name is Required";
                errorMsg.style.display = "block";
                errorMsg.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    errorMsg.style.display = "none";
                }, 1200);

                return false;
            } else if (!dt) {
                document.getElementById("dtval").style.display = "block";

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
                window.location.href = "ViewPatient.php";
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
                <h4 class="fw-bold"> Patients List</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> View Patients </span>
                </p>
            </div>
            <div class="right">
                <div class="input-search">
                    <form action="./AddPatient.php" method="post">
                        <button> <i class="bi bi-person-plus"></i> <span class="px-1"> Add Patient </span></button>
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
                            <th class="text-center">Gender</th>
                            <th class="text-center">DOB</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Phone</th>
                            <th class="text-center">Username</th>
                            <th class="text-center">Registration Date</th>
                            <th class="text-center">Update</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($patients = mysqli_fetch_assoc($query2)) { ?>
                            <tr>
                                <td class="text-center"><?php echo $patients["patient_id"]; ?></td>
                                <td class="text-center"><?php echo $patients["patient_name"]; ?></td>
                                <td class="text-center"><?php echo $patients["gender"]; ?></td>
                                <td class="text-center"><?php echo $patients["dob"]; ?></td>
                                <td class="text-center"><?php echo $patients["email"]; ?></td>
                                <td class="text-center"><?php echo $patients["contact"]; ?></td>
                                <td class="text-center"><?php echo $patients["username"]; ?></td>
                                <td class="text-center"><?php echo $patients["create_at"]; ?></td>
                                <td class="text-center">
                                    <a href="?pid=<?php echo $patients["patient_id"]; ?>" name="edit">
                                        <button type="button" class="btn" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal">
                                            <img src="./../assets/images/edit.svg" alt="Edit" height="20">
                                        </button>
                                    </a>
                                </td>
                                <td class="text-center">

                                    <form method="post" class="d-flex justify-content-center">
                                        <input type="hidden" name="patid" value="<?= $patients["patient_id"] ?>" />
                                        <div class="form-check form-switch">
                                            <!-- Increased switch size -->
                                            <input class="form-check-input fs-4 p-1 " type="checkbox"
                                                id="switch<?= $patients["patient_id"] ?>" onchange="this.form.submit()"
                                                name="status" <?php if ($patients["status"] == "Active") { ?> checked <?php } ?>>
                                            <label class="form-check-label ms-1 fs-6"
                                                for="switch<?= $patients["patient_id"] ?>">
                                                <?php echo ($patients["status"] == "Active") ? '<span class="text-success">Active</span>' : '<span class="text-danger">Suspend</span>'; ?>
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
                    <h5 class="my-1">Edit Patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" onsubmit="return validationForm()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="patient_name">Patient Name</label>
                            <input type="text" class="form-control" id="patient_name" name="patient_name"
                                value="<?= $uppname ?>" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').replace(/\s+/g, ' ')">
                            <span id="pval" style="color:red;display:none;"></span>
                        </div>
                        <div class="form-group my-2">
                            <label for="gender">Gender</label>
                            <div class="radiogroup">
                                <input type="radio" class="form-check-input" value="Male" name="gender" <?php echo ($upgender == "Male") ? "checked" : ""; ?>> Male
                                <input type="radio" class="form-check-input" value="Female" name="gender" <?php echo ($upgender == "Female") ? "checked" : ""; ?>> Female
                            </div>
                        </div>
                        <div class="form-group my-2">
                            <label for="dt">Date Of Birth</label>
                            <input type="date" class="form-control" id="dt" name="dt" value="<?= $updob ?>">
                            <span id="dtval" style="color:red;display:none;"> * Date is Required </span>
                        </div>
                        <div class="form-group my-2">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= $upemail ?>">
                            <span id="eval" style="color:red;display:none;"> * Email is Required </span>
                        </div>
                        <div class="form-group my-2">
                            <label for="phone">Phone No.</label>
                            <input type="tel" class="form-control" id="phone" name="phone" maxlength="10"
                                value="<?= $upcontact ?>" oninput="this.value = this.value.replace(/\D/, '');">
                            <span id="cval" style="color:red;display:none;"> * Phone is Required </span>
                        </div>
                        <div class="form-group my-2">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?= $upusername ?>" disabled>
                            <!-- <span id="uval" style="color:red;display:none;"> * Username is Required </span> -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="close"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_patient">Update</button>
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
                                            return node.querySelector('input[type="checkbox"]').checked ? "Active" : "Suspend";
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
                                            return node.querySelector('input[type="checkbox"]').checked ? "Active" : "Suspend";
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
                                            return node.querySelector('input[type="checkbox"]').checked ? "Active" : "Suspend";
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
                                            return node.querySelector('input[type="checkbox"]').checked ? "Active" : "Suspend";
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
            if (urlParams.has('pid')) {
                let editModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                editModal.show();
            }
        });
    </script>
</body>

</html>