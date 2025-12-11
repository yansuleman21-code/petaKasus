<?php
include 'admin/koneksi.php';
include 'includes/header.php';

// Ambil daftar desa
$desa = mysqli_query($koneksi, "SELECT * FROM desa");

// Siapkan filter
$filter = "";
if (isset($_GET['desa']) && $_GET['desa'] != "") {
    $id_desa = $_GET['desa'];
    $filter = "WHERE kasus.desa_kasus = '$id_desa'";
}

// Ambil data kasus dengan join ke tabel desa (pastikan nama kolom dan tabel sesuai)
$kasus = mysqli_query($koneksi, "SELECT kasus.*, desa.nama_desa 
                                FROM kasus 
                                JOIN desa ON kasus.desa_kasus = desa.id_desa
                                $filter
                                ORDER BY kasus.tanggal DESC");

?>


<div class="container mt-5">

    <h2 class="fw-bold text-center mb-4">Data Kasus</h2>

    <!-- FILTER DESA -->
    <form action="" method="GET" class="row mb-4">
        <div class="col-md-4">
            <select name="desa" class="form-select">
                <option value="">Semua Desa</option>
                <?php while ($d = mysqli_fetch_assoc($desa)) { ?>
                    <option value="<?= $d['id_desa'] ?>"
                        <?= isset($_GET['desa']) && $_GET['desa'] == $d['id_desa'] ? 'selected' : '' ?>>
                        <?= $d['nama_desa'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <!-- TABEL DATA KASUS -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr class="text-center">
                    <th>No</th>
                    <th>Judul Kasus</th>
                    <th>Jenis</th>
                    <th>Desa</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php 
                $no = 1;
                if (mysqli_num_rows($kasus) > 0) {
                    while ($k = mysqli_fetch_assoc($kasus)) { 
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $k['judul_kasus'] ?></td>
                    <td><span class="badge bg-danger"><?= $k['jenis_kasus'] ?></span></td>
                    <td><?= $k['nama_desa'] ?></td>
                    <td><?= date("d M Y", strtotime($k['tanggal'])) ?></td>
                    <td class="text-center">
                        <a href="detail_kasus.php?id=<?= $k['id_kasus'] ?>" class="btn btn-sm btn-success">
                            Detail
                        </a>

                        <!-- Jika admin login, munculkan edit & hapus -->
                        <?php if (isset($_SESSION['admin'])) { ?>
                            <a href="admin/edit_kasus.php?id=<?= $k['id_kasus'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="admin/hapus_kasus.php?id=<?= $k['id_kasus'] ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Yakin ingin menghapus?')">
                                Hapus
                            </a>
                        <?php } ?>
                    </td>
                </tr>

                <?php } } else { ?>

                <tr>
                    <td colspan="6" class="text-center">Belum ada data kasus.</td>
                </tr>

                <?php } ?>
            </tbody>

        </table>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
