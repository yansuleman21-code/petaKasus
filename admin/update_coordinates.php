<?php
include 'koneksi.php';

// Array Data: [Nama Desa Tanpa Suffix] => [ [Lat1, Long1], [Lat2, Long2] ]
// Index 0 -> Update ke 'NAMA DESA'
// Index 1 -> Update ke 'NAMA DESA 1'
$dataUpdate = [
    'BONGOHULAWA' => [
        ['0.6017', '122.8346'], // Kec. Bongomeme
        ['0.6490', '122.9820']  // Kec. Limboto (Jadi BONGOHULAWA 1)
    ],
    'BUHU' => [
        ['0.5810', '123.0249'], // Kec. Talaga Jaya
        ['0.7318', '122.8729']  // Kec. Tibawa
    ],
    'BULOTA' => [
        ['0.5913', '123.0260'], // Kec. Talaga Jaya
        ['0.6835', '123.0041']  // Kec. Limboto
    ],
    'DULOHUPA' => [
        ['0.6158', '122.6395'], // Kec. Boliyohuto
        ['0.5966', '123.0659']  // Kec. Telaga
    ],
    'DUNGGALA' => [
        ['0.6303', '122.8700'], // Kec. Tibawa
        ['0.5601', '122.9552']  // Kec. Batudaa
    ],
    'ILOMATA' => [
        ['0.5149', '122.6581'], // Kec. Bilato
        ['0.6371', '122.8138']  // Kec. Tibawa
    ],
    'KAYUBULAN' => [
        ['0.5246', '123.0132'], // Kec. Batudaa Pantai
        ['0.5973', '122.9943']  // Kec. Limboto
    ],
    'KAYUMERAH' => [
        ['0.5985', '122.7460'], // Kec. Bongomeme
        ['0.6389', '122.9828']  // Kec. Limboto
    ],
    'POLOHUNGO' => [
        ['0.7155', '122.9991'], // Kec. Limboto
        ['0.7541', '122.5880']  // Kec. Tolangohula
    ],
    'TINELO' => [
        ['0.6155', '123.0335'], // Kec. Telaga Biru
        ['0.5709', '123.0345']  // Kec. Tilango
    ]
];

echo "<h3>Proses Update Koordinat Desa Ganda</h3><ul>";

foreach ($dataUpdate as $namaBase => $coords) {
    // 1. Update Nama Asli (Data ke-1)
    $lat1 = $coords[0][0];
    $long1 = $coords[0][1];
    $sql1 = "UPDATE desa SET latitude='$lat1', longitude='$long1' WHERE nama_desa = '$namaBase'";

    if (mysqli_query($koneksi, $sql1)) {
        echo "<li>[OK] $namaBase -> Lat: $lat1, Long: $long1</li>";
    } else {
        echo "<li>[ERROR] $namaBase: " . mysqli_error($koneksi) . "</li>";
    }

    // 2. Update Nama Suffix 1 (Data ke-2)
    $namaSuffix = $namaBase . " 1";
    $lat2 = $coords[1][0];
    $long2 = $coords[1][1];
    $sql2 = "UPDATE desa SET latitude='$lat2', longitude='$long2' WHERE nama_desa = '$namaSuffix'";

    if (mysqli_query($koneksi, $sql2)) {
        echo "<li>[OK] $namaSuffix -> Lat: $lat2, Long: $long2</li>";
    } else {
        echo "<li>[ERROR] $namaSuffix: " . mysqli_error($koneksi) . "</li>";
    }
}
echo "</ul>";
?>