<?php
mysqli_report(MYSQLI_REPORT_OFF);
$db = new mysqli('localhost', 'root', '', 'marketplace');
if ($db->connect_error) {
  die('Koneksi database gagal: ' . $db->connect_error);
}
$db->set_charset('utf8mb4');
define('ADMIN_PASSWORD', 'admin123');
session_start();
function rp($n)
{
  return 'Rp ' . number_format($n, 0, ',', '.');
}
function e($s)
{
  return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
function gambar($g)
{
  return $g ? e($g) : 'https://placehold.co/400x300?text=Produk';
}
