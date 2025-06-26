<?php
// Mengambil status dari URL untuk notifikasi
$status_pinjaman = isset($_GET['status']) ? $_GET['status'] : '';

// Query untuk mengambil data pinjaman sesuai peran
$sql = "SELECT vp.* FROM View_Pinjaman vp JOIN Anggota a ON vp.NIK = a.NIK";
if ($role == 'pengurus') {
    $sql .= " WHERE a.ID_Kelompok = " . intval($logged_in_id_kelompok);
} elseif ($role == 'anggota') {
    $sql .= " WHERE vp.NIK = '" . mysqli_real_escape_string($koneksi, $logged_in_nik) . "'";
}
$result = mysqli_query($koneksi, $sql);
?>
<h2>Data Pinjaman</h2>

<!-- Menampilkan notifikasi sukses -->
<?php if ($status_pinjaman == 'sukses_pengajuan'): ?>
    <div class="alert alert-sukses">
        Pengajuan pinjaman Anda telah berhasil dikirim dan sedang menunggu verifikasi.
    </div>
<?php endif; ?>

<!-- Tombol Ajukan Pinjaman hanya untuk Anggota -->
<?php if ($role == 'anggota'): ?>
    <a href="tambah_pinjaman.php" class="button button-tambah">Ajukan Pinjaman Baru</a>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Anggota</th>
            <th>Tgl Pinjam</th>
            <th>Pokok Pinjaman</th>
            <th>Total Tagihan</th>
            <th>Sisa Tagihan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['ID_Pinjaman']); ?></td>
                <td><?php echo htmlspecialchars($row['Nama_Anggota']); ?></td>
                <td><?php echo htmlspecialchars($row['Tanggal_Pinjaman']); ?></td>
                <td>Rp <?php echo number_format($row['Pokok_Pinjaman'], 0, ',', '.'); ?></td>
                <td>Rp <?php echo number_format($row['Total_Tagihan'], 0, ',', '.'); ?></td>
                <td>Rp <?php echo number_format($row['Sisa_Tagihan'], 0, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($row['Status_Verifikasi']); ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7" style="text-align:center;">Belum ada data pinjaman.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

