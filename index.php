<?php
include 'admin/koneksi.php';
include 'includes/header.php';

// 1. Ambil Data Desa (Titik Kantor Desa)
$q_desa = mysqli_query($koneksi, "SELECT * FROM desa WHERE latitude != '' AND longitude != ''");
$desa_data = [];
while($d = mysqli_fetch_assoc($q_desa)) {
    // Hitung jumlah kasus per desa
    $id_desa = $d['id_desa'];
    $jml_kasus = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM kasus WHERE desa_kasus='$id_desa'"));
    $d['jumlah_kasus'] = $jml_kasus;
    $desa_data[] = $d;
}

// 2. Ambil Data Kasus (Titik Kejadian)
$q_kasus = mysqli_query($koneksi, "SELECT kasus.*, desa.nama_desa FROM kasus JOIN desa ON kasus.desa_kasus=desa.id_desa");
$kasus_data = [];
while($k = mysqli_fetch_assoc($q_kasus)) {
    $kasus_data[] = $k;
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Layout Profesional Full Screen Map */
    body { overflow-x: hidden; }
    .main-container {
        position: relative;
        height: 90vh; /* Tinggi Peta */
        width: 100%;
    }
    #map {
        height: 100%;
        width: 100%;
        z-index: 1;
    }
    
    /* Floating Info Panel (Panel Melayang) */
    .info-panel {
        position: absolute;
        top: 20px;
        left: 20px;
        width: 350px;
        max-height: 85vh;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(5px);
        z-index: 999; /* Di atas peta */
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.2);
        overflow-y: auto;
        padding: 20px;
        transition: transform 0.3s ease;
    }
    
    .info-stat-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        border-left: 4px solid #0d6efd;
    }

    /* Tombol Toggle Panel untuk Mobile */
    .panel-toggle {
        display: none;
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
    }

    @media (max-width: 768px) {
        .info-panel {
            width: 90%;
            left: 5%;
            top: auto;
            bottom: -100%; /* Sembunyi dulu */
            transition: bottom 0.4s;
        }
        .info-panel.active {
            bottom: 20px;
        }
        .panel-toggle { display: block; }
    }
</style>

<div class="main-container">
    <div id="map"></div>

    <div class="info-panel" id="infoPanel">
        <h4 class="fw-bold text-primary mb-3"><i class="bi bi-shield-fill-check"></i> Dashboard Kasus</h4>
        
        <div class="info-stat-card">
            <h2 class="fw-bold mb-0"><?= count($kasus_data) ?></h2>
            <small class="text-muted">Total Kasus Terlapor</small>
        </div>
        
        <div class="info-stat-card" style="border-left-color: #198754;">
            <h2 class="fw-bold mb-0"><?= count($desa_data) ?></h2>
            <small class="text-muted">Desa Terpantau</small>
        </div>

        <hr>

        <h6 class="fw-bold mb-3">Detail Wilayah</h6>
        <div id="dynamic-content">
            <p class="text-muted small">Klik ikon <b>Kantor Desa</b> (Biru) atau <b>Titik Kasus</b> (Merah) di peta untuk melihat detail informasi di sini.</p>
        </div>

        <div class="d-grid gap-2 mt-4">
            <button class="btn btn-outline-primary btn-sm" onclick="resetView()">
                <i class="bi bi-globe"></i> Reset Tampilan Peta
            </button>
            <button class="btn btn-success btn-sm" onclick="locateUser()">
                <i class="bi bi-geo-alt"></i> Lokasi Saya
            </button>
        </div>
    </div>

    <button class="btn btn-primary rounded-pill panel-toggle shadow" onclick="togglePanel()">
        <i class="bi bi-list"></i> Lihat/Tutup Info
    </button>
</div>

