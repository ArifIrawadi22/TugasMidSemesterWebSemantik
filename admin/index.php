<?php $base = '../'; include '../includes/header.php';
if (isset($_GET['logout'])) { unset($_SESSION['admin']); header('Location: login.php'); exit; }
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
  $st = $db->prepare("DELETE FROM produk WHERE id_produk=?"); $id = (int)$_POST['hapus']; $st->bind_param('i', $id);
  $pesan = $st->execute() ? 'Produk dihapus.' : 'Produk tidak bisa dihapus karena sudah ada di pesanan.';
}
$s = $db->query("SELECT (SELECT COUNT(*) FROM produk) np,(SELECT COUNT(*) FROM pesanan) nps,(SELECT COALESCE(SUM(total),0) FROM pesanan) omzet,(SELECT COUNT(*) FROM pembeli) nb")->fetch_assoc();
$res = $db->query("SELECT p.*, k.nama_kategori, j.nama_toko FROM produk p JOIN kategori k USING(id_kategori) JOIN penjual j USING(id_penjual) ORDER BY id_produk DESC"); ?>
<h3 class="fw-bold mb-3">Dashboard Admin</h3>
<div class="row g-3 mb-4">
 <div class="col-6 col-lg-3"><div class="card stat p-3" style="background:#6366f1"><small>Produk</small><h3><?= $s['np'] ?></h3></div></div>
 <div class="col-6 col-lg-3"><div class="card stat p-3" style="background:#10b981"><small>Pesanan</small><h3><?= $s['nps'] ?></h3></div></div>
 <div class="col-6 col-lg-3"><div class="card stat p-3" style="background:#f59e0b"><small>Pembeli</small><h3><?= $s['nb'] ?></h3></div></div>
 <div class="col-6 col-lg-3"><div class="card stat p-3" style="background:#ec4899"><small>Total Penjualan</small><h5 class="mt-1"><?= rp($s['omzet']) ?></h5></div></div></div>
<?php include '../includes/admin_nav.php'; ?>
<div class="d-flex justify-content-between mb-2"><h5>Kelola Produk</h5><a href="form.php" class="btn btn-grad"><i class="bi bi-plus-lg"></i> Tambah Produk</a></div>
<?php if ($pesan) echo '<div class="alert alert-info">' . e($pesan) . '</div>'; ?>
<div class="box p-2"><table class="table align-middle mb-0"><thead><tr><th>ID</th><th>Produk</th><th>Kategori</th><th>Penjual</th><th>Harga</th><th>Stok</th><th></th></tr></thead><tbody>
<?php while ($p = $res->fetch_assoc()): ?>
<tr><td><?= $p['id_produk'] ?></td><td><?= e($p['nama_produk']) ?></td><td><?= e($p['nama_kategori']) ?></td><td><?= e($p['nama_toko']) ?></td><td><?= rp($p['harga']) ?></td><td><?= $p['stok'] ?></td>
<td class="d-flex gap-1"><a href="form.php?id=<?= $p['id_produk'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
<form method="post" onsubmit="return confirm('Hapus produk ini?')"><button name="hapus" value="<?= $p['id_produk'] ?>" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button></form></td></tr>
<?php endwhile; ?></tbody></table></div>
<?php include '../includes/footer.php'; ?>
