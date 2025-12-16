<?php
include 'koneksi.php';

// 1. Load GeoJSON
$jsonPath = '../assets/geo/75.01_kelurahan.geojson';
if (!file_exists($jsonPath)) {
    die("File GeoJSON tidak ditemukan di $jsonPath");
}

$jsonString = file_get_contents($jsonPath);
$geoData = json_decode($jsonString, true);

if (!$geoData) {
    die("Gagal parsing JSON");
}

$counters = [];
$addedCount = 0;

echo "<h3>Proses Penambahan Desa Ganda</h3>";
echo "<ul>";

foreach ($geoData['features'] as $f) {
    // Pastikan properti nama ada
    if (!isset($f['properties']['nm_kelurahan']))
        continue;

    $namaAsli = strtoupper(trim($f['properties']['nm_kelurahan']));

    if (!isset($counters[$namaAsli])) {
        $counters[$namaAsli] = 0;
    }
    $counters[$namaAsli]++;

    // Jika ini adalah kemunculan ke-2 atau lebih (Duplikat)
    if ($counters[$namaAsli] > 1) {
        $suffix = $counters[$namaAsli] - 1;
        $namaBaru = $namaAsli . " " . $suffix; // Contoh: KAYUMERAH 1

        // Cek di Database apakah sudah ada?
        $sqlCek = "SELECT id_desa FROM desa WHERE nama_desa = '$namaBaru'";
        $cek = mysqli_query($koneksi, $sqlCek);

        if (mysqli_num_rows($cek) == 0) {
            // Belum ada, tambahkan!
            $sqlInsert = "INSERT INTO desa (nama_desa) VALUES ('$namaBaru')";
            $insert = mysqli_query($koneksi, $sqlInsert);

            if ($insert) {
                echo "<li><span style='color:green'>[BERHASIL]</span> Menambahkan: <b>$namaBaru</b></li>";
                $addedCount++;
            } else {
                echo "<li><span style='color:red'>[GAGAL]</span> Menambahkan: $namaBaru (" . mysqli_error($koneksi) . ")</li>";
            }
        } else {
            echo "<li><span style='color:gray'>[SKIP]</span> $namaBaru sudah ada.</li>";
        }
    }
}

echo "</ul>";
echo "<h4>Selesai. Total desa baru ditambahkan: $addedCount</h4>";
?>