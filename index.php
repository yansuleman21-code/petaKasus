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

        .info-box {
            position: absolute; top: 20px; right: 20px; width: 300px;
            background: white; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            display: none;
        }
        .info-header { padding: 15px; border-bottom: 1px solid #eee; }
        .info-title { font-size: 12px; color: #666; margin-bottom: 5px; }
        .info-region { font-size: 18px; font-weight: bold; color: #000; text-transform: uppercase; }
        .info-sub-region { font-size: 14px; color: #b92b27; font-weight: bold; text-transform: uppercase; margin-top:5px;} 
        .info-body { padding: 15px; background: #f9f9f9; }
        .stat-big { font-size: 32px; font-weight: bold; color: #b92b27; margin-bottom: 5px; }
        .stat-desc { font-size: 12px; color: #666; }
        .stat-detail { margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 10px; }
        .row-detail { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px; }
        .row-penduduk { background: #e8f4f8; padding: 5px 10px; margin-bottom: 10px; border-radius: 4px; display:flex; justify-content:space-between; font-weight:bold; color:#0c5460; font-size:13px;}

        .legend {
            position: absolute; bottom: 30px; left: 20px;
            background: white; padding: 10px; z-index: 1000;
            box-shadow: 0 1px 5px rgba(0,0,0,0.2); font-size: 11px;
            max-height: 300px; overflow-y: auto; /* Scroll jika kecamatan banyak */
        }
        .legend-item { display: flex; align-items: center; margin-bottom: 5px; }
        .color-box { width: 20px; height: 20px; margin-right: 8px; display: inline-block; border: 1px solid #ccc; }
        
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
        <div class="filter-header"><i class="fas fa-map"></i> Peta Administratif</div>
        <div class="filter-content">
            <div style="margin-bottom: 10px;">
                <span class="filter-title">Info Warna</span>
                <p style="font-size:12px; color:#666;">Peta diwarnai berdasarkan pembagian wilayah <b>Kecamatan</b>.</p>
            </div>
            <div>
                <span class="filter-title">Filter Data Kasus</span>
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
            <div class="info-sub-region" id="namaKecamatan">...</div>
        </div>
        <div class="info-body">
            <div class="row-penduduk">
                <span><i class="fas fa-users"></i> Penduduk:</span>
                <span id="valPenduduk">0 Jiwa</span>
            </div>

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
        <div style="font-weight:bold; margin-bottom:5px;">Legenda Kecamatan</div>
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
        var dataDesaDB = {}; 
        var currentFilter = 'all';

        // DEFINISI WARNA PER KECAMATAN
        // Tambahkan kecamatan lain di sini sesuai kebutuhan
        var warnaKecamatan = {
            'LIMBOTO': '#3498db',       // Biru
            'TELAGA': '#e74c3c',        // Merah
            'LIMBOTO BARAT': '#2ecc71', // Hijau
            'TIBAWA': '#9b59b6',        // Ungu
            'BATUDAA': '#f1c40f',       // Kuning
            'BONGOMEME': '#e67e22',     // Orange
            'TELAGA BIRU': '#1abc9c',   // Tosca
            'MOOTILANGO': '#34495e',    // Biru Gelap
            'PULUBALA': '#d35400',      // Merah Bata
            // Default jika tidak terdaftar
            'LAINNYA': '#95a5a6'        // Abu-abu
        };

        // Fungsi Ambil Warna
        function getWarnaByKecamatan(namaKecamatan) {
            if (!namaKecamatan) return warnaKecamatan['LAINNYA'];
            var key = namaKecamatan.toUpperCase();
            return warnaKecamatan[key] ? warnaKecamatan[key] : warnaKecamatan['LAINNYA'];
        }

        // Style GeoJSON
        function style(feature) {
            var namaDesa = feature.properties.nm_kelurahan; 
            if(namaDesa) namaDesa = namaDesa.toUpperCase();

            var kecamatan = "LAINNYA";
            
            // Cek apakah data desa ada di database
            if (dataDesaDB[namaDesa]) {
                kecamatan = dataDesaDB[namaDesa].kecamatan;
            }

            return {
                fillColor: getWarnaByKecamatan(kecamatan),
                weight: 1, 
                opacity: 1, 
                color: 'white', 
                dashArray: '', 
                fillOpacity: 0.8
            };
        }

        // Interaksi Mouse
        function onEachFeature(feature, layer) {
            if (feature.properties && feature.properties.nm_kelurahan) {
                layer.bindTooltip(feature.properties.nm_kelurahan, {
                    permanent: true, direction: "center", className: "label-desa"
                });
            }

            layer.on({
                mouseover: function(e) { e.target.setStyle({ weight: 3, color: '#ffff00', fillOpacity: 1 }); },
                mouseout: function(e) { geoJsonLayer.resetStyle(e.target); },
                click: function(e) {
                    var nama = e.target.feature.properties.nm_kelurahan.toUpperCase();
                    var data = dataDesaDB[nama];

                    $('#infoPanel').fadeIn();
                    $('#namaWilayah').text(nama);

                    if(data) {
                        // UPDATE INFO POPUP
                        $('#namaKecamatan').text('KEC. ' + (data.kecamatan || '-'));
                        $('#valPenduduk').text(new Intl.NumberFormat('id-ID').format(data.jumlah_penduduk) + ' Jiwa');
                        
                        // Update Data Kasus sesuai filter yang dipilih untuk tampilan angka saja
                        // (Warna tetap kecamatan, tapi angka berubah sesuai filter)
                        var totalTampil = 0;
                        if(currentFilter == 'all') totalTampil = data.total;
                        else if(currentFilter == 'pidum') totalTampil = data.pidum;
                        else if(currentFilter == 'pidsus') totalTampil = data.pidsus;
                        else if(currentFilter == 'narkotika') totalTampil = data.narkotika;
                        
                        $('#totalKasus').text(totalTampil);
                        $('#valPidum').text(data.pidum);
                        $('#valPidsus').text(data.pidsus);
                        $('#valNarkotika').text(data.narkotika);
                        $('#valPerdata').text(data.perdata);
                        
                        $('#linkDetail').attr('href', 'kasus.php?desa=' + data.id_desa).show();
                    } else {
                        $('#namaKecamatan').text('Data Belum Ada');
                        $('#valPenduduk').text('0 Jiwa');
                        $('#totalKasus').text('0');
                        $('.row-detail b').text('0');
                        $('#linkDetail').hide();
                    }
                }
            });
        }

        // Load Data
        function initMap() {
            // 1. Ambil Data DB (API Sebaran)
            $.getJSON('admin/api_sebaran.php', function(dbData) {
                dataDesaDB = dbData;

                // 2. Ambil File GeoJSON
                $.getJSON('assets/geo/75.01_kelurahan.geojson', function(geoData) {
                    if(geoJsonLayer) map.removeLayer(geoJsonLayer);
                    
                    geoJsonLayer = L.geoJson(geoData, {
                        style: style,
                        onEachFeature: onEachFeature
                    }).addTo(map);

                    map.fitBounds(geoJsonLayer.getBounds());
                    
                    // Generate Legenda setelah data kecamatan termuat
                    buatLegenda();
                }).fail(function() {
                    alert("Gagal memuat peta GeoJSON!");
                });
            });
        }

        // Update Filter (Hanya memperbarui angka di popup jika sedang dibuka, warna peta tetap)
        window.updateMap = function() {
            var radios = document.getElementsByName('jenis');
            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) { currentFilter = radios[i].value; break; }
            }
            // Kita tidak perlu me-reset style layer jika pewarnaan berdasarkan Kecamatan (statis)
            // Kecuali Anda ingin highlight kasus. Di sini saya biarkan statis sesuai request.
        }

        // Buat Legenda Dinamis
        function buatLegenda() {
            var html = '';
            // Loop object warnaKecamatan
            for (var kec in warnaKecamatan) {
                if (warnaKecamatan.hasOwnProperty(kec)) {
                    html += '<div class="legend-item">' +
                            '<i class="color-box" style="background:' + warnaKecamatan[kec] + '"></i> ' +
                            kec + '</div>';
                }
            }
            document.getElementById('legend-content').innerHTML = html;
        }

        initMap();
    </script>
</body>
</html>