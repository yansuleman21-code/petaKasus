<?php
include 'includes/header_admin.php';
include 'includes/sidebar.php';
include 'koneksi.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Laporan Pengaduan Masyarakat</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-danger">
            <h6 class="m-0 font-weight-bold text-white">Daftar Laporan Masuk</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tgl</th>
                            <th>Pelapor</th>
                            <th>Laporan</th>
                            <th>Bukti & Lokasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM laporan_masyarakat ORDER BY id_laporan DESC");
                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><small><?= date('d/m/Y', strtotime($row['tanggal'])) ?></small></td>
                            <td>
                                <b><?= htmlspecialchars($row['nama_pelapor']) ?></b><br>
                                <a href="https://wa.me/<?= $row['no_hp'] ?>" target="_blank" class="text-success small">
                                    <i class="fab fa-whatsapp"></i> <?= $row['no_hp'] ?>
                                </a>
                            </td>
                            <td>
                                <b class="text-danger"><?= htmlspecialchars($row['judul_laporan']) ?></b><br>
                                <span class="badge badge-secondary"><?= $row['kategori'] ?></span>
                                <p class="small mt-2 text-muted"><?= nl2br(substr($row['deskripsi'], 0, 100)) ?>...</p>
                            </td>
                            <td class="text-center">
                                <?php if($row['foto']) { ?>
                                    <a href="../assets/img/laporan/<?= $row['foto'] ?>" target="_blank">
                                        <img src="../assets/img/laporan/<?= $row['foto'] ?>" width="60" class="img-thumbnail mb-2">
                                    </a><br>
                                <?php } ?>
                                <a href="http://maps.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="btn btn-sm btn-info btn-block">
                                    <i class="fas fa-map-marker-alt"></i> Cek Lokasi
                                </a>
                            </td>
                            <td>
                                <?php 
                                if($row['status'] == 'Pending') echo '<span class="badge badge-warning">Menunggu</span>';
                                elseif($row['status'] == 'Diverifikasi') echo '<span class="badge badge-success">Diterima</span>';
                                else echo '<span class="badge badge-danger">Ditolak</span>';
                                ?>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Pending') { ?>
                                    <a href="verifikasi_laporan.php?id=<?= $row['id_laporan'] ?>" class="btn btn-success btn-sm btn-block mb-2" onclick="return confirm('Laporan ini valid dan akan dimasukkan ke Data Kasus?')">
                                        <i class="fas fa-check"></i> Verifikasi
                                    </a>
                                <?php } ?>
                                <a href="hapus_laporan.php?id=<?= $row['id_laporan'] ?>" class="btn btn-danger btn-sm btn-block" onclick="return confirm('Hapus laporan ini permanen?')">
                                    <i class="fas fa-trash"></i> Hapus
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