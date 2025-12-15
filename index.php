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

        /* Filter Box */
        .filter-box {
            position: absolute; top: 20px; left: 20px; width: 260px;
            background: white; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            border-radius: 5px; overflow: hidden;
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

        /* Info Popup */
        .info-box {
            position: absolute; top: 20px; right: 20px; width: 300px;
            background: white; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            display: none; border-radius: 5px; overflow: hidden;
        }
        .info-header { padding: 15px; border-bottom: 1px solid #eee; background: #fff; }
        .info-title { font-size: 12px; color: #666; margin-bottom: 5px; }
        .info-region { font-size: 18px; font-weight: bold; color: #000; text-transform: uppercase; }
        
        .info-body { padding: 15px; background: #f9f9f9; }
        .stat-big { font-size: 32px; font-weight: bold; color: #b92b27; margin-bottom: 5px; }
        .stat-desc { font-size: 12px; color: #666; }
        .stat-detail { margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 10px; }
        .row-detail { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px; }
        
        /* Legend Sederhana */
        .simple-legend {
            position: absolute; bottom: 30px; left: 20px;
            background: white; padding: 10px; z-index: 1000;
            box-shadow: 0 1px 5px rgba(0,0,0,0.2); font-size: 11px;
            border-radius: 5px;
        }
        .legend-item { display: flex; align-items: center; margin-bottom: 5px; }
        .color-box { width: 15px; height: 15px; margin-right: 8px; display: inline-block; border-radius:3px; }

        .nav-btn { position: absolute; bottom: 30px; right: 20px; z-index: 1000; }
        .btn-login {
            background: #333; color: white; padding: 10px 20px;
            text-decoration: none; font-size: 14px; font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3); display: inline-block;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <div id="map"></div>

    <div class="filter-box">
        <div class="filter-header"><i class="fas fa-map"></i> Peta Sebaran</div>
        <div class="filter-content">
            <div style="margin-bottom: 10px; font-size:12px; color:#666; line-height:1.4;">
                Peta sebaran kasus per Desa.
            </div>
            <div>
                <span class="filter-title">Filter Data</span>
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
            <div class="stat-desc">Total Kasus Terdata</div>
            
            <div class="stat-detail">
                <div class="row-detail"><span>Pidana Umum</span> <b id="valPidum">0</b></div>
                <div class="row-detail"><span>Pidana Khusus</span> <b id="valPidsus">0</b></div>
                <div class="row-detail"><span>Narkotika</span> <b id="valNarkotika">0</b></div>
                <div class="row-detail"><span>Perdata</span> <b id="valPerdata">0</b></div>
            </div>
            <div class="mt-3 text-center" style="margin-top:15px;">
                <a href="#" id="linkDetail" class="btn-login" style="font-size:11px; background:#b92b27; width:100%; text-align:center; box-sizing:border-box;">LIHAT DATA LENGKAP</a>
            </div>
        </div>
    </div>

    <div class="simple-legend">
        <div style="font-weight:bold; margin-bottom:5px;">Status Wilayah</div>
        <div class="legend-item"><i class="color-box" style="background:#e74c3c;"></i> Zona Kasus</div>
        <div class="legend-item"><i class="color-box" style="background:#2ecc71;"></i> Zona Aman (0 Kasus)</div>
        <div class="legend-item"><i class="color-box" style="background:#95a5a6;"></i> Data Tidak Tersedia</div>
    </div>

    <div class="nav-btn">
        <a href="lapor.php" class="btn-login" style="background:#28a745; margin-right:10px;">LAPOR!</a>
        <a href="login.php" class="btn-login">LOGIN ADMIN</a>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // --- SETTING WARNA KHUSUS (Opsional) ---
        var warnaSpesifikDesa = {
            'LIMBOTO': '#f1c40f',
            // Gunakan Nama + Angka 1 untuk desa kembar kedua
            'KAYU BULAN 1': '#8e44ad' 
        };

        var map = L.map('map', { zoomControl: false }).setView([0.62, 122.85], 10); 
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        var geoJsonLayer;
        var dataDesaDB = {}; 
        var currentFilter = 'all';

        // 1. STYLE PETA
        function style(feature) {
            // Gunakan 'nama_unik' hasil generate otomatis
            var namaUnik = feature.properties.nama_unik; 
            
            var fillColor = '#95a5a6'; // Default Abu-abu

            // Cek Data Kasus berdasarkan Nama Unik
            if (dataDesaDB[namaUnik]) {
                var jumlah = parseInt(dataDesaDB[namaUnik].total);
                if (jumlah > 0) fillColor = '#e74c3c'; // Merah
                else fillColor = '#2ecc71'; // Hijau
            }

            // Cek Warna Spesifik Override
            if (warnaSpesifikDesa[namaUnik]) {
                fillColor = warnaSpesifikDesa[namaUnik];
            }

            return {
                fillColor: fillColor, weight: 1, opacity: 1, 
                color: 'white', dashArray: '', fillOpacity: 0.8
            };
        }

        // 2. INTERAKSI MOUSE
        function onEachFeature(feature, layer) {
            // Label Peta tetap nama Asli (Tanpa Angka)
            if (feature.properties && feature.properties.nm_kelurahan) {
                layer.bindTooltip(feature.properties.nm_kelurahan, {
                    permanent: true, direction: "center", className: "label-desa"
                });
            }

            layer.on({
                mouseover: function(e) { e.target.setStyle({ weight: 3, color: '#ffff00', fillOpacity: 1 }); },
                mouseout: function(e) { geoJsonLayer.resetStyle(e.target); },
                click: function(e) {
                    // Saat diklik, ambil data berdasarkan NAMA UNIK (yg ada angkanya)
                    var nama = e.target.feature.properties.nama_unik; 
                    var data = dataDesaDB[nama];

                    $('#infoPanel').fadeIn();
                    // Judul di popup tetap nama asli agar user tidak bingung
                    $('#namaWilayah').text(e.target.feature.properties.nm_kelurahan); 
                    
                    // Reset
                    $('#totalKasus').text('0'); $('.stat-detail b').text('0'); $('#linkDetail').hide();

                    if(data) {
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
                    }
                }
            });
        }

        // 3. LOAD DATA (LOGIKA UTAMA)
        function initMap() {
            // Load GeoJSON dulu
            $.getJSON('assets/geo/75.01_kelurahan.geojson', function(geoData) {
                
                // --- LOGIKA PENTING: BEDAKAN NAMA KEMBAR ---
                var penghitungNama = {}; 
                
                // Loop semua wilayah peta
                geoData.features.forEach(function(f) {
                    var namaAsli = f.properties.nm_kelurahan.toUpperCase();
                    
                    if (!penghitungNama[namaAsli]) {
                        penghitungNama[namaAsli] = 0;
                    }
                    penghitungNama[namaAsli]++;

                    // Jika nama muncul pertama kali -> Tetap
                    // Jika muncul kedua kali -> Tambah angka 1, dst.
                    if (penghitungNama[namaAsli] == 1) {
                        f.properties.nama_unik = namaAsli; 
                    } else {
                        f.properties.nama_unik = namaAsli + " " + (penghitungNama[namaAsli] - 1);
                    }
                });
                // -------------------------------------------

                geoJsonLayer = L.geoJson(geoData, {
                    style: style,
                    onEachFeature: onEachFeature
                }).addTo(map);

                map.fitBounds(geoJsonLayer.getBounds());

                // Setelah peta siap, baru ambil data database
                $.getJSON('admin/api_sebaran.php', function(dbData) {
                    dataDesaDB = dbData;
                    // Refresh warna peta
                    geoJsonLayer.eachLayer(function(layer) { geoJsonLayer.resetStyle(layer); });
                });

            }).fail(function() {
                alert("ERROR: File GeoJSON tidak ditemukan.");
            });
        }

        window.updateMap = function() {
            var radios = document.getElementsByName('jenis');
            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) { currentFilter = radios[i].value; break; }
            }
            // Update manual angka popup jika sedang terbuka (optional logic)
        }

        initMap();
    </script>
</body>
</html>