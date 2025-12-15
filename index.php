<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Sebaran Kasus - Kabupaten Gorontalo</title>
    
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        body { margin: 0; padding: 0; font-family: 'Roboto', sans-serif; overflow: hidden; }
        #map { width: 100%; height: 100vh; background: #aaddf0; }

        /* STYLE LABEL NAMA DESA DI PETA */
        .label-desa {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            font-size: 8px; /* Ukuran font kecil agar tidak berantakan */
            font-weight: bold;
            color: #333;
            text-shadow: 1px 1px 0px #fff, -1px -1px 0px #fff, 1px -1px 0px #fff, -1px 1px 0px #fff;
            text-align: center;
            white-space: nowrap;
        }

        /* 1. PANEL FILTER (KIRI) */
        .filter-box {
            position: absolute; top: 20px; left: 20px; width: 260px;
            background: white; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .filter-header {
            background: #b92b27; color: white; padding: 12px 15px;
            font-weight: bold; font-size: 16px; text-transform: uppercase;
            display: flex; align-items: center;
        }
        .filter-header i { margin-right: 10px; }
        .filter-content { padding: 15px; }
        .filter-title { font-size: 13px; font-weight: bold; color: #333; margin-bottom: 8px; display: block; }
        .radio-item { display: flex; align-items: center; margin-bottom: 6px; font-size: 13px; color: #555; cursor: pointer; }
        .radio-item input { margin-right: 8px; accent-color: #b92b27; }

        /* 2. PANEL INFO (KANAN ATAS) */
        .info-box {
            position: absolute; top: 20px; right: 20px; width: 300px;
            background: white; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            display: none;
        }
        .info-header { padding: 15px; border-bottom: 1px solid #eee; }
        .info-title { font-size: 12px; color: #666; margin-bottom: 5px; }
        .info-region { font-size: 18px; font-weight: bold; color: #000; text-transform: uppercase; }
        .info-body { padding: 15px; background: #f9f9f9; }
        .stat-big { font-size: 32px; font-weight: bold; color: #b92b27; margin-bottom: 5px; }
        .stat-desc { font-size: 12px; color: #666; }
        .stat-detail { margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 10px; }
        .row-detail { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px; }

        /* 3. LEGENDA & BUTTON */
        .legend {
            position: absolute; bottom: 30px; left: 20px;
            background: white; padding: 10px; z-index: 1000;
            box-shadow: 0 1px 5px rgba(0,0,0,0.2); font-size: 11px;
        }
        .legend-item { display: flex; align-items: center; margin-bottom: 2px; }
        .color-box { width: 20px; height: 20px; margin-right: 8px; display: inline-block; }
        
        .nav-btn { position: absolute; bottom: 30px; right: 20px; z-index: 1000; }
        .btn-login {
            background: #333; color: white; padding: 10px 20px;
            text-decoration: none; font-size: 14px; font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3); display: inline-block;
        }
    </style>
</head>
<body>

    <div id="map"></div>

    <div class="filter-box">
        <div class="filter-header"><i class="fas fa-filter"></i> Filter Peta</div>
        <div class="filter-content">
            <div style="margin-bottom: 20px;">
                <span class="filter-title">Tampilan Sebaran</span>
                <label class="radio-item"><input type="radio" checked disabled> Jumlah Kasus Kriminal</label>
            </div>
            <div>
                <span class="filter-title">Ranah Kasus</span>
                <label class="radio-item"><input type="radio" name="jenis" value="all" checked onchange="updateMap()"> (Semua Kasus)</label>
                <label class="radio-item"><input type="radio" name="jenis" value="pidum" onchange="updateMap()"> Pidana Umum</label>
                <label class="radio-item"><input type="radio" name="jenis" value="pidsus" onchange="updateMap()"> Pidana Khusus</label>
                <label class="radio-item"><input type="radio" name="jenis" value="narkotika" onchange="updateMap()"> Narkotika</label>
            </div>
        </div>
    </div>

    <div class="info-box" id="infoPanel">
        <div class="info-header">
            <div class="info-title">Detail Wilayah</div>
            <div class="info-region" id="namaWilayah">...</div>
        </div>
        <div class="info-body">
            <div class="stat-big" id="totalKasus">0</div>
            <div class="stat-desc">Total Kasus</div>
            <div class="stat-detail">
                <div class="row-detail"><span>Pidana Umum</span> <b id="valPidum">0</b></div>
                <div class="row-detail"><span>Pidana Khusus</span> <b id="valPidsus">0</b></div>
                <div class="row-detail"><span>Narkotika</span> <b id="valNarkotika">0</b></div>
                <div class="row-detail"><span>Perdata</span> <b id="valPerdata">0</b></div>
            </div>
            <div class="mt-3 text-center" style="margin-top:15px;">
                <a href="#" id="linkDetail" class="btn-login" style="font-size:11px; background:#b92b27;">LIHAT DATA LENGKAP</a>
            </div>
        </div>
    </div>

    <div class="legend">
        <div style="font-weight:bold; margin-bottom:5px;">Tingkat Kerawanan</div>
        <div id="legend-content"></div>
    </div>

    <div class="nav-btn">
        <a href="lapor.php" class="btn-login" style="background:#28a745; margin-right:10px;">LAPOR!</a>
        <a href="login.php" class="btn-login">LOGIN ADMIN</a>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        var map = L.map('map', { zoomControl: false }).setView([0.62, 122.85], 10); 
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        var geoJsonLayer;
        var dataKasus = {}; 
        var currentFilter = 'all';

        // Fungsi Warna
        function getColor(d) {
            return d > 20 ? '#800026' : d > 10 ? '#BD0026' : d > 5 ? '#E31A1C' : d > 2 ? '#FC4E2A' : d > 0 ? '#FEB24C' : '#FFEDA0';
        }

        // Style GeoJSON
        function style(feature) {
            var nama = feature.properties.nm_kelurahan; 
            if(nama) nama = nama.toUpperCase();

            var jumlah = 0;
            if (dataKasus[nama]) {
                if(currentFilter == 'all') jumlah = parseInt(dataKasus[nama].total);
                else if(currentFilter == 'pidum') jumlah = parseInt(dataKasus[nama].pidum);
                else if(currentFilter == 'pidsus') jumlah = parseInt(dataKasus[nama].pidsus);
                else if(currentFilter == 'narkotika') jumlah = parseInt(dataKasus[nama].narkotika);
            }

            return {
                fillColor: getColor(jumlah),
                weight: 1, opacity: 1, color: 'white', dashArray: '', fillOpacity: 0.9
            };
        }

        // Interaksi Mouse
        function onEachFeature(feature, layer) {
            // 1. MENAMBAHKAN LABEL NAMA DESA PERMANEN
            if (feature.properties && feature.properties.nm_kelurahan) {
                layer.bindTooltip(feature.properties.nm_kelurahan, {
                    permanent: true,     // Selalu tampil tanpa perlu hover
                    direction: "center", // Posisi di tengah polygon
                    className: "label-desa" // Menggunakan style CSS yang kita buat
                });
            }

            layer.on({
                mouseover: function(e) { e.target.setStyle({ weight: 3, color: '#666' }); },
                mouseout: function(e) { geoJsonLayer.resetStyle(e.target); },
                click: function(e) {
                    var nama = e.target.feature.properties.nm_kelurahan.toUpperCase();
                    var data = dataKasus[nama];

                    $('#infoPanel').fadeIn();
                    $('#namaWilayah').text(nama);

                    if(data) {
                        $('#totalKasus').text(data.total);
                        $('#valPidum').text(data.pidum);
                        $('#valPidsus').text(data.pidsus);
                        $('#valNarkotika').text(data.narkotika);
                        $('#valPerdata').text(data.perdata);
                        $('#linkDetail').attr('href', 'kasus.php?desa=' + data.id_desa).show();
                    } else {
                        $('#totalKasus').text('0');
                        $('.row-detail b').text('0');
                        $('#linkDetail').hide();
                    }
                }
            });
        }

        // Load Data
        function initMap() {
            // 1. Ambil Data DB
            $.getJSON('admin/api_sebaran.php', function(dbData) {
                dataKasus = dbData;

                // 2. Ambil File GeoJSON
                $.getJSON('assets/geo/75.01_kelurahan.geojson', function(geoData) {
                    if(geoJsonLayer) map.removeLayer(geoJsonLayer);
                    
                    geoJsonLayer = L.geoJson(geoData, {
                        style: style,
                        onEachFeature: onEachFeature
                    }).addTo(map);

                    map.fitBounds(geoJsonLayer.getBounds());
                }).fail(function() {
                    alert("Gagal memuat peta! Pastikan file 75.01_kelurahan.geojson ada di folder assets/geo/");
                });
            });
        }

        // Update Filter
        window.updateMap = function() {
            var radios = document.getElementsByName('jenis');
            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) { currentFilter = radios[i].value; break; }
            }
            if(geoJsonLayer) geoJsonLayer.setStyle(style);
        }

        // Buat Legenda
        var grades = [0, 2, 5, 10, 20];
        var legendHtml = '';
        for (var i = 0; i < grades.length; i++) {
            legendHtml += '<div class="legend-item"><i class="color-box" style="background:' + getColor(grades[i] + 1) + '"></i> ' +
                grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] : '+') + '</div>';
        }
        document.getElementById('legend-content').innerHTML = legendHtml;

        initMap();
    </script>
</body>
</html>