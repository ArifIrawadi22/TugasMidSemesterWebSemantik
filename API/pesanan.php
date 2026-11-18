<?php
// POST /api/pesanan.php                     -> buat pesanan baru (body JSON)
// GET  /api/pesanan.php?id=7&kontak=0812... -> detail pesanan (harus cocok dengan telepon/email pembeli)
// GET  /api/pesanan.php?kontak=0812...      -> riwayat pesanan pembeli
require_once __DIR__ . '/koneksi.php';
izinkan(['GET', 'POST', 'OPTIONS']);

function detail_item($db, $idp)
{
  $st = $db->prepare("SELECT d.id_produk, p.nama_produk, d.jumlah, d.harga_satuan FROM detail_pesanan d JOIN produk p USING(id_produk) WHERE d.id_pesanan=?");
  $st->bind_param('i', $idp);
  $st->execute();
  $r = $st->get_result();
  $out = [];
  while ($x = $r->fetch_assoc()) {
    $out[] = ['id_produk' => (int)$x['id_produk'], 'nama_produk' => $x['nama_produk'], 'jumlah' => (int)$x['jumlah'],
              'harga_satuan' => (int)$x['harga_satuan'], 'subtotal' => (int)$x['harga_satuan'] * (int)$x['jumlah']];
  }
  return $out;
}

// ======================= GET =======================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $kontak = trim($_GET['kontak'] ?? '');
  if ($kontak === '') galat(400, 'Parameter kontak (telepon atau email pembeli) wajib diisi.');

  $sql = "SELECT ps.id_pesanan, ps.tanggal, ps.total, ps.status, b.nama, b.telepon, b.email, b.alamat
          FROM pesanan ps JOIN pembeli b USING(id_pembeli) WHERE (b.telepon=? OR b.email=?)";
  if (isset($_GET['id'])) {
    if (!ctype_digit((string)$_GET['id']) || (int)$_GET['id'] < 1) galat(400, 'Parameter id harus berupa angka positif.');
    $id = (int)$_GET['id'];
    $st = $db->prepare($sql . " AND ps.id_pesanan=?");
    $st->bind_param('ssi', $kontak, $kontak, $id);
    $st->execute();
    $o = $st->get_result()->fetch_assoc();
    if (!$o) galat(404, "Pesanan #$id tidak ditemukan untuk kontak tersebut.");
    $o['total'] = (int)$o['total'];
    $o['id_pesanan'] = (int)$o['id_pesanan'];
    $o['items'] = detail_item($db, $id);
    sukses($o);
  }
  $st = $db->prepare($sql . " ORDER BY ps.id_pesanan DESC");
  $st->bind_param('ss', $kontak, $kontak);
  $st->execute();
  $res = $st->get_result();
  $out = [];
  while ($o = $res->fetch_assoc()) {
    $o['id_pesanan'] = (int)$o['id_pesanan'];
    $o['total'] = (int)$o['total'];
    $o['items'] = detail_item($db, $o['id_pesanan']);
    $out[] = $o;
  }
  if (!$out) galat(404, 'Belum ada pesanan untuk kontak tersebut.');
  sukses(['jumlah' => count($out), 'pesanan' => $out]);
}

// ======================= POST: buat pesanan =======================
$in = json_decode(file_get_contents('php://input'), true);
if (!is_array($in)) galat(400, 'Body harus berupa JSON yang valid (Content-Type: application/json).');

$nama   = trim($in['nama'] ?? '');
$tel    = trim($in['telepon'] ?? '');
$email  = trim($in['email'] ?? '');
$alamat = trim($in['alamat'] ?? '');
$items  = $in['items'] ?? null;

$kurang = [];
if ($nama === '')   $kurang[] = 'nama';
if ($tel === '')    $kurang[] = 'telepon';
if ($alamat === '') $kurang[] = 'alamat';
if ($kurang) galat(422, 'Field wajib belum diisi: ' . implode(', ', $kurang));
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) galat(422, 'Format email tidak valid.');
if (!is_array($items) || !$items) galat(422, 'items wajib berisi minimal 1 produk, contoh: [{"id_produk":1,"jumlah":2}]');

// gabungkan item duplikat & validasi
$keranjang = [];
foreach ($items as $it) {
  $idp = $it['id_produk'] ?? null;
  $jml = $it['jumlah'] ?? null;
  if (!is_int($idp) || $idp < 1 || !is_int($jml) || $jml < 1) {
    galat(422, 'Setiap item harus punya id_produk dan jumlah berupa bilangan bulat positif.');
  }
  $keranjang[$idp] = ($keranjang[$idp] ?? 0) + $jml;
}

$db->begin_transaction();
function batal($db, $kode, $pesan)
{
  $db->rollback();
  galat($kode, $pesan);
}

// 1) cek produk & stok (dikunci), hitung total dari harga di database
$baris = [];
$total = 0;
foreach ($keranjang as $idp => $jml) {
  $st = $db->prepare("SELECT id_produk,nama_produk,harga,stok FROM produk WHERE id_produk=? FOR UPDATE");
  $st->bind_param('i', $idp);
  $st->execute();
  $p = $st->get_result()->fetch_assoc();
  if (!$p) batal($db, 404, "Produk dengan id $idp tidak ditemukan.");
  if ((int)$p['stok'] < $jml) batal($db, 409, "Stok '{$p['nama_produk']}' tidak cukup (tersisa {$p['stok']}, diminta $jml).");
  $p['jumlah'] = $jml;
  $total += (int)$p['harga'] * $jml;
  $baris[] = $p;
}

// 2) simpan pembeli, pesanan, detail, kurangi stok
$st = $db->prepare("INSERT INTO pembeli(nama,email,telepon,alamat) VALUES(?,?,?,?)");
$st->bind_param('ssss', $nama, $email, $tel, $alamat);
if (!$st->execute()) batal($db, 500, 'Gagal menyimpan data pembeli.');
$idb = $db->insert_id;

$st = $db->prepare("INSERT INTO pesanan(id_pembeli,total) VALUES(?,?)");
$st->bind_param('id', $idb, $total);
if (!$st->execute()) batal($db, 500, 'Gagal menyimpan pesanan.');
$idp_baru = $db->insert_id;

$hasil_items = [];
foreach ($baris as $p) {
  $s1 = $db->prepare("INSERT INTO detail_pesanan(id_pesanan,id_produk,jumlah,harga_satuan) VALUES(?,?,?,?)");
  $s1->bind_param('iiid', $idp_baru, $p['id_produk'], $p['jumlah'], $p['harga']);
  if (!$s1->execute()) batal($db, 500, 'Gagal menyimpan detail pesanan.');
  $s2 = $db->prepare("UPDATE produk SET stok=stok-? WHERE id_produk=? AND stok>=?");
  $s2->bind_param('iii', $p['jumlah'], $p['id_produk'], $p['jumlah']);
  if (!$s2->execute() || $s2->affected_rows === 0) batal($db, 409, "Stok '{$p['nama_produk']}' berubah, pesanan dibatalkan.");
  $hasil_items[] = ['id_produk' => (int)$p['id_produk'], 'nama_produk' => $p['nama_produk'], 'jumlah' => $p['jumlah'],
                    'harga_satuan' => (int)$p['harga'], 'subtotal' => (int)$p['harga'] * $p['jumlah']];
}
$db->commit();

header('Location: pesanan.php?id=' . $idp_baru . '&kontak=' . urlencode($tel));
sukses(['id_pesanan' => $idp_baru, 'status' => 'baru', 'total' => $total, 'items' => $hasil_items,
        'pesan' => 'Pesanan berhasil dibuat.'], 201);
