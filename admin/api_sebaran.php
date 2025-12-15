<?php
header('Content-Type: application/json');
include 'koneksi.php';

// Ambil data kasus dikelompokkan per desa
$query = mysqli_query($koneksi, "
    SELECT 
        d.id_desa, 
        d.nama_desa, 
        COUNT(k.id_kasus) as total,
        SUM(CASE WHEN k.jenis_kasus = 'Pidana Umum' THEN 1 ELSE 0 END) as pidum,
        SUM(CASE WHEN k.jenis_kasus = 'Pidana Khusus' THEN 1 ELSE 0 END) as pidsus,
        SUM(CASE WHEN k.jenis_kasus = 'Narkotika' THEN 1 ELSE 0 END) as narkotika,
        SUM(CASE WHEN k.jenis_kasus = 'Perdata' THEN 1 ELSE 0 END) as perdata
    FROM desa d
    LEFT JOIN kasus k ON d.id_desa = k.desa_kasus
    GROUP BY d.nama_desa
");

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    // Paksa nama desa jadi huruf besar semua untuk mempermudah pencocokan dengan GeoJSON
    $nama_clean = strtoupper(trim($row['nama_desa'])); 
    $data[$nama_clean] = $row;
}

echo json_encode($data);
?>