<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
History Transaksi Pembelian <strong><?= $username ?></strong>
<hr>
<?php if (session()->get('role') === 'admin') : ?>
    <a type="button" class="btn btn-success" href="<?= base_url('transaksi/download') ?>">
        Download Data
    </a>
<?php endif; ?>
<br>
<div class="table-responsive">
    <!-- Table with stripped rows -->
    <table class="table datatable">
        <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Username</th>
                <th scope="col">Total Harga</th>
                <th scope="col">Alamat</th>
                <th scope="col">Ongkir</th>
                <th scope="col">Status</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($buy)) : ?>
                <?php foreach ($buy as $index => $item) : ?>
                    <tr>
                        <th scope="row"><?= $index + 1 ?></th>
                        <td><?= $item['username'] ?></td>
                        <td><?= number_to_currency($item['total_harga'], 'IDR') ?></td>
                        <td><?= $item['alamat'] ?></td>
                        <td><?= number_to_currency($item['ongkir'], 'IDR') ?></td>
                        <td><?= $item['status'] ?></td>
                        <td>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ubahStatusModal-<?= $item['id'] ?>">
                                Ubah Status
                            </button>
                        </td>
                    </tr>
                    
                    <!-- Modal Begin -->
                    <div class="modal fade" id="ubahStatusModal-<?= $item['id'] ?>" tabindex="-1" aria-labelledby="ubahStatusModalLabel-<?= $item['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ubahStatusModalLabel-<?= $item['id'] ?>">Ubah Status Transaksi</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="<?= base_url('transaksi/ubah_status') ?>" method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" name="status" id="status" required>
                                                <option value="0" <?= ($item['status'] == "0") ? "selected" : "" ?>>0</option>
                                                <option value="1" <?= ($item['status'] == "1") ? "selected" : "" ?>>1</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Modal End -->
                    
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <!-- End Table with stripped rows -->
</div>
<?= $this->endSection() ?>
