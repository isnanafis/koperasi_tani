<?php
// Ambil status dari URL untuk menampilkan notifikasi
$status = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "SELECT p.Jabatan, a.Nama, kt.Nama_Kelompok FROM Pengurus p JOIN Anggota a ON p.NIK = a.NIK JOIN Kelompok_Tani kt ON a.ID_Kelompok = kt.ID_Kelompok";
if ($role == 'anggota') {
    $sql .= " WHERE a.ID_Kelompok = " . intval($logged_in_id_kelompok);
}
$sql .= " ORDER BY a.ID_Kelompok, p.Jabatan";
$result = mysqli_query($koneksi, $sql);
?>
<h2>Data Pengurus</h2>

<!-- BLOK NOTIFIKASI BARU -->
<?php
if ($status == 'sukses_tambah_pengurus') {
    echo '<div class="alert alert-sukses">Data pengurus berhasil ditambahkan.</div>';
} elseif ($status == 'gagal_sudah_jadi_pengurus') {
    echo '<div class="alert alert-error">Gagal: Anggota tersebut sudah menjadi pengurus.</div>';
} elseif ($status == 'gagal_db') {
    echo '<div class="alert alert-error">Terjadi kesalahan pada database.</div>';
}
?>
<!-- AKHIR BLOK NOTIFIKASI -->


<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Asal Kelompok</th>
        </tr>
    </thead>
    <tbody>
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['Nama']); ?></td>
                <td><?php echo htmlspecialchars($row['Jabatan']); ?></td>
                <td><?php echo htmlspecialchars($row['Nama_Kelompok']); ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3" style="text-align: center;">Belum ada data pengurus.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
