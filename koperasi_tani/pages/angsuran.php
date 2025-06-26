<?php
$status = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "SELECT ang.*, a.Nama AS Nama_Anggota FROM Angsuran ang JOIN Pinjaman p ON ang.ID_Pinjaman = p.ID_Pinjaman JOIN Anggota a ON p.NIK = a.NIK";
if ($role == 'pengurus') {
    $sql .= " WHERE a.ID_Kelompok = " . intval($logged_in_id_kelompok);
} elseif ($role == 'anggota') {
    $sql .= " WHERE a.NIK = '" . mysqli_real_escape_string($koneksi, $logged_in_nik) . "'";
}
$result = mysqli_query($koneksi, $sql);
?>
<h2>Riwayat Angsuran</h2>

<!-- Menampilkan notifikasi sukses -->
<?php if ($status == 'sukses_hapus'): ?>
    <div class="alert alert-sukses">Data angsuran berhasil dihapus.</div>
<?php endif; ?>
<?php if ($status == 'sukses_tambah'): ?>
    <div class="alert alert-sukses">Data angsuran berhasil ditambahkan.</div>
<?php endif; ?>


<?php if ($role == 'admin' || $role == 'pengurus'): ?>
    <a href="tambah_angsuran.php" class="button button-tambah">Input Pembayaran Angsuran</a>
<?php endif; ?>

<table>
<thead>
    <tr>
        <th>Nama Peminjam</th>
        <th>Tgl Bayar</th>
        <th>Jumlah Bayar</th>
        <th>Angsuran Ke-</th>
        <!-- Kolom Aksi hanya untuk Admin -->
        <?php if ($role == 'admin'): ?>
            <th>Aksi</th>
        <?php endif; ?>
    </tr>
</thead>
<tbody>
<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?php echo htmlspecialchars($row['Nama_Anggota']); ?></td>
    <td><?php echo htmlspecialchars($row['Tanggal_Bayar']); ?></td>
    <td>Rp <?php echo number_format($row['Jumlah_Bayar']); ?></td>
    <td><?php echo htmlspecialchars($row['Angsuran_Ke']); ?></td>
    <!-- Tombol Hapus hanya untuk Admin -->
    <?php if ($role == 'admin'): ?>
        <td>
            <a href="hapus_angsuran.php?id=<?php echo $row['ID_Angsuran']; ?>" class="button button-hapus" onclick="return confirm('Anda yakin ingin menghapus data pembayaran ini?')">Hapus</a>
        </td>
    <?php endif; ?>
</tr>
<?php endwhile; ?>
</tbody></table>