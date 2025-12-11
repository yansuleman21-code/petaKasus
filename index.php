<?php
include 'admin/koneksi.php';
include 'includes/header.php';

// Ambil total kasus
$total = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM kasus"))['jml'];

// Ambil daftar desa
$desa = mysqli_query($koneksi, "SELECT * FROM desa");

// Kasus terbaru
$kasus = mysqli_query($koneksi, "SELECT * FROM kasus ORDER BY id_kasus DESC LIMIT 6");
?>

<!-- HERO SECTION -->
<section class="hero">
    <div class="container">
        <h1 class="fw-bold text-white">Peta Kasus Kabupaten Gorontalo</h1>
        <p class="text-white">Sistem Informasi Monitoring Kasus Per Desa</p>
        <a href="kasus.php" class="btn btn-light">
            <i class="bi bi-search"></i> Lihat Semua Kasus
        </a>
    </div>
</section>


<!-- STATISTIK -->
<div class="container mt-5">
    <div class="row g-4 justify-content-center text-center">

        <div class="col-md-4">
            <div class="card shadow-sm p-4">
                <h1 class="text-success fw-bold"><?= $total ?></h1>
                <p>Total Kasus Terdata</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4">
                <h1 class="text-success fw-bold">
                    <?= mysqli_num_rows($desa) ?>
                </h1>
                <p>Desa Terkoneksi</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-4">
                <h3 class="text-success fw-bold">Kabupaten Gorontalo</h3>
                <p>Wilayah Utama</p>
            </div>
        </div>

    </div>
</div>

<!-- DAFTAR DESA -->
<div class="container mt-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Lihat Berdasarkan Desa</h2>
        <p class="text-muted">Pilih desa untuk melihat kasus</p>
    </div>

    <div class="row g-4 justify-content-center">

        <?php while ($d = mysqli_fetch_assoc($desa)) { ?>
        <div class="col-md-3 col-6">
            <a href="kasus.php?desa=<?= $d['id_desa'] ?>" class="text-decoration-none">
                <div class="card shadow-sm text-center p-3">
                    <i class="bi bi-geo-alt-fill text-danger" style="font-size: 2.5rem;"></i>
                    <h5 class="mt-2 fw-bold text-dark"><?= $d['nama_desa'] ?></h5>
                </div>
            </a>
        </div>
        <?php } ?>

    </div>
</div>

<!-- KASUS TERBARU -->
<div class="container mt-5">
    <div class="text-center mb-4">
        <h2 class="fw-bold">Kasus Terbaru</h2>
        <p class="text-muted">Beberapa kasus terbaru yang tercatat</p>
    </div>

    <div class="row g-4">

        <?php if (mysqli_num_rows($kasus) > 0) { ?>

            <?php while ($k = mysqli_fetch_assoc($kasus)) { ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold"><?= $k['judul_kasus'] ?></h5>

                        <span class="badge bg-danger"><?= $k['jenis_kasus'] ?></span>
                        <span class="badge bg-primary"><?= $k['wilayah'] ?></span>

                        <p class="text-muted mt-2">
                            <?= substr(strip_tags($k['deskripsi']), 0, 80) ?>...
                        </p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="detail_kasus.php?id=<?= $k['id_kasus'] ?>" 
                           class="btn btn-outline-success w-100">
                            <i class="bi bi-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>

        <?php } else { ?>

        <div class="col-12">
            <div class="alert alert-warning text-center">Belum ada kasus.</div>
        </div>

        <?php } ?>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
