<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Hamta Healthy Food</title>

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

            <!-- Topbar / Header Section -->
            <header class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-[#010101] tracking-tight">Dashboard</h2>
                </div>

                <!-- Admin Profile Chip -->
                <div class="hidden lg:flex items-center">
                    <a href="{{ route('admin.pengaturan') }}" class="bg-white hover:bg-stone-50 border border-stone-200/60 rounded-xl px-7 py-2.5 font-bold text-[#010101] text-base shadow-sm transition">
                        {{ session('admin_nama', 'Admin') }}
                    </a>
                </div>
            </header>

            @if ($jumlah_produk_sold_out > 0)
                <a href="{{ route('admin.kelola_menu') }}" class="mb-6 flex items-center justify-between gap-4 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-800" role="alert">
                    <span>{{ $jumlah_produk_sold_out }} produk sedang sold-out dan tidak tersedia untuk dipesan.</span>
                    <span class="shrink-0 underline">Kelola Menu</span>
                </a>
            @endif

            <!-- Stat Cards Row (4 Cards) -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 flex flex-col justify-between">
                    <div class="flex items-center gap-3.5"><div class="w-11 h-11 rounded-full bg-[#0B412B] text-white flex items-center justify-center shrink-0"><x-lucide-shopping-bag class="w-5 h-5" /></div><span class="text-[#010101] font-semibold text-base">Total Pesanan</span></div>
                    <div class="mt-6 mb-2 text-center"><span class="text-4xl font-extrabold text-[#010101] tracking-tight">{{ $jumlah_pesanan_hari_ini }}</span></div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 flex flex-col justify-between">
                    <div class="flex items-center gap-3.5"><div class="w-11 h-11 rounded-full bg-[#0B412B] text-white flex items-center justify-center shrink-0"><x-lucide-chart-column-increasing class="w-5 h-5" /></div><span class="text-[#010101] font-semibold text-base">Penjualan</span></div>
                    <div class="mt-6 mb-2 text-center"><span class="text-3xl lg:text-[28px] xl:text-3xl font-extrabold text-[#010101] tracking-tight">Rp {{ number_format((float) $pendapatan_hari_ini, 0, ',', '.') }}</span></div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 flex flex-col justify-between">
                    <div class="flex items-center gap-3.5"><div class="w-11 h-11 rounded-full bg-[#0B412B] text-white flex items-center justify-center shrink-0"><x-lucide-clock class="w-5 h-5" /></div><span class="text-[#010101] font-semibold text-base leading-tight">Menunggu<br class="hidden sm:inline"> Deposit</span></div>
                    <div class="mt-6 mb-2 text-center"><span class="text-4xl font-extrabold text-[#010101] tracking-tight">{{ $jumlah_menunggu_deposit }}</span></div>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 flex flex-col justify-between">
                    <div class="flex items-center gap-3.5"><div class="w-11 h-11 rounded-full bg-[#0B412B] text-white flex items-center justify-center shrink-0"><x-lucide-check class="w-5 h-5" /></div><span class="text-[#010101] font-semibold text-base leading-tight">Pesanan<br class="hidden sm:inline"> Selesai</span></div>
                    <div class="mt-6 mb-2 text-center"><span class="text-4xl font-extrabold text-[#010101] tracking-tight">{{ $jumlah_pesanan_selesai }}</span></div>
                </div>
            </section>

            <!-- Bottom Content Grid: Chart + Notifications -->
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

                <!-- Left Card: Grafik Penjualan 7 Hari Terakhir -->
                <div class="bg-white rounded-2xl p-6 md:p-7 shadow-sm border border-stone-100 flex flex-col">
                    <h3 class="text-xl font-bold text-[#010101] mb-6 tracking-tight">Grafik Penjualan 7 Hari Terakhir</h3>

                    <div class="relative h-72 w-full">
                        <canvas id="dashboard-sales-chart"
                                aria-label="Grafik penjualan 7 hari terakhir"
                                role="img"
                                data-labels='@json($grafik_penjualan->pluck("label")->values())'
                                data-values='@json($grafik_penjualan->pluck("total")->values())'></canvas>
                    </div>
                </div>

                <!-- Right Card: Notifikasi Terbaru -->
                <div class="bg-white rounded-2xl p-6 md:p-7 shadow-sm border border-stone-100 flex flex-col">
                    <h3 class="text-xl font-bold text-[#010101] mb-2 tracking-tight">Notifikasi Terbaru</h3>

                    <div class="flex-1 flex flex-col divide-y divide-[#8A8A8A]/25">
                        @forelse ($notifikasi_terbaru as $pesanan)
                            @php
                                $ringkasanItem = $pesanan->detailPesanan->map(fn ($item) => $item->jumlah . 'x ' . $item->nama_item)->implode(' + ');
                            @endphp
                            <a href="{{ route('admin.daftar_pesanan', ['detail' => $pesanan->id_pesanan]) }}" class="py-3.5 first:pt-2 block hover:bg-stone-50/70 rounded-lg px-2 -mx-2">
                                <div class="flex items-baseline justify-between gap-4">
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-[#010101] text-base leading-snug truncate">{{ $pesanan->nama_pelanggan }}</h4>
                                        <p class="text-sm text-[#8A8A8A] font-normal mt-0.5 truncate">{{ $ringkasanItem ?: 'Tidak ada item aktif' }}</p>
                                    </div>
                                    <span class="text-xs font-semibold text-[#010101] shrink-0">{{ $pesanan->created_at->format('H:i') }}</span>
                                </div>
                            </a>
                        @empty
                            <p class="py-6 text-sm text-[#8A8A8A]">Belum ada pesanan.</p>
                        @endforelse
                    </div>
                </div>

            </section>

        </main>

    </div>

</body>
</html>
