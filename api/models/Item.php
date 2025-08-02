<?php
class Item {
    private $conn;
    private $table = "items"; // change if your table is named differently

    public $id;
    public $name;
    public $quantity;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // ✅ Add this method if it's missing
    public function search($keywords)
    {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE name LIKE ? 
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $keywords = "%{$keywords}%";
        $stmt->bindParam(1, $keywords);
        $stmt->execute();

        return $stmt;
    }
}
?>
