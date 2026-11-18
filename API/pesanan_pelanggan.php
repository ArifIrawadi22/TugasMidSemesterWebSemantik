<?php

require_once __DIR__ . '/koneksi.php';
izinkan(['GET', 'OPTIONS']);

$ST = ['baru', 'diproses', 'selesai'];
$q  = '%' . trim($_GET['q'] ?? '') . '%';
$f  = in_array($_GET['status'] ?? '', $ST) ? $_GET['status'] : '';

$sql = "SELECT ps.id_pesanan, ps.tanggal, ps.total, ps.status, b.nama, b.telepon, b.email, b.alamat
        FROM pesanan ps JOIN pembeli b USING(id_pembeli)
        WHERE (b.nama LIKE ? OR b.telepon LIKE ? OR b.email LIKE ?)" . ($f ? " AND ps.status=?" : "") . "
        ORDER BY ps.id_pesanan DESC";
$st = $db->prepare($sql);
if ($f) $st->bind_param('ssss', $q, $q, $q, $f);
else $st->bind_param('sss', $q, $q, $q);
$st->execute();
$res = $st->get_result();

$out = [];
while ($o = $res->fetch_assoc()) {
  $d = $db->prepare("SELECT d.id_produk, p.nama_produk, d.jumlah, d.harga_satuan FROM detail_pesanan d JOIN produk p USING(id_produk) WHERE d.id_pesanan=?");
  $d->bind_param('i', $o['id_pesanan']);
  $d->execute();
  $items = [];
  $r = $d->get_result();
  while ($x = $r->fetch_assoc()) {
    $items[] = [
      'id_produk' => (int)$x['id_produk'],
      'nama_produk' => $x['nama_produk'],
      'jumlah' => (int)$x['jumlah'],
      'harga_satuan' => (int)$x['harga_satuan'],
      'subtotal' => (int)$x['harga_satuan'] * (int)$x['jumlah']
    ];
  }
  $o['id_pesanan'] = (int)$o['id_pesanan'];
  $o['total'] = (int)$o['total'];
  $o['items'] = $items;
  $out[] = $o;
}
sukses(['jumlah' => count($out), 'pesanan' => $out]);
