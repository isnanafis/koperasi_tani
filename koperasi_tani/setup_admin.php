<?php
include 'koneksi.php'; // Kita butuh koneksi ke DB

// --- DATA ADMIN DEFAULT ---
$username = 'admin';
$password = 'admin123'; // Password mentah
$role = 'admin';
// --------------------------

// Enkripsi password dengan metode paling aman di PHP
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Cek apakah admin sudah ada
$check_sql = "SELECT id FROM users WHERE username = ?";
$stmt_check = mysqli_prepare($koneksi, $check_sql);
mysqli_stmt_bind_param($stmt_check, "s", $username);
mysqli_stmt_execute($stmt_check);
$result = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($result) > 0) {
    echo "<h1>Admin sudah ada.</h1>";
    echo "Silakan hapus file <strong>setup_admin.php</strong> ini dari server Anda karena sudah tidak diperlukan.";
} else {
    // Jika belum ada, masukkan admin baru
    $insert_sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
    $stmt_insert = mysqli_prepare($koneksi, $insert_sql);
    mysqli_stmt_bind_param($stmt_insert, "sss", $username, $hashed_password, $role);

    if (mysqli_stmt_execute($stmt_insert)) {
        echo "<h1>Admin berhasil dibuat!</h1>";
        echo "<p>Username: <strong>" . $username . "</strong></p>";
        echo "<p>Password: <strong>" . $password . "</strong></p>";
        echo "<p>Silakan login melalui halaman <a href='login.php'>login.php</a>.</p>";
        echo "<hr>";
        echo "<p style='color:red;'><strong>PENTING:</strong> Segera hapus file <strong>setup_admin.php</strong> ini dari server Anda demi keamanan!</p>";
    } else {
        echo "Gagal membuat admin: " . mysqli_error($koneksi);
    }
}
?>
