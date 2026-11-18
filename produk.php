<?php include 'includes/header.php';
$id = (int)($_GET['id'] ?? 0);
$st = $db->prepare("SELECT p.*, k.nama_kategori, j.nama_toko FROM produk p JOIN kategori k USING(id_kategori) JOIN penjual j USING(id_penjual) WHERE id_produk=?");
$st->bind_param('i', $id); $st->execute(); $p = $st->get_result()->fetch_assoc();
if (!$p) { echo '<div class="alert alert-warning">Produk tidak ditemukan.</div>'; include 'includes/footer.php'; exit; } ?>
<nav class="small mb-3"><a href="index.php">Beranda</a> / <?= e($p['nama_kategori']) ?> / <?= e($p['nama_produk']) ?></nav>
<div class="box p-4 fade-in"><div class="row g-4"><div class="col-md-5"><img src="<?= gambar($p['gambar']) ?>" class="img-fluid rounded-4 w-100" alt=""></div>
<div class="col-md-7"><span class="badge bg-secondary"><?= e($p['nama_kategori']) ?></span>
 <h2 class="mt-2 fw-bold"><?= e($p['nama_produk']) ?></h2><h3 style="color:var(--c1)"><?= rp($p['harga']) ?></h3>
 <p class="text-muted mb-1"><i class="bi bi-shop"></i> <?= e($p['nama_toko']) ?> &middot; Stok: <b><?= $p['stok'] ?></b></p>
 <p class="mt-3"><?= nl2br(e($p['deskripsi'])) ?></p>
 <?php if ($p['stok'] > 0): ?><div class="d-flex gap-2 align-items-center">
  <div class="input-group" style="width:140px"><button type="button" class="btn btn-outline-secondary" data-step="-1">&minus;</button>
   <input id="qty" class="form-control text-center" value="1" min="1" max="<?= $p['stok'] ?>" readonly><button type="button" class="btn btn-outline-secondary" data-step="1">+</button></div>
  <button data-add="<?= $p['id_produk'] ?>" class="btn btn-grad btn-lg"><i class="bi bi-cart-plus"></i> Tambah ke Keranjang</button></div>
 <?php else: ?><div class="alert alert-danger">Stok habis</div><?php endif; ?>
</div></div></div>
<?php include 'includes/footer.php'; ?>
