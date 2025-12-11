<?php
include 'admin/koneksi.php';
include 'includes/header.php';

// --- 1. PHP: AMBIL DATA ---
$query_map = mysqli_query($koneksi, "SELECT kasus.*, desa.nama_desa, desa.id_desa 
                                     FROM kasus 
                                     JOIN desa ON kasus.desa_kasus = desa.id_desa 
                                     WHERE kasus.latitude != '' AND kasus.longitude != ''");

$cases = [];
$desas = [];

while ($row = mysqli_fetch_assoc($query_map)) {
    $cases[] = $row;

    $id_desa = $row['id_desa'];
    if (!isset($desas[$id_desa])) {
        $desas[$id_desa] = [
            'nama' => $row['nama_desa'],
            'lat_sum' => 0,
            'lng_sum' => 0,
            'count' => 0
        ];
    }
    $desas[$id_desa]['lat_sum'] += $row['latitude'];
    $desas[$id_desa]['lng_sum'] += $row['longitude'];
    $desas[$id_desa]['count']++;
}

$desa_labels = [];
foreach ($desas as $id => $d) {
    $desa_labels[] = [
        'id_desa' => $id,
        'nama' => $d['nama'],
        'lat' => $d['lat_sum'] / $d['count'],
        'lng' => $d['lng_sum'] / $d['count'],
        'jumlah_kasus' => $d['count']
    ];
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { 
        height: 550px; 
        width: 100%; 
        border-radius: 10px; 
        z-index: 1;
    }
    
    /* STYLE LABEL DESA (TOOLTIP) */
    .label-desa-tooltip {
        background: rgba(255, 255, 255, 0.95);
        border: 2px solid #0d6efd;
        color: #0d6efd;
        font-weight: bold;
        font-size: 12px;
        border-radius: 20px;
        padding: 5px 10px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.3);
        cursor: pointer !important;
        pointer-events: auto !important;
    }
    
    .leaflet-tooltip-top:before, 
    .leaflet-tooltip-bottom:before, 
    .leaflet-tooltip-left:before, 
    .leaflet-tooltip-right:before {
        display: none !important;
    }

    .label-desa-tooltip:hover {
        background: #0d6efd;
        color: white;
        border-color: #004085;
        z-index: 1000 !important;
        transform: scale(1.1);
        transition: 0.2s;
    }

    .list-container {
        max-height: 550px;
        overflow-y: auto;
    }
    .kasus-item:hover {
        background-color: #f1faff;
    }
</style>

<section class="hero-section d-flex align-items-center text-white" 
         style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/img/bg.jpg'); 
                background-size: cover; 
                padding: 40px 0; 
                height: auto !important; 
                min-height: auto !important;">
    <div class="container text-center">
        <h1 class="fw-bold display-5">Peta Sebaran Kasus</h1>
        <p class="lead mb-0" style="font-size: 1rem;">Klik label nama desa di peta untuk melihat detail</p>
    </div>
</section>

<div class="container-fluid px-4 mt-4 mb-5">
    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="m-0 fw-bold text-primary"><i class="bi bi-map"></i> Peta Digital</h5>
                    
                    <div class="mt-2 mt-md-0">
                        <button class="btn btn-sm btn-success me-2" onclick="locateUser()">
                            <i class="bi bi-crosshair"></i> Lokasi Saya
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="resetMap()">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="m-0 fw-bold" id="list-title"><i class="bi bi-list-ul"></i> Daftar Kasus</h5>
                </div>
                <div class="card-body p-0 list-container">
                    <div id="kasus-list" class="list-group list-group-flush">
                        <div class="text-center p-4 text-muted">
                            <i class="bi bi-cursor-fill display-4"></i>
                            <p class="mt-2">Silakan klik <b>Label Desa</b> di peta untuk memfilter daftar kasus.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    var dataKasus = <?= json_encode($cases) ?>;
    var dataDesa = <?= json_encode($desa_labels) ?>;
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // --- 1. SETUP MAP ---
    var osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    });
    var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri'
    });

    var map = L.map('map', {
        center: [0.626, 122.986],
        zoom: 11,
        layers: [osmLayer]
    });

    L.control.layers({ "Peta Jalan": osmLayer, "Satelit": satelliteLayer }).addTo(map);

    var markersLayer = L.layerGroup().addTo(map);
    var labelsLayer = L.layerGroup().addTo(map);
    var userLayer = L.layerGroup().addTo(map);

    var caseIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    // --- 2. RENDER LABEL DESA ---
    dataDesa.forEach(function(desa) {
        var invisibleIcon = L.divIcon({className: 'd-none'});
        var anchorMarker = L.marker([desa.lat, desa.lng], {
            icon: invisibleIcon,
            opacity: 0
        });

        var labelContent = `${desa.nama} <span class="badge bg-danger ms-1">${desa.jumlah_kasus}</span>`;

        anchorMarker.bindTooltip(labelContent, {
            permanent: true,
            direction: 'center',
            className: 'label-desa-tooltip',
            interactive: true
        });

        anchorMarker.on('click', function() {
            filterDesa(desa.id_desa, desa.nama, desa.lat, desa.lng);
        });

        labelsLayer.addLayer(anchorMarker);
    });

    // --- 3. RENDER MARKERS ---
    renderMarkers(dataKasus);

    function renderMarkers(dataset) {
        markersLayer.clearLayers();
        dataset.forEach(function(k) {
            var m = L.marker([k.latitude, k.longitude], {icon: caseIcon})
                     .bindPopup(`
                        <div class="text-center">
                            <h6 class="fw-bold mb-1">${k.judul_kasus}</h6>
                            <span class="badge bg-secondary mb-2">${k.jenis_kasus}</span><br>
                            <a href="detail_kasus.php?id=${k.id_kasus}" class="btn btn-sm btn-primary mt-2 w-100 text-white">Lihat Detail</a>
                        </div>
                     `);
            markersLayer.addLayer(m);
        });
    }

    // --- 4. FUNGSI FILTER ---
    function filterDesa(id_desa, nama_desa, lat, lng) {
        var filteredData = dataKasus.filter(k => k.desa_kasus == id_desa);
        renderMarkers(filteredData);
        
        map.flyTo([lat, lng], 15, {
            animate: true,
            duration: 1.5
        });

        var listHTML = "";
        document.getElementById('list-title').innerHTML = `<i class="bi bi-geo-alt-fill"></i> Kasus di ${nama_desa}`;
        
        if(filteredData.length > 0) {
            filteredData.forEach(k => {
                listHTML += `
                <a href="detail_kasus.php?id=${k.id_kasus}" class="list-group-item list-group-item-action kasus-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 fw-bold text-primary">${k.judul_kasus}</h6>
                        <small class="text-muted" style="font-size:10px">${k.tanggal}</small>
                    </div>
                    <p class="mb-1 small text-muted text-truncate">${k.wilayah}</p>
                    <span class="badge bg-secondary" style="font-size:10px">${k.jenis_kasus}</span>
                </a>`;
            });
        } else {
            listHTML = `<div class="p-3 text-center text-muted">Tidak ada data kasus di desa ini.</div>`;
        }
        document.getElementById('kasus-list').innerHTML = listHTML;
    }

    // --- 5. FUNGSI LOKASI SAYA ---
    window.locateUser = function() {
        if (!navigator.geolocation) {
            alert("Browser tidak mendukung GPS.");
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                
                userLayer.clearLayers();
                
                var userIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                L.marker([lat, lng], {icon: userIcon}).addTo(userLayer).bindPopup("<b>Lokasi Anda</b>").openPopup();
                
                // UPDATE: Radius ditetapkan kecil (misal 50 meter) agar tidak menutupi peta
                L.circle([lat, lng], {
                    radius: 50, // Fixed 50 meter
                    color: '#0d6efd',
                    fillColor: '#0d6efd',
                    fillOpacity: 0.2
                }).addTo(userLayer);
                
                // Zoom ke lokasi user
                map.setView([lat, lng], 16);
            },
            function(error) {
                console.log("Gagal mengambil lokasi: " + error.message);
            }
        );
    };

    window.resetMap = function() {
        map.setView([0.626, 122.986], 11);
        renderMarkers(dataKasus);
        // Jangan hapus userLayer agar lokasi tetap terlihat meski direset
        document.getElementById('list-title').innerHTML = `<i class="bi bi-list-ul"></i> Daftar Kasus`;
        document.getElementById('kasus-list').innerHTML = `
            <div class="text-center p-4 text-muted">
                <i class="bi bi-cursor-fill display-4"></i>
                <p class="mt-2">Silakan klik <b>Label Desa</b> di peta untuk memfilter daftar kasus.</p>
            </div>`;
    };

    // --- 6. AUTO START LOKASI SAYA ---
    locateUser();

</script>

<?php include 'includes/footer.php'; ?>