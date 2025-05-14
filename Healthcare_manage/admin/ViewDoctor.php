<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}
include_once('../../config.php');
$conn = connection();
$update = false;
$nochange = false;
$exits = false;

$query1 = mysqli_query($conn, "SELECT * FROM doctors");

$up_name = "";
$up_specilization = "";
$up_consult_fee = "";
$up_dob = "";
$up_gender = "";
$up_address = "";
$up_email = "";
$up_contact = "";
$up_username = "";
$up_password = "";
$up_photo = "";

if (isset($_REQUEST["did"])) {
    $did = $_REQUEST["did"];

    $q = mysqli_query($conn, "SELECT * FROM doctors WHERE doctor_id = $did");
    $doctordata = mysqli_fetch_assoc($q);
    $up_name = $doctordata["doctor_name"];
    $up_specilization = $doctordata["specilization"];
    $up_consult_fee = $doctordata["consultancy_fee"];
    $up_dob = $doctordata["dob"];
    $up_gender = $doctordata["gender"];
    $up_address = $doctordata["address"];
    $up_email = $doctordata["email"];
    $up_contact = $doctordata["contact"];
    $up_username = $doctordata["username"];
    $up_photo = $doctordata["photo"];
}

if (isset($_REQUEST["update_doctor"])) {
    $did = $_REQUEST["did"];
    $doctor = $_REQUEST["doctor_name"];
    $sp = $_REQUEST["specilization"];
    $fee = $_REQUEST["fee"];
    $dob = $_REQUEST["dob"];
    $gender = $_REQUEST["gender"];
    $address = $_REQUEST["address"];
    $email = $_REQUEST["email"];
    $contact = $_REQUEST["contact"];
    $username = $up_username;

    $B = mysqli_query($conn, "SELECT * FROM doctors WHERE doctor_id = $did");
    $old = mysqli_fetch_assoc($B);
    $old_photo = $old["photo"];

    if (!empty($_FILES["photo"]["name"])) {
        $photo = $_FILES["photo"]["name"];
        $tmpname = $_FILES["photo"]["tmp_name"];
        $folder = "./../assets/doctorphotos/" . time() . "_" . $photo; // Add timestamp to avoid overwriting

        // Move uploaded file to the folder
        if (move_uploaded_file($tmpname, $folder)) {
            // Delete old photo if exists
            if (!empty($old_photo) && file_exists("./../assets/doctorphotos/" . $old_photo)) {
                unlink("./../assets/doctorphotos/" . $old_photo);
            }
            $new_photo = basename($folder); // Save new photo filename
        } else {
            $new_photo = $old_photo; // If upload fails, keep the old photo
        }
    } else {
        $new_photo = $old_photo; // No new photo uploaded, keep the old one
    }

    if (
        $doctor == $old["doctor_name"] && $sp == $old["specilization"] && $fee == $old["consultancy_fee"] && $dob == $old["dob"] && $gender == $old["gender"] &&
        $address == $old["address"] && $email == $old["email"] && $contact == $old["contact"] && $new_photo == $old_photo
    ) {
        $nochange = true;
    } else {
        $up = "UPDATE doctors SET doctor_name='$doctor',specilization='$sp',consultancy_fee=$fee,dob='$dob',gender='$gender',`address`='$address',email='$email',
        contact=$contact,photo='$new_photo' WHERE doctor_id = $did";
        mysqli_query($conn, $up);
        $update = true;
    }
}

