<?php
include 'koneksi.php';

// Header untuk membuat browser mendownload file excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Kasus_Kejaksaan_".date('Y-m-d').".xls");
?>

<center>
    <h3>DATA PERKARA KEJAKSAAN NEGERI KAB. GORONTALO</h3>
    <p>Per Tanggal: <?php echo date("d-m-Y"); ?></p>
</center>

<table border="1">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>No</th>
            <th>Judul Kasus/Perkara</th>
            <th>No. Perkara</th>
            <th>Jenis</th>
            <th>Pasal Dakwaan</th>
            <th>Desa (Locus)</th>
            <th>Tanggal Kejadian (Tempus)</th>
            <th>Jaksa Penuntut (JPU)</th>
            <th>Status</th>
            <th>Deskripsi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        // Query join desa
        $query = mysqli_query($koneksi, "SELECT kasus.*, desa.nama_desa FROM kasus LEFT JOIN desa ON kasus.desa_kasus = desa.id_desa ORDER BY tanggal DESC");
        while($row = mysqli_fetch_assoc($query)){
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $row['judul_kasus'] ?></td>
            <td><?= isset($row['no_perkara']) ? $row['no_perkara'] : '-' ?></td>
            <td><?= $row['jenis_kasus'] ?></td>
            <td><?= isset($row['pasal_dakwaan']) ? $row['pasal_dakwaan'] : '-' ?></td>
            <td><?= $row['nama_desa'] ?></td>
            <td><?= $row['tanggal'] ?></td>
            <td><?= isset($row['nama_jaksa']) ? $row['nama_jaksa'] : '-' ?></td>
            <td><?= $row['status_kasus'] ?></td>
            <td><?= $row['deskripsi'] ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>