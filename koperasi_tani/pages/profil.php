<?php
$pesan = '';
$is_own_profile = true;
$target_nik = $logged_in_nik;
if(isset($_POST['ganti_password'])){
    $password_lama = $_POST['password_lama']; $password_baru = $_POST['password_baru']; $konfirmasi_password = $_POST['konfirmasi_password'];
    $sql_user = "SELECT password FROM users WHERE id = ?";
    $stmt_user = mysqli_prepare($koneksi, $sql_user); mysqli_stmt_bind_param($stmt_user, "i", $_SESSION['user_id']); mysqli_stmt_execute($stmt_user);
    $current_hashed_password = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_user))['password'];
    if (password_verify($password_lama, $current_hashed_password)) {
        if ($password_baru === $konfirmasi_password) {
            $new_hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
            $sql_update_pass = "UPDATE users SET password = ? WHERE id = ?";
            $stmt_pass = mysqli_prepare($koneksi, $sql_update_pass); mysqli_stmt_bind_param($stmt_pass, "si", $new_hashed_password, $_SESSION['user_id']);
            if(mysqli_stmt_execute($stmt_pass)) $pesan = "<div class='alert-sukses'>Password berhasil diubah.</div>";
            else $pesan = "<div class='alert-error'>Gagal mengubah password.</div>";
        } else $pesan = "<div class='alert-error'>Konfirmasi password baru tidak cocok.</div>";
    } else $pesan = "<div class='alert-error'>Password lama salah.</div>";
}
if(isset($_FILES['profile_picture'])){ /* ... logika upload foto ... */ }
$profil = null;
if($target_nik){
    $sql_profil = "SELECT va.* FROM View_Anggota va WHERE NIK = ?";
    $stmt = mysqli_prepare($koneksi, $sql_profil); mysqli_stmt_bind_param($stmt, "s", $target_nik); mysqli_stmt_execute($stmt);
    $profil = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}
?>
<h2>Profil Pengguna</h2><?php echo $pesan; ?>
<div class="profil-container"><div class="profil-foto"><img src="uploads/<?php echo htmlspecialchars($profil['profile_picture'] ?? 'default.png'); ?>" onerror="this.src='uploads/default.png';"><form method="POST" enctype="multipart/form-data"><input type="file" name="profile_picture" required><button type="submit" class="button button-edit">Upload</button></form></div>
<div class="profil-data"><?php if($profil): ?><h3>Data Diri: <?php echo htmlspecialchars($profil['Nama']); ?></h3><table>
<tr><th>NIK</th><td><?php echo htmlspecialchars($profil['NIK']); ?></td></tr>
<tr><th>Nama</th><td><?php echo htmlspecialchars($profil['Nama']); ?></td></tr>
<tr><th>Kelompok</th><td><?php echo htmlspecialchars($profil['Nama_Kelompok']); ?></td></tr>
<tr><th>Alamat</th><td><?php echo htmlspecialchars($profil['Alamat_Lengkap']); ?></td></tr>
<tr><th>Tanggal Lahir</th><td><?php echo htmlspecialchars($profil['Tanggal_Lahir']); ?></td></tr>
<tr><th>No. HP</th><td><?php echo htmlspecialchars($profil['No_HP']); ?></td></tr>
<tr><th>Jenis Kelamin</th><td><?php echo htmlspecialchars($profil['Jenis_Kelamin']); ?></td></tr>
</table><?php else: ?><p>Profil tidak tersedia.</p><?php endif; ?></div></div>
<div class="ganti-password"><h3 style="margin-top:30px;">Ganti Password</h3><form method="POST"><p><label>Password Lama:</label><br><input type="password" name="password_lama" required></p><p><label>Password Baru:</label><br><input type="password" name="password_baru" required></p><p><label>Konfirmasi:</label><br><input type="password" name="konfirmasi_password" required></p><button type="submit" name="ganti_password" class="button button-tambah">Update</button></form></div>
<style>.profil-container{display:flex;gap:20px;}.profil-foto{flex-basis:200px;}.profil-foto img{width:100%;border-radius:50%;}.profil-data{flex-grow:1;}.ganti-password{max-width:400px;margin-top:20px;}.alert-sukses{padding:10px;background:#dff0d8;}.alert-error{padding:10px;background:#f2dede;}</style>
