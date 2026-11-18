<?php $hal = basename($_SERVER['PHP_SELF']); ?>
<ul class="nav nav-pills mb-3">
 <li class="nav-item"><a class="nav-link <?= $hal == 'index.php' ? 'active' : '' ?>" href="index.php"><i class="bi bi-box-seam"></i> Produk</a></li>
 <li class="nav-item"><a class="nav-link <?= $hal == 'pesanan.php' ? 'active' : '' ?>" href="pesanan.php"><i class="bi bi-receipt"></i> Pesanan &amp; Riwayat Pembeli</a></li>
 <li class="nav-item ms-auto"><a class="nav-link text-danger" href="index.php?logout=1">Logout</a></li></ul>
