<?php
// Query utama untuk mengambil data anggota
$sql = "SELECT va.*, p.Jabatan, TIMESTAMPDIFF(YEAR, va.Tanggal_Lahir, CURDATE()) AS Usia 
        FROM View_Anggota va 
        LEFT JOIN Pengurus p ON va.NIK = p.NIK";

// Filter data berdasarkan role
if ($role == 'pengurus' || $role == 'anggota') {
    $sql .= " WHERE va.ID_Kelompok = " . intval($logged_in_id_kelompok);
}
$sql .= " ORDER BY va.Nama ASC";
$result = mysqli_query($koneksi, $sql);

// Ambil status dari URL untuk menampilkan notifikasi
$status = isset($_GET['status']) ? $_GET['status'] : '';
?>

<h2>Data Anggota</h2>

<!-- Tampilkan notifikasi berdasarkan status dari URL -->
<?php
if ($status == 'sukses_pengurus') {
    echo '<div class="alert alert-sukses">Anggota berhasil dijadikan pengurus.</div>';
} elseif ($status == 'gagal_pengurus') {
    echo '<div class="alert alert-error">Gagal: Anggota ini sudah menjadi pengurus.</div>';
} elseif ($status == 'gagal_db') {
    echo '<div class="alert alert-error">Terjadi kesalahan pada database saat memproses data.</div>';
}
?>

<!-- Tombol Tambah Anggota hanya untuk Admin -->
<?php if ($role == 'admin'): ?>
    <a href="tambah_anggota.php" class="button button-tambah">Tambah Anggota Baru</a>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Usia</th>
            <?php if ($role != 'anggota'): ?>
                <th>Alamat Lengkap</th>
            <?php endif; ?>
            <th>Jabatan</th>
            <?php if ($role == 'admin'): ?>
                <th>Aksi</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['Nama']); ?></td>
            <td><?php echo htmlspecialchars($row['Usia']); ?> Tahun</td>
            <?php if ($role != 'anggota'): ?>
                <td><?php echo htmlspecialchars($row['Alamat_Lengkap']); ?></td>
            <?php endif; ?>
            <td><?php echo htmlspecialchars($row['Jabatan'] ? $row['Jabatan'] : 'Anggota'); ?></td>
            
            <!-- Kolom Aksi hanya untuk Admin -->
            <?php if ($role == 'admin'): ?>
            <td>
                <!-- REVISI: Tombol Jadikan Pengurus menjadi Dropdown -->
                <?php if (!$row['Jabatan']): ?>
                    <form action="tambah_pengurus.php" method="POST" style="display:inline-block;">
                        <input type="hidden" name="nik" value="<?php echo $row['NIK']; ?>">
                        <select name="jabatan" style="padding: 3px; border-radius: 4px;">
                            <option value="Ketua">Ketua</option>
                            <option value="Sekretaris">Sekretaris</option>
                            <option value="Bendahara">Bendahara</option>
                        </select>
                        <button type="submit" class="button button-edit" style="padding: 4px 8px; font-size: 12px; vertical-align: middle;">Jadikan</button>
                    </form>
                <?php endif; ?>
                
                <a href="hapus_anggota.php?nik=<?php echo $row['NIK']; ?>" class="button button-hapus" onclick="return confirm('Yakin ingin menghapus anggota ini? Tindakan ini tidak bisa dibatalkan.')">Hapus</a>
            </td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
