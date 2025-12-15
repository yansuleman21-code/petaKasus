<?php
// 1. PANGGIL KONEKSI & STRUKTUR HALAMAN UTAMA
include 'includes/header_admin.php';
include 'includes/sidebar.php';
include 'koneksi.php';
?>

<div class="container-fluid">
    
    <h1 class="h3 mb-4 text-gray-800">Data Desa</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="tambah_desa.php" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Desa Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Desa</th>
                            <th>Koordinat</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        // Query Data Desa
                        $query = mysqli_query($koneksi, "SELECT * FROM desa ORDER BY nama_desa ASC");
                        
                        // Cek jika data kosong
                        if(mysqli_num_rows($query) > 0){
                            while ($d = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($d['nama_desa']) ?></td>
                            <td>
                                <?php if($d['latitude']) { ?>
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?= $d['latitude'] ?>,<?= $d['longitude'] ?>" target="_blank" class="btn btn-info btn-sm" style="font-size:11px;">
                                        <i class="fas fa-map-marker-alt"></i> Lihat Peta
                                    </a>
                                <?php } else { echo "-"; } ?>
                            </td>
                            <td>
                                <a href="hapus_desa.php?id=<?= $d['id_desa'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus desa ini?')">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>Belum ada data desa. Silakan tambah data.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php include 'includes/footer_admin.php'; ?>