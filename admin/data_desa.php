<?php
include 'includes/header_admin.php';
include 'includes/sidebar.php';
include 'koneksi.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Data Desa</h1>
        <a href="tambah_desa.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Desa Baru
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Desa Terdaftar</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Desa</th>
                            <th>Koordinat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM desa ORDER BY nama_desa ASC");
                        while ($d = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $d['nama_desa'] ?></td>
                            <td>
                                <?php if($d['latitude']) { ?>
                                    <small><?= $d['latitude'] ?>, <?= $d['longitude'] ?></small>
                                    <a href="http://maps.google.com/maps?q=<?= $d['latitude'] ?>,<?= $d['longitude'] ?>" target="_blank" class="text-primary"><i class="fas fa-map-marker-alt"></i></a>
                                <?php } else { echo "<span class='text-danger'>Belum set</span>"; } ?>
                            </td>
                            <td>
                                <a href="hapus_desa.php?id=<?= $d['id_desa'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus desa ini? Data kasus terkait mungkin akan error.')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>