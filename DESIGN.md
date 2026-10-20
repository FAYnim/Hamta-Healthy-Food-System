# DESIGN.md — Hamta Salad (Hamta Healthy Food)

Dokumen ini adalah acuan desain umum (design system) untuk seluruh halaman website Hamta Salad — baik sisi customer maupun admin panel. Disusun berdasarkan hasil wawancara UMKM, palet warna final yang disepakati, dan analisa terhadap beberapa halaman UI yang sudah didesain di Figma (Landing Page, Dashboard Admin, Daftar Menu), agar polanya bisa direplikasi konsisten ke halaman-halaman lain yang belum didesain.

---

## 1. Prinsip Desain

- **Bersih & modern**, mengacu pada gaya visual SaladStop! — tidak ramai, banyak whitespace, fokus ke foto produk.
- **Hangat, bukan putih polos** — background utama pakai Cream, bukan putih, supaya terasa lebih "healthy food" dan tidak klinis.
- **Hijau sebagai identitas dominan** — Green & Dark Green dipakai luas; Red dibatasi hanya untuk accent/alert supaya tidak "bersaing" visual dengan hijau.
- **Komponen berulang** — card produk, card stat, dan struktur navbar/footer dipakai ulang di banyak halaman, bukan didesain ulang tiap kali. Konsistensi komponen lebih penting daripada variasi per halaman.
- **Mobile-friendly** — tampilan harus responsif (syarat dari juknis PjBL RPL).

---

## 2. Design Tokens (Palet Warna)

| Nama | Hex | Role | Pemakaian |
|---|---|---|---|
| Green | `#65BD5E` | Primary | CTA utama (Pesan, Checkout, Add to Cart), elemen aktif/selected, highlight harga, grafik/chart |
| Dark Green | `#0B412B` | Primary (dark) | Header/navbar footer, sidebar admin, teks penting di atas background terang, hover state tombol primary |
| Cream | `#F7F2E7` | Base/Background | Latar utama seluruh halaman (customer & admin), menggantikan putih polos |
| Black | `#010101` | Teks utama | Heading, body text, data penting di tabel |
| Grey | `#8A8A8A` | Neutral/Secondary | Teks sekunder (deskripsi, caption, metadata), border, divider |
| Red | `#E05C5C` | Accent/Alert | Badge promo/diskon, notifikasi, status error ("pesanan dibatalkan", stok habis) — dipakai **terbatas/secukupnya** |

**Catatan pemakaian khusus per konteks:**
- **Customer-facing:** background Cream, tombol Green → hover Dark Green, teks Black/Grey, badge promo Red.
- **Admin panel:** sidebar Dark Green (teks Cream/putih), topbar & card putih/Cream, stat positif pakai aksen Green, alert pakai Red, teks tabel Black (data) + Grey (metadata).

---

## 3. Tipografi

- Font sans-serif, geometric/rounded, tegas — konsisten dengan wordmark logo "hamtā".
- **Heading:** bold, ukuran besar, warna Black (customer) atau Black/Dark Green (admin).
- **Body/deskripsi:** regular, warna Grey untuk teks sekunder, Black untuk teks utama.
- **Label tombol & nama produk:** uppercase + bold untuk penekanan (terlihat di nama produk pada card menu).
- **Angka statistik (admin):** ukuran sangat besar & bold, jadi focal point tiap stat card.

---

## 4. Komponen UI (Reusable)

Komponen-komponen ini dipakai berulang di berbagai halaman — jadikan acuan utama saat mendesain halaman baru agar konsisten.

### 4.1 Navbar (Customer)
- Logo "hamtā" kiri.
- Menu navigasi horizontal (Beranda, Menu, Kreasi, Promo, dst — menyesuaikan halaman).
- Ikon keranjang di ujung kanan.
- Background Cream, sticky di top.

### 4.2 Card Produk
- Container rounded, background Green terang untuk area foto.
- Foto produk di tengah.
- Nama produk: uppercase, bold, di bawah foto.
- Harga: di bawah nama.
- Tombol "+Add to Cart": full-width, Dark Green, teks putih.
- Disusun dalam grid 4 kolom (desktop), menyesuaikan jadi 1-2 kolom di mobile.
- Dipakai sama persis baik untuk preview di landing page maupun listing penuh di halaman Menu/Katalog — **satu komponen, dipakai ulang.**

