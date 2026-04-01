<?php
require_once __DIR__ . '/../config/Database.php';

class User {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT id_user, nama, username, level FROM users ORDER BY id_user DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($nama, $username, $password, $level) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (nama, username, password, level) VALUES (:nama, :username, :password, :level)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nama", $nama);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":level", $level);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM users WHERE id_user = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
