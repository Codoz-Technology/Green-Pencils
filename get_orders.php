<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "siva_polybag";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => "Connection failed: " . $conn->connect_error]));
}

// Fetch all orders
$sql = "SELECT * FROM custom_orders ORDER BY order_date DESC";
$result = $conn->query($sql);

$orders = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Return orders as JSON
echo json_encode(['status' => 'success', 'data' => $orders]);

$conn->close();
?> 