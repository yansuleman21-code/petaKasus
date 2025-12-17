<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Kasus - Kabupaten Gorontalo</title>

    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            overflow: hidden;
        }

        #map {
            width: 100%;
            height: 100vh;
            background: #aaddf0;
        }

        .label-desa {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            font-size: 8px;
            font-weight: bold;
            color: #333;
            text-shadow: 1px 1px 0px #fff, -1px -1px 0px #fff, 1px -1px 0px #fff, -1px 1px 0px #fff;
            text-align: center;
            white-space: nowrap;
        }

        /* --- NEW DESIGN: FILTER BOX (Left) --- */
        .filter-box {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 220px;
            background: white;
            z-index: 1000;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .filter-header {
            background: #b92b27;
            /* Red header */
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
        }

        .filter-header i {
            margin-right: 8px;
            font-size: 14px;
        }

        .filter-content {
            padding: 0;
        }

        .filter-section {
            border-bottom: 1px solid #eee;
            padding: 10px 15px;
        }

        .filter-section:last-child {
            border-bottom: none;
        }

        .filter-title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            display: block;
            background: #eee;
            padding: 5px;
            margin: -10px -15px 10px -15px;
            padding-left: 15px;
        }

        .radio-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
            color: #555;
            cursor: pointer;
        }

        .radio-item input {
            margin-right: 8px;
        }

        .radio-item:last-child {
            margin-bottom: 0;
        }

        /* --- NEW DESIGN: INFO PANEL (Right Top) --- */
        .info-panel {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 320px;
            background: white;
            z-index: 1000;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        .info-top {
            padding: 15px;
            background: #fff;
            border-bottom: 1px solid #eee;
        }

        .info-title-main {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .info-subtitle {
            font-size: 11px;
            color: #777;
            margin-bottom: 10px;
        }

        .info-stat-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9f9f9;
            padding: 10px;
            border-left: 4px solid #b92b27;
        }

        .stat-main-val {
            font-size: 24px;
            font-weight: bold;
            color: #b92b27;
        }

        .stat-main-label {
            font-size: 11px;
            color: #555;
            text-align: right;
            line-height: 1.2;
        }

        .info-details {
            padding: 15px;
            background: #fff;
            display: none;
        }

        /* Hidden by default until clicked */
        .detail-header {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            border-bottom: 2px solid #b92b27;
            padding-bottom: 5px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 6px 0;
            border-bottom: 1px dashed #eee;
        }

        .detail-row span {
            color: #666;
        }

        .detail-row b {
            color: #333;
        }

        /* Legend Gradient */
        .legend-gradient {
            position: absolute;
            bottom: 30px;
            left: 20px;
            background: white;
            padding: 10px;
            z-index: 1000;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.2);
            font-size: 11px;
            border-radius: 4px;
        }

        .grad-bar {
            width: 150px;
            height: 10px;
            margin-bottom: 5px;
            background: linear-gradient(to right, #2ecc71, #f1c40f, #e67e22, #e74c3c, #b92b27);
        }

        .grad-labels {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #666;
        }

        .nav-btn {
            position: absolute;
            bottom: 30px;
            right: 20px;
            z-index: 1000;
        }

        .btn-login {
            background: #333;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            display: inline-block;
            border-radius: 3px;
        }
    </style>
</head>

<body>

    <div id="map"></div>

    <!-- FILTER BOX (KIRI) -->
    <div class="filter-box">
        <div class="filter-header"><i class="fas fa-filter"></i> Filter Peta</div>
        <div class="filter-content">
            <div class="filter-section">
                <span class="filter-title">Jenis Kasus</span>
                <label class="radio-item"><input type="radio" name="jenis" value="all" checked onchange="updateMap()">
                    (Semua Kasus)</label>
                <label class="radio-item"><input type="radio" name="jenis" value="pidum" onchange="updateMap()"> Pidana
                    Umum</label>
                <label class="radio-item"><input type="radio" name="jenis" value="pidsus" onchange="updateMap()"> Pidana
                    Khusus</label>
                <label class="radio-item"><input type="radio" name="jenis" value="narkotika" onchange="updateMap()">
                    Narkotika</label>
            </div>
        </div>
    </div>

    <!-- INFO PANEL (KANAN) -->
    <div class="info-panel">
        <div class="info-top">
            <div class="info-title-main">Peta Sebaran Kasus</div>
            <div class="info-subtitle">Kabupaten Gorontalo (Kejaksaan Negeri)</div>

            <div class="info-stat-box">
                <div class="stat-main-val" id="globalSum">0</div>
                <div class="stat-main-label">Total Kasus<br>Terdata</div>
            </div>
        </div>

        <div class="info-details" id="detailPanel">
            <div class="detail-header" id="namaWilayah">Nama Desa</div>
            <div class="detail-row"><span>Total Kasus</span> <b id="valTotal">0</b></div>
            <div class="detail-row"><span>Pidana Umum</span> <b id="valPidum">0</b></div>
            <div class="detail-row"><span>Pidana Khusus</span> <b id="valPidsus">0</b></div>
            <div class="detail-row"><span>Narkotika</span> <b id="valNarkotika">0</b></div>
            <div class="detail-row"><span>Perdata</span> <b id="valPerdata">0</b></div>
            <div style="margin-top:15px; text-align:right;">
                <a href="#" id="linkDetail"
                    style="color:#b92b27; font-size:12px; font-weight:bold; text-decoration:none;">LIHAT DETAIL
                    &raquo;</a>
            </div>
        </div>
    </div>

    <!-- LEGEND -->
    <div class="legend-gradient">
        <div style="font-weight:bold; margin-bottom:5px;">Intensitas Kasus</div>
        <div class="grad-bar"></div>
        <div class="grad-labels">
            <span>Rendah (0)</span>
            <span>Tinggi</span>
        </div>
    </div>

    <div class="nav-btn">
        <a href="lapor.php" class="btn-login" style="background:#28a745; margin-right:5px;">LAPOR MASALAH</a>
        <a href="login.php" class="btn-login">LOGIN ADMIN</a>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // --- WARNA SPESIFIK & SETTING ---
        var warnaSpesifikDesa = {
            'LIMBOTO': '#f1c40f',
            // Gunakan Nama + Angka 1 untuk desa kembar kedua jika ingin warna fix
            // 'KAYU BULAN 1': '#8e44ad' 
        };

        var map = L.map('map', { zoomControl: false }).setView([0.62, 122.85], 10);
        L.control.zoom({ position: 'topleft' }).addTo(map); // Pindah zoom ke kiri atas bawah filter

        var geoJsonLayer;
        var dataDesaDB = {};
        var currentFilter = 'all';

        // FUNGSI WARNA GRADASI (CHOROPLETH)
        function getColor(count) {
            return count > 20 ? '#b92b27' : // Sangat Tinggi
                count > 10 ? '#e74c3c' : // Tinggi
                    count > 5 ? '#e67e22' : // Sedang
                        count > 0 ? '#f1c40f' : // Rendah
                            '#2ecc71';  // Nol / Aman
        }

        // 1. STYLE PETA
        function style(feature) {
            var namaUnik = feature.properties.nama_unik;
            var namaAsli = feature.properties.nm_kelurahan.toUpperCase(); // Ambil nama asli untuk fallback

            var fillColor = '#2ecc71'; // Default Hijau (Aman)
            var count = 0;
            var dataFound = null;

            // 1. Cek Data by Nama Unik (Prioritas)
            if (dataDesaDB[namaUnik]) {
                dataFound = dataDesaDB[namaUnik];
            }
            // 2. Fallback: Cek Data by Nama Asli (Jika nama unik tidak ada di DB, tapi nama asli ada)
            // Ini berguna jika di data Hanya 1 row "KAYUMERAH", tapi di Peta ada 2 Polygon "KAYUMERAH"
            else if (dataDesaDB[namaAsli]) {
                dataFound = dataDesaDB[namaAsli];
            }

            if (dataFound) {
                // Ambil jumlah berdasarkan filter aktif
                if (currentFilter == 'all') count = parseInt(dataFound.total);
                else if (currentFilter == 'pidum') count = parseInt(dataFound.pidum);
                else if (currentFilter == 'pidsus') count = parseInt(dataFound.pidsus);
                else if (currentFilter == 'narkotika') count = parseInt(dataFound.narkotika);

                fillColor = getColor(count);
            }

            // Override Warna Spesifik
            if (warnaSpesifikDesa[namaUnik]) {
                fillColor = warnaSpesifikDesa[namaUnik];
            }

            return {
                fillColor: fillColor, weight: 1, opacity: 1,
                color: 'white', dashArray: '', fillOpacity: 0.85
            };
        }

        // 2. INTERAKSI MOUSE
        function onEachFeature(feature, layer) {
            // Tooltip Nama Desa
            if (feature.properties && feature.properties.nm_kelurahan) {
                layer.bindTooltip(feature.properties.nm_kelurahan, {
                    permanent: true, direction: "center", className: "label-desa"
                });
            }

            layer.on({
                mouseover: function (e) {
                    e.target.setStyle({ weight: 2, color: '#333', fillOpacity: 1 });
                },
                mouseout: function (e) { geoJsonLayer.resetStyle(e.target); },
                click: function (e) {
                    var nama = e.target.feature.properties.nama_unik;
                    var namaAsli = e.target.feature.properties.nm_kelurahan.toUpperCase();

                    // Fallback logic
                    var data = dataDesaDB[nama] || dataDesaDB[namaAsli];

                    $('#detailPanel').show();
                    $('#namaWilayah').text(e.target.feature.properties.nm_kelurahan);

                    // Reset Default
                    $('#valTotal').text('0');
                    $('#valPidum').text('0');
                    $('#valPidsus').text('0');
                    $('#valNarkotika').text('0');
                    $('#valPerdata').text('0');
                    $('#linkDetail').hide();

                    if (data) {
                        $('#valTotal').text(data.total);
                        $('#valPidum').text(data.pidum);
                        $('#valPidsus').text(data.pidsus);
                        $('#valNarkotika').text(data.narkotika);
                        $('#valPerdata').text(data.perdata);
                        $('#linkDetail').attr('href', 'kasus.php?desa=' + data.id_desa).show();
                    }
                }
            });
        }

        // 3. LOAD DATA
        function initMap() {
            $.getJSON('assets/geo/75.01_kelurahan.geojson', function (geoData) {

                // --- LOGIKA NAMA GANDA (Backend & Frontend Sync) ---
                var penghitungNama = {};
                geoData.features.forEach(function (f) {
                    var namaAsli = f.properties.nm_kelurahan.toUpperCase();
                    if (!penghitungNama[namaAsli]) { penghitungNama[namaAsli] = 0; }
                    penghitungNama[namaAsli]++;
                    if (penghitungNama[namaAsli] == 1) {
                        f.properties.nama_unik = namaAsli;
                    } else {
                        f.properties.nama_unik = namaAsli + " " + (penghitungNama[namaAsli] - 1);
                    }
                });

                geoJsonLayer = L.geoJson(geoData, {
                    style: style,
                    onEachFeature: onEachFeature
                }).addTo(map);

                map.fitBounds(geoJsonLayer.getBounds());

                // Fetch Data DB
                $.getJSON('admin/api_sebaran.php', function (dbData) {
                    dataDesaDB = dbData;
                    updateGlobalStats(); // Update angka total global
                    geoJsonLayer.eachLayer(function (layer) { geoJsonLayer.resetStyle(layer); });
                });

            }).fail(function () {
                alert("ERROR: File GeoJSON tidak ditemukan.");
            });
        }

        function updateGlobalStats() {
            var total = 0;
            // Hitung total seluruh wilayah dari data yang ditarik
            for (var key in dataDesaDB) {
                if (dataDesaDB.hasOwnProperty(key)) {
                    // Hitung sesuai filter atau total global
                    if (currentFilter == 'all') total += parseInt(dataDesaDB[key].total);
                    else if (currentFilter == 'pidum') total += parseInt(dataDesaDB[key].pidum);
                    else if (currentFilter == 'pidsus') total += parseInt(dataDesaDB[key].pidsus);
                    else if (currentFilter == 'narkotika') total += parseInt(dataDesaDB[key].narkotika);
                }
            }
            $('#globalSum').text(total);
        }

        window.updateMap = function () {
            var radios = document.getElementsByName('jenis');
            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) { currentFilter = radios[i].value; break; }
            }
            updateGlobalStats();
            if (geoJsonLayer) {
                geoJsonLayer.eachLayer(function (layer) { geoJsonLayer.resetStyle(layer); });
            }
        }

        initMap();
    </script>
</body>

</html>