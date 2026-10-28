<?php

namespace App\Services;

use App\Models\Promo;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;

class PromoService
{
    // Cek apakah 1 promo layak dipakai untuk pesanan ini:
    // aktif, dalam rentang tanggal, kuota masih ada, syarat min porsi terpenuhi,
    // dan promo gratis ongkir hanya berguna untuk pesanan delivery
    public function promoLayak(Promo $p, float $subtotal, int $totalPorsi, string $metodeAmbil = 'pickup'): bool
    {
        // Promo harus masih aktif
        if (!$p->status_aktif) {
            return false;
        }

        // Kuota pakai promo masih tersisa
        if ($p->jumlah_terpakai >= $p->batas_penggunaan) {
            return false;
        }

        // Gratis ongkir tidak ada gunanya untuk pesanan pickup (tidak ada ongkir),
        // jadi promo ini tidak ikut terpakai & kuotanya tidak terbuang percuma
        if ($p->tipe_potongan === 'gratis_ongkir' && $metodeAmbil !== 'delivery') {
            return false;
        }

        // Hari ini dalam rentang tanggal_mulai..tanggal_selesai (null = tanpa batas).
        // Cast tanggal jadi Carbon, jadi perbandingan aman walau format input beda.
        $hariIni = now()->startOfDay();

        if ($p->tanggal_mulai !== null && $hariIni->lt($p->tanggal_mulai->startOfDay())) {
            return false;
        }

        if ($p->tanggal_selesai !== null && $hariIni->gt($p->tanggal_selesai->endOfDay())) {
            return false;
        }

        // Syarat min porsi wajib terpenuhi bila diisi
        if ($p->syarat_min_porsi !== null && $totalPorsi < (int) $p->syarat_min_porsi) {
            return false;
        }

        return true;
    }

    // Hitung nilai potongan 1 promo terhadap sisa subtotal
    public function hitungPotongan(Promo $p, float $subtotal): float
    {
        // Gratis ongkir: potongan subtotal nol — nilainya dihitung terpisah dari tarif ongkir
        if ($p->tipe_potongan === 'gratis_ongkir') {
            return 0.0;
        }

        if ($p->tipe_potongan === 'persen') {
            $potongan = ((float) $p->nilai_potongan) * $subtotal / 100;
        } else {
            $potongan = (float) $p->nilai_potongan;
        }

        // Potongan tidak boleh melebihi sisa subtotal
        return round(min($potongan, $subtotal), 2);
    }

    // Terapkan semua promo layak ke 1 pesanan (boleh stacking):
    // urut potongan terbesar dulu, tiap promo memakai sisa subtotal setelah promo sebelumnya
    public function terapkanKePesanan(Pesanan $pesanan, int $totalPorsi): void
    {
        DB::transaction(function () use ($pesanan, $totalPorsi) {
            $subtotal = (float) $pesanan->subtotal;

            // Kumpulkan semua promo yang layak untuk pesanan ini.
            // Urutan id_promo ascending supaya 2 transaksi bersamaan mengunci baris
            // promo dengan urutan sama (cegah deadlock), lalu diurutkan ulang by
            // besar potongan untuk stacking.
            $promoLayak = Promo::where('status_aktif', true)
                ->orderBy('id_promo')
                ->get()
                ->filter(fn($p) => $this->promoLayak($p, $subtotal, $totalPorsi, (string) $pesanan->metode_ambil))
                // Urut dari potongan terbesar (dihitung terhadap subtotal penuh)
                ->sortByDesc(fn($p) => $this->hitungPotongan($p, $subtotal))
                ->values();

            // Tidak ada promo layak = pesanan dibiarkan tanpa diskon
            if ($promoLayak->isEmpty()) {
                return;
            }

            $sisaSubtotal = $subtotal;
            $totalPotongan = 0.0;

            foreach ($promoLayak as $promo) {
                // Stacking: setiap promo dihitung dari sisa subtotal setelah promo sebelumnya
                $potongan = $this->hitungPotongan($promo, $sisaSubtotal);

                // Promo selain gratis ongkir yang potongannya 0 (subtotal habis) tidak
                // dianggap terpakai supaya kuota tidak terbuang percuma
                if ($potongan <= 0 && $promo->tipe_potongan !== 'gratis_ongkir') {
                    continue;
                }

                // Slot kuota dipesan atomik: hanya berhasil kalau kuota belum habis,
                // jadi 2 transaksi bersamaan tidak bisa memakai slot terakhir yang sama
                if (!$this->pesanSlotKuota($promo)) {
                    continue;
                }

                // Catat pemakaian promo di junction dengan snapshot nilai potongan
                $pesanan->promo()->attach($promo->id_promo, [
                    'nilai_potongan' => $potongan,
                ]);

                $totalPotongan += $potongan;
                $sisaSubtotal = round($sisaSubtotal - $potongan, 2);
            }

            // Total tagihan akhir = subtotal - diskon + ongkir
            // (ongkir delivery bisa NULL karena menunggu konfirmasi admin, dianggap 0 dulu)
            $pesanan->update([
                'total_diskon' => round($totalPotongan, 2),
                'total_bayar' => round($subtotal - $totalPotongan + ((float) ($pesanan->ongkir ?? 0)), 2),
            ]);
        });
    }

