<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../config/Database.php';

$database = new Database();
$db = $database->connect();

$query = "SELECT id, name, quantity, created_at FROM items ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();

$num = $stmt->rowCount();

if ($num > 0) {
    $items = array();
    $items["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $item = array(
            "id" => $id,
            "name" => $name,
            "quantity" => $quantity,
            "created_at" => $created_at
        );
        array_push($items["records"], $item);
    }

    echo json_encode($items);
} else {
    echo json_encode(
        array("message" => "No items found.")
    );
}
?>
