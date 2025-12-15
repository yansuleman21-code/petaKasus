<?php 
include 'includes/header_admin.php'; 
include 'includes/sidebar.php'; 
include 'koneksi.php'; 

// Data untuk Grafik Kategori
$kategori_query = mysqli_query($koneksi, "SELECT jenis_kasus, COUNT(*) as jumlah FROM kasus GROUP BY jenis_kasus");
$labels = [];
$data_chart = [];
while($k = mysqli_fetch_assoc($kategori_query)){
    $labels[] = $k['jenis_kasus'];
    $data_chart[] = $k['jumlah'];
}

// Data untuk Peta Sebaran (Mengambil semua kasus yang punya koordinat)
$map_query = mysqli_query($koneksi, "SELECT kasus.*, desa.nama_desa FROM kasus JOIN desa ON kasus.desa_kasus = desa.id_desa WHERE kasus.latitude != ''");
$markers = [];
while($m = mysqli_fetch_assoc($map_query)){
    $markers[] = $m;
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-chart-line"></i> Dashboard Intelijen Kejaksaan</h1>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Perkara Masuk</div>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Perkara Dalam Proses</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Perkara Selesai (Inkracht)</div>
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. KONFIGURASI PETA (LEAFLET)
    // Koordinat Default Kabupaten Gorontalo
    var map = L.map('mapDashboard').setView([0.626, 122.986], 10); 
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // Data Marker dari PHP
    var markersData = <?php echo json_encode($markers); ?>;

    markersData.forEach(function(item) {
        var color = 'blue';
        if(item.jenis_kasus == 'Narkotika') color = 'red';
        else if(item.jenis_kasus == 'Korupsi') color = 'orange';

        // Custom warna marker bisa dikembangkan dengan icon library, ini default marker
        L.marker([item.latitude, item.longitude])
         .addTo(map)
         .bindPopup(`<b>${item.judul_kasus}</b><br>Desa: ${item.nama_desa}<br>Status: ${item.status_kasus}`);
    });

    // 2. KONFIGURASI CHART (CHART.JS)
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
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          caretPadding: 10,
        },
        legend: {
          display: true,
          position: 'bottom'
        },
      },
    });
</script>

<?php include 'includes/footer_admin.php'; ?>