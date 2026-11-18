<?php require_once 'config.example.php';
require_once 'includes/fungsi.php';
[$items, $total] = item_keranjang($db);
$err = '';
$ok = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $items) {
  $nama = trim($_POST['nama']);
  $tel = trim($_POST['telepon']);
  $email = trim($_POST['email']);
  $alamat = trim($_POST['alamat']);
  if ($nama === '' || $tel === '' || $alamat === '') {
    $err = 'Nama, telepon, dan alamat wajib diisi.';
  } else {
    $db->begin_transaction();
    $gagal = false;   // harga & total dihitung dari database
    $st = $db->prepare("INSERT INTO pembeli(nama,email,telepon,alamat) VALUES(?,?,?,?)");
    $st->bind_param('ssss', $nama, $email, $tel, $alamat);
    $st->execute();
    $idb = $db->insert_id;
    $st = $db->prepare("INSERT INTO pesanan(id_pembeli,total) VALUES(?,?)");
    $st->bind_param('id', $idb, $total);
    $st->execute();
    $idp = $db->insert_id;
    foreach ($items as $it) {
      $s1 = $db->prepare("INSERT INTO detail_pesanan(id_pesanan,id_produk,jumlah,harga_satuan) VALUES(?,?,?,?)");
      $s1->bind_param('iiid', $idp, $it['id_produk'], $it['jumlah'], $it['harga']);
      $s1->execute();
      $s2 = $db->prepare("UPDATE produk SET stok=stok-? WHERE id_produk=? AND stok>=?");
      $s2->bind_param('iii', $it['jumlah'], $it['id_produk'], $it['jumlah']);
      $s2->execute();
      if ($s2->affected_rows === 0) {
        $gagal = true;
        break;
      }
    }
    if ($gagal) {
      $db->rollback();
      $err = 'Pesanan gagal: stok tidak cukup. Silakan cek keranjang.';
    } else {
      $db->commit();
      $_SESSION['cart'] = [];
      $ok = $idp;
    }
  }
}
if (!$items && !$ok) {
  header('Location: keranjang.php');
  exit;
}
include 'includes/header.php';
if ($ok): ?><div class="box p-5 text-center fade-in"><i class="bi bi-check-circle-fill text-success display-3"></i>
    <h3 class="mt-2">Pesanan Berhasil!</h3>
    <p>Nomor pesanan Anda <b>#<?= $ok ?></b>. Total <b><?= rp($total) ?></b>.</p>
    <p class="text-muted">Pesanan sudah masuk ke admin. Lihat statusnya di menu Riwayat memakai nomor telepon Anda.</p>
    <a href="riwayat.php?kontak=<?= urlencode($tel) ?>" class="btn btn-grad">Lihat Riwayat</a> <a href="index.php" class="btn btn-outline-secondary">Belanja Lagi</a>
  </div>
<?php else: ?><h3 class="fw-bold mb-3">Checkout</h3><?php if ($err) echo '<div class="alert alert-danger">' . e($err) . '</div>'; ?>
  <div class="row g-3">
    <div class="col-lg-7">
      <form method="post" class="box p-4">
        <h5 class="mb-3">Data Pembeli</h5>
        <div class="mb-2"><label class="form-label">Nama lengkap</label><input name="nama" class="form-control" required value="<?= e($_POST['nama'] ?? '') ?>"></div>
        <div class="row">
          <div class="col-md-6 mb-2"><label class="form-label">Telepon</label><input name="telepon" class="form-control" required value="<?= e($_POST['telepon'] ?? '') ?>"></div>
          <div class="col-md-6 mb-2"><label class="form-label">Email (opsional)</label><input type="email" name="email" class="form-control" value="<?= e($_POST['email'] ?? '') ?>"></div>
        </div>
        <div class="mb-3"><label class="form-label">Alamat pengiriman</label><textarea name="alamat" class="form-control" rows="3" required><?= e($_POST['alamat'] ?? '') ?></textarea></div>
        <button class="btn btn-grad btn-lg w-100">Buat Pesanan</button>
      </form>
    </div>
    <div class="col-lg-5">
      <div class="box p-4">
        <h5 class="mb-3">Ringkasan Pesanan</h5>
        <?php foreach ($items as $it) echo '<div class="d-flex justify-content-between small mb-1"><span>' . e($it['nama_produk']) . ' x' . $it['jumlah'] . '</span><span>' . rp($it['sub']) . '</span></div>'; ?>
        <hr>
        <div class="d-flex justify-content-between fw-bold"><span>Total</span><span><?= rp($total) ?></span></div>
      </div>
    </div>
  </div>
<?php endif;
include 'includes/footer.php'; ?>