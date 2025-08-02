<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../config/Database.php';

$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id) && (!empty($data->name) || !empty($data->quantity))) {
    $fields = [];
    if (!empty($data->name)) {
        $fields[] = "name = :name";
    }
    if (!empty($data->quantity)) {
        $fields[] = "quantity = :quantity";
    }

    $query = "UPDATE items SET " . implode(", ", $fields) . " WHERE id = :id";
    $stmt = $db->prepare($query);

    $stmt->bindParam(":id", $data->id);
    if (!empty($data->name)) {
        $stmt->bindParam(":name", $data->name);
    }
    if (!empty($data->quantity)) {
        $stmt->bindParam(":quantity", $data->quantity);
    }

    if ($stmt->execute()) {
        echo json_encode(["message" => "Item updated successfully."]);
    } else {
        echo json_encode(["message" => "Unable to update item."]);
    }
} else {
    echo json_encode(["message" => "Incomplete data."]);
}
