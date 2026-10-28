@php
    $rp = fn($nilai) => 'Rp ' . number_format((float) $nilai, 0, ',', '.');
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

        <p><span class="font-bold">No WhatsApp:</span> {{ $pesanan->no_whatsapp ?: '-' }}</p>

        @if ($pesanan->metode_ambil === 'delivery')
            <p><span class="font-bold">Alamat Pengiriman:</span> {{ $pesanan->alamat_delivery ?: '-' }}</p>
        @else
            <p><span class="font-bold">Cabang:</span> {{ $pesanan->cabang?->nama_cabang ?? '-' }}</p>
        @endif

        @if ($pesanan->metode_ambil === 'delivery')
            @if ($pesanan->status_ongkir === 'menunggu_konfirmasi')
                <div>
                    <p class="font-bold mb-1.5">Konfirmasi Ongkir:</p>
                    <form method="POST" action="{{ route('admin.daftar_pesanan.ongkir', $pesanan->id_pesanan) }}" class="loker-form flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="number" name="ongkir" step="0.01" min="0" required
                               value="{{ $pesanan->ongkir ?? '' }}"
                               placeholder="Nominal ongkir"
                               class="w-40 bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-hamta-black outline-none focus:border-hamta-green">
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-hamta-green text-white text-sm font-bold hover:opacity-90 transition">Konfirmasi</button>
                    </form>
                </div>
            @elseif ($pesanan->status_ongkir === 'terkonfirmasi')
                <p><span class="font-bold">Ongkir:</span> {{ $rp($pesanan->ongkir) }}</p>
            @endif
        @endif

        <div>
            <p class="font-bold mb-1.5">Status Pesanan:</p>
            <form method="POST" action="{{ route('admin.daftar_pesanan.status', $pesanan->id_pesanan) }}" class="loker-form inline-block">
                @csrf
                @method('PATCH')
                <select name="status_pesanan"
                        onchange="this.form.requestSubmit()"
                        class="w-56 bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm font-medium text-hamta-black outline-none cursor-pointer">
                    @foreach (['diproses' => 'Diproses', 'siap' => 'Siap', 'selesai' => 'Selesai', 'batal' => 'Batal'] as $nilai => $label)
                        <option value="{{ $nilai }}" @selected($pesanan->status_pesanan === $nilai)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

</div>
