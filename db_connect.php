<?php
// =====================================================================
// DATABASE CONNECTION (MySQL - cortis_db)
// =====================================================================

$host     = "localhost";
$username = "root";
$password = "";          // Default XAMPP password is empty
$database = "cortis_db";

// Suppress default PHP warning so we can handle it cleanly
$conn = @new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    // If called via API / AJAX, return JSON, otherwise set a global variable
    if (php_sapi_name() !== 'cli' && (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false))) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Database connection failed: " . $conn->connect_error
        ]);
        exit();
    }
} else {
    // Set UTF-8 charset only on successful connection
    $conn->set_charset("utf8mb4");
}
?>