    // Hitung ulang diskon & total bayar pesanan.
    // Dipakai saat data pesanan berubah: ongkir dikonfirmasi admin, atau item dibatalkan
    // (subtotal mengecil → promo persen ikut menyesuaikan, total bayar tidak jadi negatif).
    public function terapkanUlang(Pesanan $pesanan, ?float $subtotalBaru = null): void
    {
        if ($subtotalBaru !== null) {
            $pesanan->update(['subtotal' => round($subtotalBaru, 2)]);
        }

        $ongkir = (float) ($pesanan->ongkir ?? 0);
        $sisaSubtotal = (float) $pesanan->subtotal;
        $totalDiskon = 0.0;
        $ongkirSudahDipakai = false;

        // Urutan promo mengikuti urutan stacking saat promo diterapkan (potongan terbesar dulu)
        foreach ($this->promoTerpakai($pesanan) as $promo) {
            if ($promo->tipe_potongan === 'gratis_ongkir') {
                // Nilainya sama dengan tarif ongkir yang dibebaskan (baru diketahui
                // setelah admin mengonfirmasi). Hanya promo gratis ongkir pertama yang
                // membebaskan ongkir, promo berikutnya bernilai 0.
                $nilai = $ongkirSudahDipakai ? 0.0 : $ongkir;
                $ongkirSudahDipakai = true;
            } else {
                $nilai = $this->hitungPotongan($promo, $sisaSubtotal);
                $sisaSubtotal = round($sisaSubtotal - $nilai, 2);
            }

            $totalDiskon += $nilai;

            // Snapshot di junction ikut diperbarui supaya riwayat pesanan akurat
            if (round((float) $promo->pivot->nilai_potongan, 2) !== round($nilai, 2)) {
                $pesanan->promo()->updateExistingPivot($promo->id_promo, ['nilai_potongan' => $nilai]);
            }
        }

        $pesanan->update([
            'total_diskon' => round($totalDiskon, 2),
            'total_bayar' => max(0, round((float) $pesanan->subtotal - $totalDiskon + $ongkir, 2)),
        ]);
    }

    // Nonaktifkan promo otomatis kalau kuota pakainya sudah habis.
    // Dipakai oleh service ini & PromoController (sumber kebenaran tunggal).
    public function nonaktifkanJikaHabis(Promo $promo): void
    {
        if ($promo->jumlah_terpakai >= $promo->batas_penggunaan) {
            $promo->update(['status_aktif' => false]);
        }
    }

    // Pesan 1 slot kuota promo secara atomik (update bersyarat) supaya 2 transaksi
    // bersamaan tidak bisa memakai slot kuota terakhir yang sama.
    // Return true kalau slot berhasil dipesan, false kalau kuota sudah habis.
    private function pesanSlotKuota(Promo $promo): bool
    {
        $slot = DB::table('promo')
            ->where('id_promo', $promo->id_promo)
            ->whereColumn('jumlah_terpakai', '<', 'batas_penggunaan')
            ->increment('jumlah_terpakai');

        if ($slot === 0) {
            return false;
        }

        $promo->refresh();
        $this->nonaktifkanJikaHabis($promo);

        return true;
    }

    // Promo yang menempel ke pesanan, urut dari potongan terbesar terhadap subtotal
    // sekarang (sama seperti urutan stacking saat promo diterapkan).
    // ponytail: urutan diturunkan dari nilai potongan, bukan kolom urutan terpisah.
    private function promoTerpakai(Pesanan $pesanan)
    {
        $subtotal = (float) $pesanan->subtotal;

        // load() (bukan loadMissing) supaya nilai pivot selalu terbaru dari DB
        return $pesanan->load('promo')
            ->promo
            ->sortByDesc(fn($p) => $this->hitungPotongan($p, $subtotal))
            ->values();
    }
}
