<?php
function item_keranjang($db) {
  $out = []; $total = 0;
  foreach (($_SESSION['cart'] ?? []) as $id => $j) {
    $st = $db->prepare("SELECT id_produk,nama_produk,harga,stok,gambar FROM produk WHERE id_produk=?");
    $st->bind_param('i', $id); $st->execute(); $p = $st->get_result()->fetch_assoc();
    if (!$p) { unset($_SESSION['cart'][$id]); continue; }
    $p['jumlah'] = $j; $p['sub'] = $p['harga'] * $j; $total += $p['sub']; $out[] = $p;
  }
  return [$out, $total];
}
function badge_status($s) {
  $m = ['baru' => 'warning text-dark', 'diproses' => 'info', 'selesai' => 'success'];
  return '<span class="badge bg-' . ($m[$s] ?? 'secondary') . '">' . ucfirst($s) . '</span>';
}
function detail_pesanan($db, $idp) {
  $st = $db->prepare("SELECT d.*, p.nama_produk FROM detail_pesanan d JOIN produk p USING(id_produk) WHERE d.id_pesanan=?");
  $st->bind_param('i', $idp); $st->execute(); return $st->get_result();
}
