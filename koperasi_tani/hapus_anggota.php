<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') die("Akses Ditolak.");
include 'koneksi.php';
$nik = $_GET['nik'];

// --- REVISI LOGIKA ---
// Cek apakah anggota punya pinjaman aktif, yaitu yang statusnya 'Menunggu' ATAU ('Disetujui' DAN belum lunas).
// Pinjaman yang 'Ditolak' tidak lagi dihitung sebagai pinjaman aktif.
$sql_check = "SELECT COUNT(*) as total FROM View_Pinjaman 
              WHERE NIK = ? AND (Status_Verifikasi = 'Menunggu' OR (Status_Verifikasi = 'Disetujui' AND Sisa_Tagihan > 0))";
$stmt_check = mysqli_prepare($koneksi, $sql_check);
mysqli_stmt_bind_param($stmt_check, "s", $nik);
mysqli_stmt_execute($stmt_check);
$pinjaman_aktif = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_check))['total'];

if($pinjaman_aktif > 0){
    die("Gagal menghapus: Anggota masih memiliki pinjaman yang sedang diverifikasi atau belum lunas.");
} else {
    // --- PERBAIKAN LOGIKA PENGHAPUSAN ---
    // SEBELUM MENGHAPUS ANGGOTA, HAPUS DULU SEMUA RIWAYAT PINJAMANNYA (TERMASUK YG DITOLAK)
    // Karena Verifikasi dan Angsuran memiliki ON DELETE CASCADE, kita cukup hapus dari tabel Pinjaman.
    $sql_pinjaman = "DELETE FROM Pinjaman WHERE NIK = ?";
    $stmt_pinjaman = mysqli_prepare($koneksi, $sql_pinjaman);
    mysqli_stmt_bind_param($stmt_pinjaman, "s", $nik);
    mysqli_stmt_execute($stmt_pinjaman); // Eksekusi penghapusan pinjaman

    // Hapus dari tabel users
    $sql_user = "DELETE FROM users WHERE nik_anggota = ?";
    $stmt_user = mysqli_prepare($koneksi, $sql_user);
    mysqli_stmt_bind_param($stmt_user, "s", $nik);
    mysqli_stmt_execute($stmt_user);

    // Terakhir, hapus dari tabel anggota
    $sql_anggota = "DELETE FROM Anggota WHERE NIK = ?";
    $stmt_anggota = mysqli_prepare($koneksi, $sql_anggota);
    mysqli_stmt_bind_param($stmt_anggota, "s", $nik);
    if(mysqli_stmt_execute($stmt_anggota)){
        header("Location: index.php?page=anggota&status=sukses_hapus");
    } else {
        die("Error saat menghapus anggota: " . mysqli_error($koneksi));
    }
}
?>
