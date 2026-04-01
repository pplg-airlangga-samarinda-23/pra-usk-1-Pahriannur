<?php
require_once __DIR__ . '/../config/Database.php';

class Barang {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM barangs ORDER BY id_barang DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT * FROM barangs WHERE id_barang = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function search($keyword) {
        $keyword = "%{$keyword}%";
        $query = "SELECT * FROM barangs WHERE nama_barang LIKE :keyword OR kode_barang LIKE :keyword";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":keyword", $keyword);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($kode, $nama, $harga, $stok) {
        $query = "INSERT INTO barangs (kode_barang, nama_barang, harga, stok) VALUES (:kode, :nama, :harga, :stok)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":kode", $kode);
        $stmt->bindParam(":nama", $nama);
        $stmt->bindParam(":harga", $harga);
        $stmt->bindParam(":stok", $stok);
        return $stmt->execute();
    }

    public function update($id, $kode, $nama, $harga, $stok) {
        $query = "UPDATE barangs SET kode_barang = :kode, nama_barang = :nama, harga = :harga, stok = :stok WHERE id_barang = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":kode", $kode);
        $stmt->bindParam(":nama", $nama);
        $stmt->bindParam(":harga", $harga);
        $stmt->bindParam(":stok", $stok);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM barangs WHERE id_barang = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function kurangiStok($id, $jumlah) {
        $query = "UPDATE barangs SET stok = stok - :jumlah WHERE id_barang = :id AND stok >= :jumlah";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":jumlah", $jumlah, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
