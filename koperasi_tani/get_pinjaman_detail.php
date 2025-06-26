<?php
include 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'ID Pinjaman tidak valid.']);
    exit;
}

$id = intval($_GET['id']);
$response_data = [];

// 1. Ambil detail pinjaman (Besar Angsuran dan Lama Angsuran)
$sql_pinjaman = "SELECT Besar_Angsuran_Per_Bulan, Lama_Angsuran FROM Pinjaman WHERE ID_Pinjaman = ?";
$stmt_pinjaman = mysqli_prepare($koneksi, $sql_pinjaman);
mysqli_stmt_bind_param($stmt_pinjaman, "i", $id);
mysqli_stmt_execute($stmt_pinjaman);
$pinjaman_result = mysqli_stmt_get_result($stmt_pinjaman);
$pinjaman_data = mysqli_fetch_assoc($pinjaman_result);

if (!$pinjaman_data) {
    echo json_encode(['error' => 'Data pinjaman tidak ditemukan.']);
    exit;
}

$response_data['besar_angsuran'] = $pinjaman_data['Besar_Angsuran_Per_Bulan'];
$lama_angsuran = (int)$pinjaman_data['Lama_Angsuran'];

// 2. Ambil angsuran terakhir yang sudah dibayar
$sql_angsuran = "SELECT MAX(Angsuran_Ke) as last_installment FROM Angsuran WHERE ID_Pinjaman = ?";
$stmt_angsuran = mysqli_prepare($koneksi, $sql_angsuran);
mysqli_stmt_bind_param($stmt_angsuran, "i", $id);
mysqli_stmt_execute($stmt_angsuran);
$angsuran_result = mysqli_stmt_get_result($stmt_angsuran);
$angsuran_data = mysqli_fetch_assoc($angsuran_result);

$last_installment = $angsuran_data['last_installment'] ? (int)$angsuran_data['last_installment'] : 0;

// 3. Tentukan angsuran berikutnya
if ($last_installment < $lama_angsuran) {
    $response_data['next_installment'] = $last_installment + 1;
} else {
    // Jika angsuran terakhir sudah sama dengan lama angsuran, berarti LUNAS
    $response_data['next_installment'] = null; 
}

echo json_encode($response_data);
?>