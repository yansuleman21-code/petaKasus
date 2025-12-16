<?php
include 'koneksi.php';
$q = mysqli_query($koneksi, "SELECT * FROM desa ORDER BY id_desa ASC");
echo "<pre>";
while ($r = mysqli_fetch_assoc($q)) {
    echo $r['id_desa'] . " - " . $r['nama_desa'] . "<br>";
}
echo "</pre>";
