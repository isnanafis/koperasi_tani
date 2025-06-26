<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Akses Ditolak.");
}
include 'koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: index.php?page=angsuran");
    exit();
}

$id_angsuran = intval($_GET['id']);

$sql = "DELETE FROM Angsuran WHERE ID_Angsuran = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_angsuran);

if (mysqli_stmt_execute($stmt)) {
    // Redirect back with a success message
    header("Location: index.php?page=angsuran&status=sukses_hapus");
    exit();
} else {
    // Handle potential errors, e.g., display an error message
    echo "Gagal menghapus data angsuran. Error: " . mysqli_error($koneksi);
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>
