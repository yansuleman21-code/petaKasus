<?php
include 'admin/koneksi.php';
include 'includes/header.php';

// Ambil keyword pencarian jika ada
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : "";

// Query desa, jika ada keyword gunakan LIKE
if ($keyword != "") {
    $desa_query = mysqli_query($koneksi, "SELECT * FROM desa WHERE nama_desa LIKE '%$keyword%' ORDER BY nama_desa ASC");
} else {
    $desa_query = mysqli_query($koneksi, "SELECT * FROM desa ORDER BY nama_desa ASC");
}
$desa_arr = mysqli_fetch_all($desa_query, MYSQLI_ASSOC);
?>

<div class="container mt-5">
    <h2 class="fw-bold text-center mb-4">Daftar Desa</h2>

    <!-- FORM PENCARIAN -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <form method="GET" class="d-flex">
                <input type="text" name="keyword" class="form-control me-2" 
                       placeholder="Cari nama desa..." value="<?= htmlspecialchars($keyword) ?>">
                <button class="btn btn-primary">Cari</button>
            </form>
        </div>
    </div>

    <!-- DAFTAR DESA DALAM TABEL -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Desa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (count($desa_arr) > 0) {
                    $no = 1;
                    foreach ($desa_arr as $d) { 
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $d['nama_desa'] ?></td>
                    <td class="text-center">
                        <a href="kasus.php?desa=<?= $d['id_desa'] ?>" class="btn btn-sm btn-success">
                            Lihat Kasus
                        </a>
                    </td>
                </tr>
                <?php } } else { ?>
                <tr>
                    <td colspan="3" class="text-center">Desa tidak ditemukan.</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
