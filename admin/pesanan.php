<?php $base = '../'; include '../includes/header.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$ST = ['baru', 'diproses', 'selesai'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['status'], $ST)) {   // ubah status pesanan
  $st = $db->prepare("UPDATE pesanan SET status=? WHERE id_pesanan=?"); $i = (int)$_POST['id']; $st->bind_param('si', $_POST['status'], $i); $st->execute();
  header('Location: pesanan.php?' . http_build_query(['q' => $_POST['q'] ?? '', 'status' => $_POST['f'] ?? ''])); exit;
}
$q = trim($_GET['q'] ?? ''); $f = in_array($_GET['status'] ?? '', $ST) ? $_GET['status'] : ''; $like = "%$q%";
$sql = "SELECT ps.*, b.nama, b.email, b.telepon, b.alamat FROM pesanan ps JOIN pembeli b USING(id_pembeli) WHERE (b.nama LIKE ? OR b.telepon LIKE ? OR b.email LIKE ?)" . ($f ? " AND ps.status=?" : "") . " ORDER BY ps.id_pesanan DESC";
$st = $db->prepare($sql);
if ($f) $st->bind_param('ssss', $like, $like, $like, $f); else $st->bind_param('sss', $like, $like, $like);
$st->execute(); $res = $st->get_result(); ?>
<h3 class="fw-bold mb-3">Dashboard Admin</h3><?php include '../includes/admin_nav.php'; ?>
<form class="row g-2 mb-3"><div class="col-md-6"><input name="q" class="form-control" placeholder="Cari nama / telepon / email pembeli" value="<?= e($q) ?>"></div>
 <div class="col-md-3"><select name="status" class="form-select"><option value="">Semua status</option><?php foreach ($ST as $s) echo '<option' . ($f == $s ? ' selected' : '') . '>' . $s . '</option>'; ?></select></div>
 <div class="col-md-3"><button class="btn btn-grad w-100">Filter</button></div></form>
<?php if ($res->num_rows == 0) echo '<div class="alert alert-info">Belum ada pesanan.</div>';
while ($o = $res->fetch_assoc()): ?>
<div class="box p-3 mb-3"><div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
 <div><b>Pesanan #<?= $o['id_pesanan'] ?></b> <?= badge_status($o['status']) ?><div class="small text-muted"><?= $o['tanggal'] ?></div></div>
 <div class="text-end fw-bold"><?= rp($o['total']) ?></div>
 <form method="post" class="d-flex gap-1"><input type="hidden" name="id" value="<?= $o['id_pesanan'] ?>"><input type="hidden" name="q" value="<?= e($q) ?>"><input type="hidden" name="f" value="<?= e($f) ?>">
  <select name="status" class="form-select form-select-sm"><?php foreach ($ST as $s) echo '<option' . ($o['status'] == $s ? ' selected' : '') . '>' . $s . '</option>'; ?></select><button class="btn btn-sm btn-grad">Simpan</button></form></div>
 <hr class="my-2"><div class="row small"><div class="col-md-5"><b><?= e($o['nama']) ?></b><br><?= e($o['telepon']) ?> &middot; <?= e($o['email']) ?><br><?= e($o['alamat']) ?></div>
 <div class="col-md-7"><?php $d = detail_pesanan($db, $o['id_pesanan']); while ($r = $d->fetch_assoc()) echo '<div class="d-flex justify-content-between"><span>' . e($r['nama_produk']) . ' x' . $r['jumlah'] . '</span><span>' . rp($r['harga_satuan'] * $r['jumlah']) . '</span></div>'; ?></div></div></div>
<?php endwhile; include '../includes/footer.php'; ?>
