<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>S.I.P.K - Kejaksaan Negeri Kab. Gorontalo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .bg-kejaksaan { background-color: #067c3b; background-image: linear-gradient(180deg, #067c3b 10%, #045729 100%); background-size: cover; }
        .sidebar-dark .nav-item .nav-link i { color: rgba(255,255,255,0.8); }
    </style>
</head>
<body id="page-top">
<div id="wrapper">