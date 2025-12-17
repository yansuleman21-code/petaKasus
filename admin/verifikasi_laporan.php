<?php
session_start();
if (!isset($_SESSION['admin']))
    exit;
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Ambil data laporan
    $q = mysqli_query($koneksi, "SELECT * FROM laporan_masyarakat WHERE id_laporan = '$id'");
    $data = mysqli_fetch_assoc($q);

    if ($data) {
        $judul = mysqli_real_escape_string($koneksi, $data['judul_laporan']);
        $deskripsi = mysqli_real_escape_string($koneksi, $data['deskripsi'] . "\n\n(Pelapor: " . $data['nama_pelapor'] . ")");
        $kategori = $data['kategori'];
        $lat = $data['latitude'];
        $long = $data['longitude'];
        $instansi = "Laporan Masyarakat";
        $wilayah = "Lokasi Pelapor";
        $status = "Dalam Proses";
        $tanggal = date('Y-m-d');

        // Ambil ID Desa pertama sebagai default (Admin harus edit nanti untuk presisi)
        $q_desa = mysqli_query($koneksi, "SELECT id_desa FROM desa LIMIT 1");
        $d_desa = mysqli_fetch_assoc($q_desa);
        $id_desa_default = $d_desa ? $d_desa['id_desa'] : 0;

        // 2. Masukkan ke tabel kasus
        $insert = "INSERT INTO kasus (judul_kasus, jenis_kasus, deskripsi, tanggal, status_kasus, latitude, longitude, instansi, wilayah, desa_kasus) 
                   VALUES ('$judul', '$kategori', '$deskripsi', '$tanggal', '$status', '$lat', '$long', '$instansi', '$wilayah', '$id_desa_default')";

        if (mysqli_query($koneksi, $insert)) {
            $new_case_id = mysqli_insert_id($koneksi);

            // 3. Update status laporan jadi Diverifikasi
            mysqli_query($koneksi, "UPDATE laporan_masyarakat SET status = 'Diverifikasi' WHERE id_laporan = '$id'");

            echo "<script>alert('Laporan berhasil diverifikasi! Silakan perbaiki Lokasi Desa pada halaman berikut.'); window.location='proses_kasus.php?id=$new_case_id';</script>";
        } else {
            echo "<script>alert('Gagal verifikasi: " . mysqli_error($koneksi) . "'); window.location='laporan_masyarakat.php';</script>";
        }
    }
}
?>