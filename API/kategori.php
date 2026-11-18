<?php
// GET /api/kategori.php  -> daftar kategori (beserta jumlah produk)
require_once __DIR__ . '/koneksi.php';
izinkan(['GET', 'OPTIONS']);

$res = $db->query("SELECT k.id_kategori, k.nama_kategori, COUNT(p.id_produk) AS jumlah_produk
                   FROM kategori k LEFT JOIN produk p USING(id_kategori)
                   GROUP BY k.id_kategori, k.nama_kategori ORDER BY k.id_kategori");
if (!$res) galat(500, 'Gagal membaca data kategori.');
$out = [];
while ($r = $res->fetch_assoc()) {
  $out[] = ['id_kategori' => (int)$r['id_kategori'], 'nama_kategori' => $r['nama_kategori'], 'jumlah_produk' => (int)$r['jumlah_produk']];
}
sukses(['jumlah' => count($out), 'kategori' => $out]);
