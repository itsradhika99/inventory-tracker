<?php
// Required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include database and object files
include_once '../config/database.php';
include_once '../models/item.php';

// Get database connection
$database = new Database();
$db = $database->connect();

// Prepare item object
$item = new Item($db);

// Get keywords from query string
$keywords = isset($_GET["name"]) ? $_GET["name"] : "";

// Query items
$stmt = $item->search($keywords);
$num = $stmt->rowCount();

// Check if more than 0 record found
if ($num > 0) {
    // Products array
    $items_arr = array();
    $items_arr["records"] = array();

    // Retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $item_data = array(
            "id" => $id,
            "name" => $name,
            "quantity" => $quantity,
            "created_at" => $created_at
        );

        array_push($items_arr["records"], $item_data);
    }

    // Set response code - 200 OK
    http_response_code(200);

    // Show items data in JSON format
    echo json_encode($items_arr);
} else {
    // Set response code - 404 Not found
    http_response_code(404);

    // Tell the user no items found
    echo json_encode(
        array("message" => "No items found.")
    );
}
