<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cerita di Balik Hamta — Hamta Healthy Food</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .cerita-top-wave {
            display: block;
            width: 100%;
            height: 60px;
            color: #F7F2E7;
            fill: currentColor;
            transform: translateY(1px);
        }
        @media (min-width: 768px) {
            .cerita-top-wave { height: 90px; }
        }
    </style>
</head>
<body class="bg-[#F7F2E7] text-[#101010] font-sans antialiased">

<div class="page !pb-0">
    @include('customer.partials.header')
</div>

<!-- ─── 1. TOP HERO SECTION (DARK GREEN WITH ORGANIC BOTTOM WAVE) ────── -->
<section class="relative bg-[#0B412B] text-white pt-10 sm:pt-14 md:pt-16 overflow-hidden">
    <div class="max-w-4xl mx-auto px-6 sm:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-center">
            
            <!-- Left: Circular Salad Bowl Image -->
            <div class="md:col-span-5 flex justify-center">
                <div class="relative w-56 h-56 sm:w-64 sm:h-64 md:w-72 md:h-72 rounded-full overflow-hidden p-1.5 bg-gradient-to-tr from-[#65BD5E] to-white/40 shadow-2xl">
                    <img 
                        src="{{ asset('images/cerita/cerita-salad-bowl.png') }}" 
                        alt="Hamtash Salad Bowl Segar" 
                        class="w-full h-full object-cover rounded-full"
                        onerror="this.onerror=null;this.src='{{ asset('images/salad-1-clean.webp') }}'"
                    >
                </div>
            </div>

            <!-- Right: Opening Story Paragraph -->
            <div class="md:col-span-7 space-y-4 text-center md:text-left">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#65BD5E]/20 text-[#65BD5E] border border-[#65BD5E]/30 text-xs font-bold uppercase tracking-wider">
                    <x-lucide-leaf class="w-3.5 h-3.5" />
                    <span>Perjalanan Kami</span>
                </span>
                
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white leading-snug">
                    Cerita di Balik Usaha <br class="hidden sm:inline">
                    <span class="text-[#65BD5E]">Hamta Healthy Food</span>
                </h1>

                <p class="text-xs sm:text-sm text-gray-200 leading-relaxed font-normal">
                    Keluarga kami berkumpul dengan satu impian bersama — cara makan yang lebih sadar, bernutrisi tinggi, dan benar-benar <strong>BAIK</strong> untuk tubuh. Hamtash Salad (Hamta Healthy Food) didirikan dengan tujuan membawa dimensi baru pada salad segar dan pendekatan inovatif untuk <em>healthy fast food</em> berkualitas di Surabaya.
                </p>

                <p class="text-xs sm:text-sm text-gray-200 leading-relaxed font-normal">
                    Kami hadir untuk mengakhiri budaya makanan siap saji yang minim gizi dan komisi platform yang mahal. Bagi kami, setiap mangkuk salad yang Anda nikmati adalah hasil racikan penuh cinta dan katalisator bagi siklus perubahan positif menuju gaya hidup yang lebih bugar dan bahagia.
                </p>
            </div>

        </div>
    </div>

    <!-- Organic Bottom Wave Transition to Cream Page -->
    <div class="relative w-full overflow-hidden leading-none mt-12 sm:mt-16">
        <svg class="cerita-top-wave" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,48L48,58.7C96,69,192,91,288,96C384,101,480,91,576,74.7C672,59,768,37,864,37.3C960,37,1056,59,1152,74.7C1248,91,1344,101,1392,106.7L1440,112L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z"></path>
        </svg>
    </div>
</section>

