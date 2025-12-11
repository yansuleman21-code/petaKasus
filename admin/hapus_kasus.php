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
        echo "<script>alert('Data berhasil dihapus'); window.location='../kasus.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data'); window.location='../kasus.php';</script>";
    }
} else {
    header("Location: ../kasus.php");
}
?>