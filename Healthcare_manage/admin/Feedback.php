<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}

include_once('../../config.php');
include_once("../../mail_helper.php");
$conn = connection();

$query6 = mysqli_query($conn, "SELECT * FROM feedback");


if (isset($_REQUEST["fid"])) {
    $fid = $_REQUEST["fid"];
    $status = isset($_REQUEST["status"]);

    if ($status == "on") {
        $status = "Show";
        $q = "UPDATE feedback SET `status` = '$status' WHERE fid = $fid ";
        mysqli_query($conn, $q);
    } else {
        $status = "Unshow";
        $q = "UPDATE feedback SET `status` = '$status' WHERE fid = $fid ";
        mysqli_query($conn, $q);
    }
    header("Location:Feedback.php");
}

$upemail = "";

if (isset($_REQUEST["fedid"])) {
    $fedid = $_REQUEST["fedid"];
    $str = mysqli_query($conn, "SELECT * FROM feedback WHERE fid = $fedid");
    $feedback_data = mysqli_fetch_assoc($str);

    $upemail = $feedback_data["email"];
}

if (isset($_REQUEST["send"])) {
    $to = $_REQUEST["email"];
    $subject = "Thank You For Feedback";
    $message = $_REQUEST["message"];
    $fedid = $_REQUEST["fedid"];
    // Capture the result
    $result = sendEmail($to, $subject, $message);

    if ($result === true) {
        // Success
        $updateQuery = "UPDATE feedback SET response = 'Sent' WHERE fid = $fedid";
        mysqli_query($conn, $updateQuery);

        $safe_reply = mysqli_real_escape_string($conn, $message);
        $UpdateReply = "UPDATE reply SET feedback_reply = '$safe_reply', email = '$to', sent_at = NOW() WHERE fid = $fedid";
        mysqli_query($conn, $UpdateReply);

        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Success!",
                    text: "Mail Sent Successfully!",
                    icon: "success"
                }).then(() => {
                    window.location.href = "Feedback.php";
                });
            });
        </script>';
    } else {
        $updateQuery = "UPDATE feedback SET response = 'Unsent' WHERE fid = $fedid";
        mysqli_query($conn, $updateQuery);

        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Oops!",
                    text: "Something went wrong!",
                    icon: "warning"
                });
            });
        </script>';
    }
}

if (isset($_REQUEST["fedid1"])) {
    $fedid1 = $_REQUEST["fedid1"];
    $str1 = mysqli_query($conn, "SELECT * FROM reply WHERE fid = $fedid1");
    $reply = mysqli_fetch_assoc($str1);

    $reply_email = $reply["email"];
    $str2 = mysqli_query($conn, "SELECT * FROM feedback WHERE fid = $fedid1");
    $DATA = mysqli_fetch_assoc($str2);
    $reply_feedback = $DATA["feedback"];
    $reply_rep = $reply["feedback_reply"];
    $reply_sent = $reply["sent_at"];
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="./style/Feedback.css">
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
        function validationForm() {
            const message = document.getElementById('message').value.trim();

            if (!message) {
                document.getElementById("mval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("mval").style.display = "none";
                }, 1200);

                return false;
            } else {
                return true;
            }
        }
    </script>
</head>

