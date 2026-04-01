<?php
require_once __DIR__ . '/../classes/Auth.php';
$auth = new Auth();
$auth->requireAuth();

if (!isset($active_page)) {
    $active_page = 'dashboard';
}

$level = $_SESSION['level'] ?? '';
$nama = $_SESSION['nama'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Kasir P4</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .sidebar-menu li a {
            text-decoration: none;
            color: var(--text-main);
            display: block;
        }
        .main-content {
            background-color: var(--bg-color);
        }
        .sidebar {
            width: 260px;
            background-color: var(--white);
            box-shadow: var(--shadow-md);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }
        .topbar {
            background-color: var(--white);
            padding: 1rem 2rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>KASIR P4</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="<?= $active_page == 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
                
                <?php if ($level == 'administrator'): ?>
                <li><a href="barang.php" class="<?= $active_page == 'barang' ? 'active' : '' ?>">Barang & Stok</a></li>
                <li><a href="user.php" class="<?= $active_page == 'user' ? 'active' : '' ?>">Data Pengguna</a></li>
                <?php endif; ?>
                
                <?php if ($level == 'petugas'): ?>
                <li><a href="kasir.php" class="<?= $active_page == 'kasir' ? 'active' : '' ?>">Penjualan / Kasir</a></li>
                <?php endif; ?>
                
                <li><a href="laporan.php" class="<?= $active_page == 'laporan' ? 'active' : '' ?>">Laporan Transaksi</a></li>
                
                <li><hr style="margin: 1rem 0; border: 0; border-top: 1px solid var(--border);"></li>
                <li><a href="logout.php" style="color: var(--danger); font-weight: 600;">Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <h2><?= htmlspecialchars($page_title ?? 'Dashboard') ?></h2>
                <div class="user-info">
                    <span class="user-role"><?= strtoupper($level) ?></span>
                    <strong><?= htmlspecialchars($nama) ?></strong>
                </div>
            </header>
            <div class="content-wrapper">
