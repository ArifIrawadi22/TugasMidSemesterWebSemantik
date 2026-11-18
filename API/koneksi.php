<?php
// Helper bersama untuk semua endpoint API.
// Memakai koneksi $db dari config.php (di .gitignore, tidak ikut ke GitHub).
// Jika config.php belum ada, otomatis memakai config.example.php (setting default XAMPP).
$cfg = __DIR__ . '/../config.php';
if (!is_file($cfg)) $cfg = __DIR__ . '/../config.example.php';
require_once $cfg;

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

function json_out($kode, $data)
{
  http_response_code($kode);
  echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  exit;
}
function sukses($data, $kode = 200)
{
  json_out($kode, ['status' => 'success', 'data' => $data]);
}
function galat($kode, $pesan)
{
  json_out($kode, ['status' => 'error', 'message' => $pesan]);
}
// Batasi method yang diizinkan, selain itu balas 405.
function izinkan(array $methods)
{
  $m = $_SERVER['REQUEST_METHOD'];
  if ($m === 'OPTIONS') { http_response_code(204); exit; }
  if (!in_array($m, $methods)) {
    header('Allow: ' . implode(', ', $methods));
    galat(405, 'Method ' . $m . ' tidak diizinkan. Gunakan: ' . implode(', ', $methods));
  }
}
function rapikan_produk($p)
{
  return [
    'id_produk'   => (int)$p['id_produk'],
    'nama_produk' => $p['nama_produk'],
    'deskripsi'   => $p['deskripsi'],
    'harga'       => (int)$p['harga'],
    'stok'        => (int)$p['stok'],
    'gambar'      => $p['gambar'] ?: null,
    'kategori'    => ['id_kategori' => (int)$p['id_kategori'], 'nama_kategori' => $p['nama_kategori']],
    'penjual'     => ['id_penjual' => (int)$p['id_penjual'], 'nama_toko' => $p['nama_toko']],
  ];
}
