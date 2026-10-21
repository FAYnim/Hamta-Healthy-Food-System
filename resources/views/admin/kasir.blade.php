<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Hamta Healthy Food</title>

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
        <main class="flex-1 p-6 md:p-8 lg:p-10">

            <!-- Topbar Section -->
            <header class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-[#010101] tracking-tight">Kasir</h2>
                </div>

                <!-- Admin Profile Chip -->
                <div class="hidden lg:flex items-center">
                    <button type="button" class="bg-white hover:bg-stone-50 border border-stone-200/60 rounded-xl px-7 py-2.5 font-bold text-[#010101] text-base shadow-sm transition">
                        Yunita
                    </button>
                </div>
            </header>

            <!-- 2 Columns Layout: Menu Catalog & Shopping Cart -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">

                <!-- Left Column (Menu Catalog): 8 Cols -->
                <div class="xl:col-span-8 space-y-6">

                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-lucide-search class="w-5 h-5 text-[#8A8A8A]" />
                        </div>
                        <input 
                            type="text" 
                            placeholder="Cari Item" 
                            readonly 
                            class="w-full bg-white border border-stone-300 rounded-xl pl-12 pr-4 py-3 text-base text-[#010101] placeholder-[#8A8A8A] shadow-sm outline-none"
                        >
                    </div>

                    <!-- Category Filter Pills -->
                    <div class="flex items-center gap-3 overflow-x-auto pb-2">
                        <button type="button" class="px-5 py-2 rounded-full bg-[#65BD5E] text-white font-semibold text-sm shadow-sm shrink-0">
                            Semua
                        </button>
                        <button type="button" class="px-5 py-2 rounded-full border border-[#010101] bg-white text-[#010101] hover:bg-stone-100 font-semibold text-sm transition shrink-0">
                            Salad
                        </button>
                        <button type="button" class="px-5 py-2 rounded-full border border-[#010101] bg-white text-[#010101] hover:bg-stone-100 font-semibold text-sm transition shrink-0">
                            Sandwich
                        </button>
                        <button type="button" class="px-5 py-2 rounded-full border border-[#010101] bg-white text-[#010101] hover:bg-stone-100 font-semibold text-sm transition shrink-0">
                            Wrap
                        </button>
                        <button type="button" class="px-5 py-2 rounded-full border border-[#010101] bg-white text-[#010101] hover:bg-stone-100 font-semibold text-sm transition shrink-0">
                            Jus
                        </button>
                        <button type="button" class="px-5 py-2 rounded-full border border-[#010101] bg-white text-[#010101] hover:bg-stone-100 font-semibold text-sm transition shrink-0">
                            Ricebowl
                        </button>
                    </div>

                    <!-- Product Grid (3 Columns) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                        <!-- Product Card 1 -->
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 hover:shadow-md transition cursor-pointer">
                            <div class="w-full aspect-[4/3] bg-[#D9D9D9] rounded-xl mb-3 flex items-center justify-center">
                                <x-lucide-image class="w-8 h-8 text-stone-400" />
                            </div>
                            <h4 class="font-bold text-[#010101] text-base">Salad Enak</h4>
                            <p class="font-bold text-[#010101] text-base mt-1">Rp 25.000</p>
                        </div>

                        <!-- Product Card 2 -->
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 hover:shadow-md transition cursor-pointer">
                            <div class="w-full aspect-[4/3] bg-[#D9D9D9] rounded-xl mb-3 flex items-center justify-center">
                                <x-lucide-image class="w-8 h-8 text-stone-400" />
                            </div>
                            <h4 class="font-bold text-[#010101] text-base">Salad Enak</h4>
                            <p class="font-bold text-[#010101] text-base mt-1">Rp 25.000</p>
                        </div>

                        <!-- Product Card 3 -->
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 hover:shadow-md transition cursor-pointer">
                            <div class="w-full aspect-[4/3] bg-[#D9D9D9] rounded-xl mb-3 flex items-center justify-center">
                                <x-lucide-image class="w-8 h-8 text-stone-400" />
                            </div>
                            <h4 class="font-bold text-[#010101] text-base">Salad Enak</h4>
                            <p class="font-bold text-[#010101] text-base mt-1">Rp 25.000</p>
                        </div>

                        <!-- Product Card 4 -->
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 hover:shadow-md transition cursor-pointer">
                            <div class="w-full aspect-[4/3] bg-[#D9D9D9] rounded-xl mb-3 flex items-center justify-center">
                                <x-lucide-image class="w-8 h-8 text-stone-400" />
                            </div>
                            <h4 class="font-bold text-[#010101] text-base">Salad Enak</h4>
                            <p class="font-bold text-[#010101] text-base mt-1">Rp 25.000</p>
                        </div>

                        <!-- Product Card 5 -->
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 hover:shadow-md transition cursor-pointer">
                            <div class="w-full aspect-[4/3] bg-[#D9D9D9] rounded-xl mb-3 flex items-center justify-center">
                                <x-lucide-image class="w-8 h-8 text-stone-400" />
                            </div>
                            <h4 class="font-bold text-[#010101] text-base">Salad Enak</h4>
                            <p class="font-bold text-[#010101] text-base mt-1">Rp 25.000</p>
                        </div>

                        <!-- Product Card 6 -->
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 hover:shadow-md transition cursor-pointer">
                            <div class="w-full aspect-[4/3] bg-[#D9D9D9] rounded-xl mb-3 flex items-center justify-center">
                                <x-lucide-image class="w-8 h-8 text-stone-400" />
                            </div>
                            <h4 class="font-bold text-[#010101] text-base">Salad Enak</h4>
                            <p class="font-bold text-[#010101] text-base mt-1">Rp 25.000</p>
                        </div>
                    </div>

                </div>

                <!-- Right Column (Shopping Cart / Keranjang): 4 Cols -->
                <div class="xl:col-span-4 bg-white rounded-2xl p-6 shadow-sm border border-stone-100 flex flex-col justify-between min-h-[520px]">
                    
                    <div>
                        <!-- Cart Header -->
                        <div class="flex items-center gap-2.5 mb-6">
                            <x-lucide-shopping-cart class="w-6 h-6 text-[#010101]" />
                            <h3 class="text-xl font-bold text-[#010101]">Keranjang</h3>
                        </div>

                        <!-- Cart Items List -->
                        <div class="space-y-4">
                            <!-- Item 1 -->
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 bg-[#D9D9D9] rounded-xl shrink-0 flex items-center justify-center">
                                        <x-lucide-image class="w-5 h-5 text-stone-400" />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-[#010101] text-sm">Salad Enak</h4>
                                        <p class="text-xs text-[#8A8A8A] font-medium mt-0.5">Rp 25.000</p>
                                        
                                        <!-- Quantity Stepper -->
                                        <div class="flex items-center gap-1.5 mt-1.5">
                                            <button type="button" class="w-5 h-5 rounded bg-[#65BD5E] text-white flex items-center justify-center">
                                                <x-lucide-minus class="w-3 h-3 stroke-[3]" />
                                            </button>
                                            <span class="text-xs font-bold px-1.5 text-[#010101]">2</span>
                                            <button type="button" class="w-5 h-5 rounded bg-[#65BD5E] text-white flex items-center justify-center">
                                                <x-lucide-plus class="w-3 h-3 stroke-[3]" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-sm text-[#010101]">Rp 50.000</span>
                                </div>
                            </div>

                            <!-- Item 2 -->
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 bg-[#D9D9D9] rounded-xl shrink-0 flex items-center justify-center">
                                        <x-lucide-image class="w-5 h-5 text-stone-400" />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-[#010101] text-sm">Salad Enak</h4>
                                        <p class="text-xs text-[#8A8A8A] font-medium mt-0.5">Rp 25.000</p>
                                        
                                        <!-- Quantity Stepper -->
                                        <div class="flex items-center gap-1.5 mt-1.5">
                                            <button type="button" class="w-5 h-5 rounded bg-[#65BD5E] text-white flex items-center justify-center">
                                                <x-lucide-minus class="w-3 h-3 stroke-[3]" />
                                            </button>
                                            <span class="text-xs font-bold px-1.5 text-[#010101]">2</span>
                                            <button type="button" class="w-5 h-5 rounded bg-[#65BD5E] text-white flex items-center justify-center">
                                                <x-lucide-plus class="w-3 h-3 stroke-[3]" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-sm text-[#010101]">Rp 50.000</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Summary & Action Button -->
                    <div class="mt-8 pt-4 border-t border-stone-200 space-y-3">
                        <div class="flex justify-between text-sm text-[#010101]">
                            <span>Sub-total</span>
                            <span class="font-semibold">Rp 100.000</span>
                        </div>
                        <div class="flex justify-between text-sm text-[#010101]">
                            <span>PPN</span>
                            <span class="font-semibold">Rp 10.000</span>
                        </div>

                        <div class="border-t border-stone-200 pt-3 flex justify-between text-base font-bold text-[#010101]">
                            <span>Total</span>
                            <span>Rp 110.000</span>
                        </div>

                        <button 
                            type="button" 
                            class="w-full bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-base rounded-xl py-3.5 shadow-sm transition mt-4"
                        >
                            Tambah Pesanan
                        </button>
                    </div>

                </div>

            </div>

        </main>

    </div>

</body>
</html>
