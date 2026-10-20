# PRD — Hamta Healthy Food (Hamtash Salad)
**Project Based Learning (PjBL) — Jurusan RPL, SMK Negeri 1 Surabaya**
Kelas XI RPL 1, Kelompok 2 | Tahun 2026

---

## 1. Latar Belakang

Hamtash Salad (Hamta Healthy Food) adalah UMKM kuliner sehat yang berfokus pada salad, sandwich, dan wrap, dengan dua cabang: **Lidah Kulon** (dapur produksi & pemrosesan order online) dan **MERR/Apartemen Bale Hinggil** (dine-in & take-away langsung, buka 09.00–21.00 WIB setiap hari).

Saat ini Hamtash menjalankan operasionalnya secara manual dan tersebar di berbagai kanal (WhatsApp, GoFood, GrabFood, ShopeeFood), tanpa sistem katalog atau pemesanan terpusat. Hal ini memunculkan dua masalah utama yang menjadi dasar kebutuhan proyek ini:

1. **Beban komunikasi berulang** — Pelanggan terus-menerus menanyakan katalog dan harga via WhatsApp meskipun informasi tersebut sudah tersedia di katalog WhatsApp Business, menunjukkan katalog yang ada kurang mudah diakses/dieksplorasi.
2. **Tekanan margin akibat komisi platform** — Karena belum memiliki kurir internal, Hamtash bergantung penuh pada platform pihak ketiga (GoFood, GrabFood, ShopeeFood) untuk pengiriman, sehingga komisi platform memotong signifikan keuntungan mereka — terutama karena volume penjualan harian yang sudah cukup besar (puluhan porsi/hari).

Sesuai ketentuan juknis PjBL RPL, aplikasi yang dibangun **wajib memiliki alur transaksi mandiri** dan tidak boleh sekadar redirect ke WhatsApp atau berupa landing page statis setara proyek jurusan Bisnis Digital.

## 2. Tujuan Produk

Membangun website **all-in-one** untuk Hamtash Salad yang:
- Menyediakan katalog digital interaktif dan transparan sehingga pelanggan tidak perlu lagi bertanya manual via WhatsApp.
- Menyediakan sistem pemesanan dan checkout mandiri (termasuk pembayaran QRIS) tanpa redirect ke WhatsApp.
- Mereplikasi fitur andalan "Kreasikan Salad Sendiri" yang sebelumnya hanya tersedia di aplikasi ordering pihak ketiga.
- Menyediakan admin panel untuk mengelola menu, pesanan, promo, laporan, dan pengaturan toko — termasuk menggantikan sistem kasir sederhana yang sudah ada saat ini.

## 3. Lingkup Proyek (Scope)

### 3.1 In-scope
- Website customer-facing (katalog, kreasikan salad, checkout, status pesanan, rating)
- Admin panel (7 halaman — lihat bagian 5)
- Sistem kasir offline terintegrasi ke sistem yang sama (input transaksi offline)
- Pembayaran QRIS statis dengan verifikasi manual (tahap awal); integrasi Midtrans Snap (tahap lanjutan)
- Sistem promo/diskon generik dengan kondisi custom

### 3.2 Out-of-scope (untuk saat ini)
- Sistem login/akun customer (checkout tetap guest)
- Informasi nutrisi/kalori pada menu
- Kurir/pengiriman internal (delivery tetap bergantung Shopee Express/ojol pihak ketiga)
- Deployment ke hosting publik (cukup localhost sesuai tahap awal juknis PjBL)
- Tracking stok bahan baku secara kuantitatif (bahan hanya untuk info komposisi/alergen, bukan inventory counting)

## 4. Target Pengguna

| Peran | Deskripsi |
|---|---|
| **Customer** | Pelanggan umum yang memesan via website, tanpa perlu akun/login (guest checkout) |
| **Admin** | Pemilik/staf Hamtash yang mengelola menu, pesanan (online & offline/kasir), promo, laporan, dan pengaturan toko. Admin dan Kasir digabung jadi satu peran untuk simplicity |

## 5. Kebutuhan Fungsional

### 5.1 Sisi Customer

