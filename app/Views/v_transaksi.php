<!-- File: app/Views/v_transaksi.php -->

<h1>Data Transaksi</h1>

<table border="1" width="100%" cellpadding="5">
    <tr>
        <th>No</th>
        <th>Username</th>
        <th>Total Harga</th>
        <th>Alamat Ongkir</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php foreach ($transaksi as $index => $trx) : ?>
        <tr>
            <td align="center"><?= $index + 1 ?></td>
            <td><?= $trx['username'] ?></td>
            <td align="right"><?= "Rp " . number_format($trx['total_harga'], 2, ",", ".") ?></td>
            <td><?= $trx['alamat_ongkir'] ?></td>
            <td align="center"><?= $trx['status'] ?></td>
            <td align="center">
                <form action="<?= base_url('transaksi/update_status/' . $trx['id']) ?>" method="post">
                    <select name="status">
                        <option value="1" <?= ($trx['status'] == 1) ? 'selected' : '' ?>>Belum Diproses</option>
                        <option value="2" <?= ($trx['status'] == 2) ? 'selected' : '' ?>>Sedang Diproses</option>
                        <option value="3" <?= ($trx['status'] == 3) ? 'selected' : '' ?>>Selesai</option>
                    </select>
                    <button type="submit">Ubah Status</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

Downloaded on <?= date("Y-m-d H:i:s") ?>
