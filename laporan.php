<?php
$active_page = 'laporan';
$page_title = 'Laporan Transaksi';
require_once 'layouts/header.php';

require_once 'classes/Transaksi.php';
$transaksiObj = new Transaksi();

$laporan = $transaksiObj->getAllLaporan();
?>

<div class="card">
    <div class="card-header">
        <h3>Riwayat Penjualan</h3>
        <button onclick="window.print()" class="btn btn-success" style="padding: 0.5rem 1rem;">Cetak Laporan</button>
    </div>

    <div class="table-responsive">
        <table class="table" style="font-size: 0.95rem;">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>ID Nota</th>
                    <th>Tanggal Transaksi</th>
                    <th>Petugas Kasir</th>
                    <th>Total Belanja</th>
                    <th>Rincian Produk</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($laporan as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong>INV-<?= str_pad($row['id_penjualan'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                    <td><?= date('d F Y', strtotime($row['tanggal'])) ?></td>
                    <td>
                        <span style="background:var(--bg-color); padding: 2px 8px; border-radius:12px; font-size:0.8rem;">
                            <?= htmlspecialchars($row['nama_kasir']) ?>
                        </span>
                    </td>
                    <td><strong style="color:var(--primary); font-size:1.1rem;">Rp <?= number_format($row['total'], 0, ',', '.') ?></strong></td>
                    <td>
                        <ul style="padding-left: 1rem; margin:0; color:var(--text-muted); font-size:0.85rem; list-style-type:circle;">
                        <?php 
                        $details = $transaksiObj->getLaporanDetail($row['id_penjualan']);
                        foreach($details as $d):
                        ?>
                            <li><?= htmlspecialchars($d['nama_barang']) ?> (<?= $d['jumlah'] ?>x) = Rp <?= number_format($d['subtotal'], 0, ',', '.') ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($laporan)): ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding: 2rem;">Belum ada data transaksi.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
@media print {
    .sidebar, .user-info, .btn, hr { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .topbar { box-shadow: none; border-bottom: 2px solid #ccc; padding-left:0; padding-right:0; }
    .card { box-shadow: none !important; border: 1px solid #ddd; }
    body { background: white !important; }
}
</style>

<?php require_once 'layouts/footer.php'; ?>
