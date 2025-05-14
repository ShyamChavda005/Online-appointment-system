<?php
include 'config.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die("Invalid order.");
}

$ch = curl_init("https://sandbox.cashfree.com/pg/orders/{$order_id}");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "x-api-version: 2022-09-01", // ✅ updated
    "x-client-id: " . CASHFREE_APP_ID,
    "x-client-secret: " . CASHFREE_SECRET_KEY
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$responseJson = curl_exec($ch);
curl_close($ch);

$response = json_decode($responseJson, true);

// Debug the response
if (!$response || !isset($response['order_status'])) {
    echo "Failed to get valid response from Cashfree:<br><pre>";
    print_r($response);
    echo "</pre>";
    exit();
}

// Handle paid order
if (strtoupper($response['order_status']) === "PAID") {
    $status = $response['order_status'];
    $amount = $response['order_amount'];
    $patient_id = $response['customer_details']['customer_id'] ?? null;

    $txn_id = null;
    if (!empty($response['payments'])) {
        $txn_id = $response['payments'][0]['cf_payment_id'] ?? null;
    }
    if (!$txn_id) {
        $txn_id = "_TXN_" . uniqid();
    }

    $stmt = $conn->prepare("INSERT INTO payments (order_id, transection_id, patient_id, amount, `status`) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssids", $order_id, $txn_id, $patient_id, $amount, $status);
    $stmt->execute();
    $stmt->close();

    header("Location: ../AddAppointment.php?order_id=" . $order_id);
    exit();
} else {
    header("Location: ../AddAppointment.php?paymentfailed=" . $response['order_status']);
    exit();
}
?>
