<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "siva_polybag";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => $conn->connect_error]));
}

// SQL to create the table
$sql = "CREATE TABLE IF NOT EXISTS custom_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    company VARCHAR(100),
    phone VARCHAR(20),
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'pending',
    bag_types TEXT,
    width FLOAT,
    height FLOAT,
    thickness FLOAT,
    quantity INT,
    requirements TEXT,
    total_price FLOAT
)";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "message" => "Table created successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$conn->close();
?>
