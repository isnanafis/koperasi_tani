<?php
session_start();
include 'koneksi.php';
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nik'] = $user['nik_anggota'];

            if ($user['role'] != 'admin') {
                $anggota_sql = "SELECT Nama, ID_Kelompok FROM Anggota WHERE NIK = ?";
                $stmt_anggota = mysqli_prepare($koneksi, $anggota_sql);
                mysqli_stmt_bind_param($stmt_anggota, "s", $user['nik_anggota']);
                mysqli_stmt_execute($stmt_anggota);
                $anggota_result = mysqli_stmt_get_result($stmt_anggota);
                if ($anggota_info = mysqli_fetch_assoc($anggota_result)) {
                    $_SESSION['nama'] = $anggota_info['Nama'];
                    $_SESSION['id_kelompok'] = $anggota_info['ID_Kelompok'];
                }
            } else {
                $_SESSION['nama'] = 'Administrator';
            }
            header("Location: index.php");
            exit();
        } else { $error = 'Password salah!'; }
    } else { $error = 'Username tidak ditemukan!'; }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Login</title><link rel="stylesheet" href="style.css"></head><body>
<div style="max-width:400px;margin:100px auto;padding:20px;background:#fff;border-radius:8px;">
<h2>Login Koperasi Tani</h2><?php if($error): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
<form method="POST"><p><label>Username:</label><input type="text" name="username" required></p><p><label>Password:</label><input type="password" name="password" required></p><button type="submit" class="button button-tambah">Login</button></form>
</div><style>input{width:100%;padding:8px;box-sizing:border-box;}</style></body></html>

