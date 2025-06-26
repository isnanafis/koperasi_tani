<?php
session_start();

// harus sudah jd anggota
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'anggota') {
    die("Akses Ditolak. Halaman ini hanya untuk anggota koperasi.");
}

include 'koneksi.php';

// --- REVISI LOGIKA ---
// Cek pinjaman aktif: yaitu yang statusnya 'Menunggu' ATAU ('Disetujui' DAN belum lunas).
// Pinjaman yang 'Ditolak' tidak lagi dihitung sebagai pinjaman aktif.
$check_active_loan_sql = "SELECT COUNT(*) as total_aktif FROM View_Pinjaman 
                          WHERE NIK = ? AND (Status_Verifikasi = 'Menunggu' OR (Status_Verifikasi = 'Disetujui' AND Sisa_Tagihan > 0))";

$stmt_check = mysqli_prepare($koneksi, $check_active_loan_sql);
mysqli_stmt_bind_param($stmt_check, "s", $_SESSION['nik']);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$active_loan_count = mysqli_fetch_assoc($result_check)['total_aktif'];

if($active_loan_count > 0) {
    die("Akses Ditolak. Anda masih memiliki pinjaman yang sedang diverifikasi atau belum lunas.");
}


if(isset($_POST['ajukan_pinjaman'])){
    $pokok = $_POST['pokok_pinjaman'];
    $lama = 10; 
    $nik = $_SESSION['nik'];

    
    $sql_pinjaman = "INSERT INTO Pinjaman (NIK, Tanggal_Pinjaman, Pokok_Pinjaman, Lama_Angsuran) VALUES (?, CURDATE(), ?, ?)";
    $stmt_pinjaman = mysqli_prepare($koneksi, $sql_pinjaman);
    mysqli_stmt_bind_param($stmt_pinjaman, "sdi", $nik, $pokok, $lama);
    
    if(mysqli_stmt_execute($stmt_pinjaman)){
        $id_pinjaman_baru = mysqli_insert_id($koneksi);

        $sql_verifikasi = "INSERT INTO Verifikasi (ID_Pinjaman, Status) VALUES (?, 'Menunggu')";
        $stmt_verifikasi = mysqli_prepare($koneksi, $sql_verifikasi);
        mysqli_stmt_bind_param($stmt_verifikasi, "i", $id_pinjaman_baru);
        mysqli_stmt_execute($stmt_verifikasi);

        header("Location: index.php?page=pinjaman&status=sukses_pengajuan");
        exit();
    } else {
        echo "Terjadi kesalahan saat menyimpan data pinjaman.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ajukan Pinjaman</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div style="max-width:500px; margin:40px auto; padding:20px; background:#fff; border-radius: 8px;">
    <h2>Form Pengajuan Pinjaman</h2>
    <p>Semua pinjaman akan diangsur selama <strong>10 bulan</strong>.</p>
    <form method="POST" action="tambah_pinjaman.php">
        <div class="form-group">
            <label>Jumlah Pinjaman:</label><br>
            <select name="pokok_pinjaman" required style="width:100%; padding: 8px;">
                <option value="500000">Rp 500.000</option>
                <option value="1000000">Rp 1.000.000</option>
                <option value="1500000">Rp 1.500.000</option>
                <option value="2000000">Rp 2.000.000</option>
            </select>
        </div>
        <button type="submit" name="ajukan_pinjaman" class="button button-tambah">Ajukan</button>
        <a href="index.php?page=pinjaman" style="margin-left: 10px;">Batal</a>
    </form>
</div>
</body>
</html>
<style>
.form-group { margin-bottom: 15px; }
label { font-weight: bold; }
</style>
