<?php
session_start();
if (!isset($_SESSION['admin'])) exit;
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Hapus file foto jika ada
    $q = mysqli_query($koneksi, "SELECT foto FROM laporan_masyarakat WHERE id_laporan = '$id'");
    $data = mysqli_fetch_assoc($q);
    if($data['foto'] && file_exists("../assets/img/laporan/" . $data['foto'])) {
        unlink("../assets/img/laporan/" . $data['foto']);
    }

    mysqli_query($koneksi, "DELETE FROM laporan_masyarakat WHERE id_laporan = '$id'");
    header("Location: laporan_masyarakat.php");
}
?>