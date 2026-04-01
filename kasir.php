<?php
$active_page = 'kasir';
$page_title = 'Menu Kasir / Penjualan';
require_once 'layouts/header.php';
$auth->requireRole(['petugas']);

require_once 'classes/Barang.php';
require_once 'classes/Transaksi.php';
$barangObj = new Barang();
$transaksiObj = new Transaksi();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action == 'add' && isset($_POST['id_barang']) && isset($_POST['qty'])) {
        $id_barang = $_POST['id_barang'];
        $qty = (int)$_POST['qty'];
        
        $barang = $barangObj->getById($id_barang);
        if ($barang && $barang['stok'] >= $qty && $qty > 0) {
            $subtotal = $barang['harga'] * $qty;
            
            $item_exists = false;
            foreach ($_SESSION['cart'] as $k => $item) {
                if ($item['id'] == $id_barang) {
                    if ($barang['stok'] >= ($item['qty'] + $qty)) {
                        $_SESSION['cart'][$k]['qty'] += $qty;
                        $_SESSION['cart'][$k]['subtotal'] = $_SESSION['cart'][$k]['qty'] * $item['harga'];
                        $msg = "Quantity ditambahkan.";
                    } else {
                        $error = "Stok tidak mencukupi untuk ditambah.";
                    }
                    $item_exists = true;
                    break;
                }
            }
            
            if (!$item_exists) {
                $_SESSION['cart'][] = [
                    'id' => $id_barang,
                    'kode' => $barang['kode_barang'],
                    'nama' => $barang['nama_barang'],
                    'harga' => $barang['harga'],
                    'qty' => $qty,
                    'subtotal' => $subtotal
                ];
                $msg = "Barang ditambahkan ke keranjang.";
            }
        } else {
            $error = "Pilih barang yang valid dan pastikan stoknya mencukupi.";
        }
        
    } elseif ($action == 'remove' && isset($_GET['index'])) {
        $index = $_GET['index'];
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); 
        }
    } elseif ($action == 'clear') {
        $_SESSION['cart'] = [];
    } elseif ($action == 'checkout') {
        if (!empty($_SESSION['cart'])) {
            $id_penjualan = $transaksiObj->simpanTransaksi($_SESSION['user_id'], $_SESSION['cart']);
            if ($id_penjualan) {
                $_SESSION['cart'] = [];
                $success_checkout = "Transaksi Berhasil Disimpan!";
            } else {
                $error = "Gagal menyimpan transaksi. Pastikan stok mencukupi.";
            }
        } else {
            $error = "Keranjang masih kosong.";
        }
    }
}

$barangs = $barangObj->getAll();
$total_bayar = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_bayar += $item['subtotal'];
}
?>

<?php if(isset($success_checkout)): ?>
<div class="alert alert-success" style="padding:1.5rem; text-align:center;">
    <h2><?= htmlspecialchars($success_checkout) ?></h2>
    <p>Terima kasih. Lanjutkan transaksi baru atau lihat laporan.</p>
</div>
<?php endif; ?>

<div class="stats-grid" style="grid-template-columns: 1fr 2fr;">
    <!-- Form Input Section -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3>Pilih Barang</h3>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger" style="padding: 0.5rem;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if(isset($msg)): ?>
            <div class="alert alert-success" style="padding: 0.5rem;"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="POST" action="kasir.php?action=add">
            <div class="form-group">
                <label>Produk / Barcode</label>
                <select name="id_barang" class="form-control" required style="font-size: 0.9rem;">
                    <option value="">-- Pilih --</option>
                    <?php foreach($barangs as $b): ?>
                        <option value="<?= $b['id_barang'] ?>">
                            [<?= htmlspecialchars($b['kode_barang']) ?>] <?= htmlspecialchars($b['nama_barang']) ?> (<?= $b['stok'] ?>) - Rp <?= number_format($b['harga'], 0, ',', '.') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="qty" class="form-control" value="1" min="1" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Tambahkan</button>
        </form>
    </div>

    <!-- Cart Section -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3>Keranjang Penjualan</h3>
            <a href="kasir.php?action=clear" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size:0.8rem;" onclick="return confirm('Kosongkan semua pesanan?');">Bersihkan</a>
        </div>
        <div class="table-responsive" style="min-height: 200px;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Barang</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($_SESSION['cart'])): ?>
                    <tr><td colspan="6" style="text-align:center; color: var(--text-muted); padding:3rem 0;">Keranjang belanja masih kosong</td></tr>
                    <?php else: ?>
                        <?php foreach($_SESSION['cart'] as $index => $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['kode']) ?></td>
                            <td><?= htmlspecialchars($item['nama']) ?></td>
                            <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                            <td><strong><?= $item['qty'] ?></strong></td>
                            <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                            <td><a href="kasir.php?action=remove&index=<?= $index ?>" style="color:var(--danger); font-size:1.5rem; font-weight:bold; text-decoration:none;" title="Hapus">&times;</a></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div style="background:#eef2f9; padding:1.5rem; border-radius:12px; margin-top:1.5rem; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h4 style="margin:0; font-size: 1rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Total Bayar</h4>
                <h1 style="margin:0; color:var(--primary-dark); font-size: 2.5rem; font-weight:800;">Rp <?= number_format($total_bayar, 0, ',', '.') ?></h1>
            </div>
            <?php if(!empty($_SESSION['cart'])): ?>
            <form action="kasir.php?action=checkout" method="POST">
                <button type="submit" class="btn btn-success" style="font-size:1.2rem; padding:1.2rem 2.5rem; border-radius:12px; font-weight:700; box-shadow:0 8px 15px rgba(46, 204, 113, 0.3);" onclick="return confirm('Selesaikan transaksi dan potong stok?');">CHECKOUT</button>
            </form>
            <?php else: ?>
                <button class="btn btn-success" style="font-size:1.2rem; padding:1.2rem 2.5rem; border-radius:12px; font-weight:700; opacity:0.5; cursor:not-allowed;" disabled>CHECKOUT</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>
