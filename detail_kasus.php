<?php
include 'admin/koneksi.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location='index.php';</script>";
    exit;
}
$id = $_GET['id'];
$q = mysqli_query($koneksi, "SELECT kasus.*, desa.nama_desa 
                             FROM kasus 
                             JOIN desa ON kasus.desa_kasus = desa.id_desa 
                             WHERE kasus.id_kasus='$id'");
$data = mysqli_fetch_assoc($q);
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="kasus.php">Data Kasus</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white p-4">
                    <h3 class="fw-bold mb-0"><?= $data['judul_kasus'] ?></h3>
                </div>
                <div class="card-body p-4">
                    <?php 
                        $badge_color = 'bg-secondary';
                        if($data['status_kasus'] == 'Selesai') $badge_color = 'bg-success';
                        elseif($data['status_kasus'] == 'Dalam Proses') $badge_color = 'bg-warning text-dark';
                        elseif($data['status_kasus'] == 'Ditolak') $badge_color = 'bg-danger';
                    ?>
                    
                    <div class="mb-4">
                        <span class="badge <?= $badge_color ?> fs-6"><?= $data['status_kasus'] ?></span>
                        <span class="badge bg-info fs-6 text-dark"><i class="bi bi-building"></i> <?= $data['instansi'] ?></span>
                        <span class="badge bg-success fs-6"><i class="bi bi-geo-alt"></i> <?= $data['nama_desa'] ?></span>
                        <span class="badge bg-secondary fs-6"><?= date("d M Y", strtotime($data['tanggal'])) ?></span>
                    </div>

                    <h5 class="fw-bold">Deskripsi</h5>
                    <p class="text-muted" style="text-align: justify; line-height: 1.8;">
                        <?= nl2br($data['deskripsi']) ?>
                    </p>

                    <div class="alert alert-light border mt-4">
                        <strong>Lokasi Detail:</strong> <?= $data['wilayah'] ?>
                    </div>

                    <?php if(!empty($data['latitude']) && !empty($data['longitude'])) { ?>
                        <div class="mt-3">
                            <a href="https://www.google.com/maps/search/?api=1&query=<?= $data['latitude'] ?>,<?= $data['longitude'] ?>" 
                               target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-map-fill"></i> Lihat Lokasi di Google Maps
                            </a>
                        </div>
                    <?php } ?>

                    <div class="mt-5 text-end">
                        <a href="kasus.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>