if (isset($_REQUEST["docid"])) {
    $docid = $_REQUEST["docid"];
    $status = isset($_REQUEST["status"]);

    if ($status == "on") {
        $status = "Active";
        $qu = "UPDATE doctors SET `status` = '$status' WHERE doctor_id = $docid ";
        mysqli_query($conn, $qu);
    } else {
        $status = "Deactive";
        $qu = "UPDATE doctors SET `status` = '$status' WHERE doctor_id = $docid ";
        mysqli_query($conn, $qu);
    }
    header("Location:ViewDoctor.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View - Doctors</title>
    <link rel="stylesheet" href="./style/ViewDoctor.css">
    <link rel="website icon" href="./../assets/images/logo.png">

    <!-- PDFMake (Required for PDF Export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
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
            document.getElementById("dob").setAttribute("max", today);
        });

        function validationForm() {
            const doctorName = document.getElementById('doctor_name').value.trim();
            const specilization = document.getElementById('specilization').value;
            const fee = document.getElementById('fee').value.trim();
            const dob = document.getElementById('dob').value;
            const address = document.getElementById('address').value.trim();
            const email = document.getElementById('email').value.trim();
            const contact = document.getElementById('contact').value.trim();
            const photo = document.getElementById('photo').value;
            let regex = /^[A-Za-z\s]+$/; // Only allows letters and spaces

            if (!doctorName) { // Check if the input is empty
                let errorMsg = document.getElementById("nval");
                errorMsg.innerText = "* Doctor Name is Required";
                errorMsg.style.display = "block";
                errorMsg.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    errorMsg.style.display = "none";
                }, 1200);

                return false;
            } else if (!regex.test(doctorName)) { // Check if input contains invalid characters
                let errorMsg = document.getElementById("nval");
                errorMsg.innerText = "* Only letters and spaces are allowed";
                errorMsg.style.display = "block";
                errorMsg.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    errorMsg.style.display = "none";
                }, 1200);

                return false;
            } else if (specilization == "Select Specilization") {
                document.getElementById("sval").style.display = "block";
                document.getElementById("sval").scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });

                setTimeout(() => {
                    document.getElementById("sval").style.display = "none";
                }, 1200);

                return false;
            } else if (!fee || fee === "" || isNaN(fee) || fee <= 0) {
                document.getElementById("feeval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("feeval").style.display = "none";
                }, 1200);

                return false;
            } else if (!dob) {
                document.getElementById("dtval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("dtval").style.display = "none";
                }, 1200);

                return false;
            } else if (!address) {
                document.getElementById("aval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("aval").style.display = "none";
                }, 1200);

                return false;
            } else if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById("eval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("eval").style.display = "none";
                }, 1200);

                return false;
            } else if (!contact) {
                document.getElementById("cval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("cval").style.display = "none";
                }, 1200);

                return false;
            } else if (contact.length < 10) {
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
    <?php include_once("./Navbar.php"); ?>

    <?php include_once("./component/admin_header.php"); ?>

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
                window.location.href = "ViewDoctor.php";
            }, 1500);
        </script>
    <?php } ?>

    <?php if ($nochange) { ?>
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
                <h4 class="fw-bold">Doctors List</h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> View Doctors </span></p>
            </div>
            <div class="right">
                <div class="input-search">
                    <form action="./AddDoctor.php" method="post">
                        <button> <i class="bi bi-heart-pulse"></i> <span class="px-1"> Add Doctor </span></button>
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
                            <th class="text-center">Specilization</th>
                            <th class="text-center">Consultancy Fees</th>
                            <th class="text-center">DOB</th>
                            <th class="text-center">Gender</th>
                            <th class="text-center">Address</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Phone</th>
                            <th class="text-center">Username</th>
                            <th class="text-center">Registration Date</th>
                            <th class="text-center">Photo</th>
                            <th class="text-center">Update</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($doctors = mysqli_fetch_assoc($query1)) { ?>
                            <tr>
                                <td class="text-center"><?php echo $doctors["doctor_id"]; ?></td>
                                <td class="text-center"><?php echo $doctors["doctor_name"]; ?></td>
                                <td class="text-center"><?php echo $doctors["specilization"]; ?></td>
                                <td class="text-center"><?php echo $doctors["consultancy_fee"]; ?></td>
                                <td class="text-center"><?php echo $doctors["dob"]; ?></td>
                                <td class="text-center"><?php echo $doctors["gender"]; ?></td>
                                <td class="text-center"><?php echo $doctors["address"]; ?></td>
                                <td class="text-center"><?php echo $doctors["email"]; ?></td>
                                <td class="text-center"><?php echo $doctors["contact"]; ?></td>
                                <td class="text-center"><?php echo $doctors["username"]; ?></td>
                                <td class="text-center"><?php echo $doctors["create_at"]; ?></td>
                                <td class="text-center">
                                    <img src="./../assets/doctorphotos/<?php echo $doctors["photo"] ?>" alt=""
                                        height="50" />
                                </td>
                                <td class="text-center">
                                    <a href="?did=<?php echo $doctors["doctor_id"]; ?>" name="edit">
                                        <button type="button" class="btn" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal">
                                            <img src="./../assets/images/edit.svg" alt="Edit" height="20">
                                        </button>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <form method="post" class="d-flex justify-content-center">
                                        <input type="hidden" name="docid" value="<?= $doctors["doctor_id"] ?>" />
                                        <div class="form-check form-switch">
                                            <input class="form-check-input fs-4 p-1 " type="checkbox"
                                                id="switch<?= $doctors["doctor_id"] ?>" onchange="this.form.submit()"
                                                name="status" <?php if ($doctors["status"] == "Active") { ?> checked <?php } ?>>
                                            <label class="form-check-label ms-1 fs-6" for="switch<?= $doctors["status"] ?>">
                                                <?php echo ($doctors["status"] == "Active") ? '<span class="text-success">Active</span>' : '<span class="text-danger">Suspend</span>'; ?>
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
                    <h5 class="my-1">Edit Doctor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" onsubmit="return validationForm()" enctype="multipart/form-data">
                    <div class="modal-body">
                        <?php if (isset($_REQUEST["did"])) { ?>
                            <img class="img-thumbnail d-block mx-auto"
                                src="./../assets/doctorphotos/<?php echo $up_photo; ?>" alt="oldPhoto"
                                style="height: 350px;" />
                        <?php } ?>

                        <div class="form-group my-2">
                            <label for="doctor_name">Doctor Name</label>
                            <input type="text" class="form-control" id="doctor_name" name="doctor_name"
                                value="<?= $up_name ?>">
                            <span id="nval" style="color:red;display:none;"></span>
                        </div>
                        <div class="form-group my-2">
                            <label>Specialization</label>
                            <select class="form-select" id="specilization" name="specilization">
                                <option>Select Specilization</option>
                                <option value="Cardiologist" <?php if ($up_specilization == "Cardiologist") { ?> selected
                                    <?php } ?>>Cardiologist</option>
                                <option value="Dentist" <?php if ($up_specilization == "Dentist") { ?> selected <?php } ?>>Dentist</option>
                                <option value="Dermatologist" <?php if ($up_specilization == "Dermatologist") { ?>
                                    selected <?php } ?>>Dermatologist</option>
                                <option value="Gynecologist" <?php if ($up_specilization == "Gynecologist") { ?> selected
                                    <?php } ?>>Gynecologist</option>
                                <option value="Neurologist" <?php if ($up_specilization == "Neurologist") { ?> selected
                                    <?php } ?>>Neurologist</option>
                                <option value="Orthopedic" <?php if ($up_specilization == "Orthopedic") { ?> selected
                                    <?php } ?>>Orthopedic</option>
                                <option value="Psychiatrist" <?php if ($up_specilization == "Psychiatrist") { ?> selected
                                    <?php } ?>>Psychiatrist</option>
                                <option value="Radiologist" <?php if ($up_specilization == "Radiologist") { ?> selected
                                    <?php } ?>>Radiologist</option>
                            </select>
                            <span id="sval" style="color:red;display:none;"> * Specilization is Required </span>
                        </div>

                        <div class="form-group my-2">
                            <label for="fee">Consultancy Fee</label>
                            <input type="number" class="form-control" id="fee" name="fee"
                                value="<?= $up_consult_fee ?>">
                            <span id="feeval" style="color:red;display:none;"> * Consultancy Fee is Required </span>
                        </div>

                        <div class="form-group my-2">
                            <label for="dob">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="<?= $up_dob ?>">
                            <span id="dtval" style="color:red;display:none;"> * Date is Required </span>
                        </div>
                        <div class="form-group my-2">
                            <label for="gender">Gender</label>
                            <div class="radiogroup">
                                <label> <input type="radio" class="form-check-input" name="gender" value="Male" checked
                                        <?php if ($up_gender == "Male") { ?> checked <?php } ?>> Male </label>
                                <label> <input type="radio" class="form-check-input" name="gender" value="Female" <?php if ($up_gender == "Female") { ?> checked <?php } ?>> Female </label>
                            </div>
                        </div>
                        <div class="form-group my-2">
                            <label for="address">Address</label>
                            <input id="address" class="form-control" name="address" value="<?= $up_address ?>">
                            <span id="aval" style="color:red;display:none;"> * Address is Required </span>
                        </div>
                        <div class="form-group my-2">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= $up_email ?>">

                            <span id="eval" style="color:red;display:none;"> * Email is Required </span>
                        </div>
                        <div class="form-group my-2">
                            <label for="contact">Contact</label>
                            <input type="tel" class="form-control" id="contact" name="contact"
                                value="<?= $up_contact ?>" maxlength="10"
                                oninput="this.value = this.value.replace(/\D/, '');">
                            <span id="cval" style="color:red;display:none;"> * Contact is Required </span>
                        </div>
                        <div class="form-group my-2">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?= $up_username ?>" disabled>
                            <!-- <span id="uval" style="color:red;display:none;"> * Username Required </span> -->
                        </div>
                        <div class="form-group my-2">
                            <label for="photo">Photo</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*" onchange="if(this.files[0] && !['image/jpeg','image/png','image/jpg'].includes(this.files[0].type)){alert('Only image files (JPEG, PNG, JPG) are allowed.'); this.value='';}">
                            <span id="ptval" style="color:red;display:none;"> * Photo Required </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="close"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="update_doctor">Update</button>
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
                "targets": [10, 11]
            }],
            responsive: true,
            // "fixedHeader": false,
            autoWidth: false,
            pageLength: 5,
            lengthChange: false,
            layout: {
                topStart: {
                    buttons: [{
                            extend: 'csv',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 13],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 13) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Active" : "Deactive";
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 13],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 13) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Active" : "Deactive";
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'PDF',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 13], // Ensure Username (Column 9) is included
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 13) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Active" : "Deactive";
                                        }
                                        return data;
                                    }
                                }
                            },
                            customize: function(doc) {
                                doc.defaultStyle.fontSize = 9;
                                doc.pageSize = 'A4';
                                doc.pageOrientation = 'landscape';

                                // Set column widths dynamically
                                let numColumns = doc.content[1].table.body[0].length;
                                doc.content[1].table.widths = Array(numColumns).fill('auto');


                                // Align headers to the center
                                doc.styles.tableHeader = {
                                    alignment: 'center',
                                    bold: true,
                                    fontSize: 10
                                };

                                // Align specific columns (e.g., numbers to the right)
                                doc.content[1].table.body.forEach(function(row, i) {
                                    row.forEach(function(cell, j) {
                                        if ([0, 3].includes(j)) { // Center-align ID and Fees
                                            row[j].alignment = 'center';
                                        }
                                        if ([7, 8, 9].includes(j)) { // Email, Phone, and Username align left
                                            row[j].alignment = 'left';
                                        }
                                    });
                                });
                            }
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 13],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 13) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Active" : "Deactive";
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                    ]
                }
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('did')) {
                let editModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                editModal.show();
            }
        });
    </script>
</body>

</html>