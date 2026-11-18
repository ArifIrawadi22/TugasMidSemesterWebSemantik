# Marketplace Sederhana (PHP Native + MySQLi + Bootstrap)
Dokumen perancangan: lihat `PERANCANGAN.md` (tabel, ERD, alur transaksi, mockup).

## Fitur
Daftar produk (cari, filter kategori, urutkan), detail produk, keranjang interaktif (AJAX), checkout dengan transaksi database,
riwayat pembelian pembeli (`riwayat.php`), admin: CRUD produk, daftar pesanan + riwayat pembeli, ubah status pesanan.

## Menjalankan Lokal (XAMPP)
1. phpMyAdmin: buat database `marketplace`, import `database.sql`.
2. Salin `config.example.php` menjadi `config.php`, isi koneksi dan password admin.
3. Buka `http://localhost/marketplace/`. Admin: `/admin/`.

## GitHub
`git init`, `git add .`, `git commit -m "Marketplace"`, `git remote add origin <url>`, `git push -u origin main`. `config.php` sudah di `.gitignore`.
