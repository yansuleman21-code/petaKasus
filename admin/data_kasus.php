<?php
include 'includes/header_admin.php';
include 'includes/sidebar.php';
include 'koneksi.php';
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Data Perkara</h1>
        <a href="tambah_kasus.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Perkara Baru
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Semua Kasus</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Judul Perkara</th>
                            <th>Kategori</th>
                            <th>Lokasi (Desa)</th>
                            <th>Tgl Kejadian</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT kasus.*, desa.nama_desa 
                                                         FROM kasus 
                                                         LEFT JOIN desa ON kasus.desa_kasus = desa.id_desa 
                                                         ORDER BY kasus.tanggal DESC");
                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <span class="font-weight-bold text-dark"><?= $row['judul_kasus'] ?></span>
                            </td>
                            <td><span class="badge badge-info"><?= $row['jenis_kasus'] ?></span></td>
                            <td>
                                <?= $row['nama_desa'] ?><br>
                                <a href="http://maps.google.com/?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="small text-primary"><i class="fas fa-map-marker-alt"></i> Cek Peta</a>
                            </td>
                            <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                            <td>
                                <?php 
                                $badge = 'secondary';
                                if($row['status_kasus'] == 'Selesai') $badge = 'success';
                                elseif($row['status_kasus'] == 'Dalam Proses') $badge = 'warning';
                                elseif($row['status_kasus'] == 'Ditolak') $badge = 'danger';
                                ?>
                                <span class="badge badge-<?= $badge ?>"><?= $row['status_kasus'] ?></span>
                            </td>
                            <td class="text-center">
                                <a href="edit_kasus.php?id=<?= $row['id_kasus'] ?>" class="btn btn-warning btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="hapus_kasus.php?id=<?= $row['id_kasus'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data perkara ini?')" title="Hapus">
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