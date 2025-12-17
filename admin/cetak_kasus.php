<?php
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Data Perkara - Kejaksaan Negeri</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }

        .header h2,
        .header h3,
        .header p {
            margin: 2px 0;
        }

        .header img {
            width: 80px;
            position: absolute;
            left: 20px;
            top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .ttd-area {
            float: right;
            margin-top: 50px;
            text-align: center;
            width: 200px;
        }

        .ttd-area p {
            margin-bottom: 60px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .btn-print {
            padding: 10px 20px;
            background: #333;
            color: #fff;
            text-decoration: none;
            position: fixed;
            top: 20px;
            right: 20px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body onload="window.print()">

    <a href="#" onclick="window.print()" class="btn-print no-print">Cetak Halaman</a>

    <div class="header">
        <!-- Logo Kejaksaan (Placeholder) -->
        <img src="../assets/img/logo.png" alt="Logo">
        <h2>KEJAKSAAN NEGERI KABUPATEN GORONTALO</h2>
        <h3>LAPORAN DATA PERKARA TIPIKOR & PIDUM</h3>
        <p>Alamat: Jl. Ahmad A. Wahab, Limboto, Gorontalo</p>
    </div>

    <p>Dicetak Tanggal: <?= date('d F Y') ?></p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Judul Perkara</th>
                <th width="15%">Kategori</th>
                <th width="15%">Tanggal Kejadian</th>
                <th width="15%">Lokasi</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // Urutkan berdasarkan tanggal terbaru
            $query = mysqli_query($koneksi, "SELECT kasus.*, desa.nama_desa 
                                             FROM kasus 
                                             LEFT JOIN desa ON kasus.desa_kasus = desa.id_desa 
                                             ORDER BY kasus.tanggal DESC");

            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                    // Penyesuaian warna status sederhana untuk cetak (biasanya hitam putih, jadi teks saja)
                    ?>
                    <tr>
                        <td style="text-align:center;"><?= $no++ ?></td>
                        <td>
                            <b><?= htmlspecialchars($row['judul_kasus']) ?></b><br>
                            <small><i>Pelapor/Instansi: <?= $row['instansi'] ?></i></small>
                        </td>
                        <td><?= $row['jenis_kasus'] ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= $row['nama_desa'] ?></td>
                        <td style="text-align:center;"><?= $row['status_kasus'] ?></td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center'>Tidak ada data perkara.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="ttd-area">
        <p>Mengetahui,<br>KEPALA SEKSI INTELIJEN</p>
        <br>
        <b>.............................................</b><br>
        NIP. .....................................
    </div>

</body>

</html>