<thead>
    <tr>
        <th>No</th>
        <th>Nama Desa</th>
        <th>Koordinat</th>
        <th>Aksi</th>
    </tr>
</thead>
<tbody>
    <?php
    $no = 1;
    // Hapus ORDER BY kecamatan, ganti jadi nama_desa saja
    $query = mysqli_query($koneksi, "SELECT * FROM desa ORDER BY nama_desa ASC");
    while ($d = mysqli_fetch_assoc($query)) {
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= $d['nama_desa'] ?></td>
        <td>
            <?php if($d['latitude']) { ?>
                <a href="http://maps.google.com/maps?q=<?= $d['latitude'] ?>,<?= $d['longitude'] ?>" target="_blank" class="text-primary"><i class="fas fa-map-marker-alt"></i> Cek</a>
            <?php } else { echo "-"; } ?>
        </td>
        <td>
            <a href="hapus_desa.php?id=<?= $d['id_desa'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i></a>
        </td>
    </tr>
    <?php } ?>
</tbody>