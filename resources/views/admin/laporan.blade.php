<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Hamta Healthy Food</title>

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
        <main class="flex-1 p-6 md:p-8 lg:p-10 max-w-7xl">

            <!-- Topbar Section -->
            <header class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-hamta-black tracking-tight">Laporan Penjualan</h2>
                </div>

                <!-- Admin Profile Chip -->
                <div class="hidden lg:flex items-center">
                    <button type="button" class="bg-white hover:bg-stone-50 border border-stone-200/60 rounded-xl px-7 py-2.5 font-bold text-hamta-black text-base shadow-sm transition">
                        {{ session('admin_nama', 'Admin') }}
                    </button>
                </div>
            </header>

            <!-- Filter Rentang Tanggal -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 mb-6">
                <form method="GET" action="{{ route('admin.laporan') }}" class="flex flex-col md:flex-row items-end gap-4">
                    <div class="flex-1 min-w-0">
                        <label for="tanggal_mulai" class="block text-sm font-semibold text-hamta-black mb-1.5">Dari Tanggal</label>
                        <input
                            type="date"
                            id="tanggal_mulai"
                            name="tanggal_mulai"
                            value="{{ $tanggal_mulai }}"
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-hamta-black outline-none focus:border-hamta-green focus:ring-1 focus:ring-hamta-green"
                        >
                    </div>

                    <div class="flex-1 min-w-0">
                        <label for="tanggal_selesai" class="block text-sm font-semibold text-hamta-black mb-1.5">Sampai Tanggal</label>
                        <input
                            type="date"
                            id="tanggal_selesai"
                            name="tanggal_selesai"
                            value="{{ $tanggal_selesai }}"
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-hamta-black outline-none focus:border-hamta-green focus:ring-1 focus:ring-hamta-green"
                        >
                    </div>

                    <div class="shrink-0">
                        <button
                            type="submit"
                            class="bg-hamta-green hover:bg-[#58a851] text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-sm transition"
                        >
                            Tampilkan
                        </button>
                    </div>
                </form>

                <!-- Periode aktif yang sedang ditampilkan -->
                <p class="mt-3 text-xs text-hamta-gray">
                    Periode:
                    <span class="font-semibold text-hamta-black">
                        {{ \Carbon\Carbon::parse($tanggal_mulai)->translatedFormat('d M Y') }}
                        &ndash;
                        {{ \Carbon\Carbon::parse($tanggal_selesai)->translatedFormat('d M Y') }}
                    </span>
                    &middot; hanya transaksi dengan pembayaran <span class="font-semibold">terverifikasi</span>, item <span class="font-semibold">aktif</span>
                </p>
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
                                <th class="py-4 px-6">#</th>
                                <th class="py-4 px-6">Nama Item</th>
                                <th class="py-4 px-6 text-center w-40">Jumlah Terjual</th>
                                <th class="py-4 px-6 text-right w-52">Total Pendapatan</th>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-stone-200 text-sm">
                            @forelse ($laporan as $i => $item)
                                <tr class="hover:bg-stone-50/50 transition">
                                    <td class="py-5 px-6 text-hamta-gray font-medium">
                                        {{ $i + 1 }}
                                    </td>
                                    <td class="py-5 px-6 font-bold text-hamta-black text-base">
                                        {{ $item->nama_item }}
                                    </td>
                                    <td class="py-5 px-6 text-center font-semibold text-hamta-black">
                                        {{ number_format((int) $item->total_terjual, 0, ',', '.') }} porsi
                                    </td>
                                    <td class="py-5 px-6 text-right font-bold text-hamta-dark">
                                        Rp {{ number_format((float) $item->total_pendapatan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-14 px-6 text-center text-hamta-gray">
                                        <p class="font-semibold text-hamta-black mb-1">Tidak ada data penjualan</p>
                                        <p class="text-sm">
                                            Belum ada transaksi terverifikasi di periode
                                            {{ \Carbon\Carbon::parse($tanggal_mulai)->translatedFormat('d M Y') }}
                                            &ndash;
                                            {{ \Carbon\Carbon::parse($tanggal_selesai)->translatedFormat('d M Y') }}.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        <!-- Table Footer: Total keseluruhan -->
                        @if ($laporan->isNotEmpty())
                            <tfoot>
                                <tr class="bg-hamta-cream border-t-2 border-stone-200">
                                    <td colspan="3" class="py-4 px-6 text-sm font-bold text-right text-hamta-black">
                                        Total Pendapatan Periode Ini
                                    </td>
                                    <td class="py-4 px-6 text-right font-bold text-lg text-hamta-dark">
                                        Rp {{ number_format((float) $total_pendapatan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        </main>

    </div>

</body>
</html>
