<?php
require_once __DIR__ . '/../config/Database.php';

class Auth {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $row['password'])) {
                session_start();
                $_SESSION['user_id'] = $row['id_user'];
                $_SESSION['nama'] = $row['nama'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['level'] = $row['level'];
                return true;
            }
        }
        return false;
    }

    public function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        return true;
    }

    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header("Location: index.php");
            exit;
        }
    }

    public function requireRole($allowed_roles) {
        $this->requireAuth();
        if (!in_array($_SESSION['level'], $allowed_roles)) {
            header("Location: dashboard.php?error=unauthorized");
            exit;
        }
    }
}
?>