<script>
    var dataDesa = <?= json_encode($desa_data) ?>;
    var dataKasus = <?= json_encode($kasus_data) ?>;
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // 1. SETUP MAP
    var map = L.map('map', { zoomControl: false }).setView([0.626, 122.986], 11);
    L.control.zoom({ position: 'topright' }).addTo(map);

    var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: 'OSM' }).addTo(map);
    var satelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: 'Esri' });

    L.control.layers({ "Peta Jalan": osm, "Satelit": satelit }).addTo(map);

    // 2. ICON MARKERS
    var iconDesa = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34]
    });

    var iconKasus = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        iconSize: [20, 32], iconAnchor: [10, 32], popupAnchor: [1, -24]
    });

    // 3. RENDER DESA (TITIK PUSAT)
    dataDesa.forEach(d => {
        if(d.latitude && d.longitude) {
            var marker = L.marker([d.latitude, d.longitude], {icon: iconDesa}).addTo(map);
            
            // Label Nama Desa Permanen (Kecil)
            marker.bindTooltip(d.nama_desa, {permanent: true, direction: 'bottom', className: 'bg-transparent border-0 text-primary fw-bold'});

            // Klik Desa
            marker.on('click', () => {
                map.flyTo([d.latitude, d.longitude], 15);
                showDesaInfo(d);
            });
        }
    });

    // 4. RENDER KASUS (TITIK MERAH)
    dataKasus.forEach(k => {
        if(k.latitude && k.longitude) {
            var marker = L.marker([k.latitude, k.longitude], {icon: iconKasus}).addTo(map);
            marker.bindPopup(`<b>${k.judul_kasus}</b><br>${k.jenis_kasus}`);
            
            marker.on('click', () => {
                showKasusInfo(k);
            });
        }
    });

    // 5. FUNGSI DINAMIS INFO PANEL
    function showDesaInfo(data) {
        var html = `
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white py-2">Kantor Desa</div>
                <div class="card-body py-2">
                    <h5 class="card-title fw-bold">${data.nama_desa}</h5>
                    <p class="card-text small">${data.deskripsi || 'Tidak ada deskripsi.'}</p>
                    <span class="badge bg-warning text-dark">Total Kasus: ${data.jumlah_kasus}</span>
                </div>
            </div>
            <h6>Daftar Kasus di Sini:</h6>
            <ul class="list-group list-group-flush small">`;

        // Filter kasus di desa ini
        var kasusDiDesa = dataKasus.filter(k => k.desa_kasus == data.id_desa);
        
        if(kasusDiDesa.length > 0){
            kasusDiDesa.forEach(k => {
                html += `<li class="list-group-item px-0">
                    <i class="bi bi-exclamation-circle text-danger"></i> 
                    <b>${k.judul_kasus}</b> <br> 
                    <span class="text-muted">${k.tanggal}</span>
                </li>`;
            });
        } else {
            html += `<li class="list-group-item text-muted">Belum ada kasus tercatat.</li>`;
        }
        
        html += `</ul>`;
        document.getElementById('dynamic-content').innerHTML = html;
        openPanelMobile();
    }

    function showKasusInfo(data) {
        var html = `
            <div class="alert alert-danger">
                <h6 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle"></i> Detail Kasus</h6>
                <hr>
                <h5 class="fw-bold">${data.judul_kasus}</h5>
                <span class="badge bg-secondary mb-2">${data.jenis_kasus}</span>
                <p class="mb-0 small">${data.deskripsi}</p>
                <hr>
                <small class="text-muted">Lokasi: ${data.wilayah} (${data.nama_desa})</small><br>
                <small class="text-muted">Tanggal: ${data.tanggal}</small>
            </div>
            <a href="detail_kasus.php?id=${data.id_kasus}" class="btn btn-primary btn-sm w-100 mt-2">Lihat Halaman Detail</a>
        `;
        document.getElementById('dynamic-content').innerHTML = html;
        openPanelMobile();
    }

    function resetView() {
        map.setView([0.626, 122.986], 11);
        document.getElementById('dynamic-content').innerHTML = `<p class="text-muted small">Klik ikon <b>Kantor Desa</b> (Biru) atau <b>Titik Kasus</b> (Merah) di peta untuk melihat detail informasi di sini.</p>`;
    }

    // Fungsi Mobile
    function togglePanel() {
        document.getElementById('infoPanel').classList.toggle('active');
    }
    function openPanelMobile() {
        document.getElementById('infoPanel').classList.add('active');
    }

    // Lokasi Saya
    function locateUser() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(pos => {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Anda").openPopup();
                map.setView([lat, lng], 16);
            });
        }
    }
</script>