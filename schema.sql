-- =========================================================
-- Hamta Healthy Food (Hamtash Salad) — schema.sql
-- Database: MySQL
-- Berdasarkan ERD hasil UML/ERD stage (use case, class diagram, sequence diagram)
-- =========================================================

-- -----------------------------------------------------
-- CABANG
-- -----------------------------------------------------
CREATE TABLE cabang (
    id_cabang      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_cabang    VARCHAR(100) NOT NULL,
    alamat         VARCHAR(255) NOT NULL,
    jam_buka       TIME NOT NULL,
    jam_tutup      TIME NOT NULL,
    layani_pickup  BOOLEAN NOT NULL DEFAULT TRUE,
    layani_dinein  BOOLEAN NOT NULL DEFAULT FALSE,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- ADMIN  (tidak ada relasi FK ke tabel lain / tanpa audit log)
-- -----------------------------------------------------
CREATE TABLE admin (
    id_admin       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama           VARCHAR(100) NOT NULL,
    username       VARCHAR(50) NOT NULL UNIQUE,
    password       VARCHAR(255) NOT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- BAHAN  (info komposisi/alergen, tanpa tracking stok kuantitatif)
-- -----------------------------------------------------
CREATE TABLE bahan (
    id_bahan       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_bahan     VARCHAR(100) NOT NULL,
    keterangan     VARCHAR(255) NULL COMMENT 'info alergen/komposisi tambahan',
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- PRODUK  (menu reguler: sandwich, wrap, salad sayur/buah, jus, rice bowl, chef's salad, dll)
-- -----------------------------------------------------
CREATE TABLE produk (
    id_produk      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_produk    VARCHAR(100) NOT NULL,
    deskripsi      TEXT NULL,
    kategori       ENUM('Salad', 'Sandwich', 'Juice') NOT NULL COMMENT 'kategori utk tab filter menu',
    harga_offline  DECIMAL(10,2) NOT NULL,
    harga_online   DECIMAL(10,2) NOT NULL COMMENT 'harga online dibuat lebih tinggi dari offline',
    foto           VARCHAR(255) NULL,
    status_aktif   BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'toggle sold-out manual',
    bisa_dikustom  BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'false utk Chef''s Salad (komposisi tetap)',
    id_cabang      INT UNSIGNED NULL COMMENT 'NULL = tersedia di semua cabang; diisi jika eksklusif cabang tertentu (misal Rice Bowl = MERR)',
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cabang) REFERENCES cabang(id_cabang) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- PRODUK_BAHAN  (junction: komposisi/alergen tiap produk reguler)
-- -----------------------------------------------------
CREATE TABLE produk_bahan (
    id_produk      INT UNSIGNED NOT NULL,
    id_bahan       INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_produk, id_bahan),
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE CASCADE,
    FOREIGN KEY (id_bahan) REFERENCES bahan(id_bahan) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- KOMPONEN_SALAD  (pilihan untuk fitur "Kreasikan Salad Sendiri": sayuran & protein)
-- -----------------------------------------------------
CREATE TABLE komponen_salad (
    id_komponen    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_komponen  VARCHAR(100) NOT NULL,
    jenis          ENUM('sayuran', 'protein') NOT NULL,
    harga_tambahan DECIMAL(10,2) NOT NULL DEFAULT 0,
    foto           VARCHAR(255) NULL,
    deskripsi      VARCHAR(255) NULL,
    status_aktif   BOOLEAN NOT NULL DEFAULT TRUE,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- KOMPONEN_BAHAN  (junction: komposisi/alergen tiap komponen custom salad)
-- -----------------------------------------------------
CREATE TABLE komponen_bahan (
    id_komponen    INT UNSIGNED NOT NULL,
    id_bahan       INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_komponen, id_bahan),
    FOREIGN KEY (id_komponen) REFERENCES komponen_salad(id_komponen) ON DELETE CASCADE,
    FOREIGN KEY (id_bahan) REFERENCES bahan(id_bahan) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- PROMO  (sistem promo generik dengan kondisi custom, berlaku web & kasir)
-- -----------------------------------------------------
CREATE TABLE promo (
    id_promo         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_promo       VARCHAR(100) NOT NULL,
    deskripsi        VARCHAR(255) NULL,
    tipe_potongan    ENUM('nominal', 'persen', 'gratis_ongkir') NOT NULL,
    nilai_potongan   DECIMAL(10,2) NULL COMMENT 'nominal Rp atau persen, NULL jika gratis_ongkir',
    syarat_min_porsi INT UNSIGNED NULL COMMENT 'kondisi custom, misal >20 porsi',
    batas_penggunaan INT UNSIGNED NOT NULL COMMENT 'maksimal jumlah customer yg bisa pakai',
    jumlah_terpakai  INT UNSIGNED NOT NULL DEFAULT 0,
    status_aktif     BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'auto nonaktif saat jumlah_terpakai >= batas_penggunaan',
    tanggal_mulai    DATE NULL,
    tanggal_selesai  DATE NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- PESANAN  (tabel terpadu: online web & offline kasir, dibedakan field jenis)
-- -----------------------------------------------------
CREATE TABLE pesanan (
    id_pesanan         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_pesanan       VARCHAR(20) NOT NULL UNIQUE,
    jenis              ENUM('online', 'kasir') NOT NULL,
    nama_pelanggan     VARCHAR(100) NULL COMMENT 'NULL diperbolehkan utk transaksi kasir walk-in',
    no_whatsapp        VARCHAR(20) NULL,
    metode_ambil       ENUM('pickup', 'delivery') NOT NULL DEFAULT 'pickup',
    id_cabang          INT UNSIGNED NOT NULL COMMENT 'cabang pickup / cabang asal produksi',
    alamat_delivery    VARCHAR(255) NULL,
    ongkir             DECIMAL(10,2) NULL COMMENT 'NULL = menunggu konfirmasi admin via WA',
    status_ongkir      ENUM('tidak_perlu', 'menunggu_konfirmasi', 'terkonfirmasi') NOT NULL DEFAULT 'tidak_perlu',
    subtotal           DECIMAL(10,2) NOT NULL,
    total_diskon       DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_bayar        DECIMAL(10,2) NOT NULL COMMENT 'dihitung di backend untuk validasi',
    metode_bayar       ENUM('qris', 'tunai') NOT NULL DEFAULT 'qris',
    bukti_bayar        VARCHAR(255) NULL COMMENT 'path upload bukti bayar QRIS',
    status_pembayaran  ENUM('menunggu_verifikasi', 'terverifikasi', 'ditolak') NOT NULL DEFAULT 'menunggu_verifikasi',
    status_pesanan     ENUM('diproses', 'siap', 'selesai', 'batal') NOT NULL DEFAULT 'diproses',
    id_admin           INT UNSIGNED NULL COMMENT 'admin/kasir yang input transaksi offline, NULL utk pesanan online',
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cabang) REFERENCES cabang(id_cabang),
    FOREIGN KEY (id_admin) REFERENCES admin(id_admin) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- DETAIL_PESANAN  (item per pesanan: produk reguler ATAU custom salad)
-- -----------------------------------------------------
CREATE TABLE detail_pesanan (
    id_detail       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pesanan      INT UNSIGNED NOT NULL,
    id_produk       INT UNSIGNED NULL COMMENT 'NULL jika item ini adalah custom salad',
    nama_item       VARCHAR(100) NOT NULL COMMENT 'snapshot nama produk/"Kreasikan Salad Sendiri" saat transaksi',
    jumlah          INT UNSIGNED NOT NULL DEFAULT 1,
    harga_satuan    DECIMAL(10,2) NOT NULL COMMENT 'snapshot harga saat transaksi',
    subtotal_item   DECIMAL(10,2) NOT NULL,
    status_item     ENUM('aktif', 'dibatalkan') NOT NULL DEFAULT 'aktif' COMMENT 'utk pembatalan parsial saat stok habis',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- DETAIL_PESANAN_KOMPONEN  (junction: pilihan sayuran/protein per item custom salad)
-- -----------------------------------------------------
CREATE TABLE detail_pesanan_komponen (
    id_detail       INT UNSIGNED NOT NULL,
    id_komponen     INT UNSIGNED NOT NULL,
    harga_tambahan  DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'snapshot harga_tambahan komponen saat transaksi',
    PRIMARY KEY (id_detail, id_komponen),
    FOREIGN KEY (id_detail) REFERENCES detail_pesanan(id_detail) ON DELETE CASCADE,
    FOREIGN KEY (id_komponen) REFERENCES komponen_salad(id_komponen)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- PESANAN_PROMO  (junction: promo yang diterapkan per pesanan, mendukung stacking)
-- -----------------------------------------------------
CREATE TABLE pesanan_promo (
    id_pesanan      INT UNSIGNED NOT NULL,
    id_promo        INT UNSIGNED NOT NULL,
    nilai_potongan  DECIMAL(10,2) NOT NULL COMMENT 'snapshot nilai potongan saat diterapkan',
    PRIMARY KEY (id_pesanan, id_promo),
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE,
    FOREIGN KEY (id_promo) REFERENCES promo(id_promo)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- RATING  (per-pesanan, opsional, skala 1-5)
-- -----------------------------------------------------
CREATE TABLE rating (
    id_rating       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_pesanan      INT UNSIGNED NOT NULL UNIQUE,
    bintang         TINYINT UNSIGNED NOT NULL CHECK (bintang BETWEEN 1 AND 5),
    ulasan          TEXT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE
) ENGINE=InnoDB;