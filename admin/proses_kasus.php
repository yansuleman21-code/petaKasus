<?php
include 'includes/header_admin.php';
include 'includes/sidebar.php';
include 'koneksi.php';

// Validasi ID
if (!isset($_GET['id'])) {
    echo "<script>window.location='data_kasus.php';</script>";
    exit;
}
$id = $_GET['id'];

// 1. PROSES UPDATE JIKA ADA POST
if (isset($_POST['proses'])) {
    $status = $_POST['status'];
    $desa = $_POST['desa'];
    $catatan_baru = mysqli_real_escape_string($koneksi, $_POST['catatan']);

    // Ambil deskripsi lama dulu untuk diappend
    $q_old = mysqli_query($koneksi, "SELECT deskripsi FROM kasus WHERE id_kasus = '$id'");
    $d_old = mysqli_fetch_assoc($q_old);
    $deskripsi_lama = $d_old['deskripsi'];
    $timestamp = date('d-m-Y H:i');

    if (!empty(trim($_POST['catatan']))) {
        $deskripsi_update = $deskripsi_lama . "\n\n[LOG $timestamp]: " . $catatan_baru;
    } else {
        $deskripsi_update = $deskripsi_lama;
    }

    // UPDATE QUERY
    $query_update = "UPDATE kasus SET status_kasus='$status', desa_kasus='$desa', deskripsi='$deskripsi_update' WHERE id_kasus='$id'";
    $update = mysqli_query($koneksi, $query_update);

    if ($update) {
        // Cek impacted rows
        if (mysqli_affected_rows($koneksi) > 0) {
            echo "<script>alert('BERHASIL! Status berubah menjadi: $status'); window.location='data_kasus.php';</script>";
        } else {
            echo "<script>alert('Data disimpan, tetapi tidak ada perubahan yang terdeteksi (Mungkin data sama).'); window.location='data_kasus.php';</script>";
        }
    } else {
        $err = mysqli_error($koneksi);
        echo "<script>alert('GAGAL UPDATE: $err');</script>";
    }
}

// 2. FETCH DATA UNTUK TAMPILAN
$query = mysqli_query($koneksi, "SELECT kasus.*, desa.nama_desa FROM kasus LEFT JOIN desa ON kasus.desa_kasus = desa.id_desa WHERE id_kasus = '$id'");
$data = mysqli_fetch_assoc($query);

// Validasi Data Exist
if (!$data) {
    echo "<script>alert('Data kasus tidak ditemukan!'); window.location='data_kasus.php';</script>";
    exit;
}
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Proses Perkara (V3 - Debug Mode)</h1>
        <a href="data_kasus.php" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left"></i>
            Kembali</a>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Detail Kasus -->
        <div class="col-xl-5 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">Detail Perkara</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="30%"><b>Judul</b></td>
                                <td><?= $data['judul_kasus'] ?></td>
                            </tr>
                            <tr>
                                <td><b>Status Saat Ini</b></td>
                                <td><span class="badge badge-warning"
                                        style="font-size:1.2em;"><?= $data['status_kasus'] ?></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Proses -->
        <div class="col-xl-7 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success">
                    <h6 class="m-0 font-weight-bold text-white">Form Update</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label class="font-weight-bold text-danger">PILIH STATUS BARU:</label>
                            <select name="status" class="form-control form-control-lg border-danger">
                                <option value="<?= $data['status_kasus'] ?>" selected hidden>--
                                    <?= $data['status_kasus'] ?> --</option>
                                <option value="Dalam Proses">Dalam Proses</option>
                                <option value="P21">P21</option>
                                <option value="Tahap 2">Tahap 2</option>
                                <option value="Persidangan">Persidangan</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Validasi Desa</label>
                            <select name="desa" class="form-control">
                                <?php
                                $q_desa = mysqli_query($koneksi, "SELECT * FROM desa ORDER BY nama_desa ASC");
                                while ($d = mysqli_fetch_assoc($q_desa)) {
                                    $selected = ($d['id_desa'] == $data['desa_kasus']) ? 'selected' : '';
                                    echo "<option value='" . $d['id_desa'] . "' $selected>" . $d['nama_desa'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" name="proses" class="btn btn-success btn-block btn-lg">
                            <i class="fas fa-save"></i> SIMPAN DAN UPDATE
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>