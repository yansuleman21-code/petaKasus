<?php
include 'koneksi.php';

// 1. Ambil semua data desa
$query = mysqli_query($koneksi, "SELECT id_desa, nama_desa FROM desa");

echo "<h3>Memulai Perbaikan Nama Desa...</h3><hr>";

$count = 0;
while ($row = mysqli_fetch_assoc($query)) {
    $id = $row['id_desa'];
    $nama_lama = $row['nama_desa'];
    
    // Cek apakah ada kata 'Desa ' di awal (Case Insensitive)
    if (stripos($nama_lama, 'Desa ') === 0) {
        // Hapus kata 'Desa ' (5 karakter pertama: D-e-s-a-spasi)
        $nama_baru = substr($nama_lama, 5); 
        
        // Update ke database
        // Kita juga gunakan TRIM untuk menghapus spasi yang mungkin tersisa
        $nama_baru = trim($nama_baru);
        
        mysqli_query($koneksi, "UPDATE desa SET nama_desa = '$nama_baru' WHERE id_desa = '$id'");
        
        echo "Diubah: <b>$nama_lama</b> -> <span style='color:green'>$nama_baru</span><br>";
        $count++;
    } elseif (stripos($nama_lama, 'Kelurahan ') === 0) {
        // Hapus kata 'Kelurahan ' jika ada
        $nama_baru = trim(substr($nama_lama, 10));
        mysqli_query($koneksi, "UPDATE desa SET nama_desa = '$nama_baru' WHERE id_desa = '$id'");
        
        echo "Diubah: <b>$nama_lama</b> -> <span style='color:green'>$nama_baru</span><br>";
        $count++;
    }
}

if ($count == 0) {
    echo "Tidak ada data yang perlu diubah. Semua nama desa sudah bersih.";
} else {
    echo "<hr><h4>Berhasil mengubah $count nama desa!</h4>";
    echo "<a href='data_desa.php'>Kembali ke Data Desa</a>";
}
?>