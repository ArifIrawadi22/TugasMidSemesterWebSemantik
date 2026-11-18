<?php $base = '../'; include '../includes/header.php';
if (empty($_SESSION['admin'])) { header('Location: login.php'); exit; }
$id = (int)($_GET['id'] ?? 0); $p = ['id_penjual'=>'','id_kategori'=>'','nama_produk'=>'','deskripsi'=>'','harga'=>'','stok'=>'','gambar'=>''];
if ($id) { $st = $db->prepare("SELECT * FROM produk WHERE id_produk=?"); $st->bind_param('i', $id); $st->execute(); $p = $st->get_result()->fetch_assoc() ?: $p; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $d = [(int)$_POST['id_penjual'], (int)$_POST['id_kategori'], trim($_POST['nama_produk']), $_POST['deskripsi'], (float)$_POST['harga'], (int)$_POST['stok'], trim($_POST['gambar'])];
  if ($id) { $st = $db->prepare("UPDATE produk SET id_penjual=?,id_kategori=?,nama_produk=?,deskripsi=?,harga=?,stok=?,gambar=? WHERE id_produk=?"); $st->bind_param('iissdisi', ...[...$d, $id]); }   // UPDATE
  else { $st = $db->prepare("INSERT INTO produk(id_penjual,id_kategori,nama_produk,deskripsi,harga,stok,gambar) VALUES(?,?,?,?,?,?,?)"); $st->bind_param('iissdis', ...$d); }  // CREATE
  $st->execute(); header('Location: index.php'); exit;
}
$pj = $db->query("SELECT * FROM penjual"); $kt = $db->query("SELECT * FROM kategori"); ?>
<h3><?= $id ? 'Edit' : 'Tambah' ?> Produk</h3>
<form method="post" class="bg-white p-3 rounded shadow-sm" style="max-width:560px">
 <label>Penjual</label><select name="id_penjual" class="form-select mb-2"><?php while ($r = $pj->fetch_assoc()) echo '<option value="' . $r['id_penjual'] . '"' . ($r['id_penjual'] == $p['id_penjual'] ? ' selected' : '') . '>' . e($r['nama_toko']) . '</option>'; ?></select>
 <label>Kategori</label><select name="id_kategori" class="form-select mb-2"><?php while ($r = $kt->fetch_assoc()) echo '<option value="' . $r['id_kategori'] . '"' . ($r['id_kategori'] == $p['id_kategori'] ? ' selected' : '') . '>' . e($r['nama_kategori']) . '</option>'; ?></select>
 <label>Nama Produk</label><input name="nama_produk" class="form-control mb-2" value="<?= e($p['nama_produk']) ?>" required>
 <label>Deskripsi</label><textarea name="deskripsi" class="form-control mb-2"><?= e($p['deskripsi']) ?></textarea>
 <label>Harga</label><input type="number" name="harga" class="form-control mb-2" value="<?= e($p['harga']) ?>" required>
 <label>Stok</label><input type="number" name="stok" class="form-control mb-2" value="<?= e($p['stok']) ?>" required>
 <label>URL Gambar (opsional)</label><input name="gambar" class="form-control mb-3" value="<?= e($p['gambar']) ?>">
 <button class="btn btn-primary">Simpan</button> <a href="index.php" class="btn btn-outline-secondary">Batal</a></form>
<?php include '../includes/footer.php'; ?>
