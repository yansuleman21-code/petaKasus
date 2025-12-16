<?php
include 'includes/header_admin.php';
include 'includes/sidebar.php';
include 'koneksi.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location='data_desa.php';</script>";
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM desa WHERE id_desa = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='data_desa.php';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $lat = mysqli_real_escape_string($koneksi, $_POST['latitude']);
    $long = mysqli_real_escape_string($koneksi, $_POST['longitude']);

    $update = mysqli_query($koneksi, "UPDATE desa SET nama_desa='$nama', latitude='$lat', longitude='$long' WHERE id_desa='$id'");

    if ($update) {
        echo "<script>alert('Data desa berhasil diupdate!'); window.location='data_desa.php';</script>";
    } else {
        echo "<script>alert('Gagal update data: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data Desa</h6>
        </div>
        <div class="card-body">
            <form method="POST">

                <div class="form-group">
                    <label>Nama Desa</label>
                    <input type="text" name="nama" class="form-control"
                        value="<?= htmlspecialchars($data['nama_desa']) ?>" required>
                    <small class="text-muted">Pastikan nama sesuai dengan yang ada di Peta (GeoJSON) agar warna
                        muncul.</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Latitude</label>
                            <input type="text" name="latitude" class="form-control" value="<?= $data['latitude'] ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Longitude</label>
                            <input type="text" name="longitude" class="form-control" value="<?= $data['longitude'] ?>">
                        </div>
                    </div>
                </div>

                <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                <a href="data_desa.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>