<?php
include 'koneksi.php';
$query = mysqli_query($koneksi, "DESCRIBE admin");
echo "<pre>";
while ($row = mysqli_fetch_assoc($query)) {
    print_r($row);
}
echo "</pre>";

$q2 = mysqli_query($koneksi, "SELECT * FROM admin");
echo "<pre>";
while ($r = mysqli_fetch_assoc($q2)) {
    echo "User: " . $r['username'] . " | Pass Hash: " . substr($r['password'], 0, 10) . "... | Role?: " . ($r['role'] ?? 'N/A') . "\n";
}
echo "</pre>";
?>