<?php
session_start();
// error_reporting(0);
if (!isset($_SESSION["admin"])) {
    header("location:../index.php");
}
include_once('../../config.php');
include_once("../../mail_helper.php");

$conn = connection();

$query5 = mysqli_query($conn, "SELECT * FROM query");

$upname = "";
$upemail = "";
$upquery = "";

if (isset($_REQUEST["quid"])) {
    $quid = $_REQUEST["quid"];
    $str = mysqli_query($conn, "SELECT * FROM query WHERE qid = $quid");
    $query_data = mysqli_fetch_assoc($str);

    $upname = $query_data["name"];
    $upemail = $query_data["email"];
    $upquery = $query_data["query"];
}

if (isset($_REQUEST["send"])) {
    $to = $_REQUEST["email"];
    $subject = "Response to Your Query";
    $response = $_REQUEST["response"];
    $quid = $_REQUEST["quid"];

    // Capture the result
    $result = sendEmail($to, $subject, $response);

    if ($result === true) {
        // Success
        $updateQuery = "UPDATE query SET response = 'Sent' WHERE qid = $quid";
        mysqli_query($conn, $updateQuery);

        $safe_response = mysqli_real_escape_string($conn, $response);
        $UpdateResponse = "UPDATE response SET res_name = '$safe_response', email = '$to', sent_at = NOW() WHERE qid = $quid";
        mysqli_query($conn, $UpdateResponse);

        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Success!",
                text: "Response Sent Successfully!",
                icon: "success"
            }).then(() => {
                window.location.href = "Query.php";
            });
        });
        </script>';
    } else {
        // Failure, use $result to display the error
        $updateQuery = "UPDATE query SET response = 'Unsent' WHERE qid = $quid";
        mysqli_query($conn, $updateQuery);

        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Oops!",
                text: "Email sending failed: ' . $result . '",
                icon: "error"
            });
        });
        </script>';
    }
}