<body>
    <?php include_once("./Navbar.php");
    ?>

    <?php include_once("./component/admin_header.php"); ?>

    <div class="content">

        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold"> Feedback </h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Feedback </span></p>
            </div>
            <!-- <div class="right">
                <div class="input-search">
                    <form action="./AddPatient.php" method="post">
                        <button> <i class="bi bi-person-plus"></i> <span class="px-1"> Add Patient </span></button>
                    </form>
                </div>
            </div> -->
        </div>

        <div class="container">
            <div class="table-container">
                <table id="myTable" class="nowrap">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Feedback</th>
                            <th class="text-center">Date&Time</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Response</th>
                            <th class="text-center">Reply</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($feedbacks = mysqli_fetch_assoc($query6)) { ?>
                            <tr>
                                <td class="text-center"><?php echo $feedbacks["fid"]; ?></td>
                                <td class="text-center"><?php echo $feedbacks["name"]; ?></td>
                                <td class="text-center"><?php echo $feedbacks["email"]; ?></td>
                                <td class="text-center"><?php echo $feedbacks["feedback"]; ?></td>
                                <td class="text-center"><?php echo $feedbacks["date&time"]; ?></td>
                                <td class="text-center">
                                    <form method="post" class="d-flex justify-content-center">
                                        <input type="hidden" name="fid" value="<?= $feedbacks["fid"] ?>" />
                                        <div class="form-check form-switch">
                                            <input class="form-check-input fs-4 p-1 " type="checkbox" id="switch<?= $feedbacks["fid"]  ?>" onchange="this.form.submit()" name="status"
                                                <?php if ($feedbacks["status"] == "Show") { ?> checked <?php } ?>>
                                            <label class="form-check-label ms-1 fs-6" for="switch<?= $feedbacks["status"] ?>">
                                                <?php echo ($feedbacks["status"] == "Show") ? '<span class="text-success">Show</span>' : '<span class="text-danger">Unshow</span>'; ?>
                                            </label>
                                        </div>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <?php if ($feedbacks["response"] == "Sent") { ?>
                                        <img src="./../assets/images/success.svg" alt="Sent" height="20">
                                    <?php } else { ?>
                                        <img src="./../assets/images/notsent.svg" alt="Not Sent" height="20">
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($feedbacks["response"] == "Sent") { ?>
                                        <a href="?fedid1=<?php echo $feedbacks['fid']; ?>">
                                            <button type="button" class="btn btn-outline-success fw-semibold shadow-sm d-flex align-items-center gap-2">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?fedid=<?php echo $feedbacks['fid']; ?>">
                                            <button type="button" class="btn btn-outline-primary fw-semibold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                <i class="bi bi-envelope-fill"></i>
                                            </button>
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Feedback Sent Form Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <img src="./../assets/images/mail.svg" class="img-fluid-rounded-circle" alt="Mail" height="30" />
                    <h5 class="px-2 my-1">Send Feedback Response</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" onsubmit="return validationForm()">
                    <input type="hidden" name="fedid" value="<?= $fedid ?>">
                    <div class="modal-body">
                        <div class="form-group my-2">
                            <div style="display: flex;justify-content:space-between;">
                                <label for="email">From</label>
                                <small class="pt-1 text-danger" style="letter-spacing: 1px;"> (Pre-filled with hospital email)</small>
                            </div>
                            <input type="email" class="form-control my-1" value="teamhealthcarehospital@gmail.com" readonly>
                        </div>
                        <div class="form-group my-2">
                            <div style="display: flex;justify-content:space-between;">
                                <label for="email">Recipient Email</label>
                                <small class="pt-1 text-danger" style="letter-spacing: 1px;"> (Pre-filled with user's email)</small>
                            </div>
                            <input type="email" class="form-control my-1" id="email" name="email" value="<?= $upemail ?>" readonly>
                        </div>
                        <div class="form-group my-3 d-flex justify-content-center">
                            <button class="btn btn-outline-primary px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" id="default" onclick="defaultMessage(event)">
                                <i class="bi bi-chat-dots-fill"></i> Default Message
                            </button>
                        </div>

                        <div class="form-group my-2">
                            <label for="message">Message</label>
                            <textarea class="form-control my-1" id="message" name="message" placeholder="Write Message..." rows="8" cols="20"></textarea>
                            <span id="mval" style="color:red;display:none;"> * Enter message first </span>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-danger px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" id="close" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" name="send">
                            <i class="bi bi-send-fill"></i> Send
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- View Sent Feedbback Modal -->
    <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <img src="./../assets/images/past.svg" class="img-fluid-rounded-circle" alt="Mail" height="30" />
                    <h5 class="px-2 my-1">View Sent Feedback Response</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <input type="hidden" name="fedid1" value="<?= $fedid1 ?>">
                    <div class="modal-body">
                        <div class="form-group my-2">
                            <div style="display: flex;justify-content:space-between;">
                                <label for="email">Recipient Email</label>
                                <small class="pt-1 text-danger"> (Pre-filled with user's email) </small>
                            </div>
                            <input type="email" class="form-control my-1" id="email" name="email" value="<?= $reply_email ?>" readonly>
                        </div>

                        <div class="form-group my-2">
                            <div style="display: flex;justify-content:space-between;">
                                <label for="feedback">User Feedback</label>
                                <small class="pt-1 text-danger"> (Pre-filled with original feedback message)</small>
                            </div>
                            <textarea class="form-control my-1" id="feedback1" name="feedback1" rows="5" cols="20" readonly></textarea>
                            <script>
                                document.getElementById("feedback1").value = <?php echo json_encode($reply_feedback); ?>;
                            </script>
                        </div>

                        <div class="form-group my-2">
                            <div style="display: flex;justify-content:space-between;">
                                <label for="reply">Response Message</label>
                                <small class="pt-1 text-danger"> (Pre-filled with the sent reply) </small>
                            </div>
                            <textarea class="form-control my-1" id="reply" name="reply" rows="8" cols="20" readonly></textarea>
                            <script>
                                document.getElementById("reply").value = <?php echo json_encode($reply_rep); ?>;
                            </script>
                        </div>

                        <div class="form-group my-2">
                            <div style="display: flex;justify-content:space-between;">
                                <label for="time">Response Time</label>
                                <small class="pt-1 text-danger"> (Pre-filled with date & time of the reply) </small>
                            </div>
                            <input type="datetime" class="form-control my-1" value="<?= $reply_sent ?>" readonly>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-dark w-100 justify-content-center px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" id="close" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Close Popup
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function defaultMessage(event) {
            let message = document.getElementById("message");
            message.value = `Thank you for sharing your valuable feedback with us. 

We greatly appreciate your time and insights, as they help us continuously improve our services and enhance patient care.

Please feel free to reach out at any time. We truly value your trust and support.`;
            event.preventDefault();
        }


        let table = new DataTable('#myTable', {
            paging: true,
            searching: true,
            ordering: true,
            scrollX: true,
            info: false,
            "columnDefs": [{
                "orderable": false,
                "targets": [7]
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
                                columns: [0, 1, 2, 3, 4, 5, 6],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 5) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Show" : "Unshow";
                                        }
                                        let img = node.querySelector('img');
                                        if (img) {
                                            let src = img.getAttribute('src');
                                            if (src.includes('success.svg')) {
                                                return "Sent";
                                            } else if (src.includes('notsent.svg')) {
                                                return "Not Sent";
                                            }
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 5) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Show" : "Unshow";
                                        }
                                        let img = node.querySelector('img');
                                        if (img) {
                                            let src = img.getAttribute('src');
                                            if (src.includes('success.svg')) {
                                                return "Sent";
                                            } else if (src.includes('notsent.svg')) {
                                                return "Not Sent";
                                            }
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'pdf',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 5) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Show" : "Unshow";
                                        }
                                        let img = node.querySelector('img');
                                        if (img) {
                                            let src = img.getAttribute('src');
                                            if (src.includes('success.svg')) {
                                                return "Sent";
                                            } else if (src.includes('notsent.svg')) {
                                                return "Not Sent";
                                            }
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 5) {
                                            return node.querySelector('input[type="checkbox"]').checked ? "Show" : "Unshow";
                                        }
                                        let img = node.querySelector('img');
                                        if (img) {
                                            let src = img.getAttribute('src');
                                            if (src.includes('success.svg')) {
                                                return "Sent";
                                            } else if (src.includes('notsent.svg')) {
                                                return "Not Sent";
                                            }
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
            if (urlParams.has('fedid')) {
                let editModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                editModal.show();
            }
            if (urlParams.has('fedid1')) {
                let editModal = new bootstrap.Modal(document.getElementById('exampleModal1'));
                editModal.show();
            }
        });
    </script>
</body>

</html>