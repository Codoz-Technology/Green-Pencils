<?php
// Allow requests from your domain
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Log the raw POST data
error_log("Raw POST data: " . file_get_contents('php://input'));
error_log("POST array: " . print_r($_POST, true));
error_log("FILES array: " . print_r($_FILES, true));

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "siva_polybag";

try {
    // Create connection without database first
    $conn = new mysqli($servername, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if (!$conn->query($sql)) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select database
    $conn->select_db($dbname);
    
    // Create table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS custom_orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        company VARCHAR(100),
        phone VARCHAR(20),
        bag_types VARCHAR(255),
        width DECIMAL(10,2),
        height DECIMAL(10,2),
        thickness DECIMAL(10,2),
        quantity VARCHAR(50),
        requirements TEXT,
        order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20) DEFAULT 'pending'
    )";
    
    if (!$conn->query($sql)) {
        throw new Exception("Error creating table: " . $conn->error);
    }

    // Get form data and sanitize
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $company = isset($_POST['company']) ? $conn->real_escape_string($_POST['company']) : '';
    $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';
    
    // Handle bag types array
    $bag_types = '';
    if (isset($_POST['bag_types']) && is_array($_POST['bag_types'])) {
        $bag_types = implode(", ", array_map([$conn, 'real_escape_string'], $_POST['bag_types']));
    }
    
    // Convert and validate numeric values
    $width = isset($_POST['width']) ? floatval($_POST['width']) : 0;
    $height = isset($_POST['height']) ? floatval($_POST['height']) : 0;
    $thickness = isset($_POST['thickness']) ? floatval($_POST['thickness']) : 0;
    $quantity = isset($_POST['quantity']) ? $conn->real_escape_string($_POST['quantity']) : '';
    $requirements = isset($_POST['requirements']) ? $conn->real_escape_string($_POST['requirements']) : '';

    // Log processed data
    error_log("Processed data: " . print_r([
        'name' => $name,
        'email' => $email,
        'company' => $company,
        'phone' => $phone,
        'bag_types' => $bag_types,
        'width' => $width,
        'height' => $height,
        'thickness' => $thickness,
        'quantity' => $quantity,
        'requirements' => $requirements
    ], true));

    // Validate required fields
    if (empty($name)) {
        throw new Exception('Name is required');
    }
    if (empty($email)) {
        throw new Exception('Email is required');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    // Prepare SQL statement
    $sql = "INSERT INTO custom_orders (name, email, company, phone, bag_types, width, height, thickness, quantity, requirements, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Log SQL statement
    error_log("SQL statement: " . $sql);

    $stmt->bind_param("sssssdddss", 
        $name, 
        $email, 
        $company, 
        $phone, 
        $bag_types, 
        $width, 
        $height, 
        $thickness, 
        $quantity, 
        $requirements
    );

    // Execute the statement
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    // Get the inserted ID
    $orderId = $conn->insert_id;

    // Log success
    error_log("Order inserted successfully with ID: " . $orderId);

    // Close statement
    $stmt->close();

    // Return success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Order submitted successfully',
        'order_id' => $orderId
    ]);

} catch (Exception $e) {
    // Log the error
    error_log("Order submission error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    // Close connection if it exists
    if (isset($conn)) {
        $conn->close();
    }
}
?> 