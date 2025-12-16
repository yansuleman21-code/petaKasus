<?php
header('Content-Type: application/json');
error_reporting(0); // Matikan error warning PHP agar tidak merusak format JSON
include 'koneksi.php';

// Jika koneksi gagal, kembalikan array kosong
if (!$koneksi) {
    echo json_encode([]);
    exit;
}

// Query mengambil data statistik per desa
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
    GROUP BY d.id_desa, d.nama_desa
    ORDER BY d.id_desa ASC
");

$data = [];
$counter = [];
while ($row = mysqli_fetch_assoc($query)) {
    // Pastikan nama desa UPPERCASE dan di-trim agar cocok dengan GeoJSON
    $nama_clean = strtoupper(trim($row['nama_desa']));

    // Logika penanganan nama ganda (Sesuai frontend: Nama, Nama 1, Nama 2...)
    if (!isset($counter[$nama_clean])) {
        $counter[$nama_clean] = 0;
        $key = $nama_clean;
    } else {
        $counter[$nama_clean]++;
        $key = $nama_clean . " " . $counter[$nama_clean];
    }

    $data[$key] = $row;
}

echo json_encode($data);
?>