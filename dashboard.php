<?php
$active_page = 'dashboard';
$page_title = 'Dashboard';
require_once 'layouts/header.php';
require_once 'classes/Database.php';

$db = (new Database())->getConnection();

// Get stats cautiously in case tables are not yet created
try {
    $total_barang = $db->query("SELECT COUNT(*) FROM barangs")->fetchColumn();
    $total_transaksi = $db->query("SELECT COUNT(*) FROM penjualans")->fetchColumn();
    $pendapatan_hari_ini = $db->query("SELECT SUM(total) FROM penjualans WHERE date(tanggal) = CURDATE()")->fetchColumn() ?? 0;
} catch (PDOException $e) {
    $total_barang = 0;
    $total_transaksi = 0;
    $pendapatan_hari_ini = 0;
}

?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Barang</h3>
            <h2><?= number_format($total_barang, 0, ',', '.') ?></h2>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Transaksi</h3>
            <h2><?= number_format($total_transaksi, 0, ',', '.') ?></h2>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Pendapatan Hari Ini</h3>
            <h2>Rp <?= number_format($pendapatan_hari_ini, 0, ',', '.') ?></h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Selamat Datang, <?= htmlspecialchars($nama) ?>!</h2>
    </div>
    <div>
        <p>Aplikasi Kasir Berbasis OOP PHP dan MySQL. Anda login sebagai <strong style="color:var(--primary);"><?= strtoupper($level) ?></strong>.</p>
        <p>Gunakan menu disamping untuk menggunakan aplikasi ini.</p>
        
        <br>
        <?php if ($level == 'administrator'): ?>
        <a href="barang.php" class="btn btn-primary">Data Barang</a>
        <a href="user.php" class="btn btn-primary" style="margin-left:10px;">Data User</a>
        <?php endif; ?>
        
        <?php if ($level == 'petugas'): ?>
        <a href="kasir.php" class="btn btn-primary">Buka Kasir Sekarang</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>
