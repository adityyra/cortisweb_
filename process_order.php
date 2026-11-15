<?php
// =====================================================================
// PROCESS CONCERT TICKET ORDER (INSERT INTO ticket_orders)
// =====================================================================

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

require_once __DIR__ . "/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method. Only POST is accepted."
    ]);
    exit();
}

// Retrieve POST fields
$concert_id      = isset($_POST["concert_id"]) ? intval($_POST["concert_id"]) : 0;
$fullname        = isset($_POST["fullname"]) ? trim($_POST["fullname"]) : "";
$identity_no     = isset($_POST["identity_no"]) ? trim($_POST["identity_no"]) : "";
$email           = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$phone           = isset($_POST["phone"]) ? trim($_POST["phone"]) : "";
$ticket_category = isset($_POST["ticket_category"]) ? trim($_POST["ticket_category"]) : "";
$quantity        = isset($_POST["quantity"]) ? intval($_POST["quantity"]) : 1;
$payment_method  = isset($_POST["payment_method"]) ? trim($_POST["payment_method"]) : "";
$order_status    = "PENDING";

// Validation
if (empty($concert_id) || empty($fullname) || empty($identity_no) || empty($email) || empty($phone) || empty($ticket_category) || empty($payment_method)) {
    echo json_encode([
        "success" => false,
        "message" => "Please fill in all required fields."
    ]);
    exit();
}

if ($quantity < 1 || $quantity > 4) {
    echo json_encode([
        "success" => false,
        "message" => "Quantity must be between 1 and 4 tickets."
    ]);
    exit();
}

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO `ticket_orders` (`concert_id`, `fullname`, `identity_no`, `email`, `phone`, `ticket_category`, `quantity`, `payment_method`, `order_status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Query preparation failed: " . $conn->error
    ]);
    exit();
}

$stmt->bind_param("isssssiss", $concert_id, $fullname, $identity_no, $email, $phone, $ticket_category, $quantity, $payment_method, $order_status);

if ($stmt->execute()) {
    $inserted_id = $stmt->insert_id;
    echo json_encode([
        "success"   => true,
        "order_id"  => $inserted_id,
        "message"   => "Ticket order successfully recorded into MySQL database!"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to save order: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
