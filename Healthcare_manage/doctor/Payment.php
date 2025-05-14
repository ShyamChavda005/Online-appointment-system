<?php
session_start();
if (!isset($_SESSION["doctor"])) {
    header("location:../index.php");
}
include_once('../../config.php');
$conn = connection();

$doctor_username = $_SESSION['doctor'];
$Q1 = mysqli_query($conn, "SELECT * FROM doctors WHERE username='$doctor_username'");
$doctor = mysqli_fetch_assoc($Q1);
$doctor_id = $doctor["doctor_id"];

// Get payments linked to this doctor
$query1 = mysqli_query($conn, "
    SELECT p.payment_id, p.order_id, p.amount, p.status, p.create_at, pat.patient_name, p.transection_id 
    FROM payments p
    JOIN appointments a ON p.payment_id = a.payment_id
    JOIN patients pat ON a.patient_id = pat.patient_id
    WHERE a.doctor_id = $doctor_id
");

$Q = mysqli_query($conn, "
    SELECT SUM(p.amount) AS total_amount 
    FROM payments p
    JOIN appointments a ON p.payment_id = a.payment_id
    WHERE a.doctor_id = $doctor_id
");
$amount = mysqli_fetch_assoc($Q);
$totalAmount = $amount['total_amount'];

if (isset($_REQUEST["payid"])) {
    $payid = $_REQUEST["payid"];
    $query = mysqli_query($conn, "SELECT 
            p.payment_id, p.order_id, p.transection_id, p.amount, p.status AS payment_status,p.create_at,
            a.appoint_id, a.appointment_date,a.appointment_time, a.doctor_id, 
            d.doctor_name, d.specilization, 
            pt.patient_id, pt.patient_name, pt.email,pt.contact
          FROM payments p
          JOIN appointments a ON p.payment_id = a.payment_id
          JOIN patients pt ON a.patient_id = pt.patient_id
          JOIN doctors d ON a.doctor_id = d.doctor_id
          WHERE p.payment_id = $payid");
    $details = mysqli_fetch_assoc($query);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor's Payments</title>
    <link rel="stylesheet" href="./style/Payment.css">
    <link rel="website icon" href="./../assets/images/logo.png">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php
    include_once("./Navbar.php");
    include_once("./admin_header.php");
    ?>

    <div class="content">
        <div class="header-list">
            <div class="left pt-4">
                <h4 class="fw-bold"> Doctor's Payments </h4>
                <p style="margin-top:10px;"><a href="./Dashboard.php"> HealthCare </a> > <span> Payments </span></p>
            </div>
            <div class="right">
                <div class="alert alert-primary text-center fw-bold fs-5" role="alert">
                    <i class="bi bi-currency-rupee"></i> Total: <?php echo number_format($totalAmount, 2); ?>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="table-container">
                <table id="myTable" class="nowrap">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Order ID</th>
                            <th class="text-center">Patient</th>
                            <th class="text-center">Amount (&#8377;)</th>
                            <th class="text-center">Payment Status</th>
                            <th class="text-center">More</th>
                            <th class="text-center">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = mysqli_fetch_assoc($query1)) { ?>
                            <tr>
                                <td><?php echo $payment['payment_id'] ?></td>
                                <td><?php echo $payment['order_id'] ?></td>
                                <td><?php echo $payment['patient_name'] ?></td>
                                <td><?php echo $payment['amount'] ?></td>
                                <td class="text-center">
                                    <?php if ($payment["status"] == "PAID") { ?>
                                        <div class="badge bg-success">Success</div>
                                    <?php } else { ?>
                                        <div class="badge bg-danger">Failed</div>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href="?payid=<?php echo $payment["payment_id"]; ?>" name="edit">
                                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-outline-danger btn-sm pdf-btn" title="Download PDF"
                                        data-txnid="<?php echo $payment['transection_id']; ?>"
                                        data-pay-date="<?php echo $payment['create_at']; ?>">
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

    <!-- Bootstrap 5 Modal -->
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
                                <p><i class="bi bi-receipt"></i> <strong>Payment Reference:</strong>
                                    <?php echo $details["payment_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-cart-check-fill"></i> <strong>Order Reference:</strong>
                                    <?php echo $details["order_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-arrow-left-right"></i> <strong>Transaction ID:</strong>
                                    <?php echo $details["transection_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-person-fill"></i> <strong>Patient Name:</strong>
                                    <?php echo $details["patient_name"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-telephone-fill"></i> <strong>Contact Number:</strong>
                                    <?php echo $details["contact"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-envelope-at-fill"></i> <strong>Email Address:</strong>
                                    <?php echo $details["email"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-calendar-check-fill"></i> <strong>Appointment Reference:</strong>
                                    <?php echo $details["appoint_id"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-calendar-date-fill"></i> <strong>Appointment Date:</strong>
                                    <?php echo $details["appointment_date"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-calendar-date-fill"></i> <strong>Appointment Time:</strong>
                                    <?php echo $details["appointment_time"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-clock-fill"></i> <strong>Payment Date & Time:</strong>
                                    <?php echo $details["create_at"]; ?></p>
                            </div>
                            <div class="col-md-6 mb-2">
                                <p><i class="bi bi-info-circle-fill"></i> <strong>Payment Status:</strong>
                                    <span
                                        class="badge bg-<?php echo ($details["payment_status"] == 'PAID') ? 'success' : 'danger'; ?>">
                                        <?php echo $details["payment_status"]; ?>
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
        $(document).ready(function () {
            let table = new DataTable("#myTable", {
                paging: true,
                searching: true,
                ordering: true,
                scrollX: true,
                info: true,
                columnDefs: [{
                    orderable: false,
                    targets: [5, 6]
                }],
                responsive: true,
                autoWidth: true,
                pageLength: 5,
                lengthChange: false,
                language: {
                    info: "", // Hide default DataTable info text
                },
                drawCallback: function (settings) {
                    let api = this.api();
                    let pageInfo = api.page.info();

                    let customInfo = `
        <span class="text-muted">
            <i class="bi bi-info-circle-fill text-primary"></i> 
            Showing <strong>${pageInfo.start + 1} - ${pageInfo.end}</strong> 
            of <strong>${pageInfo.recordsTotal}</strong> total records.
        </span>`;

                    $('#myTable_info').html(customInfo);
                }
            });

            $('#myTable tbody').on('click', '.pdf-btn', function () {
                let row = $(this).closest('tr');
                let rowData = table.row(row).data();

                if (!rowData) {
                    console.error("❌ Error: No row data found!");
                    return;
                }

                let txnid = $(this).data('txnid') || 'N/A';
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
                doc.text("Contact: +0261 250 5050 | Email: teamhealthcarehospital@gmail.com", 40, 28);
                doc.line(10, 32, 200, 32);

                doc.setFontSize(16);
                doc.setFont("helvetica", "bold");
                doc.text("Payment Receipt", 80, 40);
                doc.setFont("helvetica", "normal");
                doc.setFontSize(12);
                doc.text(`Date: ${new Date().toLocaleDateString()}`, 150, 40);
                doc.line(10, 44, 200, 44);

                let startY = 55;
                let details = [
                    ["Payment ID:", rowData[0]],
                    ["Order ID:", rowData[1]],
                    ["Patient Name:", rowData[2]],
                    ["Transaction ID:", txnid],
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
                doc.text("Thank you!", 80, startY + (details.length * 10) + 20);
                doc.save(`Payment_Receipt_${rowData[1]}.pdf`);
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('payid')) {
                let editModal = new bootstrap.Modal(document.getElementById('exampleModal'));
                editModal.show();
            }
        });
    </script>
</body>

</html>