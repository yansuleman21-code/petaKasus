<?php
include 'includes/header_admin.php';
include 'includes/sidebar.php';
include 'koneksi.php';

// Data untuk Grafik Kategori
$kategori_query = mysqli_query($koneksi, "SELECT jenis_kasus, COUNT(*) as jumlah FROM kasus GROUP BY jenis_kasus");
$labels = [];
$data_chart = [];
while ($k = mysqli_fetch_assoc($kategori_query)) {
    $labels[] = $k['jenis_kasus'];
    $data_chart[] = $k['jumlah'];
}

// Data untuk Peta Sebaran (Mengambil semua kasus yang punya koordinat)
$map_query = mysqli_query($koneksi, "SELECT kasus.*, desa.nama_desa FROM kasus JOIN desa ON kasus.desa_kasus = desa.id_desa WHERE kasus.latitude != ''");
$markers = [];
while ($m = mysqli_fetch_assoc($map_query)) {
    $markers[] = $m;
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container-fluid">
    <style>
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
    </style>

    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-chart-line"></i> Dashboard Intelijen Kejaksaan</h1>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Perkara Masuk
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM kasus")); ?>
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-gavel fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Perkara Dalam Proses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM kasus WHERE status_kasus='Dalam Proses'")); ?>
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-spinner fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Perkara Selesai
                                (Inkracht)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM kasus WHERE status_kasus='Selesai'")); ?>
                            </div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Tren -->
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Grafik Tren Kasus Bulanan (2025)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 350px;">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Peta Sebaran Kriminalitas</h6>
            </div>
            <div class="card-body">
                <div id="mapDashboard" style="height: 400px; width: 100%; border-radius: 5px;"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Statistik Jenis Perkara</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<?php include 'includes/footer_admin.php'; ?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. KONFIGURASI PETA (LEAFLET - CHOROPLETH)
    var map = L.map('mapDashboard').setView([0.626, 122.986], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    var geoJsonLayer;
    var dataDesaDB = {};
    var warnaSpesifikDesa = { 'LIMBOTO': '#f1c40f' };

    // FUNGSI WARNA
    function getColor(count) {
        return count > 20 ? '#b92b27' :
            count > 10 ? '#e74c3c' :
                count > 5 ? '#e67e22' :
                    count > 0 ? '#f1c40f' :
                        '#2ecc71';
    }

    // STYLE
    function style(feature) {
        var namaUnik = feature.properties.nama_unik;
        var namaAsli = feature.properties.nm_kelurahan.toUpperCase();
        var fillColor = '#2ecc71';
        var count = 0;
        var dataFound = dataDesaDB[namaUnik] || dataDesaDB[namaAsli];

        if (dataFound) {
            count = parseInt(dataFound.total);
            fillColor = getColor(count);
        }
        if (warnaSpesifikDesa[namaUnik]) { fillColor = warnaSpesifikDesa[namaUnik]; }

        return { fillColor: fillColor, weight: 1, opacity: 1, color: 'white', fillOpacity: 0.85 };
    }

    // INTERAKSI
    function onEachFeature(feature, layer) {
        if (feature.properties && feature.properties.nm_kelurahan) {
            layer.bindTooltip(feature.properties.nm_kelurahan, { permanent: true, direction: "center", className: "label-desa" });
        }
        layer.on({
            mouseover: function (e) { e.target.setStyle({ weight: 2, color: '#333', fillOpacity: 1 }); },
            mouseout: function (e) { geoJsonLayer.resetStyle(e.target); },
            click: function (e) {
                var nama = e.target.feature.properties.nama_unik;
                var namaAsli = e.target.feature.properties.nm_kelurahan.toUpperCase();
                var data = dataDesaDB[nama] || dataDesaDB[namaAsli];

                var content = "<div style='text-align:center;'><b>" + feature.properties.nm_kelurahan + "</b></div><hr style='margin:5px 0;'>";
                if (data) {
                    content += "Total Kasus: <b>" + data.total + "</b><br>Pidum: " + data.pidum + "<br>Pidsus: " + data.pidsus + "<br>Narkotika: " + data.narkotika;
                } else {
                    content += "<i>Belum ada data kasus.</i>";
                }
                L.popup().setLatLng(e.latlng).setContent(content).openOn(map);
            }
        });
    }

    // LOAD DATA
    $.getJSON('../assets/geo/75.01_kelurahan.geojson', function (geoData) {
        // Logika Nama Ganda
        var penghitungNama = {};
        geoData.features.forEach(function (f) {
            var namaAsli = f.properties.nm_kelurahan.toUpperCase();
            if (!penghitungNama[namaAsli]) { penghitungNama[namaAsli] = 0; }
            penghitungNama[namaAsli]++;
            if (penghitungNama[namaAsli] == 1) { f.properties.nama_unik = namaAsli; }
            else { f.properties.nama_unik = namaAsli + " " + (penghitungNama[namaAsli] - 1); }
        });

        $.getJSON('api_sebaran.php', function (dbData) {
            dataDesaDB = dbData;
            geoJsonLayer = L.geoJson(geoData, { style: style, onEachFeature: onEachFeature }).addTo(map);
            map.fitBounds(geoJsonLayer.getBounds());

            // Re-add legend logic if needed or omitted for brevity, but let's correct it properly
            var legend = L.control({ position: 'bottomleft' });
            legend.onAdd = function (map) {
                var div = L.DomUtil.create('div', 'info legend');
                div.style.backgroundColor = "white"; div.style.padding = "5px"; div.style.borderRadius = "5px";
                div.innerHTML = "<small><b>Intensitas</b></small><br>" +
                    "<i style='background:#b92b27; width:10px; height:10px; display:inline-block;'></i> Tinggi<br>" +
                    "<i style='background:#f1c40f; width:10px; height:10px; display:inline-block;'></i> Sedang<br>" +
                    "<i style='background:#2ecc71; width:10px; height:10px; display:inline-block;'></i> Aman";
                return div;
            };
            legend.addTo(map);
        });
    });

    // 3. LOGIKA CHART LINE (TREN BULANAN)
    var monthLabels = <?php
    $indoMonths = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    echo json_encode($indoMonths);
    ?>;

    var trendData = <?php
    $monthlyCounts = array_fill(0, 12, 0);
    $year = date('Y');
    $trend_query = mysqli_query($koneksi, "SELECT MONTH(tanggal) as bulan, COUNT(*) as total FROM kasus WHERE YEAR(tanggal) = '$year' GROUP BY MONTH(tanggal)");
    while ($t = mysqli_fetch_assoc($trend_query)) {
        $idx = $t['bulan'] - 1;
        if (isset($monthlyCounts[$idx])) {
            $monthlyCounts[$idx] = $t['total'];
        }
    }
    echo json_encode($monthlyCounts);
    ?>;

    var ctxTrend = document.getElementById("myAreaChart");
    if (ctxTrend) {
        var myAreaChart = new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: "Jumlah Kasus",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: trendData,
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                scales: {
                    xAxes: [{ time: { unit: 'date' }, gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 7 } }],
                    yAxes: [{ ticks: { maxTicksLimit: 5, padding: 10, callback: function (value) { return value; } }, gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] } }],
                },
                legend: { display: false },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                }
            }
        });
    }

    // KONFIGURASI CHART PIE
    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                data: <?php echo json_encode($data_chart); ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#e74a3b', '#f6c23e'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#be2617', '#dda20a'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: { backgroundColor: "rgb(255,255,255)", bodyFontColor: "#858796", borderColor: '#dddfeb', borderWidth: 1, xPadding: 15, yPadding: 15, displayColors: false, caretPadding: 10, },
            legend: { display: true, position: 'bottom' },
        },
    });
</script>