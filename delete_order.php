<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "siva_polybag";

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['orderId'];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => "Connection failed: " . $conn->connect_error]));
}

// Delete order
$sql = "DELETE FROM custom_orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderId);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?> 