<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

// Include database
include_once '../config/Database.php';

// Get DB connection
$database = new Database();
$db = $database->connect();

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Validate data
if (
    !empty($data->name) &&
    !empty($data->quantity)
) {
    // Prepare SQL
    $query = "INSERT INTO items (name, quantity) VALUES (:name, :quantity)";
    $stmt = $db->prepare($query);

    // Bind data
    $stmt->bindParam(":name", $data->name);
    $stmt->bindParam(":quantity", $data->quantity);

    // Execute
    if ($stmt->execute()) {
        echo json_encode(["message" => "Item created successfully."]);
    } else {
        echo json_encode(["message" => "Unable to create item."]);
    }
} else {
    echo json_encode(["message" => "Incomplete data."]);
}
?>
