<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}

include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Query hapus
    $hapus = mysqli_query($koneksi, "DELETE FROM kasus WHERE id_kasus = '$id'");
    
   if ($hapus) {
    echo "<script>alert('Data berhasil dihapus'); window.location='data_kasus.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus data'); window.location='data_kasus.php';</script>";
}
// ...
header("Location: data_kasus.php");
}
?>