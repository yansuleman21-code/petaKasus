<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Kasus Kabupaten Gorontalo</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* NAVBAR */
        .navbar-custom {
            background-color: #0d6efd;
        }
        .navbar-custom .nav-link,
        .navbar-custom .navbar-brand {
            color: white !important;
            font-weight: 500;
        }
        .navbar-custom .nav-link:hover {
            color: #d9eaff !important;
        }

        /* HERO */
        .hero-section {
            height: 70vh;
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
                        url('assets/img/bg.jpg') center/cover no-repeat;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom shadow-sm fixed-top">
    <div class="container">

        <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
            <img src="assets/img/logo.png" 
                 alt="Logo Kejaksaan" 
                 style="height: 40px; margin-right: 10px;">
            <span>Peta Kasus Kab. Gorontalo</span>
        </a>

        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse text-center" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">Beranda</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="kasus.php">Data Kasus</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="desa.php">Data Desa</a>
                </li>

                <li class="nav-item">
                    <a class="btn btn-light ms-lg-3" href="login.php">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<!-- Spacer for fixed navbar -->
<div style="height: 80px;"></div>
