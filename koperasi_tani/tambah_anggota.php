<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin'])) {
    die("Akses Ditolak. Hanya admin yang bisa menambah anggota.");
}
include 'koneksi.php';
$kelompok_tani_result = mysqli_query($koneksi, "SELECT * FROM Kelompok_Tani ORDER BY Nama_Kelompok");
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nik = $_POST['nik']; 
    $nama = trim($_POST['nama']); // trim untuk hapus spasi
    $dusun = $_POST['dusun'];
    $rt = $_POST['rt']; 
    $rw = $_POST['rw']; 
    $tgl_lahir = $_POST['tgl_lahir'];
    $no_hp = $_POST['no_hp']; 
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $id_kelompok = $_POST['id_kelompok'];
    $jabatan = $_POST['jabatan'];

    // --- BLOK VALIDASI ---
    // 1. Validasi Nama Anggota
    if (is_numeric($nama)) {
        $error = "Nama anggota tidak boleh hanya terdiri dari angka.";
    } else if (strlen(preg_replace('/[^a-zA-Z]/', '', $nama)) < 3) {
        $error = "Nama anggota harus mengandung minimal 3 huruf.";
    } 
    // 2. Validasi NIK sudah terdaftar atau belum
    else {
        $check_nik_sql = "SELECT NIK FROM Anggota WHERE NIK = ?";
        $stmt_check = mysqli_prepare($koneksi, $check_nik_sql);
        mysqli_stmt_bind_param($stmt_check, "s", $nik);
        mysqli_stmt_execute($stmt_check);
        if (mysqli_stmt_get_result($stmt_check)->num_rows > 0) {
            $error = "NIK sudah terdaftar.";
        }
    }
    // --- AKHIR BLOK VALIDASI ---

    // Jika semua validasi lolos (tidak ada error)
    if (empty($error)) {
        // 1. Selalu INSERT ke tabel Anggota
        $sql_anggota = "INSERT INTO Anggota (NIK, Nama, Dusun, RT, RW, Tanggal_Lahir, No_HP, Jenis_Kelamin, ID_Kelompok) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_anggota = mysqli_prepare($koneksi, $sql_anggota);
        mysqli_stmt_bind_param($stmt_anggota, "ssssssssi", $nik, $nama, $dusun, $rt, $rw, $tgl_lahir, $no_hp, $jenis_kelamin, $id_kelompok);
        
        if (mysqli_stmt_execute($stmt_anggota)) {
            // 2. JIKA jabatan BUKAN 'Anggota', INSERT juga ke tabel Pengurus
            if ($jabatan !== 'Anggota') {
                $sql_pengurus = "INSERT INTO Pengurus (NIK, Jabatan, Tanggal_Mulai_Jabatan) VALUES (?, ?, CURDATE())";
                $stmt_pengurus = mysqli_prepare($koneksi, $sql_pengurus);
                mysqli_stmt_bind_param($stmt_pengurus, "ss", $nik, $jabatan);
                mysqli_stmt_execute($stmt_pengurus);
            }
            header("Location: index.php?page=anggota&status=sukses_tambah");
            exit();
        } else {
            $error = "Error saat menambah anggota: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Tambah Anggota Baru</title><link rel="stylesheet" href="style.css">
</head>
<body>
<div style="max-width:600px; margin:40px auto; padding:20px; background:#fff; border-radius:8px;">
    <h2>Form Pendaftaran Anggota Baru</h2>
    <?php if(!empty($error)): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
    <form method="POST" action="tambah_anggota.php">
        <p><label>NIK:</label><br><input type="text" name="nik" required pattern="\d{16}" title="NIK harus 16 digit angka"></p>
        <p><label>Nama Lengkap:</label><br><input type="text" name="nama" required></p>
        <p><label>Tanggal Lahir:</label><br><input type="date" name="tgl_lahir" required></p>
        <p><label>Alamat:</label><br>
            <input type="text" name="dusun" placeholder="Dusun" required>
            <input type="text" name="rt" placeholder="RT" required style="width:20%; margin-top:5px;">
            <input type="text" name="rw" placeholder="RW" required style="width:20%; margin-top:5px;">
        </p>
        <p><label>No. HP:</label><br><input type="text" name="no_hp" required></p>
        <p><label>Jenis Kelamin:</label><br>
            <select name="jenis_kelamin" required>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </p>
        <p><label>Kelompok Tani:</label><br>
            <select name="id_kelompok" required>
                <option value="">-- Pilih Kelompok --</option>
                <?php mysqli_data_seek($kelompok_tani_result, 0); while($kelompok = mysqli_fetch_assoc($kelompok_tani_result)): ?>
                    <option value="<?php echo $kelompok['ID_Kelompok']; ?>"><?php echo htmlspecialchars($kelompok['Nama_Kelompok']); ?></option>
                <?php endwhile; ?>
            </select>
        </p>
        <p><label>Jabatan:</label><br>
            <select name="jabatan" required>
                <option value="Anggota">Anggota Biasa</option>
                <option value="Ketua">Ketua</option>
                <option value="Sekretaris">Sekretaris</option>
                <option value="Bendahara">Bendahara</option>
            </select>
        </p>
        <button type="submit" class="button button-tambah">Simpan Anggota</button>
        <a href="index.php?page=anggota" style="margin-left:10px;">Batal</a>
    </form>
</div>
<style>
    .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; text-align: center; }
    .alert-error { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
    input, select {width:100%; padding:8px; box-sizing:border-box;}
</style>
</body></html>
