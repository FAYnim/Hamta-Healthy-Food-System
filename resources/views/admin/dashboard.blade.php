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
                <!-- Item 1: Dashboard (Active) -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl bg-[#65BD5E] text-white font-semibold shadow-sm transition whitespace-nowrap">
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

                <!-- Item 4: Daftar Pesanan -->
                <a href="#" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium transition whitespace-nowrap">
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

            <!-- Topbar / Header Section -->
            <header class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-[#010101] tracking-tight">Dashboard</h2>
                </div>

                <!-- Admin Profile Chip -->
                <div class="hidden lg:flex items-center">
                    <button type="button" class="bg-white hover:bg-stone-50 border border-stone-200/60 rounded-xl px-7 py-2.5 font-bold text-[#010101] text-base shadow-sm transition">
                        Yunita
                    </button>
                </div>
            </header>

            <!-- Stat Cards Row (4 Cards) -->
            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Stat Card 1: Total Pesanan -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 flex flex-col justify-between">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-full bg-[#0B412B] text-white flex items-center justify-center shrink-0">
                            <x-lucide-shopping-bag class="w-5 h-5" />
                        </div>
                        <span class="text-[#010101] font-semibold text-base">Total Pesanan</span>
                    </div>
                    <div class="mt-6 mb-2 text-center">
                        <span class="text-4xl font-extrabold text-[#010101] tracking-tight">24</span>
                    </div>
                </div>

                <!-- Stat Card 2: Penjualan -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 flex flex-col justify-between">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-full bg-[#0B412B] text-white flex items-center justify-center shrink-0">
                            <x-lucide-chart-column-increasing class="w-5 h-5" />
                        </div>
                        <span class="text-[#010101] font-semibold text-base">Penjualan</span>
                    </div>
                    <div class="mt-6 mb-2 text-center">
                        <span class="text-3xl lg:text-[28px] xl:text-3xl font-extrabold text-[#010101] tracking-tight">Rp 3.500.000</span>
                    </div>
                </div>

                <!-- Stat Card 3: Menunggu Deposit -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 flex flex-col justify-between">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-full bg-[#0B412B] text-white flex items-center justify-center shrink-0">
                            <x-lucide-clock class="w-5 h-5" />
                        </div>
                        <span class="text-[#010101] font-semibold text-base leading-tight">Menunggu<br class="hidden sm:inline"> Deposit</span>
                    </div>
                    <div class="mt-6 mb-2 text-center">
                        <span class="text-4xl font-extrabold text-[#010101] tracking-tight">6</span>
                    </div>
                </div>

                <!-- Stat Card 4: Pesanan Selesai -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100 flex flex-col justify-between">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-full bg-[#0B412B] text-white flex items-center justify-center shrink-0">
                            <x-lucide-check class="w-5 h-5" />
                        </div>
                        <span class="text-[#010101] font-semibold text-base leading-tight">Pesanan<br class="hidden sm:inline"> Selesai</span>
                    </div>
                    <div class="mt-6 mb-2 text-center">
                        <span class="text-4xl font-extrabold text-[#010101] tracking-tight">24</span>
                    </div>
                </div>
            </section>

            <!-- Bottom Content Grid: Chart + Notifications -->
            <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

                <!-- Left Card: Grafik Penjualan 7 Hari Terakhir -->
                <div class="bg-white rounded-2xl p-6 md:p-7 shadow-sm border border-stone-100 flex flex-col">
                    <h3 class="text-xl font-bold text-[#010101] mb-6 tracking-tight">Grafik Penjualan 7 Hari Terakhir</h3>

                    <!-- Line Chart Container -->
                    <div class="w-full flex-1 flex items-center justify-center pt-2 pb-4">
                        <svg viewBox="0 0 540 330" class="w-full h-auto max-h-[340px]" xmlns="http://www.w3.org/2000/svg">
                            <!-- Background Horizontal Grid Lines & Y-Axis Labels -->
                            <!-- Y = 400 -->
                            <text x="35" y="44" text-anchor="end" class="text-[13px] fill-[#8A8A8A] font-medium">400</text>
                            <line x1="50" y1="40" x2="520" y2="40" stroke="#E5E7EB" stroke-width="1.2" />

                            <!-- Y = 300 -->
                            <text x="35" y="104" text-anchor="end" class="text-[13px] fill-[#8A8A8A] font-medium">300</text>
                            <line x1="50" y1="100" x2="520" y2="100" stroke="#E5E7EB" stroke-width="1.2" />

                            <!-- Y = 200 -->
                            <text x="35" y="164" text-anchor="end" class="text-[13px] fill-[#8A8A8A] font-medium">200</text>
                            <line x1="50" y1="160" x2="520" y2="160" stroke="#E5E7EB" stroke-width="1.2" />

                            <!-- Y = 100 -->
                            <text x="35" y="224" text-anchor="end" class="text-[13px] fill-[#8A8A8A] font-medium">100</text>
                            <line x1="50" y1="220" x2="520" y2="220" stroke="#E5E7EB" stroke-width="1.2" />

                            <!-- Baseline Y = 0 (Darker Line) -->
                            <text x="35" y="284" text-anchor="end" class="text-[13px] fill-[#8A8A8A] font-medium">0</text>
                            <line x1="50" y1="280" x2="520" y2="280" stroke="#8A8A8A" stroke-width="1.5" />

                            <!-- Ticks on Baseline -->
                            <line x1="60" y1="280" x2="60" y2="286" stroke="#8A8A8A" stroke-width="1.5" />
                            <line x1="135" y1="280" x2="135" y2="286" stroke="#8A8A8A" stroke-width="1.5" />
                            <line x1="210" y1="280" x2="210" y2="286" stroke="#8A8A8A" stroke-width="1.5" />
                            <line x1="285" y1="280" x2="285" y2="286" stroke="#8A8A8A" stroke-width="1.5" />
                            <line x1="360" y1="280" x2="360" y2="286" stroke="#8A8A8A" stroke-width="1.5" />
                            <line x1="435" y1="280" x2="435" y2="286" stroke="#8A8A8A" stroke-width="1.5" />
                            <line x1="510" y1="280" x2="510" y2="286" stroke="#8A8A8A" stroke-width="1.5" />

                            <!-- X-Axis Day Labels -->
                            <text x="60" y="302" text-anchor="middle" class="text-[13px] fill-[#8A8A8A] font-medium">Mon</text>
                            <text x="135" y="302" text-anchor="middle" class="text-[13px] fill-[#8A8A8A] font-medium">Tue</text>
                            <text x="210" y="302" text-anchor="middle" class="text-[13px] fill-[#8A8A8A] font-medium">Wed</text>
                            <text x="285" y="302" text-anchor="middle" class="text-[13px] fill-[#8A8A8A] font-medium">Thu</text>
                            <text x="360" y="302" text-anchor="middle" class="text-[13px] fill-[#8A8A8A] font-medium">Fri</text>
                            <text x="435" y="302" text-anchor="middle" class="text-[13px] fill-[#8A8A8A] font-medium">Sat</text>
                            <text x="510" y="302" text-anchor="middle" class="text-[13px] fill-[#8A8A8A] font-medium">Sun</text>

                            <!-- Sales Trend Polyline (Green: #65BD5E) -->
                            <polyline
                                fill="none"
                                stroke="#65BD5E"
                                stroke-width="2.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                points="60,175 135,95 210,138 285,235 360,152 435,148 510,172"
                            />

                            <!-- Data Point Nodes (Circles with white center & green border) -->
                            <!-- Mon (175) -->
                            <circle cx="60" cy="175" r="4.5" fill="#FFFFFF" stroke="#65BD5E" stroke-width="2.5" />
                            <!-- Tue (95) -->
                            <circle cx="135" cy="95" r="4.5" fill="#FFFFFF" stroke="#65BD5E" stroke-width="2.5" />
                            <!-- Wed (138) -->
                            <circle cx="210" cy="138" r="4.5" fill="#FFFFFF" stroke="#65BD5E" stroke-width="2.5" />
                            <!-- Thu (235) -->
                            <circle cx="285" cy="235" r="4.5" fill="#FFFFFF" stroke="#65BD5E" stroke-width="2.5" />
                            <!-- Fri (152) -->
                            <circle cx="360" cy="152" r="4.5" fill="#FFFFFF" stroke="#65BD5E" stroke-width="2.5" />
                            <!-- Sat (148) -->
                            <circle cx="435" cy="148" r="4.5" fill="#FFFFFF" stroke="#65BD5E" stroke-width="2.5" />
                            <!-- Sun (172) -->
                            <circle cx="510" cy="172" r="4.5" fill="#FFFFFF" stroke="#65BD5E" stroke-width="2.5" />
                        </svg>
                    </div>
                </div>

                <!-- Right Card: Notifikasi Terbaru -->
                <div class="bg-white rounded-2xl p-6 md:p-7 shadow-sm border border-stone-100 flex flex-col">
                    <h3 class="text-xl font-bold text-[#010101] mb-2 tracking-tight">Notifikasi Terbaru</h3>

                    <!-- List Container -->
                    <div class="flex-1 flex flex-col justify-between divide-y divide-[#8A8A8A]/25">
                        <!-- Notification Item 1 -->
                        <div class="py-3.5 first:pt-2">
                            <div class="flex items-baseline justify-between gap-4">
                                <div>
                                    <h4 class="font-bold text-[#010101] text-base leading-snug">Jasmine Olivia Putri</h4>
                                    <p class="text-sm text-[#8A8A8A] font-normal mt-0.5">1x Salad Original &nbsp;+ 2x Sandwich</p>
                                </div>
                                <span class="text-xs font-semibold text-[#010101] shrink-0">10:24 AM</span>
                            </div>
                        </div>

                        <!-- Notification Item 2 -->
                        <div class="py-3.5">
                            <div class="flex items-baseline justify-between gap-4">
                                <div>
                                    <h4 class="font-bold text-[#010101] text-base leading-snug">Jasmine Olivia Putri</h4>
                                    <p class="text-sm text-[#8A8A8A] font-normal mt-0.5">1x Salad Original &nbsp;+ 2x Sandwich</p>
                                </div>
                                <span class="text-xs font-semibold text-[#010101] shrink-0">10:24 AM</span>
                            </div>
                        </div>

                        <!-- Notification Item 3 -->
                        <div class="py-3.5">
                            <div class="flex items-baseline justify-between gap-4">
                                <div>
                                    <h4 class="font-bold text-[#010101] text-base leading-snug">Jasmine Olivia Putri</h4>
                                    <p class="text-sm text-[#8A8A8A] font-normal mt-0.5">1x Salad Original &nbsp;+ 2x Sandwich</p>
                                </div>
                                <span class="text-xs font-semibold text-[#010101] shrink-0">10:24 AM</span>
                            </div>
                        </div>

                        <!-- Notification Item 4 -->
                        <div class="py-3.5">
                            <div class="flex items-baseline justify-between gap-4">
                                <div>
                                    <h4 class="font-bold text-[#010101] text-base leading-snug">Jasmine Olivia Putri</h4>
                                    <p class="text-sm text-[#8A8A8A] font-normal mt-0.5">1x Salad Original &nbsp;+ 2x Sandwich</p>
                                </div>
                                <span class="text-xs font-semibold text-[#010101] shrink-0">10:24 AM</span>
                            </div>
                        </div>

                        <!-- Notification Item 5 -->
                        <div class="py-3.5 last:pb-1">
                            <div class="flex items-baseline justify-between gap-4">
                                <div>
                                    <h4 class="font-bold text-[#010101] text-base leading-snug">Jasmine Olivia Putri</h4>
                                    <p class="text-sm text-[#8A8A8A] font-normal mt-0.5">1x Salad Original &nbsp;+ 2x Sandwich</p>
                                </div>
                                <span class="text-xs font-semibold text-[#010101] shrink-0">10:24 AM</span>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

        </main>

    </div>

</body>
</html>
