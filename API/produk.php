<?php
// GET /api/produk.php                      -> semua produk
// GET /api/produk.php?q=kopi&kategori=3&urut=murah  -> pencarian/filter/urut
// GET /api/produk.php?id=1                 -> detail produk
require_once __DIR__ . '/koneksi.php';
izinkan(['GET', 'OPTIONS']);

$dasar = "SELECT p.*, k.nama_kategori, j.nama_toko FROM produk p
          JOIN kategori k USING(id_kategori) JOIN penjual j USING(id_penjual)";

// ---- detail produk ----
if (isset($_GET['id'])) {
  if (!ctype_digit((string)$_GET['id']) || (int)$_GET['id'] < 1) {
    galat(400, 'Parameter id harus berupa angka positif.');
  }
  $id = (int)$_GET['id'];
  $st = $db->prepare($dasar . " WHERE p.id_produk=?");
  $st->bind_param('i', $id);
  $st->execute();
  $p = $st->get_result()->fetch_assoc();
  if (!$p) galat(404, "Produk dengan id $id tidak ditemukan.");
  sukses(rapikan_produk($p));
}

// ---- daftar produk ----
$q    = '%' . trim($_GET['q'] ?? '') . '%';
$kat  = (int)($_GET['kategori'] ?? 0);
$urut = ['baru' => 'p.id_produk DESC', 'murah' => 'p.harga ASC', 'mahal' => 'p.harga DESC'][$_GET['urut'] ?? 'baru'] ?? 'p.id_produk DESC';
$st = $db->prepare($dasar . " WHERE p.nama_produk LIKE ? AND (?=0 OR p.id_kategori=?) ORDER BY $urut");
$st->bind_param('sii', $q, $kat, $kat);
$st->execute();
$hasil = [];
$res = $st->get_result();
while ($r = $res->fetch_assoc()) $hasil[] = rapikan_produk($r);
sukses(['jumlah' => count($hasil), 'produk' => $hasil]);
