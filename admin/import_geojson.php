<?php
// admin/import_geojson.php
include 'koneksi.php';

$file_path = '../assets/geo/75.01_kelurahan.geojson';

if (!file_exists($file_path)) {
    die("File GeoJSON tidak ditemukan di: <b>$file_path</b>");
}

// Tombol Aksi
if (!isset($_GET['aksi'])) {
    echo "<h3>Import Data Desa (Full 205 Data)</h3>";
    echo "<p>Script ini akan <b>MENGHAPUS SEMUA DATA DESA LAMA</b> dan menggantinya dengan 205 data baru dari file Peta.</p>";
    echo "<p>Jika ada desa dengan nama sama (wilayah terpisah), akan otomatis ditambahkan angka di belakang namanya.</p>";
    echo "<a href='?aksi=import' onclick=\"return confirm('Yakin ingin mereset dan import ulang? Data desa lama akan hilang.')\" style='background:red; color:white; padding:10px; text-decoration:none; font-weight:bold;'>MULAI IMPORT ULANG & RESET</a>";
    echo "<br><br><a href='data_desa.php'>Kembali</a>";
    exit;
}

// PROSES IMPORT
$json_content = file_get_contents($file_path);
$data = json_decode($json_content, true);

// 1. KOSONGKAN TABEL DESA DULU (RESET)
mysqli_query($koneksi, "TRUNCATE TABLE desa");

echo "<h3>Proses Import Sedang Berjalan...</h3>";

$berhasil = 0;
$gagal = 0;

foreach ($data['features'] as $feature) {
    // Ambil Nama Desa
    $nama_asal = strtoupper(trim($feature['properties']['nm_kelurahan']));
    $nama_clean = mysqli_real_escape_string($koneksi, $nama_asal);
    
    // Hitung Titik Tengah
    $coords = [];
    $type = $feature['geometry']['type'];
    if ($type == 'Polygon') {
        $coords = $feature['geometry']['coordinates'][0];
    } elseif ($type == 'MultiPolygon') {
        $coords = $feature['geometry']['coordinates'][0][0];
    }

    $total_lat = 0; $total_lng = 0; $count_points = 0;
    if (!empty($coords)) {
        foreach ($coords as $point) {
            $total_lng += $point[0]; 
            $total_lat += $point[1]; 
            $count_points++;
        }
        $avg_lat = $total_lat / $count_points;
        $avg_lng = $total_lng / $count_points;
    } else {
        $avg_lat = 0; $avg_lng = 0;
    }

    // 2. CEK APAKAH NAMA INI SUDAH ADA DI DB?
    // Kita gunakan loop untuk mengecek nama_desa, nama_desa (2), nama_desa (3), dst.
    $nama_final = $nama_clean;
    $counter = 1;
    
    while(true) {
        $cek = mysqli_query($koneksi, "SELECT id_desa FROM desa WHERE nama_desa = '$nama_final'");
        if (mysqli_num_rows($cek) > 0) {
            // Jika ada, tambah counter
            $counter++;
            $nama_final = "$nama_clean (Bagian $counter)";
        } else {
            // Jika belum ada, gunakan nama ini
            break;
        }
    }

    // 3. INSERT (SELALU INSERT, TIDAK ADA UPDATE)
    $query = "INSERT INTO desa (nama_desa, latitude, longitude, deskripsi) 
              VALUES ('$nama_final', '$avg_lat', '$avg_lng', 'Diimpor dari GeoJSON')";
    
    if (mysqli_query($koneksi, $query)) {
        // Tampilkan pesan hanya jika itu data duplikat yang diberi nomor
        if ($counter > 1) {
            echo "<small style='color:blue'>[DUPLIKAT]</small> $nama_asal -> Disimpan sebagai <b>$nama_final</b><br>";
        }
        $berhasil++;
    } else {
        echo "<small style='color:red'>[GAGAL]</small> $nama_final<br>";
        $gagal++;
    }
}

echo "<hr><h4>Selesai!</h4>";
echo "Total Data Desa Sekarang: <b>$berhasil</b> (Seharusnya 205)<br><br>";
echo "<a href='data_desa.php' style='background:blue; color:white; padding:10px; text-decoration:none;'>Lihat Data Desa</a>";
?>