<?php
// Cek apakah session sudah dimulai atau belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - Peta Kasus</title>
    <link href="../assets/sbadmin/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../assets/css/admin_style.css" rel="stylesheet">
</head>
<body id="page-top">

<div id="wrapper">
