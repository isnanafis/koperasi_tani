<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'pengurus'])) die("Akses Ditolak.");
include 'koneksi.php';
$error = '';
// Query pinjaman yang belum lunas
$sql_pinjaman_aktif = "SELECT p.ID_Pinjaman, a.Nama 
                       FROM Pinjaman p 
                       JOIN Anggota a ON p.NIK = a.NIK 
                       JOIN Verifikasi v ON p.ID_Pinjaman = v.ID_Pinjaman 
                       WHERE v.Status = 'Disetujui' AND 
                       (SELECT COUNT(*) FROM Angsuran WHERE ID_Pinjaman = p.ID_Pinjaman) < p.Lama_Angsuran";
if ($_SESSION['role'] == 'pengurus') $sql_pinjaman_aktif .= " AND a.ID_Kelompok = " . intval($_SESSION['id_kelompok']);
$sql_pinjaman_aktif .= " ORDER BY a.Nama";
$result_pinjaman_aktif = mysqli_query($koneksi, $sql_pinjaman_aktif);
if(isset($_POST['simpan_angsuran'])){
    $id_pinjaman = $_POST['id_pinjaman']; $jumlah_bayar = $_POST['jumlah_bayar'];
    $tanggal_bayar = $_POST['tanggal_bayar']; $angsuran_ke = $_POST['angsuran_ke'];
    $sql = "INSERT INTO Angsuran (ID_Pinjaman, Tanggal_Bayar, Jumlah_Bayar, Angsuran_Ke) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql); mysqli_stmt_bind_param($stmt, "isdi", $id_pinjaman, $tanggal_bayar, $jumlah_bayar, $angsuran_ke);
    if(mysqli_stmt_execute($stmt)) { header("Location: index.php?page=angsuran&status=sukses"); exit(); }
    else { $error = "Gagal menyimpan: " . mysqli_error($koneksi); }
}
?>
<!DOCTYPE html><html><head><title>Input Angsuran</title><link rel="stylesheet" href="style.css"></head><body>
<div style="max-width:500px; margin:40px auto; padding:20px; background:#fff;">
<h2>Form Input Angsuran</h2>
<?php if($error): ?><div style="color:red;"><?php echo $error; ?></div><?php endif; ?>
<form method="POST">
    <p><label>Pilih Pinjaman Aktif:</label><br>
        <select name="id_pinjaman" id="id_pinjaman" required>
            <option value="">-- Pilih Anggota --</option>
            <?php while($pinjaman = mysqli_fetch_assoc($result_pinjaman_aktif)): ?>
                <option value="<?php echo $pinjaman['ID_Pinjaman']; ?>"><?php echo htmlspecialchars($pinjaman['Nama']); ?></option>
            <?php endwhile; ?>
        </select>
    </p>
    <p><label>Angsuran Ke-:</label><br><input type="number" id="angsuran_ke" name="angsuran_ke" readonly required></p>
    <p><label>Jumlah Bayar (Rp):</label><br><input type="number" id="jumlah_bayar" name="jumlah_bayar" readonly required></p>
    <p><label>Tanggal Bayar:</label><br><input type="date" name="tanggal_bayar" value="<?php echo date('Y-m-d'); ?>" required></p>
    <button type="submit" name="simpan_angsuran" id="tombol-simpan" class="button button-tambah" disabled>Simpan</button>
</form></div>
<script>
document.getElementById('id_pinjaman').addEventListener('change', function() {
    var idPinjaman = this.value;
    var angsuranKeInput = document.getElementById('angsuran_ke');
    var jumlahBayarInput = document.getElementById('jumlah_bayar');
    var tombolSimpan = document.getElementById('tombol-simpan');
    
    // Reset dan nonaktifkan field saat pilihan berubah
    angsuranKeInput.value = ''; jumlahBayarInput.value = ''; tombolSimpan.disabled = true;

    if (idPinjaman) {
        fetch('get_pinjaman_detail.php?id=' + idPinjaman)
        .then(response => response.json())
        .then(data => {
            if(data.error){
                alert(data.error); return;
            }
            if (data.next_installment) {
                angsuranKeInput.value = data.next_installment;
                jumlahBayarInput.value = Math.round(data.besar_angsuran);
                tombolSimpan.disabled = false;
            } else {
                angsuranKeInput.value = 'LUNAS';
            }
        }).catch(error => console.error('Error:', error));
    }
});
</script></body></html>
