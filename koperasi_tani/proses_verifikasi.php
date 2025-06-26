<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) {
    die("Akses Ditolak.");
}
include 'koneksi.php';

$id_pinjaman = $_GET['id'];
$aksi = $_GET['aksi'];
$nik_penyetuju = $_SESSION['nik'];
$status_baru = ($aksi == 'setuju') ? 'Disetujui' : 'Ditolak';

$sql = "UPDATE Verifikasi SET Status=?, Tanggal_Verifikasi=CURDATE(), NIK_Penyetuju=? WHERE ID_Pinjaman=?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "ssi", $status_baru, $nik_penyetuju, $id_pinjaman);

if(mysqli_stmt_execute($stmt)){
    header("Location: index.php?page=verifikasi&status=sukses");
} else {
    echo "Gagal memproses verifikasi.";
}
?>