if (isset($_REQUEST["quid1"])) {
    $quid1 = $_REQUEST["quid1"];
    $str1 = mysqli_query($conn, "SELECT * FROM response WHERE qid = $quid1");
    $response = mysqli_fetch_assoc($str1);

    $res_email = $response["email"];
    $str2 = mysqli_query($conn, "SELECT * FROM query WHERE qid = $quid1");
    $DATA = mysqli_fetch_assoc($str2);
    $res_query = $DATA["query"];
    $res_response = $response["res_name"];
    $res_sent = $response["sent_at"];
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query</title>
    <link rel="stylesheet" href="./style/Query.css">
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
        function validationForm() {
            const response = document.getElementById('response').value.trim();

            if (!response) {
                document.getElementById("rval").style.display = "block";

                setTimeout(() => {
                    document.getElementById("rval").style.display = "none";
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

    <div class="content">

        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold"> Query </h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Query </span></p>
            </div>
        </div>

        <div class="container">
            <div class="table-container">
                <table id="myTable" class="nowrap">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Query</th>
                            <th class="text-center">Date&Time</th>
                            <th class="text-center">Response</th>
                            <th class="text-center">Reply</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($querys = mysqli_fetch_assoc($query5)) { ?>
                            <tr>
                                <td class="text-center"><?php echo $querys["qid"]; ?></td>
                                <td class="text-center"><?php echo $querys["name"]; ?></td>
                                <td class="text-center"><?php echo $querys["email"]; ?></td>
                                <td class="text-center"><?php echo $querys["query"]; ?></td>
                                <td class="text-center"><?php echo $querys["date&time"]; ?></td>
                                <td class="text-center">
                                    <?php if ($querys["response"] == "Sent") { ?>
                                        <img src="./../assets/images/success.svg" alt="Sent" height="20">
                                    <?php } else { ?>
                                        <img src="./../assets/images/notsent.svg" alt="Not Sent" height="20">
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($querys["response"] == "Sent") { ?>
                                        <a href="?quid1=<?php echo $querys['qid']; ?>">
                                            <button type="button"
                                                class="btn btn-outline-success fw-semibold shadow-sm d-flex align-items-center"
                                                data-bs-toggle="modal" data-bs-target="#exampleModal1">
                                                <i class="bi bi-clock-history"></i>
                                            </button>
                                        </a>
                                    <?php } else { ?>
                                        <a href="?quid=<?php echo $querys['qid']; ?>">
                                            <button type="button"
                                                class="btn btn-outline-primary fw-semibold shadow-sm d-flex align-items-center"
                                                data-bs-toggle="modal" data-bs-target="#exampleModal">
                                                <i class="bi bi-reply-fill"></i>
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

    <!-- Query Sent Form Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <img src="./../assets/images/query_resonse.svg" class="img-fluid-rounded-circle" alt="Response"
                        height="30" />
                    <h5 class="px-2 my-1">Reply to Queries</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" onsubmit="return validationForm()">
                    <input type="hidden" name="quid" value="<?= $quid ?>">
                    <div class="modal-body">
                        <div class="form-group my-2">
                            <div style="display: flex;justify-content:space-between;">
                                <label for="name">Name</label>
                                <small class="pt-1 text-danger">* Read Only</small>
                            </div>
                            <input type="text" class="form-control my-1" value="<?= $upname ?>" readonly>
                        </div>
                        <div class="form-group my-2">
                            <div style="display: flex;justify-content:space-between;">
                                <label for="email">Email Address</label>
                                <small class="pt-1 text-danger">* Read Only</small>
                            </div>
                            <input type="email" class="form-control my-1" id="email" name="email"
                                value="<?= $upemail ?>" readonly>
                        </div>
                        <div class="form-group my-2">
                            <div style="display: flex;justify-content:space-between;">
                                <label for="query">User Inquiry</label>
                                <small class="pt-1 text-danger">* Read Only</small>
                            </div>
                            <textarea class="form-control my-1" id="query" name="query" rows="3" cols="10"
                                readonly></textarea>
                            <script>
                                document.getElementById("query").value = <?php echo json_encode($upquery); ?>;
                            </script>
                        </div>
                        <div class="form-group my-3 d-flex justify-content-center">
                            <button
                                class="btn btn-outline-primary px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2"
                                id="default" onclick="defaultMessage(event)">
                                <i class="bi bi-chat-dots-fill"></i> Default Response
                            </button>
                        </div>

                        <div class="form-group my-2">
                            <label for="message">Admin Response</label>
                            <textarea class="form-control my-1" id="response" name="response"
                                placeholder="Write response..." rows="15" cols="20"></textarea>
                            <span id="rval" style="color:red;display:none;"> * Enter Response first </span>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button"
                            class="btn btn-outline-danger px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2"
                            id="close" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="submit" name="send"
                            class="btn btn-primary px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2"
                            name="send">
                            <i class="bi bi-send-fill"></i> Send
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- View Sent query Modal -->
    <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <img src="./../assets/images/past.svg" class="img-fluid-rounded-circle" alt="Response"
                        height="30" />
                    <h5 class="px-2 my-1">Past Replied to patient</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group my-3">
                        <div style="display: flex;justify-content:space-between;">
                            <label for="email">Recipient Email</label>
                            <small class="pt-1 text-danger">* User Email</small>
                        </div>
                        <input type="email" class="form-control my-1" id="email" name="email" value="<?= $res_email ?>"
                            readonly>
                    </div>
                    <div class="form-group my-3">
                        <div style="display: flex;justify-content:space-between;">
                            <label for="query">Original Inquiry</label>
                            <small class="pt-1 text-danger">* User Query</small>
                        </div>
                        <textarea class="form-control my-1" id="query1" name="query1" rows="3" cols="10"
                            readonly></textarea>
                        <script>
                            document.getElementById("query1").value = <?php echo json_encode($res_query); ?>;
                        </script>
                    </div>
                    <div class="form-group my-3">
                        <div style="display: flex;justify-content:space-between;">
                            <label for="response">Previous Response</label>
                            <small class="pt-1 text-danger">* Your Response to that query</small>
                        </div>
                        <textarea class="form-control my-1" id="response1" value="<?= $res_response ?>" name="response1"
                            rows="15" cols="20" readonly> </textarea>
                        <script>
                            document.getElementById("response1").value = <?php echo json_encode($res_response); ?>;
                        </script>
                    </div>
                    <div class="form-group my-3">
                        <div style="display: flex;justify-content:space-between;">
                            <label for="time">Response Time</label>
                            <small class="pt-1 text-danger">* Response Sent Date-time</small>
                        </div>
                        <input type="email" class="form-control my-1" value="<?= $res_sent ?>" readonly>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button"
                        class="btn btn-dark w-100 justify-content-center px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2"
                        id="close" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        function defaultMessage(event) {
            let response = document.getElementById("response");
            response.value = `Dear [User's Name],

Thank you for reaching out to us. We appreciate your patience while we reviewed your inquiry.

Regarding your query: 
[Your response here]

If you require any further clarification or assistance, please do not hesitate to contact us.
We are happy to assist you.

Best regards,  
Healthcare,
Team PSDK`;
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
                "targets": [6]
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
                                columns: [0, 1, 2, 3, 4, 5],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 5) {
                                            return data.includes("success.svg") ? "Sent" : "Unsent";
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 5) {
                                            return data.includes("success.svg") ? "Sent" : "Unsent";
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'pdf',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 5) {
                                            return data.includes("success.svg") ? "Sent" : "Unsent";
                                        }
                                        return data;
                                    }
                                }
                            }
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5],
                                format: {
                                    body: function(data, row, column, node) {
                                        if (column === 5) {
                                            return data.includes("success.svg") ? "Sent" : "Unsent";
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

            if (urlParams.has('quid')) {
                let replyModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                replyModal.show();
            }

            if (urlParams.has('quid1')) {
                let historyModal = new bootstrap.Modal(document.getElementById('exampleModal1'));
                historyModal.show();
            }
        });
    </script>
</body>

</html>