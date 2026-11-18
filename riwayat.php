<?php include 'includes/header.php'; $kontak = trim($_GET['kontak'] ?? ''); $res = null;
if ($kontak !== '') {
  $st = $db->prepare("SELECT ps.*, b.nama FROM pesanan ps JOIN pembeli b USING(id_pembeli) WHERE b.telepon=? OR b.email=? ORDER BY ps.id_pesanan DESC");
  $st->bind_param('ss', $kontak, $kontak); $st->execute(); $res = $st->get_result();
} ?>
<h3 class="fw-bold mb-3"><i class="bi bi-clock-history"></i> Riwayat Pembelian</h3>
<form class="input-group mb-4" style="max-width:520px"><input name="kontak" class="form-control" placeholder="Masukkan nomor telepon atau email saat checkout" value="<?= e($kontak) ?>" required><button class="btn btn-grad">Cari</button></form>
<?php if ($res && $res->num_rows == 0) echo '<div class="alert alert-warning">Belum ada pesanan untuk kontak tersebut.</div>';
while ($res && ($o = $res->fetch_assoc())): ?>
<div class="box p-3 mb-3 fade-in"><div class="d-flex justify-content-between"><b>Pesanan #<?= $o['id_pesanan'] ?></b><?= badge_status($o['status']) ?></div>
 <div class="text-muted small mb-2"><?= $o['tanggal'] ?> &middot; <?= e($o['nama']) ?></div>
 <?php $d = detail_pesanan($db, $o['id_pesanan']); while ($r = $d->fetch_assoc()) echo '<div class="d-flex justify-content-between small"><span>' . e($r['nama_produk']) . ' x' . $r['jumlah'] . '</span><span>' . rp($r['harga_satuan'] * $r['jumlah']) . '</span></div>'; ?>
 <hr class="my-2"><div class="text-end fw-bold"><?= rp($o['total']) ?></div></div>
<?php endwhile; include 'includes/footer.php'; ?>
