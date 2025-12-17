<?php
$host = "localhost";

// Deteksi Environment: Apakah berjalan di Localhost (XAMPP) atau Hosting Live?
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // --- KONFIGURASI LOKAL (XAMPP Default) ---
    $user = "root";
    $pass = "";
    $db = "petakasus";
} else {
    // --- KONFIGURASI LIVE SERVER (Hosting) ---
    // Isi data ini sesuai dengan akun hosting Anda nanti
    $user = "u_username_hosting";   // Ganti dengan username database hosting
    $pass = "p_password_hosting";   // Ganti dengan password database hosting
    $db = "d_nama_database";      // Ganti dengan nama database hosting
}

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>