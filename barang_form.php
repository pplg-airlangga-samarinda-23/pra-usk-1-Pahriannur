<?php
$active_page = 'barang';
$page_title = isset($_GET['id']) ? 'Edit Barang' : 'Tambah Barang';
require_once 'layouts/header.php';
$auth->requireRole(['administrator']);

require_once 'classes/Barang.php';
$barangObj = new Barang();

$id = $_GET['id'] ?? '';
$isEdit = !empty($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = $_POST['kode_barang'];
    $nama = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if ($isEdit) {
        $barangObj->update($id, $kode, $nama, $harga, $stok);
        $success = "Barang berhasil diupdate.";
    } else {
        $barangObj->create($kode, $nama, $harga, $stok);
        $success = "Barang berhasil ditambahkan.";
    }
}

$data = ['kode_barang' => '', 'nama_barang' => '', 'harga' => '', 'stok' => ''];
if ($isEdit) {
    $result = $barangObj->getById($id);
    if($result) $data = $result;
}
?>

<div class="card" style="max-width: 600px;">
    <div class="card-header">
        <h3><?= $page_title ?></h3>
        <a href="barang.php" class="btn" style="background:#e0e6ed;">Kembali</a>
    </div>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Kode Barang</label>
            <input type="text" name="kode_barang" class="form-control" required value="<?= htmlspecialchars($data['kode_barang']) ?>" placeholder="Ex: BRG001">
        </div>
        <div class="form-group">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required value="<?= htmlspecialchars($data['nama_barang']) ?>">
        </div>
        <div class="form-group">
            <label>Harga (Rp)</label>
            <input type="number" name="harga" class="form-control" required min="0" value="<?= htmlspecialchars($data['harga']) ?>">
        </div>
        <div class="form-group">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" required min="0" value="<?= htmlspecialchars($data['stok']) ?>">
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Simpan Data</button>
    </form>
</div>

<?php require_once 'layouts/footer.php'; ?>
