<?php
// Query untuk mengambil data SHU dari View yang baru
$result = mysqli_query($koneksi, "SELECT * FROM View_SHU_Tahunan");
?>
<h2>Data Sisa Hasil Usaha (SHU)</h2>
<table>
<thead>
    <tr>
        <th>Tahun</th>
        <th>Total SHU dari Bunga</th>
    </tr>
</thead>
<tbody>
<?php if($result && mysqli_num_rows($result) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['Tahun']); ?></td>
        <td>Rp <?php echo number_format($row['Total_SHU'], 2, ',', '.'); ?></td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="2" style="text-align:center;">Belum ada data angsuran untuk menghitung SHU.</td></tr>
<?php endif; ?>
</tbody>
</table>