| # | Fitur | Deskripsi |
|---|---|---|
| F1 | Lihat Katalog Menu | Menampilkan menu (nama, deskripsi, harga, foto), termasuk penandaan menu yang hanya tersedia di cabang tertentu (misal Rice Bowl khusus MERR). Tidak menampilkan info kalori/nutrisi |
| F2 | Kreasikan Salad Sendiri | Customer merakit salad sendiri: pilih ~7 jenis sayuran (selada, romaine, kol ungu, tomat, timun, kacang polong, mixed vegetables) dan ~4 jenis protein (chicken patties, beef patties, grilled chicken, telur — bersifat opsional, tanpa batas maksimal pilihan). Berbeda dari Chef's Salad yang komposisinya tetap/tidak bisa dikustom |
| F3 | Keranjang & Checkout Mandiri | Checkout tanpa redirect ke WhatsApp; guest checkout (tanpa login); pilih metode ambil (Pickup di cabang / Delivery); untuk delivery, ongkir dikonfirmasi manual oleh admin via WhatsApp setelah pesanan dibuat |
| F4 | Terapkan Promo | Customer dapat menerapkan promo yang berlaku otomatis sesuai kondisi (lihat 5.3); beberapa promo yang cocok bisa digabung/stacking, bukan hanya promo terbaik yang dipakai |
| F5 | Pembayaran QRIS | Checkout dengan QRIS statis + upload bukti bayar manual (tahap awal); verifikasi oleh admin. Total harga dihitung di backend untuk validasi, bukan di frontend |
| F6 | Lihat Status Pesanan | Customer dapat memantau status pesanannya (diproses → siap → selesai, atau batal untuk item yang di-cancel admin) |
| F7 | Beri Rating/Ulasan | Rating per-pesanan (bukan per-produk), skala 1–5 bintang, sepenuhnya opsional dan dapat diberikan kapan saja (tidak dipaksa setelah pesanan selesai) |

### 5.2 Sisi Admin (7 halaman)

| # | Halaman | Fungsi |
|---|---|---|
| A1 | Dashboard | Ringkasan statistik penjualan dan panel notifikasi |
| A2 | Kelola Menu | Mengatur seluruh menu reguler dan komponen "Kreasikan Salad" (sayuran, protein) beserta daftar bahan terkait (info komposisi/alergen, tanpa tracking stok kuantitatif). Setiap item memiliki atribut: nama, harga, foto, deskripsi, status aktif/nonaktif (toggle sold-out manual) |
| A3 | Kasir | Input transaksi offline (termasuk penggunaan fitur Kreasikan Salad di kasir) |
| A4 | Daftar Pesanan/Kelola Pesanan | Mengelola pesanan masuk dari kedua kanal (online web & offline kasir) dalam satu tabel terpadu (field `jenis`: Online/Kasir); update status, konfirmasi ongkir delivery, pembatalan item yang tidak tersedia |
| A5 | Pengaturan Promo/Diskon | Admin membuat promo dengan kondisi custom bebas (contoh: "potongan Rp5.000 untuk pesanan >20 porsi"), berlaku untuk kanal web maupun kasir offline secara bersamaan; setiap promo punya batas penggunaan (misal maks. 100 customer) dan otomatis nonaktif saat limit tercapai |
| A6 | Laporan | Laporan penjualan per item menu, mencakup transaksi online dan offline |
| A7 | Pengaturan Toko | Informasi operasional: jam buka per cabang dan detail cabang lainnya |

### 5.3 Logika Bisnis Khusus

- **Stok menu reguler**: diasumsikan selalu tersedia; jika ternyata habis, admin membatalkan item terkait secara manual (sebagian atau seluruh pesanan) dan customer melihat perubahan ini di halaman status pesanannya — tidak ada auto-cancel otomatis oleh sistem.
- **Sinkronisasi online-offline**: tidak ada proses sinkronisasi terpisah — baik pesanan online maupun transaksi kasir offline ditulis ke tabel Pesanan yang sama (dibedakan field jenis), sehingga Daftar Pesanan dan Laporan cukup query dari satu sumber data.
- **Harga online vs offline**: harga di channel online sengaja dibuat lebih tinggi dari harga offline untuk menutupi biaya platform — berlaku juga untuk pemesanan via website customer-facing.
- **Diskon**: tidak ada diskon reguler untuk transaksi harian biasa; kebijakan diskon lama (pesanan 150–200 porsi → maks. Rp500/porsi atau gratis ongkir) kini digeneralisasi menjadi sistem promo fleksibel yang bisa dikonfigurasi admin (lihat A5), bukan hardcoded.

