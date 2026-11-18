CREATE TABLE penjual(id_penjual INT AUTO_INCREMENT PRIMARY KEY,nama_toko VARCHAR(100) NOT NULL,telepon VARCHAR(20),alamat VARCHAR(255)) ENGINE=InnoDB;
CREATE TABLE kategori(id_kategori INT AUTO_INCREMENT PRIMARY KEY,nama_kategori VARCHAR(50) NOT NULL) ENGINE=InnoDB;
CREATE TABLE produk(id_produk INT AUTO_INCREMENT PRIMARY KEY,id_penjual INT NOT NULL,id_kategori INT NOT NULL,nama_produk VARCHAR(150) NOT NULL,deskripsi TEXT,harga DECIMAL(12,0) NOT NULL,stok INT NOT NULL DEFAULT 0,gambar VARCHAR(255),
 FOREIGN KEY(id_penjual) REFERENCES penjual(id_penjual),FOREIGN KEY(id_kategori) REFERENCES kategori(id_kategori)) ENGINE=InnoDB;
CREATE TABLE pembeli(id_pembeli INT AUTO_INCREMENT PRIMARY KEY,nama VARCHAR(100) NOT NULL,email VARCHAR(100),telepon VARCHAR(20),alamat VARCHAR(255) NOT NULL) ENGINE=InnoDB;
CREATE TABLE pesanan(id_pesanan INT AUTO_INCREMENT PRIMARY KEY,id_pembeli INT NOT NULL,tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,total DECIMAL(14,0) NOT NULL,status ENUM('baru','diproses','selesai') DEFAULT 'baru',
 FOREIGN KEY(id_pembeli) REFERENCES pembeli(id_pembeli)) ENGINE=InnoDB;
CREATE TABLE detail_pesanan(id_detail INT AUTO_INCREMENT PRIMARY KEY,id_pesanan INT NOT NULL,id_produk INT NOT NULL,jumlah INT NOT NULL,harga_satuan DECIMAL(12,0) NOT NULL,
 FOREIGN KEY(id_pesanan) REFERENCES pesanan(id_pesanan),FOREIGN KEY(id_produk) REFERENCES produk(id_produk)) ENGINE=InnoDB;

INSERT INTO penjual(nama_toko,telepon,alamat) VALUES('Toko Sumber Rejeki','08111111111','Bengkulu'),('Lapak Digital','08222222222','Palembang');
INSERT INTO kategori(nama_kategori) VALUES('Elektronik'),('Fashion'),('Makanan');
INSERT INTO produk(id_penjual,id_kategori,nama_produk,deskripsi,harga,stok,gambar) VALUES
(2,1,'Headset Gaming','Headset dengan mikrofon dan suara jernih.',150000,20,''),
(2,1,'Mouse Wireless','Mouse nirkabel 2.4GHz, hemat baterai.',85000,35,''),
(1,2,'Kaos Polos Hitam','Kaos katun nyaman dipakai sehari-hari.',60000,50,''),
(1,2,'Tas Ransel','Tas ransel muat laptop 14 inci.',175000,15,''),
(1,3,'Kopi Bubuk 250g','Kopi lokal sangrai sedang.',45000,40,''),
(1,3,'Keripik Pisang','Renyah dan gurih, kemasan 200g.',20000,60,'');
