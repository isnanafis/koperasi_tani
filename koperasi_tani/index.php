<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'koneksi.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'profil';
if ($page == 'profil' && $_SESSION['role'] == 'admin') $page = 'anggota';

$valid_pages = ['anggota', 'kelompok', 'pengurus', 'pinjaman', 'angsuran', 'shu', 'profil', 'manajemen_user', 'verifikasi'];
$page_file = in_array($page, $valid_pages) ? "pages/{$page}.php" : "pages/anggota.php";

$role = $_SESSION['role'];
$logged_in_nik = isset($_SESSION['nik']) ? $_SESSION['nik'] : null;
$logged_in_id_kelompok = isset($_SESSION['id_kelompok']) ? $_SESSION['id_kelompok'] : null;
?>
<!DOCTYPE html><html lang="id"><head>
    <meta charset="UTF-8"><title>Sistem Koperasi Tani</title>
    <link rel="stylesheet" href="style.css?v=3.0">
</head><body>
    <header>
        <h1>Sistem Peminjaman Koperasi Tani</h1>
        <div class="user-info">
            Login sebagai: <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong> (<?php echo ucfirst($role); ?>)
            | <a href="?page=profil">Profil Saya</a> | <a href="logout.php">Logout</a>
        </div>
    </header>
    <nav class="nav-tabs">
        <a href="?page=anggota" class="<?php echo ($page == 'anggota') ? 'active' : ''; ?>">Anggota</a>
        <a href="?page=kelompok" class="<?php echo ($page == 'kelompok') ? 'active' : ''; ?>">Kelompok Tani</a>
        <a href="?page=pengurus" class="<?php echo ($page == 'pengurus') ? 'active' : ''; ?>">Pengurus</a>
        <a href="?page=pinjaman" class="<?php echo ($page == 'pinjaman') ? 'active' : ''; ?>">Pinjaman</a>
        <?php if ($role == 'pengurus' || $role == 'admin'): ?>
            <a href="?page=verifikasi" class="<?php echo ($page == 'verifikasi') ? 'active' : ''; ?>">Verifikasi</a>
        <?php endif; ?>
        <a href="?page=angsuran" class="<?php echo ($page == 'angsuran') ? 'active' : ''; ?>">Angsuran</a>
        <a href="?page=shu" class="<?php echo ($page == 'shu') ? 'active' : ''; ?>">SHU</a>
        <?php if ($role == 'admin'): ?>
            <a href="?page=manajemen_user" class="<?php echo ($page == 'manajemen_user') ? 'active' : ''; ?>">Manajemen User</a>
        <?php endif; ?>
    </nav>
    <main class="content">
        <?php if (file_exists($page_file)) { include $page_file; } else { echo "<h2>Halaman tidak ditemukan.</h2>"; } ?>
    </main>
</body></html>
<style>.user-info a { color: #15afac; font-weight: bold; }</style>