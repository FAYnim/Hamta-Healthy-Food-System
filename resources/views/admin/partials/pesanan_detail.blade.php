@php
    $rp = fn($nilai) => 'Rp ' . number_format((float) $nilai, 0, ',', '.');

    // Format nomor WhatsApp internasional pelanggan
    $cleanWa = null;
    if ($pesanan->no_whatsapp) {
        $c = preg_replace('/[^0-9]/', '', $pesanan->no_whatsapp);
        if (str_starts_with($c, '0')) {
            $cleanWa = '62' . substr($c, 1);
        } elseif (!str_starts_with($c, '62') && str_starts_with($c, '8')) {
            $cleanWa = '62' . $c;
        } else {
            $cleanWa = $c;
        }
    }

    $customerTrackingUrl = route('customer.status_pesanan', $pesanan->kode_pesanan);
    $namaCust = $pesanan->nama_pelanggan ?: 'Pelanggan';

    // Template pesan WhatsApp berdasarkan status
    if ($pesanan->status_pesanan === 'siap') {
        $waText = "Halo kak {$namaCust}! ✨\nKabar gembira, pesanan Anda *#{$pesanan->kode_pesanan}* di *Hamta Healthy Food* sekarang sudah *SIAP* " . ($pesanan->metode_ambil === 'delivery' ? '(sedang bersiap diantar kurir)' : '(sudah bisa diambil di outlet kami)') . "!\n\nLihat status pesanan live:\n{$customerTrackingUrl}\n\nSelamat menikmati makanan sehat dari Hamta! 🥗";
    } elseif ($pesanan->status_pesanan === 'selesai') {
        $waText = "Halo kak {$namaCust}! 🎉\nPesanan *#{$pesanan->kode_pesanan}* telah *SELESAI*. Terima kasih banyak telah memesan di *Hamta Healthy Food*!\n\nSemoga harimu menyenangkan & menu kami menyegarkan! Pantau riwayat dan beri ulasan di sini:\n{$customerTrackingUrl}";
    } elseif ($pesanan->status_pesanan === 'batal') {
        $waText = "Halo kak {$namaCust},\nMohon maaf pesanan *#{$pesanan->kode_pesanan}* di *Hamta Healthy Food* tidak dapat diproses / dibatalkan.\n\nJika ada pertanyaan atau konfirmasi pengembalian dana, silakan balas pesan ini. Terima kasih 🙏";
    } else {
        $waText = "Halo kak {$namaCust}! 🥗\nPesanan Anda di *Hamta Healthy Food* dengan nomor *#{$pesanan->kode_pesanan}* telah kami terima dan *SEDANG DIPROSES* di dapur kami.\n\n• Total: {$rp($pesanan->total_bayar)}\n• Pengambilan: " . ($pesanan->metode_ambil === 'delivery' ? 'Delivery' : 'Pickup') . "\n\nPantau status pesanan Anda secara live di tautan berikut:\n{$customerTrackingUrl}\n\nTerima kasih telah memilih hidup sehat bersama Hamta! 🥬";
    }

    $waLink = $cleanWa ? 'https://wa.me/' . $cleanWa . '?text=' . rawurlencode($waText) : null;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 py-5" data-status="{{ $pesanan->status_pesanan }}" data-total="{{ $pesanan->total_bayar }}">

    <!-- Kolom Kiri: Daftar item + total (kartu hijau muda) -->
    <div class="bg-[#BEE3B6] rounded-2xl p-5">
        <p class="text-base font-bold text-hamta-black mb-3">Daftar Pesanan</p>

        <ul class="text-sm text-hamta-black">
            @foreach ($pesanan->detailPesanan as $item)
                <li class="flex items-baseline gap-2 py-1 {{ $item->status_item === 'dibatalkan' ? 'opacity-60 line-through' : '' }}">
                    <span class="shrink-0">•</span>
                    <span class="font-semibold shrink-0">{{ $item->nama_item }}</span>
                    <span class="shrink-0">{{ $item->jumlah }}x {{ $rp($item->harga_satuan) }}</span>
                    <span class="flex-1 border-b border-hamta-black/25 translate-y-[-2px]"></span>
                    <span class="font-semibold shrink-0">{{ $rp($item->subtotal_item) }}</span>
                    @if ($item->status_item !== 'dibatalkan')
                        <form method="POST" action="{{ route('admin.daftar_pesanan.item.batal', $item->id_detail) }}" class="loker-form inline" onsubmit="return confirm('Batalkan item {{ $item->nama_item }}?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Batalkan</button>
                        </form>
                    @endif
                </li>

                @if ($item->komponenSalad->isNotEmpty())
                    <li class="pl-6 text-xs text-hamta-black/70 pb-1">
                        {{ $item->komponenSalad->pluck('nama_komponen')->implode(', ') }}
                    </li>
                @endif
            @endforeach
        </ul>

        <div class="mt-2 pt-1 border-t border-hamta-black/25 text-sm space-y-1">
            <div class="flex items-baseline gap-2">
                <span class="font-semibold text-hamta-black">Subtotal</span>
                <span class="flex-1"></span>
                <span class="font-semibold text-hamta-black">{{ $rp($pesanan->subtotal) }}</span>
            </div>
            @if ($pesanan->total_diskon > 0)
                <div class="flex items-baseline gap-2">
                    <span class="font-semibold text-hamta-black">Diskon</span>
                    <span class="flex-1"></span>
                    <span class="font-semibold text-hamta-black">-{{ $rp($pesanan->total_diskon) }}</span>
                </div>
                {{-- Promo yang terpakai, biar admin tahu diskonnya datang dari promo mana --}}
                @foreach ($pesanan->promo as $promoTerpakai)
                    <div class="flex items-baseline gap-2 text-xs text-hamta-gray">
                        <span class="shrink-0">• {{ $promoTerpakai->nama_promo }}</span>
                        <span class="flex-1"></span>
                        <span class="shrink-0">-{{ $rp($promoTerpakai->pivot->nilai_potongan) }}</span>
                    </div>
                @endforeach
            @endif
            @if ($pesanan->metode_ambil === 'delivery' && $pesanan->ongkir != null)
                <div class="flex items-baseline gap-2">
                    <span class="font-semibold text-hamta-black">Ongkir</span>
                    <span class="flex-1"></span>
                    <span class="font-semibold text-hamta-black">{{ $rp($pesanan->ongkir) }}</span>
                </div>
            @endif
            <div class="flex items-baseline gap-2">
                <span class="font-semibold text-hamta-black">Total</span>
                <span class="flex-1"></span>
                <span class="font-bold text-hamta-black" data-total-display="{{ $pesanan->total_bayar }}">{{ $rp($pesanan->total_bayar) }}</span>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Info pembayaran & status -->
    <div class="text-sm text-hamta-black space-y-4 py-1">
        <p><span class="font-bold">Metode Pembayaran:</span> {{ strtoupper($pesanan->metode_bayar) }}</p>

        @if ($pesanan->midtrans_order_id)
            <p><span class="font-bold">Midtrans Order ID:</span> <code class="text-xs bg-stone-100 px-2 py-0.5 rounded text-emerald-800">{{ $pesanan->midtrans_order_id }}</code></p>
        @endif

        @if ($pesanan->bukti_bayar)
            <div class="flex items-center gap-2">
                <span class="font-bold">Bukti Transfer:</span>
                <a href="{{ asset('storage/' . $pesanan->bukti_bayar) }}" target="_blank" class="text-xs font-bold text-[#0B412B] underline hover:text-hamta-green">
                    Lihat Foto Bukti Bayar ↗
                </a>
            </div>
        @endif

        <p><span class="font-bold">No WhatsApp:</span> {{ $pesanan->no_whatsapp ?: '-' }}</p>

        @if ($pesanan->metode_ambil === 'delivery')
            <p><span class="font-bold">Alamat Pengiriman:</span> {{ $pesanan->alamat_delivery ?: '-' }}</p>
        @else
            <p><span class="font-bold">Cabang:</span> {{ $pesanan->cabang?->nama_cabang ?? '-' }}</p>
        @endif

        @if ($pesanan->metode_ambil === 'delivery')
            <p><span class="font-bold">Ongkir:</span> {{ $rp($pesanan->ongkir) }} <span class="text-xs text-emerald-700 font-bold ml-1">✓ Terkonfirmasi</span></p>
        @endif

        <div>
            <p class="font-bold mb-1.5">Status Pembayaran:</p>
            <form method="POST" action="{{ route('admin.daftar_pesanan.pembayaran', $pesanan->id_pesanan) }}" class="loker-form inline-block">
                @csrf
                @method('PATCH')
                <select name="status_pembayaran"
                        onchange="this.form.requestSubmit()"
                        class="w-56 bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm font-medium text-hamta-black outline-none cursor-pointer">
                    <option value="menunggu_verifikasi" @selected($pesanan->status_pembayaran === 'menunggu_verifikasi')>Menunggu Verifikasi</option>
                    <option value="pending" @selected($pesanan->status_pembayaran === 'pending')>Pending (Menunggu Bayar)</option>
                    <option value="terverifikasi" @selected($pesanan->status_pembayaran === 'terverifikasi')>Terverifikasi (Lunas)</option>
                    <option value="settlement" @selected($pesanan->status_pembayaran === 'settlement')>Settlement (Lunas Midtrans)</option>
                    <option value="ditolak" @selected($pesanan->status_pembayaran === 'ditolak')>Ditolak</option>
                    <option value="expired" @selected($pesanan->status_pembayaran === 'expired')>Expired</option>
                    <option value="gagal" @selected($pesanan->status_pembayaran === 'gagal')>Gagal</option>
                </select>
            </form>
        </div>

        <div>
            <p class="font-bold mb-1.5">Status Pesanan:</p>
            <form method="POST" action="{{ route('admin.daftar_pesanan.status', $pesanan->id_pesanan) }}" class="loker-form inline-block">
                @csrf
                @method('PATCH')
                <select name="status_pesanan"
                        onchange="this.form.requestSubmit()"
                        class="w-56 bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm font-medium text-hamta-black outline-none cursor-pointer">
                    @foreach (['diproses' => 'Diproses (Sedang Disiapkan)', 'siap' => 'Siap (Ambil / Antar)', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $nilai => $label)
                        <option value="{{ $nilai }}" @selected($pesanan->status_pesanan === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="pt-3 border-t border-stone-200">
            <p class="font-bold text-xs text-stone-500 uppercase tracking-wider mb-2">Koneksi &amp; Notifikasi Pelanggan:</p>
            <div class="flex flex-wrap items-center gap-2">
                @if ($waLink)
                    <a href="{{ $waLink }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#25D366] hover:bg-[#20bd5a] text-white font-bold text-xs rounded-xl shadow-sm transition">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.41 8.86 7.65C8.63 7.9 7.99 8.5 7.99 9.71C7.99 10.93 8.88 12.1 9 12.27C9.13 12.44 10.74 14.92 13.2 15.97C13.78 16.22 14.24 16.38 14.6 16.49C15.19 16.68 15.72 16.65 16.14 16.59C16.61 16.51 17.58 16 17.79 15.41C17.99 14.83 17.99 14.33 17.93 14.22C17.87 14.12 17.7 14.05 17.44 13.93C17.19 13.8 15.93 13.18 15.7 13.1C15.47 13.01 15.3 12.97 15.13 13.22C14.96 13.47 14.47 14.05 14.33 14.22C14.18 14.38 14.04 14.4 13.79 14.27C13.54 14.15 12.72 13.88 11.75 13.02C10.99 12.35 10.48 11.51 10.33 11.26C10.19 11.01 10.31 10.88 10.44 10.75C10.55 10.64 10.69 10.46 10.82 10.31C10.95 10.16 11 10.05 11.08 9.89C11.16 9.72 11.12 9.57 11.06 9.45C11 9.33 10.5 8.12 10.3 7.62C10.1 7.15 9.9 7.21 9.74 7.21C9.6 7.21 9.43 7.21 9.26 7.21L9.53 7.34Z"/></svg>
                        <span>Beri Tahu Pelanggan via WhatsApp</span>
                    </a>
                @else
                    <span class="text-xs text-stone-400 italic">(Nomor WhatsApp pelanggan tidak tersedia)</span>
                @endif

                <a href="{{ $customerTrackingUrl }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-stone-100 hover:bg-stone-200 text-hamta-black font-semibold text-xs rounded-xl transition">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    <span>Buka Live Tracker Pelanggan ↗</span>
                </a>
            </div>
        </div>
    </div>

</div>
