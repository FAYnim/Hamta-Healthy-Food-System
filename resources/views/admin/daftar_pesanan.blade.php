<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan - Hamta Healthy Food</title>
    @include('partials.favicon')

    <!-- Tailwind CSS via Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

</head>
<body class="bg-hamta-cream text-hamta-black antialiased min-h-screen">

    <div class="min-h-screen flex flex-col lg:flex-row">

        <x-admin.sidebar />

        <!-- ========================================== -->
        <!-- 2. MAIN CONTENT AREA (Cream: #F7F2E7)      -->
        <!-- ========================================== -->
        <main class="flex-1 p-4 sm:p-6 md:p-8 lg:p-10 max-w-7xl">

            <!-- Topbar Section -->
            <header class="flex items-center justify-between mb-6 md:mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-hamta-black tracking-tight">Daftar Pesanan</h2>
                </div>

                <!-- Admin Profile Chip -->
                <div class="hidden lg:flex items-center">
                    <button type="button" class="bg-white hover:bg-stone-50 border border-stone-200/60 rounded-xl px-7 py-2.5 font-bold text-hamta-black text-base shadow-sm transition">
                        {{ session('admin_nama', 'Admin') }}
                    </button>
                </div>
            </header>

            <!-- Top Action / Filter Bar -->
            <div class="bg-white rounded-2xl p-3.5 sm:p-4 shadow-sm border border-stone-100 mb-6 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 md:gap-4">
                
                @php
                    $tabs = [
                        '' => 'Semua',
                        'diproses' => 'Diproses',
                        'siap' => 'Siap',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                    ];
                @endphp

                <!-- Status Tabs -->
                <div class="flex items-center gap-2 sm:gap-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0 scroll-smooth [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                    @foreach ($tabs as $nilai => $label)
                        @php $aktif = ($status ?? '') === $nilai; @endphp
                        <a href="{{ route('admin.daftar_pesanan', array_filter(['status' => $nilai, 'q' => $q ?: null])) }}"
                           class="px-3.5 sm:px-5 py-1.5 sm:py-2 rounded-xl text-xs sm:text-sm shrink-0 transition {{ $aktif ? 'bg-hamta-green text-white font-bold shadow-sm' : 'text-hamta-black hover:bg-stone-100 font-semibold' }}"
                           @if ($aktif) aria-current="page" @endif>
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <!-- Search Input -->
                <form method="GET" action="{{ route('admin.daftar_pesanan') }}" class="flex items-center gap-3.5 w-full md:w-auto justify-end">
                    @if ($status)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <div class="relative w-full md:w-64">
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Cari Pesanan"
                            class="w-full bg-white border border-stone-300 rounded-xl pl-4 pr-10 py-2.5 text-sm text-hamta-black placeholder-hamta-gray outline-none focus:border-hamta-green"
                        >
                        <button type="submit" class="absolute inset-y-0 right-0 pr-3.5 flex items-center" aria-label="Cari">
                            <x-lucide-search class="w-4 h-4 text-hamta-gray" />
                        </button>
                    </div>
                </form>

            </div>

            @if (session('success'))
                <div class="bg-hamta-green/15 border border-hamta-green text-hamta-dark text-sm font-semibold rounded-xl px-4 py-3 mb-6">
                    {{ session('success') }}
                </div>
            @elseif (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm font-semibold rounded-xl px-4 py-3 mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Table Container -->
            <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <!-- Table Header -->
                        <thead>
                            <tr class="bg-[#E5E5E5] text-hamta-black text-sm font-bold">
                                <th class="py-4 px-6">No. Pesanan</th>
                                <th class="py-4 px-6">Customer</th>
                                <th class="py-4 px-6">Cabang</th>
                                <th class="py-4 px-6">Total</th>
                                <th class="py-4 px-6">Catatan Pembeli</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6 text-center w-36">Aksi</th>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-stone-200 text-sm">
                            @forelse ($pesanan as $item)
                                @php
                                    $isBatal = $item->status_pesanan === 'batal';
                                    $isPendingPay = in_array($item->status_pembayaran, ['pending', 'menunggu_verifikasi'], true);

                                    if ($isBatal) {
                                        $badgeClass = 'bg-red-100 text-red-700';
                                    } elseif ($item->status_pesanan === 'diproses' && $isPendingPay) {
                                        $badgeClass = 'bg-amber-100 text-amber-800 border border-amber-300/80';
                                    } else {
                                        $badgeClass = 'bg-[#BEE3B6] text-hamta-dark';
                                    }
                                    $catatanItems = $item->detailPesanan->filter(fn ($d) => !empty(trim($d->catatan ?? '')));
                                @endphp
                                <tr class="hover:bg-stone-50/50 transition">
                                    <td class="py-5 px-6 font-semibold text-hamta-black">
                                        {{ $item->kode_pesanan }}
                                    </td>
                                    <td class="py-5 px-6 font-bold text-hamta-black text-base">
                                        {{ $item->nama_pelanggan ?: 'Walk-in' }}
                                    </td>
                                    <td class="py-5 px-6 font-medium text-hamta-black">
                                        {{ $item->cabang?->nama_cabang ?? '-' }}
                                    </td>
                                    <td class="cell-total py-5 px-6 font-bold text-hamta-black text-base">
                                        Rp {{ number_format((float) $item->total_bayar, 0, ',', '.') }}
                                    </td>
                                    <td class="cell-catatan py-5 px-6 max-w-xs">
                                        @if ($catatanItems->isNotEmpty())
                                            <div class="space-y-1.5">
                                                @foreach ($catatanItems as $catItem)
                                                    <div class="inline-flex items-start gap-1.5 bg-amber-50/90 border border-amber-200/80 text-amber-900 rounded-lg px-2.5 py-1 text-xs">
                                                        <x-lucide-clipboard-pen class="w-3.5 h-3.5 text-amber-700 shrink-0 mt-0.5" />
                                                        <span class="break-words">
                                                            <strong class="font-semibold text-stone-700">{{ $catItem->nama_item }}:</strong> {{ $catItem->catatan }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-stone-400 italic">- Tidak ada -</span>
                                        @endif
                                    </td>
                                    <td class="cell-status py-5 px-6">
                                        <div class="flex flex-col items-start gap-1">
                                            <span class="badge-status inline-block px-3 py-1 rounded-full font-semibold text-xs capitalize {{ $badgeClass }}">
                                                {{ $item->status_pesanan }}
                                            </span>

                                            <div class="badge-pay-wrap">
                                                @if (in_array($item->status_pembayaran, ['terverifikasi', 'settlement'], true))
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                                        <x-lucide-check-circle-2 class="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                                        <span>Lunas</span>
                                                    </span>
                                                @elseif ($item->status_pembayaran === 'pending')
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700">
                                                        <x-lucide-clock class="w-3.5 h-3.5 text-amber-600 shrink-0" />
                                                        <span>Menunggu Bayar</span>
                                                    </span>
                                                @elseif ($item->status_pembayaran === 'menunggu_verifikasi')
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-700">
                                                        <x-lucide-file-search class="w-3.5 h-3.5 text-blue-600 shrink-0" />
                                                        <span>Verifikasi TF</span>
                                                    </span>
                                                @elseif ($item->status_pembayaran === 'expired')
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600">
                                                        <x-lucide-timer-off class="w-3.5 h-3.5 text-red-500 shrink-0" />
                                                        <span>QR Kedaluwarsa</span>
                                                    </span>
                                                @elseif (in_array($item->status_pembayaran, ['ditolak', 'gagal'], true))
                                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600">
                                                        <x-lucide-x-circle class="w-3.5 h-3.5 text-red-500 shrink-0" />
                                                        <span>Bayar Gagal</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <button type="button"
                                                data-detail-url="{{ route('admin.daftar_pesanan.show', $item->id_pesanan) }}"
                                                class="btn-detail inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full border border-hamta-black text-hamta-black hover:bg-stone-100 font-semibold text-xs transition"
                                                aria-expanded="false">
                                            <x-lucide-eye class="w-4 h-4" />
                                            <span>Detail</span>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Baris loker: panel detail yang membuka tepat di bawah baris ini -->
                                <tr class="detail-row hidden">
                                    <td colspan="7" class="p-0 border-0">
                                        <div class="loker-collapse overflow-hidden" style="max-height: 0; opacity: 0;">
                                            <div class="detail-slot bg-white border-y border-stone-200" data-loaded="1">
                                                <div class="loker-wrap px-4 sm:px-6 py-4">
                                                    @include('admin.partials.pesanan_detail', ['pesanan' => $item])
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-14 px-6 text-center text-hamta-gray font-medium">
                                        Belum ada pesanan{{ $q !== '' ? ' yang cocok dengan pencarian.' : '.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($pesanan->hasPages())
                    <div class="px-6 py-4 border-t border-stone-200">
                        {{ $pesanan->links() }}
                    </div>
                @endif
            </div>

        </main>

    </div>

    <script>
        (function () {
            // Bungkus isi loker
            function bungkus(html) {
                return '<div class="loker-wrap px-4 sm:px-6 py-4">' + html + '</div>';
            }

            function tampilkanPesan(slot, pesan) {
                const wrap = slot.querySelector('.loker-wrap');
                if (!wrap) return;
                let p = wrap.querySelector('.loker-error');
                if (!p) {
                    p = document.createElement('p');
                    p.className = 'loker-error mt-3 text-sm font-semibold text-red-600';
                    wrap.appendChild(p);
                }
                p.textContent = pesan;
            }

            function bukaLoker(loker, tombol) {
                loker.classList.remove('hidden');
                tombol.setAttribute('aria-expanded', 'true');
                tombol.classList.add('bg-hamta-black', 'text-white');
                tombol.classList.remove('hover:bg-stone-100');

                const collapse = loker.querySelector('.loker-collapse');
                if (!collapse) return;

                // Animasi expand halus
                collapse.style.transition = 'max-height 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.28s ease';
                collapse.style.maxHeight = collapse.scrollHeight + 'px';
                collapse.style.opacity = '1';

                const onEnd = function (e) {
                    if (e.propertyName === 'max-height' && !loker.classList.contains('hidden')) {
                        collapse.style.maxHeight = 'none';
                        collapse.removeEventListener('transitionend', onEnd);
                    }
                };
                collapse.addEventListener('transitionend', onEnd);
            }

            function tutupLoker(loker, tombol) {
                tombol.setAttribute('aria-expanded', 'false');
                tombol.classList.remove('bg-hamta-black', 'text-white');
                tombol.classList.add('hover:bg-stone-100');

                const collapse = loker.querySelector('.loker-collapse');
                if (!collapse) {
                    loker.classList.add('hidden');
                    return;
                }

                // Tetapkan height awal dulu sebelum transisi ke 0
                collapse.style.maxHeight = collapse.scrollHeight + 'px';
                collapse.offsetHeight; // trigger reflow
                collapse.style.transition = 'max-height 0.25s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease';
                collapse.style.maxHeight = '0px';
                collapse.style.opacity = '0';

                const onEnd = function (e) {
                    if (e.propertyName === 'max-height') {
                        loker.classList.add('hidden');
                        collapse.removeEventListener('transitionend', onEnd);
                    }
                };
                collapse.addEventListener('transitionend', onEnd);
            }

            document.addEventListener('click', function (e) {
                const tombol = e.target.closest('.btn-detail');
                if (!tombol) return;

                const baris = tombol.closest('tr');
                const loker = baris.nextElementSibling; // <tr class="detail-row">
                if (!loker || !loker.classList.contains('detail-row')) return;

                const terbuka = !loker.classList.contains('hidden');

                // Klik lagi = tutup loker
                if (terbuka) {
                    tutupLoker(loker, tombol);
                    return;
                }

                // Tutup loker lain yang sedang terbuka
                document.querySelectorAll('.detail-row:not(.hidden)').forEach(function (r) {
                    const b = r.previousElementSibling && r.previousElementSibling.querySelector('.btn-detail');
                    if (b) tutupLoker(r, b);
                });

                // Buka loker ini dengan animasi smooth
                bukaLoker(loker, tombol);
            });

            // Submit form aksi di dalam loker (ajax) — loker tetap terbuka, isi disegarkan
            document.addEventListener('submit', function (e) {
                const form = e.target.closest('.loker-form');
                if (!form) return;
                e.preventDefault();

                const loker = form.closest('.detail-row');
                const slot = loker && loker.querySelector('.detail-slot');
                if (!slot) return;

                const tombol = form.querySelector('button[type="submit"]');
                if (tombol) tombol.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                    .then(function (res) {
                        if (res.status === 422) {
                            return res.json().then(function (data) {
                                const pesan = data.message || Object.values(data.errors || {}).flat().join(' ');
                                throw new Error(pesan || 'Data tidak valid.');
                            });
                        }
                        if (!res.ok) throw new Error('Gagal menyimpan perubahan.');
                        return res.text();
                    })
                    .then(function (html) {
                        slot.innerHTML = bungkus(html);
                        slot.dataset.loaded = '1';

                        // Sinkronkan badge status + total di baris tabel induk
                        try {
                            const root = slot.querySelector('[data-status]');
                            if (!root) return;
                            const barisInduk = loker.previousElementSibling;
                            if (!barisInduk || barisInduk.tagName !== 'TR') return;

                            const statusBaru = root.getAttribute('data-status');
                            const payBaru = root.getAttribute('data-pembayaran');
                            const isPendingPay = payBaru === 'pending' || payBaru === 'menunggu_verifikasi';

                            if (statusBaru) {
                                const badge = barisInduk.querySelector('.badge-status');
                                if (badge) {
                                    badge.textContent = statusBaru;
                                    if (statusBaru === 'batal') {
                                        badge.className = 'badge-status inline-block px-3 py-1 rounded-full font-semibold text-xs capitalize bg-red-100 text-red-700';
                                    } else if (statusBaru === 'diproses' && isPendingPay) {
                                        badge.className = 'badge-status inline-block px-3 py-1 rounded-full font-semibold text-xs capitalize bg-amber-100 text-amber-800 border border-amber-300/80';
                                    } else {
                                        badge.className = 'badge-status inline-block px-3 py-1 rounded-full font-semibold text-xs capitalize bg-[#BEE3B6] text-hamta-dark';
                                    }
                                }
                            }

                            if (payBaru) {
                                const payWrap = barisInduk.querySelector('.badge-pay-wrap');
                                if (payWrap) {
                                    if (payBaru === 'terverifikasi' || payBaru === 'settlement') {
                                        payWrap.innerHTML = '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700"><svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg><span>Lunas</span></span>';
                                    } else if (payBaru === 'pending') {
                                        payWrap.innerHTML = '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700"><svg class="w-3.5 h-3.5 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>Menunggu Bayar</span></span>';
                                    } else if (payBaru === 'menunggu_verifikasi') {
                                        payWrap.innerHTML = '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-700"><svg class="w-3.5 h-3.5 text-blue-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/><polyline points="14 2 14 8 20 8"/><path d="M5 17a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="m9 18-1.5-1.5"/></svg><span>Verifikasi TF</span></span>';
                                    } else if (payBaru === 'expired') {
                                        payWrap.innerHTML = '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600"><svg class="w-3.5 h-3.5 text-red-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="2" y1="2" x2="22" y2="22"/><path d="M12 2v2"/><path d="m19 5 1-1"/><path d="M22 12h-2"/><path d="m9 4 2-2"/><path d="M20.2 16.2A10 10 0 0 0 8 3.8"/><path d="M4 8a10 10 0 0 0 13.8 13.8"/></svg><span>QR Kedaluwarsa</span></span>';
                                    } else if (payBaru === 'ditolak' || payBaru === 'gagal') {
                                        payWrap.innerHTML = '<span class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600"><svg class="w-3.5 h-3.5 text-red-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg><span>Bayar Gagal</span></span>';
                                    }
                                }
                            }

                            const totalBaru = root.getAttribute('data-total');
                            if (totalBaru) {
                                const cellTotal = barisInduk.querySelector('.cell-total');
                                if (cellTotal) {
                                    cellTotal.textContent = 'Rp ' + Number(totalBaru).toLocaleString('id-ID', { maximumFractionDigits: 0 });
                                }
                            }
                        } catch (_) { /* partial lama tanpa data-status/total — abaikan */ }
                    })
                    .catch(function (err) {
                        if (tombol) tombol.disabled = false;
                        tampilkanPesan(slot, err.message || 'Gagal menyimpan perubahan.');
                    });
            });
        })();
    </script>

</body>
</html>
