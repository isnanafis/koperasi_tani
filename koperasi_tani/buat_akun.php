<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Akses Ditolak.");
}
include 'koneksi.php';

$nik = $_GET['nik'];
$error = '';

$anggota_info = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT Nama FROM Anggota WHERE NIK='$nik'"));
$nama_anggota = $anggota_info['Nama'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $default_password = '123456'; // Password default

    $check_sql = "SELECT id FROM users WHERE username = ?";
    $stmt_check = mysqli_prepare($koneksi, $check_sql);
    mysqli_stmt_bind_param($stmt_check, "s", $username);
    mysqli_stmt_execute($stmt_check);
    if(mysqli_stmt_get_result($stmt_check)->num_rows > 0) {
        $error = "Username sudah digunakan.";
    } else {
        $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
        $insert_sql = "INSERT INTO users (username, password, role, nik_anggota) VALUES (?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($koneksi, $insert_sql);
        mysqli_stmt_bind_param($stmt_insert, "ssss", $username, $hashed_password, $role, $nik);
        
        if (mysqli_stmt_execute($stmt_insert)) {
            $_SESSION['info_akun_baru'] = "Akun untuk <strong>".htmlspecialchars($nama_anggota)."</strong> berhasil dibuat. Username: <strong>$username</strong>, Password Default: <strong>$default_password</strong>";
            header("Location: index.php?page=manajemen_user");
            exit();
        } else {
            $error = "Terjadi kesalahan.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Buat Akun Baru</title><link rel="stylesheet" href="style.css"></head>
<body>
<div style="max-width:500px; margin:40px auto; padding:20px; background:#fff;">
    <h2>Buat Akun untuk: <?php echo htmlspecialchars($nama_anggota); ?></h2>
    <?php if($error): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
    <form method="POST">
        <p><label>Username:</label><br><input type="text" name="username" required></p>
        <p><label>Password:</label><br><input type="text" value="123456" disabled> <em>(Password default)</em></p>
        <p><label>Peran (Role):</label><br>
            <select name="role" required>
                <option value="anggota">Anggota</option>
                <option value="pengurus">Pengurus</option>
            </select>
        </p>
        <button type="submit" class="button button-tambah">Buat Akun</button>
    </form>
</div>
</body>
</html>
