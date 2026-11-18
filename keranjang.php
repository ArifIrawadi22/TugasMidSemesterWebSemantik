<?php require_once 'config.example.php';
require_once 'includes/fungsi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $cart = &$_SESSION['cart'];
  $cart = $cart ?? [];
  $id = (int)$_POST['id'];
  $aksi = $_POST['aksi'] ?? '';
  $st = $db->prepare("SELECT stok FROM produk WHERE id_produk=?");
  $st->bind_param('i', $id);
  $st->execute();
  $r = $st->get_result()->fetch_assoc();
  $stok = $r ? (int)$r['stok'] : 0;
  if ($aksi === 'tambah' && $stok > 0) {
    $cart[$id] = min($stok, ($cart[$id] ?? 0) + max(1, (int)$_POST['jumlah']));
  } elseif ($aksi === 'ubah') {
    $j = (int)$_POST['jumlah'];
    if ($j > 0 && $stok > 0) $cart[$id] = min($stok, $j);
    else unset($cart[$id]);
  } elseif ($aksi === 'hapus') {
    unset($cart[$id]);
  }
  if (isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    echo json_encode(['jumlah' => array_sum($cart)]);
    exit;
  }
  header('Location: keranjang.php');
  exit;
}
include 'includes/header.php';
[$items, $total] = item_keranjang($db); ?>
<h3 class="fw-bold mb-3"><i class="bi bi-cart3"></i> Keranjang Belanja</h3>
<?php if (!$items): ?><div class="box p-5 text-center"><i class="bi bi-cart-x display-3 text-muted"></i>
    <p class="mt-2">Keranjang masih kosong.</p><a href="index.php" class="btn btn-grad">Mulai Belanja</a>
  </div>
<?php else: ?><div class="row g-3">
    <div class="col-lg-8">
      <?php foreach ($items as $it): ?>
        <div class="box p-3 mb-3 d-flex gap-3 align-items-center fade-in"><img src="<?= gambar($it['gambar']) ?>" style="width:80px;height:80px;object-fit:cover" class="rounded-3" alt="">
          <div class="flex-grow-1">
            <div class="fw-semibold"><?= e($it['nama_produk']) ?></div>
            <div class="text-muted small"><?= rp($it['harga']) ?></div>
            <div class="fw-bold" style="color:var(--c1)"><?= rp($it['sub']) ?></div>
          </div>
          <form method="post"><input type="hidden" name="aksi" value="ubah"><input type="hidden" name="id" value="<?= $it['id_produk'] ?>">
            <div class="input-group input-group-sm" style="width:120px"><button type="button" class="btn btn-outline-secondary" data-step="-1">&minus;</button>
              <input name="jumlah" data-auto="1" class="form-control text-center" value="<?= $it['jumlah'] ?>" min="0" max="<?= $it['stok'] ?>" readonly><button type="button" class="btn btn-outline-secondary" data-step="1">+</button>
            </div>
          </form>
          <form method="post"><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= $it['id_produk'] ?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="col-lg-4">
      <div class="box p-4">
        <h5 class="fw-bold">Ringkasan</h5>
        <div class="d-flex justify-content-between"><span>Total</span><b class="fs-5"><?= rp($total) ?></b></div>
        <a href="checkout.php" class="btn btn-grad w-100 mt-3">Checkout <i class="bi bi-arrow-right"></i></a><a href="index.php" class="btn btn-outline-secondary w-100 mt-2">Lanjut Belanja</a>
      </div>
    </div>
  </div>
<?php endif;
include 'includes/footer.php'; ?>