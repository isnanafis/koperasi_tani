<?php
// Pastikan hanya pengurus dan admin yg bisa akses
if ($role != 'pengurus' && $role != 'admin') die("Akses ditolak.");

$sql = "SELECT vp.* FROM View_Pinjaman vp JOIN Anggota a ON vp.NIK = a.NIK WHERE vp.Status_Verifikasi = 'Menunggu'";
if ($role == 'pengurus') {
    $sql .= " AND a.ID_Kelompok = " . intval($logged_in_id_kelompok);
}
$result = mysqli_query($koneksi, $sql);
?>
<h2>Verifikasi Pengajuan Pinjaman</h2>
<table>
<thead><tr><th>Nama Anggota</th><th>Tgl Pinjam</th><th>Pokok Pinjaman</th><th>Aksi</th></tr></thead>
<tbody>
<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?php echo htmlspecialchars($row['Nama_Anggota']); ?></td>
    <td><?php echo htmlspecialchars($row['Tanggal_Pinjaman']); ?></td>
    <td>Rp <?php echo number_format($row['Pokok_Pinjaman']); ?></td>
    <td>
        <a href="proses_verifikasi.php?id=<?php echo $row['ID_Pinjaman']; ?>&aksi=setuju" class="button button-edit">Setujui</a>
        <a href="proses_verifikasi.php?id=<?php echo $row['ID_Pinjaman']; ?>&aksi=tolak" class="button button-hapus">Tolak</a>
    </td>
</tr>
<?php endwhile; ?>
<?php if(mysqli_num_rows($result) == 0): ?>
    <tr><td colspan="4" style="text-align:center;">Tidak ada pengajuan pinjaman baru.</td></tr>
<?php endif; ?>
</tbody></table>