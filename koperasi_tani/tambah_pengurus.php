<?php
session_start();
// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Akses Ditolak.");
}
include 'koneksi.php';

// Cek apakah data NIK dan Jabatan dikirim melalui metode POST
if (isset($_POST['nik']) && isset($_POST['jabatan'])) {
    $nik = $_POST['nik'];
    $jabatan = $_POST['jabatan'];

    // Validasi sederhana untuk jabatan
    $valid_jabatan = ['Ketua', 'Sekretaris', 'Bendahara'];
    if (!in_array($jabatan, $valid_jabatan)) {
        die("Jabatan yang dipilih tidak valid.");
    }

    // Cek terlebih dahulu apakah anggota tersebut sudah menjadi pengurus
    $check_sql = "SELECT NIK FROM Pengurus WHERE NIK = ?";
    $stmt_check = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($stmt_check, "s", $nik);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    
    // Jika sudah ada (jumlah baris > 0), kembalikan dengan pesan error
    if (mysqli_num_rows($result_check) > 0) {
        // DIARAHKAN KE HALAMAN PENGURUS DENGAN STATUS GAGAL
        header("Location: index.php?page=pengurus&status=gagal_sudah_jadi_pengurus");
        exit();
    }

    // Jika belum menjadi pengurus, lanjutkan proses insert
    $sql = "INSERT INTO Pengurus (NIK, Jabatan, Tanggal_Mulai_Jabatan) VALUES (?, ?, CURDATE())";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $nik, $jabatan);

    if(mysqli_stmt_execute($stmt)){
        // JIKA BERHASIL, DIARAHKAN KE HALAMAN PENGURUS
        header("Location: index.php?page=pengurus&status=sukses_tambah_pengurus");
    } else {
        // JIKA GAGAL, DIARAHKAN KE HALAMAN PENGURUS
        header("Location: index.php?page=pengurus&status=gagal_db");
    }
    exit();

} else {
    // Jika data NIK atau Jabatan tidak dikirim, kembalikan ke halaman pengurus
    header("Location: index.php?page=pengurus");
    exit();
}
?>
