# Dokumentasi REST API Marketplace

Base URL (XAMPP): `http://localhost/marketplace/api/`

## A.a Fungsi Web Service/API
API (Application Programming Interface) adalah antarmuka yang membuat aplikasi lain dapat membaca/menulis data marketplace
lewat HTTP tanpa mengakses database langsung. Data dikirim dalam format JSON yang bisa dipahami semua bahasa pemrograman.
API diperlukan karena: (1) aplikasi mobile, aplikasi pihak ketiga, atau toko lain tidak boleh/tidak bisa terhubung langsung ke MySQL;
(2) logika bisnis (validasi stok, hitung total) tetap satu tempat di server; (3) keamanan terjaga karena hanya data tertentu yang dibuka;
(4) satu backend dapat melayani banyak klien (web, Android, iOS) sekaligus.

## A.b Rancangan Endpoint
| No | Method | Endpoint | Fungsi | Kode respons |
|---|---|---|---|---|
| 1 | GET | /api/produk.php | Daftar semua produk (opsional `q`, `kategori`, `urut`=baru/murah/mahal) | 200 |
| 2 | GET | /api/produk.php?id=1 | Detail produk | 200, 400, 404 |
| 3 | GET | /api/kategori.php | Daftar kategori + jumlah produk | 200 |
| 4 | POST | /api/pesanan.php | Buat pesanan baru (body JSON) | 201, 400, 404, 409, 422 |
| 5 | GET | /api/pesanan.php?id=7&kontak=0812xxx | Detail pesanan (harus cocok dengan telepon/email) | 200, 400, 404 |
| 6 | GET | /api/pesanan.php?kontak=0812xxx | Riwayat pesanan pembeli | 200, 400, 404 |

Method lain dibalas **405 Method Not Allowed**.

## A.c Struktur JSON
Format sukses: `{"status":"success","data":{...}}`  — Format error: `{"status":"error","message":"..."}`

Daftar produk (`GET /api/produk.php`):
```json
{
  "status": "success",
  "data": {
    "jumlah": 6,
    "produk": [
      {
        "id_produk": 1,
        "nama_produk": "Headset Gaming",
        "deskripsi": "Headset dengan mikrofon dan suara jernih.",
        "harga": 150000,
        "stok": 20,
        "gambar": null,
        "kategori": { "id_kategori": 1, "nama_kategori": "Elektronik" },
        "penjual": { "id_penjual": 2, "nama_toko": "Lapak Digital" }
      }
    ]
  }
}
```
Detail produk (`GET /api/produk.php?id=1`): isi `data` sama dengan satu elemen `produk` di atas.

Body POST pesanan:
```json
{
  "nama": "Budi Santoso",
  "telepon": "081234567890",
  "email": "budi@example.com",
  "alamat": "Jl. Merdeka No. 1, Bengkulu",
  "items": [ { "id_produk": 1, "jumlah": 2 }, { "id_produk": 5, "jumlah": 1 } ]
}
```
Response 201:
```json
{ "status": "success", "data": { "id_pesanan": 1, "status": "baru", "total": 345000, "items": [ ... ], "pesan": "Pesanan berhasil dibuat." } }
```
> Contoh di atas hanya untuk menjelaskan format. Untuk laporan, gunakan response ASLI hasil pengujian Anda (screenshot).

## Skenario Pengujian
| No | Skenario | Request | Hasil yang diharapkan |
|---|---|---|---|
| 1 | Semua produk | GET /api/produk.php | 200, 6 produk |
| 2 | Detail produk | GET /api/produk.php?id=1 | 200, Headset Gaming |
| 3 | Produk tidak ada | GET /api/produk.php?id=999 | 404 |
| 4 | ID tidak valid | GET /api/produk.php?id=abc | 400 |
| 5 | Kategori | GET /api/kategori.php | 200, 3 kategori |
| 6 | Buat pesanan | POST /api/pesanan.php (body valid) | 201, stok berkurang di DB |
| 7 | Data tidak lengkap | POST tanpa `alamat` | 422 |
| 8 | Stok tidak cukup | POST jumlah 9999 | 409, tidak ada data tersimpan |
| 9 | Method salah | DELETE /api/produk.php | 405 |
| 10 | Riwayat | GET /api/pesanan.php?kontak=081234567890 | 200 |

## Laporan Hasil Pengujian (isi sendiri)
Tabel: No, Skenario, Request, Status yang diterima, Lulus/Tidak, Nomor screenshot.
