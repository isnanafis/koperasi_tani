<?php
// Pastikan hanya admin yang bisa mengakses halaman ini
if ($role != 'admin') {
    die("Akses Ditolak. Halaman ini hanya untuk Administrator.");
}

// Query untuk mencari anggota yang NIK-nya belum ada di tabel 'users'
$sql = "SELECT a.NIK, a.Nama 
        FROM Anggota a 
        LEFT JOIN users u ON a.NIK = u.nik_anggota 
        WHERE u.id IS NULL
        ORDER BY a.Nama";
$result_no_account = mysqli_query($koneksi, $sql);

// Query untuk mencari user yang sudah punya akun
$sql_has_account = "SELECT u.id, u.username, u.role, a.Nama 
                    FROM users u
                    LEFT JOIN Anggota a ON u.nik_anggota = a.NIK
                    WHERE u.role != 'admin'
                    ORDER BY a.Nama";
$result_has_account = mysqli_query($koneksi, $sql_has_account);
?>

<h2>Manajemen Akun Pengguna</h2>

<h3>Anggota yang Belum Memiliki Akun</h3>
<p>Daftar anggota di bawah ini sudah terdaftar di koperasi tetapi belum memiliki akun untuk login. Klik "Buat Akun" untuk membuatkan mereka username dan password.</p>
<table>
    <thead>
        <tr><th>NIK</th><th>Nama</th><th>Aksi</th></tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($result_no_account)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['NIK']); ?></td>
            <td><?php echo htmlspecialchars($row['Nama']); ?></td>
            <td>
                <a href="buat_akun.php?nik=<?php echo $row['NIK']; ?>" class="button button-edit">Buat Akun</a>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php if(mysqli_num_rows($result_no_account) == 0): ?>
            <tr><td colspan="3" style="text-align:center;">Semua anggota sudah memiliki akun.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<h3 style="margin-top: 40px;">Pengguna yang Sudah Terdaftar</h3>
<table>
    <thead>
        <tr><th>Username</th><th>Nama Anggota</th><th>Peran (Role)</th></tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($result_has_account)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['Nama']); ?></td>
            <td><?php echo ucfirst(htmlspecialchars($row['role'])); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
