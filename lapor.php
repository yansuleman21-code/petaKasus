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
        $target_dir = "assets/img/laporan/";
        // Buat folder jika belum ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = time() . "_" . rand(1000,9999) . "." . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $foto);
    }

    $sql = "INSERT INTO laporan_masyarakat (nama_pelapor, no_hp, judul_laporan, deskripsi, kategori, foto, latitude, longitude) 
            VALUES ('$nama', '$hp', '$judul', '$deskripsi', '$kategori', '$foto', '$lat', '$long')";
            
    if(mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Laporan berhasil dikirim! Terima kasih atas partisipasi Anda.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mengirim laporan: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #mapPicker { 
        height: 350px; 
        width: 100%; 
        border-radius: 8px; 
        border: 2px solid #dc3545; /* Merah sesuai tema lapor */
        cursor: crosshair; 
        margin-bottom: 10px;
    }
</style>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-danger text-white p-4">
                    <h3 class="fw-bold mb-0"><i class="bi bi-megaphone-fill"></i> Layanan Pengaduan Masyarakat</h3>
                    <p class="mb-0 text-white-50">Sampaikan laporan dugaan pelanggaran hukum di wilayah Gorontalo.</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="bi bi-shield-lock-fill me-2 fs-4"></i>
                            <div>Identitas pelapor akan dirahasiakan oleh pihak Kejaksaan.</div>
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
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Dugaan Korupsi Dana Desa X" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kategori Kasus</label>
                                <select name="kategori" class="form-select" required>
                                    <option value="">- Pilih Kategori -</option>
                                    <option value="Pidana Umum">Pidana Umum</option>
                                    <option value="Pidana Khusus">Pidana Khusus</option>
                                    <option value="Narkotika">Narkotika</option>
                                    <option value="Ketertiban Umum">Ketertiban Umum</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Bukti Foto (Jika ada)</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi Kejadian</label>
                            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan kronologi kejadian secara rinci..." required></textarea>
                        </div>

                        <hr class="my-4">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-danger">Lokasi Kejadian (Pilih di Peta)</label>
                            
                            <div id="mapPicker"></div>
                            
                            <div class="input-group mb-2">
                                <button type="button" class="btn btn-outline-danger" onclick="getLocation()">
                                    <i class="bi bi-crosshair"></i> Gunakan Lokasi Saya (GPS)
                                </button>
                                <input type="text" id="lat" name="latitude" class="form-control bg-light" placeholder="Latitude" readonly required>
                                <input type="text" id="lng" name="longitude" class="form-control bg-light" placeholder="Longitude" readonly required>
                            </div>
                            <small class="text-muted"><i class="bi bi-info-circle"></i> Klik pada peta untuk menandai lokasi kejadian yang tepat.</small>
                        </div>

                        <button type="submit" name="kirim" class="btn btn-danger w-100 fw-bold py-3 mt-3 shadow">
                            <i class="bi bi-send-fill"></i> KIRIM LAPORAN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // 1. Inisialisasi Peta (Default Center Gorontalo)
    var map = L.map('mapPicker').setView([0.626, 122.986], 11);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    var marker;

    // 2. Fungsi Klik Peta untuk Menaruh Marker
    map.on('click', function(e) {
        updateMarker(e.latlng.lat, e.latlng.lng);
    });

    // 3. Fungsi Update Marker & Input
    function updateMarker(lat, lng) {
        // Jika marker sudah ada, pindahkan posisinya
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            // Jika belum ada, buat marker baru yang bisa digeser (draggable)
            marker = L.marker([lat, lng], {draggable: true}).addTo(map);
            
            // Update input saat marker digeser manual
            marker.on('dragend', function(e) {
                var position = marker.getLatLng();
                document.getElementById('lat').value = position.lat;
                document.getElementById('lng').value = position.lng;
            });
        }
        
        // Isi kolom input latitude & longitude
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
    }

    // 4. Fungsi Geolocation (Lokasi Saya)
    function getLocation() {
        if (navigator.geolocation) {
            document.body.style.cursor = "wait"; // Ubah kursor jadi loading
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else { 
            alert("Browser tidak mendukung Geolocation.");
        }
    }

    function showPosition(position) {
        document.body.style.cursor = "default";
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
        
        // Zoom ke lokasi user
        map.setView([lat, lng], 16);
        
        // Pasang marker di lokasi user
        updateMarker(lat, lng);
        
        // Tambahkan popup info
        marker.bindPopup("Lokasi Anda").openPopup();
    }

    function showError(error) {
        document.body.style.cursor = "default";
        alert("Gagal mengambil lokasi. Pastikan GPS aktif dan izin lokasi diberikan.");
    }
</script>

<?php include 'includes/footer.php'; ?>