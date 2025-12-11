<?php include 'includes/header_admin.php'; ?>
<?php include 'koneksi.php'; ?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-success fw-bold">Dashboard Admin</h1>

    <div class="row">

        <!-- Total Kasus -->
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm p-4">
                <h1 class="text-success fw-bold">
                    <?php
                    $q = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM kasus");
                    echo mysqli_fetch_assoc($q)['jml'];
                    ?>
                </h1>
                <p>Total Kasus</p>
            </div>
        </div>

        <!-- Total Desa -->
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm p-4">
                <h1 class="text-success fw-bold">
                    <?php
                    $q = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM desa");
                    echo mysqli_fetch_assoc($q)['jml'];
                    ?>
                </h1>
                <p>Desa Terdaftar</p>
            </div>
        </div>

    </div>

</div>

<?php include 'includes/footer_admin.php'; ?>
