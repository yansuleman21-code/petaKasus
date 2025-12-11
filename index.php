<?php
include 'admin/koneksi.php';
include 'includes/header.php';

if(isset($_POST['kirim'])) {
    $nama = htmlspecialchars($_POST['nama']);
    $hp = htmlspecialchars($_POST['hp']);
    $judul = htmlspecialchars($_POST['judul']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);
    $kategori = $_POST['kategori'];
    $lat = $_POST['latitude'];
    $long = $_POST['longitude'];

    // Upload Foto
    $foto = "";
    if(!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = time() . "_" . rand(1000,9999) . "." . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], 'assets/img/laporan/' . $foto);
    }

    $sql = "INSERT INTO laporan_masyarakat (nama_pelapor, no_hp, judul_laporan, deskripsi, kategori, foto, latitude, longitude) 
            VALUES ('$nama', '$hp', '$judul', '$deskripsi', '$kategori', '$foto', '$lat', '$long')";
            
    if(mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Terima kasih! Laporan Anda berhasil dikirim dan akan diverifikasi.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mengirim laporan: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-danger text-white p-4">
                    <h3 class="fw-bold mb-0"><i class="bi bi-megaphone-fill"></i> Lapor Pak Jaksa!</h3>
                    <p class="mb-0 opacity-75">Sampaikan pengaduan pelanggaran hukum di sekitar Anda.</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        
                        <div class="alert alert-info border-0 d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                            <div>Identitas pelapor dirahasiakan oleh Kejaksaan.</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">No. HP / WhatsApp</label>
                                <input type="text" name="hp" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Laporan</label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Dugaan Pungli di Pasar X" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kategori</label>
                                <select name="kategori" class="form-select" required>
                                    <option value="">- Pilih Kategori -</option>
                                    <option value="Pidana Umum">Pidana Umum</option>
                                    <option value="Pidana Khusus">Pidana Khusus (Korupsi)</option>
                                    <option value="Narkotika">Narkotika</option>
                                    <option value="Ketertiban Umum">Ketertiban Umum</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bukti Foto</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi Kejadian</label>
                            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Ceritakan kronologi kejadian secara detail..." required></textarea>
                        </div>

                        <hr class="my-4">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">Lokasi Kejadian (Wajib)</label>
                            <div class="input-group mb-2">
                                <button type="button" class="btn btn-outline-danger" onclick="getLocation()">
                                    <i class="bi bi-geo-alt-fill"></i> Ambil Lokasi Saya
                                </button>
                                <input type="text" id="lat" name="latitude" class="form-control bg-light" placeholder="Latitude" readonly required>
                                <input type="text" id="lng" name="longitude" class="form-control bg-light" placeholder="Longitude" readonly required>
                            </div>
                            <small class="text-muted">Pastikan GPS Anda aktif lalu klik tombol di atas.</small>
                        </div>

                        <button type="submit" name="kirim" class="btn btn-danger w-100 fw-bold py-3 mt-3 shadow-sm">
                            <i class="bi bi-send-fill"></i> KIRIM LAPORAN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else { 
        alert("Browser ini tidak mendukung Geolocation.");
    }
}

function showPosition(position) {
    document.getElementById("lat").value = position.coords.latitude;
    document.getElementById("lng").value = position.coords.longitude;
    alert("Lokasi berhasil dikunci!");
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            alert("Mohon izinkan akses lokasi di browser Anda.");
            break;
        default:
            alert("Terjadi kesalahan saat mengambil lokasi.");
    }
}
</script>

<?php include 'includes/footer.php'; ?>