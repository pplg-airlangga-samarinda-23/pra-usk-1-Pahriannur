<?php
require_once __DIR__ . '/../config/Database.php';

class Transaksi {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function simpanTransaksi($id_user, $cartItems) {
        try {
            $this->conn->beginTransaction();

            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['subtotal'];
            }

            // Insert ke tabel penjualans
            $query = "INSERT INTO penjualans (tanggal, total, id_user) VALUES (CURDATE(), :total, :id_user)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":total", $total);
            $stmt->bindParam(":id_user", $id_user);
            $stmt->execute();
            
            $id_penjualan = $this->conn->lastInsertId();

            // Insert ke tabel detail_penjualans dan kurangi stok
            require_once 'Barang.php';
            $barangObj = new Barang();

            foreach ($cartItems as $item) {
                $queryDetail = "INSERT INTO detail_penjualans (id_penjualan, id_barang, jumlah, subtotal) VALUES (:id_penjualan, :id_barang, :jumlah, :subtotal)";
                $stmtDetail = $this->conn->prepare($queryDetail);
                $stmtDetail->bindParam(":id_penjualan", $id_penjualan);
                $stmtDetail->bindParam(":id_barang", $item['id']);
                $stmtDetail->bindParam(":jumlah", $item['qty']);
                $stmtDetail->bindParam(":subtotal", $item['subtotal']);
                $stmtDetail->execute();

                // Kurangi stok
                if (!$barangObj->kurangiStok($item['id'], $item['qty'])) {
                    throw new Exception("Stok tidak mencukupi untuk barang ID: " . $item['id']);
                }
            }

            $this->conn->commit();
            return $id_penjualan;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function getAllLaporan() {
        $query = "SELECT p.*, u.nama as nama_kasir 
                  FROM penjualans p 
                  JOIN users u ON p.id_user = u.id_user 
                  ORDER BY p.tanggal DESC, p.id_penjualan DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getLaporanDetail($id_penjualan) {
        $query = "SELECT d.*, b.nama_barang, b.kode_barang 
                  FROM detail_penjualans d 
                  JOIN barangs b ON d.id_barang = b.id_barang 
                  WHERE d.id_penjualan = :id_penjualan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_penjualan", $id_penjualan);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
