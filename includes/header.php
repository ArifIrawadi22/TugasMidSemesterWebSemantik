<?php ob_start();
require_once __DIR__ . '/../config.example.php';
require_once __DIR__ . '/fungsi.php';
$base = $base ?? '';
$jml = array_sum($_SESSION['cart'] ?? []); ?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Marketplace Bengkulu</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= $base ?>assets/style.css" rel="stylesheet">
  <script>
    const BASE = '<?= $base ?>';
  </script>
</head>

<body>
  <nav class="navbar navbar-expand navbar-dark nav-grad mb-4 shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="<?= $base ?>index.php"><i class="bi bi-bag-heart-fill"></i> Marketplace Bengkulu</a>
      <div class="navbar-nav ms-auto">
        <a class="nav-link" href="<?= $base ?>riwayat.php"><i class="bi bi-clock-history"></i> Riwayat Belanja</a>
        <a class="nav-link" href="<?= $base ?>keranjang.php"><i class="bi bi-cart3"></i> Keranjang <span id="cartCount" class="badge bg-light text-dark"><?= $jml ?></span></a>
        <a class="nav-link" href="<?= $base ?>admin/index.php"><i class="bi bi-person-gear"></i> Admin</a>
      </div>
    </div>
  </nav>
  <div class="container">
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
      <div id="toastBox" class="toast text-bg-success border-0">
        <div class="d-flex">
          <div id="toastMsg" class="toast-body"></div>
        </div>
      </div>
    </div>