## 6. Kebutuhan Non-Fungsional

| # | Kebutuhan | Detail |
|---|---|---|
| NF1 | UI/UX | Bersih dan modern, mengacu pada gaya visual SaladStop! |
| NF2 | Identitas Visual | Mengikuti palet warna resmi (lihat bagian 7) |
| NF3 | Responsif | Mobile-friendly, karena mayoritas pelanggan kemungkinan mengakses via HP |
| NF4 | Keamanan | reCAPTCHA v2 pada form checkout dan opsional pada login admin |
| NF5 | Teknologi | Tidak boleh PHP/HTML native sederhana; wajib stack modern sesuai standar industri (lihat bagian 8) |
| NF6 | Deployment tahap awal | Cukup berjalan di localhost, sesuai juknis PjBL |

## 7. Identitas Visual

| Warna | Hex | Role | Penggunaan |
|---|---|---|---|
| Green | `#65BD5E` | Primary | CTA utama (Pesan, Checkout), elemen aktif, highlight harga |
| Dark Green | `#0B412B` | Primary (dark) | Teks penting di atas bg terang, header/navbar, hover state tombol primary |
| Cream | `#F7F2E7` | Base/Background | Latar utama seluruh halaman |
| Black | `#010101` | Teks utama | Heading dan body text |
| Grey | `#8A8A8A` | Neutral/Secondary | Teks sekunder, border, divider |
| Red | `#E05C5C` | Accent/Alert | Badge promo/diskon, notifikasi, status error (dipakai terbatas) |

Logo: wordmark "hamta" dengan ikon daun hijau. Referensi gaya: SaladStop!.

## 8. Tech Stack

| Komponen | Pilihan |
|---|---|
| Framework | Laravel 13 + Blade (full-stack monolith) |
| Styling | Tailwind CSS via Vite |
| Database | MySQL, Eloquent ORM |
| Auth | Manual, hanya untuk Admin |
| Storage | Local disk Laravel |
| Icon | Lucide / Heroicons |
| Payment | QRIS statis + verifikasi manual (tahap awal) → Midtrans Snap (tahap lanjutan) |
| Captcha | Google reCAPTCHA v2 |
| Environment | PHP 8.5.8, Node 18+ |

*(Detail konvensi development & aturan kerja AI agent ada di `AGENTS.md`, tidak diulang di sini)*

## 9. Promosi & Fitur Musiman (Konteks Bisnis)

- Paket bundling (salad/wrap + jus) dipasarkan terutama lewat platform online — perlu diakomodasi di struktur katalog.
- Promo tanggal kembar (misal 10.10) sebagai strategi musiman.
- Pesanan hampers untuk momen tertentu (Lebaran, Natal) — berpotensi jadi fitur musiman khusus di website.
- Strategi pemasaran utama: organik (media sosial & word of mouth), bukan iklan berbayar — tidak memerlukan fitur marketing/ads khusus di MVP.

## 10. Rencana Pengembangan Produk (Future Scope)

- Menu minuman baru: matcha latte, milk tea, es jeli
- Paket bundling tambahan
- Struktur katalog perlu dirancang agar penambahan produk baru ini tidak memerlukan perombakan sistem

## 11. Asumsi & Batasan

- Target konsumen Hamtash bukan pelaku diet ketat → info kalori/nutrisi sengaja tidak ditampilkan.
- Pengiriman delivery tetap bergantung pihak ketiga (Shopee Express/ojol) untuk saat ini; website hanya menangani sisi pemesanan & pembayaran, bukan logistik fisik.
- Mayoritas pelanggan pickup datang ke outlet (cabang MERR), bukan ke dapur produksi (Lidah Kulon) — relevan untuk default/pengurutan pilihan cabang pickup.
- Sistem kasir sebelumnya (buatan mahasiswa, bukan sistem komersial) digantikan sepenuhnya oleh admin panel baru ini.

## 12. Dokumen Terkait

- `AGENTS.md` — instruksi kerja untuk AI coding agent & konvensi development
- `schema.sql` — skema database (hasil translate dari ERD)
- `DESIGN.md` — design system (komponen, layout)
- Diagram UML (use case, sequence) dan ERD — dokumentasi terpisah, sudah selesai