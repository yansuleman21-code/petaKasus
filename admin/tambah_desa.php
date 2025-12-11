<?php
include 'includes/header_admin.php';
include 'includes/sidebar.php';
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $lat  = $_POST['latitude'];
    $long = $_POST['longitude'];
    $desc = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    $simpan = mysqli_query($koneksi, "INSERT INTO desa (nama_desa, latitude, longitude, deskripsi) VALUES ('$nama', '$lat', '$long', '$desc')");
    
    if ($simpan) {
        echo "<script>alert('Desa Berhasil Ditambahkan'); window.location='data_desa.php';</script>";
    } else {
        echo "<script>alert('Gagal Simpan');</script>";
    }
}
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Desa Baru</h6>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label>Nama Desa</label>
                    <input type="text" name="nama" class="form-control" required placeholder="Contoh: Desa Pone">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Latitude (Titik Tengah Desa)</label>
                            <input type="text" name="latitude" class="form-control" placeholder="-0.xxxx">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Longitude (Titik Tengah Desa)</label>
                            <input type="text" name="longitude" class="form-control" placeholder="122.xxxx">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Deskripsi Singkat Desa</label>
                    <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" name="simpan" class="btn btn-primary">Simpan Desa</button>
                <a href="data_desa.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>