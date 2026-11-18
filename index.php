<?php include 'includes/header.php';
$q = '%' . ($_GET['q'] ?? '') . '%'; $kat = (int)($_GET['kat'] ?? 0);
$urut = ['baru' => 'p.id_produk DESC', 'murah' => 'p.harga ASC', 'mahal' => 'p.harga DESC'][$_GET['urut'] ?? 'baru'] ?? 'p.id_produk DESC';
$st = $db->prepare("SELECT p.*, k.nama_kategori, j.nama_toko FROM produk p JOIN kategori k USING(id_kategori) JOIN penjual j USING(id_penjual) WHERE p.nama_produk LIKE ? AND (?=0 OR p.id_kategori=?) ORDER BY $urut");
$st->bind_param('sii', $q, $kat, $kat); $st->execute(); $res = $st->get_result();
$kts = $db->query("SELECT * FROM kategori"); ?>
<form class="hero mb-4 fade-in" method="get">
 <h2 class="fw-bold">Belanja mudah, harga bersahabat</h2><p class="mb-3">Temukan produk dari berbagai penjual dalam satu tempat.</p>
 <input type="hidden" name="kat" value="<?= $kat ?>">
 <div class="input-group input-group-lg"><input name="q" class="form-control" placeholder="Cari produk..." value="<?= e($_GET['q'] ?? '') ?>">
 <select name="urut" class="form-select" style="max-width:170px" onchange="this.form.submit()">
  <?php foreach (['baru' => 'Terbaru', 'murah' => 'Termurah', 'mahal' => 'Termahal'] as $k => $v) echo '<option value="' . $k . '"' . (($_GET['urut'] ?? 'baru') == $k ? ' selected' : '') . '>' . $v . '</option>'; ?></select>
 <button class="btn btn-dark"><i class="bi bi-search"></i></button></div></form>
<div class="mb-3"><a class="chip <?= !$kat ? 'active' : '' ?>" href="index.php">Semua</a>
<?php while ($k = $kts->fetch_assoc()) echo '<a class="chip ' . ($kat == $k['id_kategori'] ? 'active' : '') . '" href="?kat=' . $k['id_kategori'] . '">' . e($k['nama_kategori']) . '</a>'; ?></div>
<div class="row">
<?php if ($res->num_rows == 0) echo '<div class="alert alert-warning">Produk tidak ditemukan.</div>';
while ($p = $res->fetch_assoc()): ?>
 <div class="col-6 col-md-4 col-lg-3 mb-4 fade-in"><div class="card card-produk h-100 shadow-sm">
  <div class="position-relative"><img src="<?= gambar($p['gambar']) ?>" class="card-img-top" alt="">
  <?php if ($p['stok'] <= 0) echo '<span class="badge bg-danger position-absolute top-0 end-0 m-2">Habis</span>';
  elseif ($p['stok'] <= 5) echo '<span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">Sisa ' . $p['stok'] . '</span>'; ?></div>
  <div class="card-body"><small class="text-muted"><?= e($p['nama_kategori']) ?> &middot; <?= e($p['nama_toko']) ?></small>
   <h6 class="mt-1 fw-semibold"><?= e($p['nama_produk']) ?></h6><div class="fw-bold" style="color:var(--c1)"><?= rp($p['harga']) ?></div></div>
  <div class="card-footer bg-white border-0 d-flex gap-2">
   <a href="produk.php?id=<?= $p['id_produk'] ?>" class="btn btn-outline-secondary btn-sm flex-fill">Detail</a>
   <button data-add="<?= $p['id_produk'] ?>" class="btn btn-grad btn-sm flex-fill" <?= $p['stok'] <= 0 ? 'disabled' : '' ?>><i class="bi bi-cart-plus"></i> Tambah</button></div>
 </div></div>
<?php endwhile; ?></div>
<?php include 'includes/footer.php'; ?>
