<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("location:../login.php");
}

include_once("../../config.php");
$conn = connection();

$_SESSION["appointment_dt"] = "";
$_SESSION["appointment_tm"]= "";
$puname = $_SESSION['user'];
$Q1 = mysqli_query($conn, "SELECT * FROM patients where username='$puname'");
$patient = mysqli_fetch_assoc($Q1);
$patient_id = $patient["patient_id"];

$query1 = mysqli_query($conn, "SELECT * FROM payments WHERE patient_id = $patient_id");

$appointment_id = "";

if (isset($_REQUEST["payid"])) {
    $pay_id = $_REQUEST["payid"];
    $query3 = mysqli_query($conn, "SELECT * FROM payments WHERE payment_id=$pay_id");
    $payment = mysqli_fetch_assoc($query3);

    $payment_id = $payment["payment_id"];
    $patient_id = $payment["patient_id"];

    $Q6 = mysqli_query($conn, "SELECT * FROM appointments WHERE payment_id = $payment_id");
    $DATA = mysqli_fetch_assoc($Q6);

    $appointment_id = $DATA["appoint_id"];  
    $_SESSION["appointment_dt"] = $DATA["appointment_date"];
    $_SESSION["appointment_tm"] = $DATA["appointment_time"];

    $Q7 = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $patient_id");
    $patient = mysqli_fetch_assoc($Q7);
}

