<?php
$sql = "SELECT kt.*, COUNT(a.NIK) as jumlah_anggota FROM Kelompok_Tani kt LEFT JOIN Anggota a ON kt.ID_Kelompok = a.ID_Kelompok GROUP BY kt.ID_Kelompok ORDER BY kt.ID_Kelompok ASC";
$result = mysqli_query($koneksi, $sql);
?>
<h2>Data Kelompok Tani</h2>
<?php if ($role == 'admin'): ?>
    <a href="tambah_kelompok.php" class="button button-tambah">Tambah Kelompok Tani Baru</a>
<?php endif; ?>
<table>
<thead><tr><th>ID</th><th>Nama Kelompok</th><th>Alamat (Desa)</th><th>Jumlah Anggota</th></tr></thead>
<tbody>
<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?php echo htmlspecialchars($row['ID_Kelompok']); ?></td>
    <td><?php echo htmlspecialchars($row['Nama_Kelompok']); ?></td>
    <td><?php echo htmlspecialchars($row['Alamat_Kelompok']); ?></td>
    <td><?php echo htmlspecialchars($row['jumlah_anggota']); ?> Orang</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
