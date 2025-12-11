<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-balance-scale"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Admin Peta</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Manajemen Data
    </div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseKasus"
            aria-expanded="true" aria-controls="collapseKasus">
            <i class="fas fa-fw fa-folder"></i>
            <span>Data Kasus</span>
        </a>
        <div id="collapseKasus" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="../kasus.php" target="_blank">Lihat Semua Kasus</a>
                <a class="collapse-item" href="tambah_kasus.php">Tambah Kasus Baru</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="../desa.php" target="_blank">
            <i class="fas fa-fw fa-map"></i>
            <span>Data Desa</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <li class="nav-item">
        <a class="nav-link" href="logout.php" onclick="return confirm('Apakah anda yakin ingin keluar?');">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Logout</span></a>
    </li>

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
        ```

**PENTING:**
Agar sidebar ini muncul, Anda harus mengedit file `admin/index.php` dan `admin/tambah_kasus.php`. Ubah baris paling atas menjadi seperti ini:

```php
<?php 
include 'includes/header_admin.php'; 
include 'includes/sidebar.php'; // Tambahkan baris ini
include 'koneksi.php'; 
?>