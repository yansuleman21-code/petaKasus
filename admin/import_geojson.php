<?php
// admin/import_geojson.php
include 'koneksi.php';

// Lokasi file GeoJSON
$file_path = '../assets/geo/75.01_kelurahan.geojson';

if (!file_exists($file_path)) {
    die("File GeoJSON tidak ditemukan di: <b>$file_path</b>");
}

$json_content = file_get_contents($file_path);
$data = json_decode($json_content, true);

echo "<h3>Proses Sinkronisasi Data Desa & Koordinat...</h3>";
echo "<a href='data_desa.php'>Kembali ke Data Desa</a><hr>";

$tambah = 0;
$update = 0;
$gagal = 0;

foreach ($data['features'] as $feature) {
    // 1. Ambil Nama Desa & Bersihkan
    $nama_desa = mysqli_real_escape_string($koneksi, strtoupper(trim($feature['properties']['nm_kelurahan'])));
    
    // 2. Hitung Titik Tengah (Centroid)
    $coords = [];
    $type = $feature['geometry']['type'];
    
    if ($type == 'Polygon') {
        $coords = $feature['geometry']['coordinates'][0];
    } elseif ($type == 'MultiPolygon') {
        $coords = $feature['geometry']['coordinates'][0][0];
    }

    $total_lat = 0;
    $total_lng = 0;
    $count_points = 0;

    if (!empty($coords)) {
        foreach ($coords as $point) {
            $total_lng += $point[0]; 
            $total_lat += $point[1]; 
            $count_points++;
        }
        $avg_lat = $total_lat / $count_points;
        $avg_lng = $total_lng / $count_points;
    } else {
        $avg_lat = 0;
        $avg_lng = 0;
    }

    // 3. Cek Database
    $cek = mysqli_query($koneksi, "SELECT id_desa FROM desa WHERE nama_desa = '$nama_desa'");
    
    if (mysqli_num_rows($cek) > 0) {
        // --- JIKA SUDAH ADA, UPDATE KOORDINATNYA ---
        $q_update = "UPDATE desa SET latitude = '$avg_lat', longitude = '$avg_lng' WHERE nama_desa = '$nama_desa'";
        if (mysqli_query($koneksi, $q_update)) {
            echo "<small style='color:blue'>[UPDATE]</small> Koordinat <b>$nama_desa</b> diperbarui.<br>";
            $update++;
        } else {
            echo "<small style='color:red'>[GAGAL UPDATE]</small> $nama_desa<br>";
            $gagal++;
        }
    } else {
        // --- JIKA BELUM ADA, INSERT BARU ---
        $q_insert = "INSERT INTO desa (nama_desa, latitude, longitude, deskripsi) 
                     VALUES ('$nama_desa', '$avg_lat', '$avg_lng', 'Diimpor dari GeoJSON')";
        
        if (mysqli_query($koneksi, $q_insert)) {
            echo "<small style='color:green'>[BARU]</small> Desa <b>$nama_desa</b> ditambahkan.<br>";
            $tambah++;
        } else {
            echo "<small style='color:red'>[GAGAL INSERT]</small> $nama_desa<br>";
            $gagal++;
        }
    }
}

echo "<hr><h4>Selesai!</h4>";
echo "<ul>";
echo "<li>Data Baru Ditambahkan: <b>$tambah</b></li>";
echo "<li>Data Lama Diperbarui (Update Koordinat): <b>$update</b></li>";
echo "<li>Gagal: <b>$gagal</b></li>";
echo "</ul>";
echo "<a href='data_desa.php' style='background:blue; color:white; padding:10px; text-decoration:none;'>Cek Data Desa Sekarang</a>";
?>