$Q = mysqli_query($conn, "SELECT SUM(amount) AS total_amount FROM payments WHERE patient_id = $patient_id");
$amount = mysqli_fetch_assoc($Q);
$totalAmount = $amount['total_amount'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="./style/Payment.css">
    <link rel="website icon" href="./image/logo.png">
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
    <!-- Correct jsPDF Import -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- Sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include_once("./Navbar.php");
    ?>

    <?php include_once("./admin_header.php"); ?>

    <div class="content">

        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold"> Payments </h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Payment </span></p>
            </div>
            <div class="right">
                <div class="alert alert-primary text-center fw-bold fs-5" role="alert" style="letter-spacing: 1px;">
                    <i class="bi bi-currency-rupee"></i> Total : <?php echo number_format($totalAmount, 2); ?>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="table-container">
                <table id="myTable" class="nowrap">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Order_id</th>
                            <th class="text-center">Patient</th>
                            <th class="text-center">Amount (&#8377;)</th>
                            <th class="text-center">Payment Status</th>
                            <th class="text-center">More</th>
                            <th class="text-center">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($payments = mysqli_fetch_assoc($query1)) {
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $payments['payment_id'] ?></td>
                                <td class="text-center"><?php echo $payments['order_id'] ?></td>
                                <td class="text-center">
                                    <?php
                                    $patient_id = $payments['patient_id'];
                                    $query2 = mysqli_query($conn, "SELECT patient_name FROM patients WHERE patient_id=$patient_id");
                                    $patients = mysqli_fetch_assoc($query2);
                                    echo $patients["patient_name"];
                                    ?>
                                </td>
                                <td class="text-center"><?php echo $payments['amount'] ?></td>
                                <td class="text-center">
                                    <?php if ($payments["status"] == "PAID") { ?>
                                        <span class="badge bg-success">Success</span>
                                    <?php } else { ?>
                                        <span class="badge bg-danger">Failed</span>
                                    <?php } ?>
                                </td>
                                <td class="text-center">
                                    <a href="?payid=<?php echo $payments["payment_id"]; ?>" name="edit">
                                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-outline-danger btn-sm pdf-btn"
                                        title="Download PDF"
                                        data-txnid="<?php echo $payments['transection_id']; ?>"
                                        data-appointment-date="<?php echo $_SESSION["appointment_dt"] ?>"
                                        data-appointment-time="<?php echo $_SESSION["appointment_tm"] ?>"
                                        data-pay-date="<?php echo $payments['create_at']; ?>">
                                        <i class="bi bi-file-earmark-pdf-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-receipt"></i> <strong>Payment Reference:</strong> <?php echo $payment["payment_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-cart-check-fill"></i> <strong>Order Reference:</strong> <?php echo $payment["order_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-arrow-left-right"></i> <strong>Transaction ID:</strong> <?php echo $payment["transection_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-person-fill"></i> <strong>Patient Name:</strong> <?php echo $patient["patient_name"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-telephone-fill"></i> <strong>Contact Number:</strong> <?php echo $patient["contact"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-envelope-at-fill"></i> <strong>Email Address:</strong> <?php echo $patient["email"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-calendar-check-fill"></i> <strong>Appointment Reference:</strong> <?php echo $appointment_id; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-calendar-date-fill"></i> <strong>Appointment Date & Time:</strong> <br> <?php echo $DATA["appointment_date"]." ". $DATA["appointment_time"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-clock-fill"></i> <strong>Payment Date & Time:</strong> <?php echo $payment["create_at"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-info-circle-fill"></i> <strong>Payment Status:</strong>
                                    <span class="badge bg-<?php echo ($payment["status"] == 'PAID') ? 'success' : 'danger'; ?>">
                                        <?php echo $payment["status"]; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let table = new DataTable("#myTable", {
                paging: true,
                searching: true,
                ordering: true,
                scrollX: true,
                info: false,
                columnDefs: [{
                    orderable: false,
                    targets: [5, 6]
                }],
                responsive: true,
                autoWidth: true,
                pageLength: 5,
                lengthChange: false
            });

            $('#myTable tbody').on('click', '.pdf-btn', function() {
                let row = $(this).closest('tr');
                let rowData = table.row(row).data();

                if (!rowData) {
                    console.error("❌ Error: No row data found!");
                    return;
                }

                let txnid = $(this).data('txnid') || 'N/A';
                let appointmentDate = $(this).data('appointment-date') || 'N/A';
                let appointmentTime = $(this).data('appointment-time') || 'N/A';
                let paymentDate = $(this).data('pay-date') || 'N/A';

                let paymentStatusHtml = rowData[4];
                let tempDiv = document.createElement("div");
                tempDiv.innerHTML = paymentStatusHtml;
                let paymentStatus = tempDiv.innerText.trim();

                const {
                    jsPDF
                } = window.jspdf;
                let doc = new jsPDF();

                doc.setFont("helvetica", "bold");
                doc.setFontSize(18);
                doc.text("HealthCare Hospital", 65, 15);
                doc.setFontSize(12);
                doc.setFont("helvetica", "normal");
                doc.text("Contact: +0261 250 250 | Email: teamhealthcarehospital@gmail.com", 40, 28);
                doc.line(10, 32, 200, 32);

                doc.setFontSize(16);
                doc.setFont("helvetica", "bold");
                doc.text("Payment Receipt", 80, 40);
                doc.setFont("helvetica", "normal");
                doc.setFontSize(12);
                doc.text(`Date: ${new Date().toLocaleDateString()}`, 150, 40);
                doc.line(10, 44, 200, 44);

                let startY = 55;
                doc.setFontSize(12);

                let details = [
                    ["Payment ID:", rowData[0]],
                    ["Order ID:", rowData[1]],
                    ["Patient Name:", rowData[2]],
                    ["Email:", "<?php echo $patient["email"] ?>"],
                    ["Contact No:", "<?php echo $patient["contact"] ?>"],
                    ["Transaction ID:", txnid],
                    ["Appointment Date:", appointmentDate],
                    ["Appointment Time:", appointmentTime],
                    ["Payment Date & Time:", paymentDate],
                    ["Amount Paid:", `Rs. ${rowData[3]}`],
                    ["Payment Status:", paymentStatus]
                ];

                details.forEach((detail, index) => {
                    let yPosition = startY + (index * 10);
                    doc.text(detail[0], 20, yPosition);
                    doc.text(detail[1], 80, yPosition);
                });

                doc.line(10, startY + (details.length * 10) + 5, 200, startY + (details.length * 10) + 5);
                doc.setFontSize(10);
                doc.text("Thank you for your payment!", 80, startY + (details.length * 10) + 20);
                doc.text("For any queries, please contact our support team.", 60, startY + (details.length * 10) + 25);

                doc.save(`Payment_Receipt_${rowData[1]}.pdf`);
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('payid')) {
                let editModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                editModal.show();
            }
        });
    </script>
</body>

</html>