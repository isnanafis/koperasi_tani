<?php
session_start();
// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Akses Ditolak.");
}
include 'koneksi.php';
$error = ''; // Variabel untuk menyimpan pesan error

// Cek jika form disubmit
if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama_kelompok']); // trim() untuk menghapus spasi di awal/akhir
    $alamat = trim($_POST['alamat_kelompok']); // trim() untuk menghapus spasi di awal/akhir

    // --- BLOK VALIDASI ---
    // 1. Validasi Nama Kelompok
    if (empty($nama)) {
        $error = "Nama kelompok tidak boleh kosong.";
    } 
    else if (is_numeric($nama)) {
        $error = "Nama kelompok tidak boleh hanya terdiri dari angka.";
    }
    else if (strlen(preg_replace('/[^a-zA-Z]/', '', $nama)) < 3) {
        $error = "Nama kelompok harus mengandung minimal 3 huruf.";
    }
    // 2. Validasi Alamat Kelompok (REVISI BARU)
    else if (empty($alamat)) {
        $error = "Alamat kelompok tidak boleh kosong.";
    }
    else if (is_numeric($alamat)) {
        $error = "Alamat kelompok tidak boleh hanya terdiri dari angka.";
    }
    else if (strlen(preg_replace('/[^a-zA-Z]/', '', $alamat)) < 3) {
        $error = "Alamat kelompok harus mengandung minimal 3 huruf.";
    }
    // 3. Validasi nama kelompok sudah ada atau belum
    else {
        $check_sql = "SELECT ID_Kelompok FROM Kelompok_Tani WHERE Nama_Kelompok = ?";
        $stmt_check = mysqli_prepare($koneksi, $check_sql);
        mysqli_stmt_bind_param($stmt_check, "s", $nama);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        if (mysqli_num_rows($result_check) > 0) {
            $error = "Nama kelompok tani '".htmlspecialchars($nama)."' sudah ada. Silakan gunakan nama lain.";
        }
    }
    // --- AKHIR BLOK VALIDASI ---

    // Jika tidak ada error, lanjutkan proses simpan ke database
    if (empty($error)) {
        $sql = "INSERT INTO Kelompok_Tani (Nama_Kelompok, Alamat_Kelompok) VALUES (?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $nama, $alamat);
        if(mysqli_stmt_execute($stmt)) {
            // Jika berhasil, arahkan ke halaman kelompok dengan status sukses
            header("Location: index.php?page=kelompok&status=sukses_tambah");
            exit();
        } else {
            // Jika gagal simpan ke db
            $error = "Gagal menyimpan data ke database.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Kelompok Tani</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div style="max-width:500px; margin:40px auto; padding:20px; background:#fff; border-radius: 8px;">
    <h2>Form Tambah Kelompok Tani</h2>
    
    <!-- Tampilkan pesan error jika ada -->
    <?php if(!empty($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <p>
            <label>Nama Kelompok:</label><br>
            <input type="text" name="nama_kelompok" required value="<?php echo isset($_POST['nama_kelompok']) ? htmlspecialchars($_POST['nama_kelompok']) : ''; ?>">
        </p>
        <p>
            <label>Alamat (Desa):</label><br>
            <input type="text" name="alamat_kelompok" required value="<?php echo isset($_POST['alamat_kelompok']) ? htmlspecialchars($_POST['alamat_kelompok']) : ''; ?>">
        </p>
        <button type="submit" name="simpan" class="button button-tambah">Simpan</button>
        <a href="index.php?page=kelompok" style="margin-left: 10px;">Batal</a>
    </form>
</div>
<style>
    .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; text-align: center; }
    .alert-error { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
    input, select {width:100%; padding:8px; box-sizing:border-box;}
</style>
</body>
</html>
