<?php
$host = "localhost";
$user = "root";       // Username database Anda
$pass = "";           // Password database Anda
$db = "petakasus"; // Ganti dengan nama database Anda yang sebenarnya

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>