<?php
$active_page = 'user';
$page_title = 'Manajemen Pengguna';
require_once 'layouts/header.php';
$auth->requireRole(['administrator']);

require_once 'classes/User.php';
$userObj = new User();

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id == $_SESSION['user_id']) {
        $error = "Tidak dapat menghapus diri sendiri!";
    } else {
        if ($userObj->delete($id)) {
            $msg = "Pengguna berhasil dihapus.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $level = $_POST['level'];
    
    try {
        if ($userObj->create($nama, $username, $password, $level)) {
            $msg = "Pengguna berhasil diregistrasi.";
        } else {
            $error = "Gagal meregistrasi pengguna.";
        }
    } catch (PDOException $e) {
        $error = "Gagal meregistrasi pengguna. Username mungkin sudah digunakan.";
    }
}

$users = $userObj->getAll();
?>

<div class="stats-grid">
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3>Registrasi Pengguna</h3>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if(isset($msg)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Hak Akses / Jabatan</label>
                <select name="level" class="form-control" required>
                    <option value="petugas">Petugas</option>
                    <option value="administrator">Administrator</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Registrasi</button>
        </form>
    </div>

    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3>Daftar Pengguna</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Level</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($users as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td>
                            <span class="user-role" style="background: <?= $row['level']=='administrator' ? 'var(--primary-dark)' : 'var(--primary-light)' ?>">
                                <?= strtoupper($row['level']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if($row['id_user'] != $_SESSION['user_id']): ?>
                                <a href="user.php?delete=<?= $row['id_user'] ?>" class="btn btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;" onclick="return confirm('Yakin ingin menghapus pengguna ini?');">Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>