<!-- ─── 2. MIDDLE STORY SECTIONS (CREAM BACKGROUND) ──────────────────── -->
<div class="max-w-4xl mx-auto px-6 sm:px-8 py-10 sm:py-16 space-y-16 sm:space-y-24">

    <!-- Row 1: Filosofi Bahan & Sandwich -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-center">
        <!-- Text Column -->
        <div class="md:col-span-7 space-y-4 text-left order-2 md:order-1">
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-[#010101] leading-snug">
                Makan dengan Penuh Kesadaran <br>
                <span class="text-[#0B412B] font-extrabold italic">(Eat Wide Awake)</span>
            </h2>

            <p class="text-xs sm:text-sm text-[#4a5a52] leading-relaxed">
                <strong>Eat Wide Awake</strong> adalah komitmen kami untuk menemukan bahan-bahan terbaik yang menutrisi pikiran dan tubuh dengan tetap mempertimbangkan kelestarian lingkungan serta rantai pasokan petani sayur lokal Surabaya dan sekitarnya.
            </p>

            <p class="text-xs sm:text-sm text-[#4a5a52] leading-relaxed">
                Di dunia yang serba cepat, kita terkadang terburu-buru dan lupa menikmati apa yang menjadi bahan bakar utama tubuh kita — makanan kita sendiri. Kami ingin mengajak masyarakat untuk sejenak berhenti, merasakan setiap kesegaran sayuran renyah, dan memahami dari mana makanan mereka berasal.
            </p>

            <p class="text-xs sm:text-sm text-[#4a5a52] leading-relaxed">
                Kami bermitra dengan penyuplai lokal terpercaya yang memegang standar higienis ketat. Kami memastikan keberlanjutan operasional dengan bahan kemasan ramah lingkungan, proses produksi bersih, dan pengantaran langsung ke tangan Anda tanpa komisi terselubung.
            </p>
        </div>

        <!-- Image Column -->
        <div class="md:col-span-5 flex justify-center order-1 md:order-2">
            <div class="relative max-w-[260px] sm:max-w-[290px] rounded-3xl p-3 bg-white shadow-md border border-emerald-100 transition-transform hover:scale-105 duration-300">
                <img 
                    src="{{ asset('images/cerita/cerita-sandwich.png') }}" 
                    alt="Hamta Gourmet Sandwich Segar" 
                    class="w-full h-auto object-contain rounded-2xl"
                    onerror="this.onerror=null;this.src='{{ asset('images/salad-1-clean.webp') }}'"
                >
                <div class="mt-2 text-center">
                    <span class="text-[11px] font-bold text-[#0B412B] uppercase tracking-wider">Gourmet Healthy Sandwich</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Transparansi Pilihan & Wrap Roll -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-center">
        <!-- Image Column -->
        <div class="md:col-span-5 flex justify-center order-1">
            <div class="relative max-w-[260px] sm:max-w-[290px] rounded-3xl p-3 bg-white shadow-md border border-emerald-100 transition-transform hover:scale-105 duration-300">
                <img 
                    src="{{ asset('images/cerita/cerita-wrap.png') }}" 
                    alt="Hamta Healthy Salad Wrap" 
                    class="w-full h-auto object-contain rounded-2xl"
                    onerror="this.onerror=null;this.src='{{ asset('images/salad-1-clean.webp') }}'"
                >
                <div class="mt-2 text-center">
                    <span class="text-[11px] font-bold text-[#0B412B] uppercase tracking-wider">Fresh Salad Wrap Roll</span>
                </div>
            </div>
        </div>

        <!-- Text Column -->
        <div class="md:col-span-7 space-y-4 text-left order-2">
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-[#010101] leading-snug">
                Transparansi Nutrisi &amp; <br>
                <span class="text-[#0B412B] font-extrabold">Kendali Penuh di Tangan Anda</span>
            </h2>

            <p class="text-xs sm:text-sm text-[#4a5a52] leading-relaxed">
                Kami memberdayakan pelanggan dengan transparansi penuh untuk makan lebih baik. Melalui fitur unggulan <strong>"Kreasikan Salad Sendiri"</strong> di website ini, Anda memiliki kebebasan memilih takaran sayuran segar, varian protein bergizi, serta pilihan saus dressing sesuai kebutuhan kalori dan target kesehatan Anda.
            </p>

            <p class="text-xs sm:text-sm text-[#4a5a52] leading-relaxed">
                Gerakan <strong>Eat Good, Feel Good</strong> adalah tentang merevolusi cara pandang terhadap makanan sehat di Surabaya: sehat itu tidak harus hambar, sehat itu menyenangkan, dan makan sehat berkualitas bisa dinikmati siapa saja dengan harga yang transparan dan terjangkau.
            </p>

            <div class="pt-4 flex flex-wrap gap-3">
                <a 
                    href="{{ route('customer.kreasikan_salad') }}" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#65BD5E] hover:bg-[#52a64c] text-[#0B412B] font-extrabold text-xs sm:text-sm shadow-sm transition active:scale-95"
                >
                    <x-lucide-sparkles class="w-4 h-4 text-[#0B412B]" />
                    <span>Kreasikan Salad Sendiri</span>
                </a>
                <a 
                    href="{{ route('customer.daftar_menu') }}" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-stone-50 border border-stone-200 text-[#0B412B] font-bold text-xs sm:text-sm shadow-xs transition active:scale-95"
                >
                    <span>Lihat Menu Lengkap</span>
                    <x-lucide-arrow-right class="w-4 h-4" />
                </a>
            </div>
        </div>
    </div>

</div>

<!-- ─── 3. FOOTER SECTION ────────────────────────────────────────────── -->
@include('customer.partials.footer')

</body>
</html>

