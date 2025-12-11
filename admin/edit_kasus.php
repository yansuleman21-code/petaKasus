<?php
include 'includes/header_admin.php';
include 'includes/sidebar.php';
include 'koneksi.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location='../kasus.php';</script>";
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM kasus WHERE id_kasus = '$id'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
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

    $update = mysqli_query($koneksi, "UPDATE kasus SET 
        judul_kasus='$judul', jenis_kasus='$jenis', desa_kasus='$desa', 
        tanggal='$tanggal', wilayah='$wilayah', instansi='$instansi', 
        status_kasus='$status', latitude='$lat', longitude='$long', 
        deskripsi='$deskripsi' 
        WHERE id_kasus='$id'");

    if ($update) {
        echo "<script>alert('Data berhasil diupdate!'); window.location='data_kasus.php';</script>";
    } else {
        echo "<script>alert('Gagal update data');</script>";
    }
}
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">Edit Data Kasus</h6>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label>Judul Kasus</label>
                    <input type="text" name="judul" class="form-control" value="<?= $data['judul_kasus'] ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jenis Kasus</label>
                            <select name="jenis" class="form-control">
                                <option value="<?= $data['jenis_kasus'] ?>" selected><?= $data['jenis_kasus'] ?> (Saat ini)</option>
                                <option value="Pidana Umum">Pidana Umum</option>
                                <option value="Pidana Khusus">Pidana Khusus</option>
                                <option value="Perdata">Perdata</option>
                                <option value="Narkotika">Narkotika</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status Kasus</label>
                            <select name="status" class="form-control">
                                <option value="<?= $data['status_kasus'] ?>" selected><?= $data['status_kasus'] ?> (Saat ini)</option>
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
                            <label>Desa</label>
                            <select name="desa" class="form-control">
                                <?php
                                $q_desa = mysqli_query($koneksi, "SELECT * FROM desa");
                                while ($d = mysqli_fetch_assoc($q_desa)) {
                                    $selected = ($d['id_desa'] == $data['desa_kasus']) ? 'selected' : '';
                                    echo "<option value='".$d['id_desa']."' $selected>".$d['nama_desa']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= $data['tanggal'] ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Instansi</label>
                            <input type="text" name="instansi" class="form-control" value="<?= $data['instansi'] ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                         <div class="form-group">
                            <label>Lat</label>
                            <input type="text" name="latitude" class="form-control" value="<?= $data['latitude'] ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                         <div class="form-group">
                            <label>Long</label>
                            <input type="text" name="longitude" class="form-control" value="<?= $data['longitude'] ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Wilayah</label>
                    <input type="text" name="wilayah" class="form-control" value="<?= $data['wilayah'] ?>">
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="5"><?= $data['deskripsi'] ?></textarea>
                </div>

                <button type="submit" name="update" class="btn btn-warning text-white">Update Data</button>
                <a href="../kasus.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>