<?php
// Ganti dengan kredensial user database Anda
$server   = "127.0.0.1";
$username = "admin_koperasi";
$password = "PasswordAdmin123!";
$database = "koperasi_tani";

$koneksi = mysqli_connect($server, $username, $password, $database);
if (!$koneksi) die("Koneksi GAGAL: " . mysqli_connect_error());
?>
