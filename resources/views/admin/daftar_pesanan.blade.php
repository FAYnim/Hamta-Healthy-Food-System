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

        <!-- ========================================== -->
        <!-- 1. SIDEBAR (Dark Green: #0B412B)           -->
        <!-- ========================================== -->
        <aside class="w-full lg:w-72 bg-[#0B412B] text-white flex flex-col shrink-0 lg:min-h-screen">
            <!-- Brand / Logo Header -->
            <div class="px-7 pt-8 pb-6 lg:pb-8 flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight text-white">Hamta Panel</h1>
                
                <!-- Badge Admin (Mobile Only) -->
                <div class="lg:hidden bg-white text-[#010101] text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm">
                    Yunita
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 pb-6 space-y-1.5 overflow-x-auto lg:overflow-visible flex lg:flex-col gap-1 lg:gap-0">
                <!-- Item 1: Dashboard -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium transition whitespace-nowrap">
                    <x-lucide-home class="w-5 h-5 shrink-0" />
                    <span>Dashboard</span>
                </a>

                <!-- Item 2: Kasir -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium transition whitespace-nowrap">
                    <x-lucide-calculator class="w-5 h-5 shrink-0" />
                    <span>Kasir</span>
                </a>

                <!-- Item 3: Kelola Menu -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium transition whitespace-nowrap">
                    <x-lucide-concierge-bell class="w-5 h-5 shrink-0" />
                    <span>Kelola Menu</span>
                </a>

                <!-- Item 4: Daftar Pesanan (Active) -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl bg-[#65BD5E] text-white font-semibold shadow-sm transition whitespace-nowrap">
                    <x-lucide-clipboard-list class="w-5 h-5 shrink-0" />
                    <span>Daftar Pesanan</span>
                </a>

                <!-- Item 5: Promo -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium transition whitespace-nowrap">
                    <x-lucide-tag class="w-5 h-5 shrink-0" />
                    <span>Promo</span>
                </a>

                <!-- Item 6: Pengaturan -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium transition whitespace-nowrap">
                    <x-lucide-store class="w-5 h-5 shrink-0" />
                    <span>Pengaturan</span>
                </a>
            </nav>
        </aside>

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
                
                <!-- Status Tabs -->
                <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto">
                    <button type="button" class="px-5 py-2 rounded-xl bg-[#65BD5E] text-white font-bold text-sm shadow-sm shrink-0">
                        Semua
                    </button>
                    <button type="button" class="px-5 py-2 rounded-xl text-[#010101] hover:bg-stone-100 font-semibold text-sm transition shrink-0">
                        Baru
                    </button>
                    <button type="button" class="px-5 py-2 rounded-xl text-[#010101] hover:bg-stone-100 font-semibold text-sm transition shrink-0">
                        Diproses
                    </button>
                    <button type="button" class="px-5 py-2 rounded-xl text-[#010101] hover:bg-stone-100 font-semibold text-sm transition shrink-0">
                        Selesai
                    </button>
                </div>

                <!-- Search Input + Filter Button -->
                <div class="flex items-center gap-3.5 w-full md:w-auto justify-end">
                    <!-- Search Input -->
                    <div class="relative w-full md:w-64">
                        <input 
                            type="text" 
                            placeholder="Cari Pesanan" 
                            readonly 
                            class="w-full bg-white border border-stone-300 rounded-xl pl-4 pr-10 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <x-lucide-search class="w-4 h-4 text-[#8A8A8A]" />
                        </div>
                    </div>

                    <!-- Filter Button -->
                    <button 
                        type="button" 
                        class="bg-white hover:bg-stone-50 border border-stone-300 text-[#010101] font-semibold text-sm px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2 shrink-0"
                    >
                        <x-lucide-filter class="w-4 h-4 text-[#010101]" />
                        <span>Filter</span>
                    </button>
                </div>

            </div>

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
                                <th class="py-4 px-6 text-center w-36">Aksi</th>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-stone-200 text-sm">
                            <!-- Row 1 -->
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-5 px-6 font-semibold text-[#010101]">
                                    #1959
                                </td>
                                <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                    Jasmine Olivia
                                </td>
                                <td class="py-5 px-6 font-medium text-[#010101]">
                                    Lidah Kulon
                                </td>
                                <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                    Rp 45.000
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <button type="button" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full border border-[#010101] text-[#010101] hover:bg-stone-100 font-semibold text-xs transition">
                                        <x-lucide-eye class="w-4 h-4" />
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 2 -->
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-5 px-6 font-semibold text-[#010101]">
                                    #1959
                                </td>
                                <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                    Jasmine Olivia
                                </td>
                                <td class="py-5 px-6 font-medium text-[#010101]">
                                    Lidah Kulon
                                </td>
                                <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                    Rp 45.000
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <button type="button" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full border border-[#010101] text-[#010101] hover:bg-stone-100 font-semibold text-xs transition">
                                        <x-lucide-eye class="w-4 h-4" />
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 3 -->
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-5 px-6 font-semibold text-[#010101]">
                                    #1958
                                </td>
                                <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                    Budi Santoso
                                </td>
                                <td class="py-5 px-6 font-medium text-[#010101]">
                                    Merr
                                </td>
                                <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                    Rp 75.000
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <button type="button" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full border border-[#010101] text-[#010101] hover:bg-stone-100 font-semibold text-xs transition">
                                        <x-lucide-eye class="w-4 h-4" />
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Row 4 -->
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-5 px-6 font-semibold text-[#010101]">
                                    #1957
                                </td>
                                <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                    Siti Rahma
                                </td>
                                <td class="py-5 px-6 font-medium text-[#010101]">
                                    Lidah Kulon
                                </td>
                                <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                    Rp 30.000
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <button type="button" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full border border-[#010101] text-[#010101] hover:bg-stone-100 font-semibold text-xs transition">
                                        <x-lucide-eye class="w-4 h-4" />
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

    </div>

</body>
</html>
