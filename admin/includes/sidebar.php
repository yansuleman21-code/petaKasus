<ul class="navbar-nav bg-gradient-success sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-balance-scale"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SIPK KEJARI</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Perkara & Laporan</div>

    <li class="nav-item">
        <a class="nav-link" href="data_kasus.php">
            <i class="fas fa-fw fa-folder-open"></i>
            <span>Kelola Perkara Baru</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="laporan_masyarakat.php">
            <i class="fas fa-fw fa-bullhorn"></i>
            <span>Laporan Masyarakat</span>
            <span class="badge badge-danger badge-counter">!</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading">Wilayah</div>

    <li class="nav-item">
        <a class="nav-link" href="data_desa.php">
            <i class="fas fa-fw fa-map-marked-alt"></i>
            <span>Kelola Data Desa</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="../index.php" target="_blank">
            <i class="fas fa-fw fa-globe"></i>
            <span>Lihat Website</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <li class="nav-item">
        <a class="nav-link" href="logout.php" onclick="return confirm('Yakin ingin keluar?');">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span></a>
    </li>

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">Administrator</span>
                </li>
            </ul>
        </nav>