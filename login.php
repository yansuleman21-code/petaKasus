<?php
session_start();
include 'admin/koneksi.php';

// Jika sudah login, redirect ke dashboard/admin page
if (isset($_SESSION['admin'])) {
    header("Location: admin/dashboard.php");
    exit;
}

$error = '';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Ambil data admin dari database
    $query = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '$username'");
    if (mysqli_num_rows($query) > 0) {
        $admin = mysqli_fetch_assoc($query);

        // Verifikasi password
        if (password_verify($password, $admin['password'])) {
            // Login berhasil, simpan session
            $_SESSION['admin'] = $admin['username'];
            header("Location: admin/dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Peta Kasus Kabupaten Gorontalo</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #0d6efd, #6c757d);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: #ffffff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-card h3 {
            color: #0d6efd;
            font-weight: 700;
        }

        .login-card .btn-primary {
            background-color: #0d6efd;
            border: none;
        }

        .login-card .btn-primary:hover {
            background-color: #0b5ed7;
        }

        .login-card .alert {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="text-center mb-4"><i class="bi bi-shield-lock-fill"></i> Login Admin</h3>

    <?php if ($error != '') { ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php } ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right"></i> Login
        </button>
    </form>
</div>

<!-- BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
