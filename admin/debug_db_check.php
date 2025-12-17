<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'koneksi.php';

if (!$koneksi) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "<p>Connected successfully.</p>";
}

echo "<h2>Check Desa Table</h2>";
$q_desa = mysqli_query($koneksi, "SELECT * FROM desa");
if (!$q_desa) {
    echo "Query Error: " . mysqli_error($koneksi);
} else {
    echo "<table border='1'><tr><th>ID</th><th>Nama</th></tr>";
    while ($d = mysqli_fetch_assoc($q_desa)) {
        echo "<tr><td>{$d['id_desa']}</td><td>{$d['nama_desa']}</td></tr>";
    }
    echo "</table>";
}

echo "<h2>Check Kasus Table</h2>";
$q_kasus = mysqli_query($koneksi, "SELECT id_kasus, judul_kasus, desa_kasus, status_kasus FROM kasus LIMIT 10");
if (!$q_kasus) {
    echo "Query Error: " . mysqli_error($koneksi);
} else {
    echo "<table border='1'><tr><th>ID Kasus</th><th>Judul</th><th>Desa Kasus ID</th><th>Status</th></tr>";
    while ($k = mysqli_fetch_assoc($q_kasus)) {
        echo "<tr><td>{$k['id_kasus']}</td><td>{$k['judul_kasus']}</td><td>{$k['desa_kasus']}</td><td>{$k['status_kasus']}</td></tr>";
    }
    echo "</table>";
}
?>