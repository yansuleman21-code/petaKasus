<?php
include 'includes/header_admin.php';
include 'includes/sidebar.php'; 
include 'koneksi.php';

// Proses simpan data
if (isset($_POST['simpan'])) {
    $judul      = $_POST['judul'];
    $jenis      = $_POST['jenis'];
    $desa       = $_POST['desa'];
    $tanggal    = $_POST['tanggal'];
    $wilayah    = $_POST['wilayah'];
    $instansi   = $_POST['instansi'];
    $status     = $_POST['status'];
    $lat        = $_POST['latitude'];
    $long       = $_POST['longitude'];
    $deskripsi  = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    // Query insert lengkap sesuai struktur database
    $query = "INSERT INTO kasus (judul_kasus, jenis_kasus, desa_kasus, tanggal, deskripsi, wilayah, instansi, status_kasus, latitude, longitude) 
              VALUES ('$judul', '$jenis', '$desa', '$tanggal', '$deskripsi', '$wilayah', '$instansi', '$status', '$lat', '$long')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil disimpan!'); window.location='data_kasus.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Data Kasus Baru</h6>
        </div>
        <div class="card-body">
            <form method="POST">
                
                <div class="form-group">
                    <label>Judul Kasus</label>
                    <input type="text" name="judul" class="form-control" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jenis Kasus</label>
                            <select name="jenis" class="form-control" required>
                                <option value="Pidana Umum">Pidana Umum</option>
                                <option value="Pidana Khusus">Pidana Khusus</option>
                                <option value="Perdata">Perdata</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status Kasus</label>
                            <select name="status" class="form-control" required>
                                <option value="Dalam Proses">Dalam Proses</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Ditolak">Ditolak</option>
                                <option value="Tidak Cukup Bukti">Tidak Cukup Bukti</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Desa Kejadian</label>
                            <select name="desa" class="form-control" required>
                                <option value="">- Pilih Desa -</option>
                                <?php
                                $q_desa = mysqli_query($koneksi, "SELECT * FROM desa ORDER BY nama_desa ASC");
                                while ($d = mysqli_fetch_assoc($q_desa)) {
                                    echo "<option value='".$d['id_desa']."'>".$d['nama_desa']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Kejadian</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Instansi Penanggung Jawab</label>
                            <input type="text" name="instansi" class="form-control" placeholder="Misal: Polres Gorontalo">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Latitude (Opsional)</label>
                            <input type="text" name="latitude" class="form-control" placeholder="-0.xxxx">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Longitude (Opsional)</label>
                            <input type="text" name="longitude" class="form-control" placeholder="123.xxxx">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Wilayah / Dusun Detail</label>
                    <input type="text" name="wilayah" class="form-control" placeholder="Contoh: Dusun III, Dekat Pasar">
                </div>

                <div class="form-group">
                    <label>Deskripsi Lengkap</label>
                    <textarea name="deskripsi" class="form-control" rows="5" required></textarea>
                </div>

                <button type="submit" name="simpan" class="btn btn-primary">Simpan Data</button>
                <a href="../kasus.php" class="btn btn-secondary">Batal</a>

            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>