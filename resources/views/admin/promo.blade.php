<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo - Hamta Healthy Food</title>

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
                    <h2 class="text-2xl md:text-3xl font-bold text-[#010101] tracking-tight">Promo</h2>
                </div>

                <!-- Admin Profile Chip -->
                <div class="hidden lg:flex items-center">
                    <button type="button" class="bg-white hover:bg-stone-50 border border-stone-200/60 rounded-xl px-7 py-2.5 font-bold text-[#010101] text-base shadow-sm transition">
                        Yunita
                    </button>
                </div>
            </header>

            <!-- Top Action Bar -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 mb-6 flex items-center justify-end">
                <div class="flex items-center gap-3.5 w-full md:w-auto justify-end">
                    <!-- Search Input -->
                    <div class="relative w-full md:w-64">
                        <input 
                            type="text" 
                            placeholder="Cari Promo" 
                            readonly 
                            class="w-full bg-white border border-stone-300 rounded-xl pl-4 pr-10 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none">
                            <x-lucide-search class="w-4 h-4 text-[#8A8A8A]" />
                        </div>
                    </div>

                    <!-- Tambah Promo Button -->
                    <button 
                        type="button" 
                        class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-sm transition shrink-0"
                    >
                        Tambah Promo
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
                                <th class="py-4 px-6">Promo</th>
                                <th class="py-4 px-6 text-center w-36">Status</th>
                                <th class="py-4 px-6 text-center w-32">Aksi</th>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-stone-200 text-sm">
                            <!-- Row 1 -->
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                    Potongan Rp 500 pembelian minimal 50
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <!-- Toggle Switch -->
                                    <div class="inline-flex items-center cursor-pointer">
                                        <div class="w-12 h-6 bg-stone-300 rounded-full relative transition p-0.5">
                                            <div class="w-5 h-5 bg-white rounded-full shadow-md"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-5 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" class="w-9 h-9 rounded-lg bg-[#E08A3C] hover:bg-[#d07b30] text-white flex items-center justify-center transition">
                                            <x-lucide-pencil class="w-4 h-4" />
                                        </button>
                                        <button type="button" class="w-9 h-9 rounded-lg bg-[#E05C5C] hover:bg-[#d04b4b] text-white flex items-center justify-center transition">
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 2 -->
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                    Potongan Rp 500 pembelian minimal 50
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <div class="inline-flex items-center cursor-pointer">
                                        <div class="w-12 h-6 bg-stone-300 rounded-full relative transition p-0.5">
                                            <div class="w-5 h-5 bg-white rounded-full shadow-md"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-5 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" class="w-9 h-9 rounded-lg bg-[#E08A3C] hover:bg-[#d07b30] text-white flex items-center justify-center transition">
                                            <x-lucide-pencil class="w-4 h-4" />
                                        </button>
                                        <button type="button" class="w-9 h-9 rounded-lg bg-[#E05C5C] hover:bg-[#d04b4b] text-white flex items-center justify-center transition">
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 3 -->
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                    Potongan Rp 500 pembelian minimal 50
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <div class="inline-flex items-center cursor-pointer">
                                        <div class="w-12 h-6 bg-stone-300 rounded-full relative transition p-0.5">
                                            <div class="w-5 h-5 bg-white rounded-full shadow-md"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-5 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" class="w-9 h-9 rounded-lg bg-[#E08A3C] hover:bg-[#d07b30] text-white flex items-center justify-center transition">
                                            <x-lucide-pencil class="w-4 h-4" />
                                        </button>
                                        <button type="button" class="w-9 h-9 rounded-lg bg-[#E05C5C] hover:bg-[#d04b4b] text-white flex items-center justify-center transition">
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 4 -->
                            <tr class="hover:bg-stone-50/50 transition">
                                <td class="py-5 px-6 font-bold text-[#010101] text-base">
                                    Potongan Rp 500 pembelian minimal 50
                                </td>
                                <td class="py-5 px-6 text-center">
                                    <div class="inline-flex items-center cursor-pointer">
                                        <div class="w-12 h-6 bg-stone-300 rounded-full relative transition p-0.5">
                                            <div class="w-5 h-5 bg-white rounded-full shadow-md"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-5 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" class="w-9 h-9 rounded-lg bg-[#E08A3C] hover:bg-[#d07b30] text-white flex items-center justify-center transition">
                                            <x-lucide-pencil class="w-4 h-4" />
                                        </button>
                                        <button type="button" class="w-9 h-9 rounded-lg bg-[#E05C5C] hover:bg-[#d04b4b] text-white flex items-center justify-center transition">
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </button>
                                    </div>
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