### 4.3 Card Kategori/Section (Customer)
- Container besar, border Green, rounded corner.
- Judul kategori (mis. "Best Seller", "Salad", "Juice") di luar/atas card.
- Berisi grid card produk di dalamnya.
- Pola ini dipakai untuk mengelompokkan produk per kategori di halaman manapun yang menampilkan katalog.

### 4.4 Card Promosi/Highlight
- Container besar rounded, border Green.
- Kombinasi teks (headline + deskripsi singkat) di satu sisi, foto besar di sisi lain.
- CTA button Green di bawah teks.
- Dipakai untuk highlight fitur (mis. "Kreasikan Salad") atau campaign tertentu.

### 4.5 Card Testimoni
- Card kecil dalam carousel/slider.
- Avatar bulat, rating bintang (ikon kuning/gold), kutipan teks, nama reviewer di bawah.
- Navigasi carousel dengan arrow kiri-kanan + progress bar bawah.

### 4.6 Footer (Customer)
- Background Dark Green dengan bentuk wave/gelombang sebagai transisi dari section sebelumnya.
- Logo + tagline.
- Info lokasi dengan toggle pilih cabang (Lidah Kulon / Merr), alamat, peta kecil.
- Link sosmed & kontak.
- Dipakai sama di semua halaman customer (bukan didesain ulang per halaman).

### 4.7 Sidebar Admin
- Background Dark Green, full height.
- Logo/judul panel di atas ("Hamta Panel").
- List menu dengan ikon + label putih; item aktif di-highlight background Green terang.
- Menu mengikuti struktur halaman admin panel final (lihat bagian 6).

### 4.8 Topbar Admin
- Judul halaman di kiri (sesuai halaman aktif).
- Info user (nama, chip putih) di kanan.
- Background Cream/putih, konsisten di semua halaman admin.

### 4.9 Stat Card (Admin)
- Container putih/Cream rounded.
- Ikon bulat Dark Green di kiri atas.
- Label singkat + angka besar bold di bawahnya.
- Dipakai berulang untuk ringkasan angka di halaman mana pun yang butuh (Dashboard, bisa juga Laporan).

### 4.10 Panel Chart & List (Admin)
- Chart (line chart, warna garis Green) dalam card putih/Cream rounded — untuk data tren (penjualan, dsb).
- List notifikasi/item dalam card terpisah, tiap item dipisah divider tipis (Grey), info utama bold + metadata (waktu/tanggal) di kanan warna Grey.

### 4.11 Tabel Admin (untuk halaman seperti Daftar Pesanan, Kelola Menu, Laporan)
- Belum ada contoh visual langsung, tapi mengikuti prinsip warna yang sudah disepakati: teks data utama Black, metadata (ID, tanggal) Grey, status/alert pakai badge Red atau Green tergantung konteks (selesai = Green, pending/batal = Red).

---

## 5. Pola Layout Umum

**Halaman Customer:**
Navbar → Hero/Section utama → Section konten (katalog/promo/dsb, pakai Card Kategori + Card Produk) → Footer.

**Halaman Admin:**
Sidebar (tetap) + Topbar (judul halaman berubah sesuai page) + Content area (kombinasi Stat Card, Chart, List, atau Tabel tergantung kebutuhan halaman).

Pola ini berlaku untuk seluruh halaman baru yang belum didesain — tinggal pilih kombinasi komponen di atas sesuai kebutuhan fungsional halaman tersebut, bukan membuat gaya visual baru.

---

## 6. Referensi Struktur Halaman (untuk konsistensi penerapan komponen)

**Customer (8 halaman):** termasuk Landing Page, Menu/Katalog, Kreasikan Salad Sendiri, Checkout, Status Pesanan, dll — semua memakai komponen di bagian 4.1–4.6.

**Admin (7 halaman):** Dashboard, Kelola Menu, Kasir, Daftar Pesanan/Kelola Pesanan, Pengaturan Promo/Diskon, Laporan, Pengaturan Toko — semua memakai komponen di bagian 4.7–4.11.

---

## 7. Catatan

- Dokumen ini akan terus diperbarui seiring halaman-halaman baru didesain di Figma.
- Jika ada komponen baru yang muncul di halaman berikutnya dan berpotensi dipakai ulang, tambahkan ke bagian 4 (Komponen UI) alih-alih mendeskripsikannya sebagai bagian dari satu halaman saja.