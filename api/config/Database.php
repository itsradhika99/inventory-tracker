<?php
// Prevent redeclaring the class
if (!class_exists('Database')) {
    class Database {
        private $host = "localhost";
        private $db_name = "inventory_db";
        private $username = "root";
        private $password = "";
        public $conn;

        public function getConnection() {
            $this->conn = null;

            try {
                $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                                      $this->username, $this->password);
                $this->conn->exec("set names utf8");
            } catch(PDOException $exception) {
                echo json_encode([
                    "message" => "Connection error: " . $exception->getMessage()
                ]);
                exit;
            }

            return $this->conn;
        }
    }
}
?>
<?php
class Database {
    private $host = "localhost";
    private $db_name = "inventory_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>
