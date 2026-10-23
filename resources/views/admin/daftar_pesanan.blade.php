<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan - Hamta Healthy Food</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>
<body class="bg-[#F7F2E7] text-[#010101] antialiased min-h-screen">

    <div class="min-h-screen flex flex-col lg:flex-row">

        <x-admin.sidebar />

        <!-- ========================================== -->
        <!-- 2. MAIN CONTENT AREA (Cream: #F7F2E7)      -->
        <!-- ========================================== -->
        <main class="flex-1 p-6 md:p-8 lg:p-10 max-w-7xl">

            <!-- Topbar Section -->
            <header class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-[#010101] tracking-tight">Daftar Pesanan</h2>
                </div>

                <!-- Admin Profile Chip -->
                <div class="hidden lg:flex items-center">
                    <button type="button" class="bg-white hover:bg-stone-50 border border-stone-200/60 rounded-xl px-7 py-2.5 font-bold text-[#010101] text-base shadow-sm transition">
                        Yunita
                    </button>
                </div>
            </header>

            <!-- Top Action / Filter Bar -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
                
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
                <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto">
                    @foreach ($tabs as $nilai => $label)
                        @php $aktif = ($status ?? '') === $nilai; @endphp
                        <a href="{{ route('admin.daftar_pesanan', array_filter(['status' => $nilai, 'q' => $q ?: null])) }}"
                           class="px-5 py-2 rounded-xl text-sm shrink-0 transition {{ $aktif ? 'bg-[#65BD5E] text-white font-bold shadow-sm' : 'text-[#010101] hover:bg-stone-100 font-semibold' }}"
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
                            class="w-full bg-white border border-stone-300 rounded-xl pl-4 pr-10 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E]"
                        >
                        <button type="submit" class="absolute inset-y-0 right-0 pr-3.5 flex items-center" aria-label="Cari">
                            <x-lucide-search class="w-4 h-4 text-[#8A8A8A]" />
                        </button>
                    </div>
                </form>

            </div>

            @if (session('success'))
                <div class="bg-[#65BD5E]/15 border border-[#65BD5E] text-[#0B412B] text-sm font-semibold rounded-xl px-4 py-3 mb-6">
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
                            <tr class="bg-[#E5E5E5] text-[#010101] text-sm font-bold">
                                <th class="py-4 px-6">No. Pesanan</th>
                                <th class="py-4 px-6">Customer</th>
                                <th class="py-4 px-6">Cabang</th>
                                <th class="py-4 px-6">Total</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6 text-center w-36">Aksi</th>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-stone-200 text-sm">
                            @forelse ($pesanan as $item)
                                @php
                                    $badge = $item->status_pesanan === 'batal'
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-[#BEE3B6] text-[#0B412B]';
                                @endphp
                                <tr class="hover:bg-stone-50/50 transition">
                                    <td class="py-5 px-6 font-semibold text-[#010101]">
                                        {{ $item->kode_pesanan }}
                                    </td>
                                    <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                        {{ $item->nama_pelanggan ?: 'Walk-in' }}
                                    </td>
                                    <td class="py-5 px-6 font-medium text-[#010101]">
                                        {{ $item->cabang?->nama_cabang ?? '-' }}
                                    </td>
                                    <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                        Rp {{ number_format((float) $item->total_bayar, 0, ',', '.') }}
                                    </td>
                                    <td class="py-5 px-6">
                                        <span class="inline-block px-3 py-1 rounded-full font-semibold text-xs capitalize {{ $badge }}">
                                            {{ $item->status_pesanan }}
                                        </span>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <button type="button"
                                                data-detail-url="{{ route('admin.daftar_pesanan.show', $item->id_pesanan) }}"
                                                class="btn-detail inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full border border-[#010101] text-[#010101] hover:bg-stone-100 font-semibold text-xs transition"
                                                aria-expanded="false">
                                            <x-lucide-eye class="w-4 h-4" />
                                            <span>Detail</span>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Baris loker: panel detail yang membuka tepat di bawah baris ini -->
                                <tr class="detail-row hidden">
                                    <td colspan="6" class="p-0">
                                        <div class="detail-slot bg-white border-y border-stone-200">
                                            <div class="p-10 text-center text-sm text-[#8A8A8A]">Memuat detail…</div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-14 px-6 text-center text-[#8A8A8A] font-medium">
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

            document.addEventListener('click', function (e) {
                const tombol = e.target.closest('.btn-detail');
                if (!tombol) return;

                const baris = tombol.closest('tr');
                const loker = baris.nextElementSibling; // <tr class="detail-row">
                if (!loker || !loker.classList.contains('detail-row')) return;

                const slot = loker.querySelector('.detail-slot');
                const terbuka = !loker.classList.contains('hidden');

                // Klik lagi = tutup loker
                if (terbuka) {
                    loker.classList.add('hidden');
                    tombol.setAttribute('aria-expanded', 'false');
                    return;
                }

                // Tutup loker lain yang sedang terbuka
                document.querySelectorAll('.detail-row:not(.hidden)').forEach(function (r) {
                    r.classList.add('hidden');
                    const b = r.previousElementSibling && r.previousElementSibling.querySelector('.btn-detail');
                    if (b) b.setAttribute('aria-expanded', 'false');
                });

                loker.classList.remove('hidden');
                tombol.setAttribute('aria-expanded', 'true');

                // Sudah pernah dimuat → tidak perlu fetch ulang
                if (slot.dataset.loaded === '1') return;

                slot.innerHTML = '<div class="p-10 text-center text-sm text-[#8A8A8A]">Memuat detail…</div>';

                fetch(tombol.dataset.detailUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (res) {
                        if (!res.ok) throw new Error('Gagal memuat');
                        return res.text();
                    })
                    .then(function (html) {
                        slot.innerHTML = bungkus(html);
                        slot.dataset.loaded = '1';
                    })
                    .catch(function () {
                        slot.innerHTML = '<div class="p-10 text-center text-sm text-red-600">Gagal memuat detail pesanan.</div>';
                    });
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
                            const tds = barisInduk.querySelectorAll('td');
                            const statusBaru = root.getAttribute('data-status');
                            if (statusBaru && tds.length > 4) {
                                const badge = tds[4].querySelector('span');
                                if (badge) {
                                    badge.textContent = statusBaru;
                                    badge.className = 'inline-block px-3 py-1 rounded-full font-semibold text-xs capitalize ' +
                                        (statusBaru === 'batal' ? 'bg-red-100 text-red-700' : 'bg-[#BEE3B6] text-[#0B412B]');
                                }
                            }
                            const totalBaru = root.getAttribute('data-total');
                            if (totalBaru && tds.length > 3) {
                                const cell = tds[3];
                                cell.textContent = 'Rp ' + Number(totalBaru).toLocaleString('id-ID', { maximumFractionDigits: 0 });
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
