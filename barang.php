<?php
$active_page = 'barang';
$page_title = 'Data Barang & Stok';
require_once 'layouts/header.php';
$auth->requireRole(['administrator']);

require_once 'classes/Barang.php';
$barangObj = new Barang();

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($barangObj->delete($id)) {
        $msg = "Data barang berhasil dihapus.";
    }
}

$barangs = $barangObj->getAll();
?>

<div class="card">
    <div class="card-header">
        <h3>Daftar Barang</h3>
        <a href="barang_form.php" class="btn btn-primary">+ Tambah Barang</a>
    </div>

    <?php if(isset($msg)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($barangs as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($row['kode_barang']) ?></strong></td>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td>
                        <?php if($row['stok'] <= 5): ?>
                            <span style="color:var(--danger); font-weight:bold;"><?= $row['stok'] ?> (Kritis)</span>
                        <?php else: ?>
                            <?= $row['stok'] ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="barang_form.php?id=<?= $row['id_barang'] ?>" class="btn btn-primary" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Edit</a>
                        <a href="barang.php?delete=<?= $row['id_barang'] ?>" class="btn btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($barangs)): ?>
                <tr>
                    <td colspan="6" style="text-align:center;">Belum ada data barang.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>
