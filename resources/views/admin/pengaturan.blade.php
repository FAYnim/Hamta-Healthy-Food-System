<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Hamta Healthy Food</title>

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
                    <h2 class="text-2xl md:text-3xl font-bold text-[#010101] tracking-tight">Pengaturan</h2>
                </div>

                <!-- Admin Profile Chip -->
                <div class="hidden lg:flex items-center">
                    <button type="button" class="bg-white hover:bg-stone-50 border border-stone-200/60 rounded-xl px-7 py-2.5 font-bold text-[#010101] text-base shadow-sm transition">
                        Yunita
                    </button>
                </div>
            </header>

            <!-- Top Settings Section: 2 Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

                <!-- Left Card: Logo dan Identitas Usaha -->
                <div class="bg-white rounded-2xl p-6 md:p-7 shadow-sm border border-stone-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-[#010101] mb-5">Logo dan Identitas Usaha</h3>
                        <div class="w-36 h-24 bg-[#D9D9D9] rounded-2xl flex items-center justify-center">
                            <x-lucide-image class="w-10 h-10 text-stone-500" />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 mt-8">
                        <button type="button" class="bg-[#8A8A8A] hover:bg-stone-500 text-white font-semibold text-sm px-5 py-2 rounded-xl transition">
                            Batal
                        </button>
                        <button type="button" class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-semibold text-sm px-5 py-2 rounded-xl shadow-sm transition">
                            Simpan
                        </button>
                    </div>
                </div>

                <!-- Right Card: Kontak dan Media Sosial -->
                <div class="bg-white rounded-2xl p-6 md:p-7 shadow-sm border border-stone-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-[#010101] mb-4">Kontak dan Media Sosial</h3>

                        <div class="space-y-4">
                            <!-- WhatsApp Field -->
                            <div>
                                <label class="block text-sm font-semibold text-[#010101] mb-1.5">WhatsApp</label>
                                <input 
                                    type="text" 
                                    value="+6285812345678" 
                                    readonly 
                                    class="w-full bg-[#E5E5E5] text-[#010101] text-sm font-medium rounded-xl px-4 py-2.5 outline-none border border-transparent"
                                >
                            </div>

                            <!-- Instagram Field -->
                            <div>
                                <label class="block text-sm font-semibold text-[#010101] mb-1.5">Instagram</label>
                                <input 
                                    type="text" 
                                    value="@hamta.sby" 
                                    readonly 
                                    class="w-full bg-[#E5E5E5] text-[#010101] text-sm font-medium rounded-xl px-4 py-2.5 outline-none border border-transparent"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 mt-8">
                        <button type="button" class="bg-[#8A8A8A] hover:bg-stone-500 text-white font-semibold text-sm px-5 py-2 rounded-xl transition">
                            Batal
                        </button>
                        <button type="button" class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-semibold text-sm px-5 py-2 rounded-xl shadow-sm transition">
                            Simpan
                        </button>
                    </div>
                </div>

            </div>

            <!-- Bottom Section: Cabang -->
            <div class="bg-white rounded-2xl p-6 md:p-7 shadow-sm border border-stone-100">
                
                <!-- Section Header -->
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-[#010101]">Cabang</h3>
                    <button 
                        type="button" 
                        class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-sm transition"
                    >
                        Tambah Cabang
                    </button>
                </div>

                <!-- Branches Container -->
                <div class="space-y-5">
                    
                    <!-- Branch 1: Lidah Kulon -->
                    <div class="border border-stone-300 rounded-2xl p-5 md:p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-lg font-bold text-[#010101]">Lidah Kulon</h4>
                                <p class="text-xs text-[#8A8A8A] font-medium mt-1">
                                    Jl. Lidah Kulon Gg. I, Lidah Kulon, Kec. Lakarsantri, Surabaya, Jawa Timur 60213
                                </p>
                            </div>
                            <button type="button" class="text-[#010101] hover:text-[#65BD5E] transition">
                                <x-lucide-pencil class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Operational Hours -->
                        <div class="mt-4">
                            <h5 class="text-sm font-bold text-[#010101] mb-2.5">Jam Operasional</h5>
                            <div class="bg-[#E5E5E5] rounded-xl p-4 space-y-2.5 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-[#010101]">Senin - Jumat</span>
                                    <div class="flex items-center gap-3">
                                        <span class="font-semibold text-[#010101]">09:00 - 20:00</span>
                                        <x-lucide-pencil class="w-4 h-4 text-[#010101] cursor-pointer hover:text-[#65BD5E] transition" />
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-[#010101]">Sabtu - Minggu</span>
                                    <div class="flex items-center gap-3">
                                        <span class="font-semibold text-[#010101]">09:00 - 21:00</span>
                                        <x-lucide-pencil class="w-4 h-4 text-[#010101] cursor-pointer hover:text-[#65BD5E] transition" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Branch 2: Merr -->
                    <div class="border border-stone-300 rounded-2xl p-5 md:p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-lg font-bold text-[#010101]">Merr</h4>
                                <p class="text-xs text-[#8A8A8A] font-medium mt-1">
                                    Area Apartemen Bale Hinggil Jl. Medokan Semampir Indah No.63, Medokan Semampir, Kec. Sukolilo, Surabaya, Jawa Timur 60119
                                </p>
                            </div>
                            <button type="button" class="text-[#010101] hover:text-[#65BD5E] transition">
                                <x-lucide-pencil class="w-5 h-5" />
                            </button>
                        </div>

                        <!-- Operational Hours -->
                        <div class="mt-4">
                            <h5 class="text-sm font-bold text-[#010101] mb-2.5">Jam Operasional</h5>
                            <div class="bg-[#E5E5E5] rounded-xl p-4 space-y-2.5 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-[#010101]">Senin - Jumat</span>
                                    <div class="flex items-center gap-3">
                                        <span class="font-semibold text-[#010101]">09:00 - 20:00</span>
                                        <x-lucide-pencil class="w-4 h-4 text-[#010101] cursor-pointer hover:text-[#65BD5E] transition" />
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-[#010101]">Sabtu - Minggu</span>
                                    <div class="flex items-center gap-3">
                                        <span class="font-semibold text-[#010101]">09:00 - 21:00</span>
                                        <x-lucide-pencil class="w-4 h-4 text-[#010101] cursor-pointer hover:text-[#65BD5E] transition" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </main>

    </div>

</body>
</html>
