<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "your_password";
$dbname = "siva_polybag";

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['orderId'];
$status = $data['status'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => "Connection failed: " . $conn->connect_error]));
}

// Update order status
$sql = "UPDATE custom_orders SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $orderId);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?> 