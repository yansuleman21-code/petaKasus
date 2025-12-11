<?php 
include 'includes/header_admin.php'; 
include 'includes/sidebar.php'; 
include 'koneksi.php'; 
?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Dashboard Admin</h1>

    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Kasus</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $q = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM kasus");
                                echo mysqli_fetch_assoc($q)['jml'];
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Desa Terdaftar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $q = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM desa");
                                echo mysqli_fetch_assoc($q)['jml'];
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
</div>
<?php include 'includes/footer_admin.php'; ?>