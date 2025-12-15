<?php
header('Content-Type: application/json');
include 'koneksi.php';

// Ambil data desa beserta kecamatan dan jumlah penduduk, serta hitung kasus
$query = mysqli_query($koneksi, "
    SELECT 
        d.id_desa, 
        d.nama_desa, 
        d.kecamatan,        -- Tambahan
        d.jumlah_penduduk,  -- Tambahan
        COUNT(k.id_kasus) as total,
        SUM(CASE WHEN k.jenis_kasus = 'Pidana Umum' THEN 1 ELSE 0 END) as pidum,
        SUM(CASE WHEN k.jenis_kasus = 'Pidana Khusus' THEN 1 ELSE 0 END) as pidsus,
        SUM(CASE WHEN k.jenis_kasus = 'Narkotika' THEN 1 ELSE 0 END) as narkotika,
        SUM(CASE WHEN k.jenis_kasus = 'Perdata' THEN 1 ELSE 0 END) as perdata
    FROM desa d
    LEFT JOIN kasus k ON d.id_desa = k.desa_kasus
    GROUP BY d.id_desa -- Group by ID agar lebih akurat
");

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    // Bersihkan nama desa dan kecamatan (huruf besar semua) untuk pencocokan
    $nama_clean = strtoupper(trim($row['nama_desa'])); 
    
    // Pastikan jika kecamatan kosong, diset string kosong agar tidak error di JS
    if(empty($row['kecamatan'])) {
        $row['kecamatan'] = "LAINNYA";
    } else {
        $row['kecamatan'] = strtoupper(trim($row['kecamatan']));
    }

    $data[$nama_clean] = $row;
}

echo json_encode($data);
?>