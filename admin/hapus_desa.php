<?php
session_start();
if (!isset($_SESSION['admin'])) exit;
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($koneksi, "DELETE FROM desa WHERE id_desa = '$id'");
    header("Location: data_desa.php");
}
?>