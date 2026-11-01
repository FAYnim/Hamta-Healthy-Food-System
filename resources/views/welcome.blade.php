<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hamta — Eat Good, Feel Good</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .store { position:relative; border:3px solid var(--green); border-radius:13px; overflow:hidden; color:white; }
        h2:not(.tracking-tight) { margin:26px 8px 18px; font-size:29px; line-height:1.15; letter-spacing:-1px; }
        .catalog { border:3px solid #426a57; border-radius:14px; padding:17px 15px 22px; display:grid; grid-template-columns:repeat(4,1fr); gap:24px 20px; }
        .product { min-width:0; }
        .product-art { aspect-ratio:1/1; border-radius:9px; background:var(--lime); display:grid; place-items:center; position:relative; overflow:hidden; }
        .product-art:before { content:''; width:66%; height:66%; border-radius:50%; background:radial-gradient(circle at 35% 30%,#ffe49c 0 16%,transparent 17%), radial-gradient(circle at 62% 58%,#eb4d35 0 15%,transparent 16%), radial-gradient(circle at 42% 70%,#7dce59 0 18%,transparent 19%), #f2c17b; box-shadow:0 7px 0 #d49a5b; transform:rotate(-9deg); }
        .product:nth-child(2) .product-art:before { border-radius:48% 52% 42% 58%; background:linear-gradient(135deg,#efc779,#9c573d 52%,#f7e4ad); }
        .product:nth-child(3) .product-art:before { background:radial-gradient(circle,#dc5741 0 13%,transparent 14%),radial-gradient(circle at 70% 28%,#f2d36f 0 13%,transparent 14%),#f1e6a7; }
        .product:nth-child(n+5) .product-art:before { width:35%; height:70%; border-radius:12px 12px 28px 28px; background:linear-gradient(90deg,#e7a348,#fff0bc,#d58241); box-shadow:0 9px 0 #cb762f; }
        .product:nth-child(6) .product-art:before { background:linear-gradient(90deg,#efe2b6,#a9d85f,#734631); }
        .product:nth-child(7) .product-art:before { background:linear-gradient(90deg,#f08b39,#ffcf5f,#e75e30); }
        .product:nth-child(8) .product-art:before { background:linear-gradient(90deg,#e77791,#ffe1a3,#dc4d66); }
        .product-name { position:absolute; bottom:8px; left:9px; right:9px; color:white; font-size:17px; line-height:1.2; font-weight:700; text-transform:uppercase; text-shadow:0 1px 2px #255c27; }
        .price { margin:10px 5px 7px; font-size:18px; font-weight:700; }
        .product-photo { width:100%; height:100%; object-fit:contain; position:relative; z-index:1; }
        .product .btn { display:block; width:100%; text-align:center; }
        .catalog-more { display:flex; justify-content:center; margin:18px 0 6px; }
        .catalog-empty { grid-column:1 / -1; text-align:center; font-size:14px; font-weight:600; padding:24px 0; }
        .btn { display:inline-block; padding:9px 15px; border:0; border-radius:7px; background:var(--green); color:#fff; font-weight:700; font-size:13px; text-decoration:none; transition:transform .2s,background .2s; }
        .btn:hover { background:#16613f; transform:translateY(-2px); }
        .event { min-height:245px; border:3px solid #426a57; border-radius:13px; display:flex; align-items:center; padding:28px 30px; overflow:hidden; position:relative; }
        .event-copy { width:49%; z-index:1; }
        .event h3 { margin:0 0 7px; font-size:27px; line-height:1.05; }
        .event p { font-size:13px; line-height:1.25; font-weight:600; margin:0 0 17px; max-width:260px; }
        .event-img { position:absolute; width:50%; height:118%; right:-2%; object-fit:contain; }
        .store { min-height:260px; margin-top:58px; padding:38px 40px; background:linear-gradient(90deg,rgba(5,65,43,.93),rgba(5,65,43,.56)), url('/images/salad-1-clean.webp') center/cover; display:flex; align-items:center; }
        .store h3 { font-size:24px; line-height:1.1; margin:0 0 15px; max-width:300px; }
        .store p { margin:0 0 20px; font-size:14px; }
        @media (max-width:700px) { h2{font-size:22px;margin:20px 6px 12px}.catalog{padding:12px 9px 15px;gap:17px 9px}.product-name{font-size:12px;left:5px;right:5px;bottom:5px}.price{font-size:14px;margin:7px 3px 5px}.btn{padding:7px 8px;font-size:10px}.event{min-height:180px;padding:20px 15px}.event-copy{width:58%}.event h3{font-size:20px}.event p{font-size:10px}.event-img{width:55%;right:-9%}.store{margin-top:35px;min-height:190px;padding:28px 24px}.store h3{font-size:19px} }
    </style>
</head>
<body>
<div class="page">
    @include('customer.partials.header')
    <!-- ─── HERO SECTION ────────────────────────────────────────────── -->
    <section id="home" class="max-w-6xl mx-auto px-4 sm:px-6 pt-4 pb-12">
        <div class="relative rounded-3xl overflow-hidden shadow-lg border-2 border-[#65BD5E]/40 min-h-[460px] md:min-h-[520px] flex items-center bg-gradient-to-r from-[#0B412B] via-[#0B412B]/85 to-[#0B412B]/40">
            <!-- Background Image Banner / Pattern -->
            <img 
                src="{{ asset('images/hamta-background.png') }}" 
                alt="Hero Background" 
                class="absolute inset-0 w-full h-full object-cover object-center mix-blend-overlay opacity-60 pointer-events-none"
            >

            <div class="relative z-10 max-w-2xl px-6 sm:px-12 py-10 text-white">
                <!-- Top Badge -->
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#65BD5E]/20 text-[#65BD5E] border border-[#65BD5E]/40 text-xs font-extrabold uppercase tracking-widest mb-4 backdrop-blur-xs">
                    🥗 Solusi Makan Sehat Hemat Surabaya
                </span>

                <!-- Main Title -->
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold leading-[1.15] tracking-tight">
                    Mau hidup sehat tapi gak boncos banyak? <br class="hidden sm:inline">
                    <span class="text-[#65BD5E]">Yuk pesan di Hamta!</span>
                </h1>

                <!-- Description -->
                <p class="text-gray-200 text-sm sm:text-base mt-4 leading-relaxed font-normal">
                    Katalog salad renyah, wrap lezat, sandwich bergizi, dan jus buah asli tanpa komisi mahal platform ojol. Checkout langsung, transparan, dan praktis via QRIS.
                </p>

                <!-- CTA Buttons -->
                <div class="mt-8 flex flex-wrap items-center gap-3.5">
                    <a 
                        href="#menu" 
                        class="px-6 py-3 rounded-xl bg-[#65BD5E] hover:bg-[#52a64c] active:scale-95 text-[#0B412B] font-extrabold text-sm sm:text-base transition-all duration-200 shadow-md flex items-center gap-2"
                    >
                        <span>Eksplor Katalog</span>
                        <svg class="w-4 h-4 stroke-[3]" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a 
                        href="{{ route('customer.kreasikan_salad') }}" 
                        class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-white border border-white/25 font-bold text-sm sm:text-base transition-all duration-200 backdrop-blur-xs flex items-center gap-2"
                    >
                        <span>✨ Kreasikan Salad Sendiri</span>
                    </a>
                </div>

                <!-- Micro Features -->
                <div class="mt-8 pt-6 border-t border-white/15 grid grid-cols-3 gap-4 text-xs font-medium text-gray-200">
                    <div class="flex items-center gap-2">
                        <span class="text-base">🥬</span>
                        <span>100% Sayur Segar</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-base">💸</span>
                        <span>Harga Lebih Murah</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-base">⚡</span>
                        <span>Pickup & Delivery</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <h2 id="menu">Menu / Katalog</h2>
    <section class="catalog">
        @forelse ($menus as $menu)
            <article class="product">
                <div class="product-art">
                    @if ($menu->foto)
                        <img class="product-photo" src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_produk }}" loading="lazy">
                    @endif
                    <span class="product-name">{{ strtoupper($menu->nama_produk) }}</span>
                </div>
                <div class="price">Rp. {{ number_format($menu->harga_online, 0, ',', '.') }}</div>
                <a class="btn" href="#" data-add-cart data-id="{{ $menu->id_produk }}" data-nama="{{ $menu->nama_produk }}" data-harga="{{ $menu->harga_online }}" @if($menu->foto) data-foto="{{ asset('storage/' . $menu->foto) }}" @endif>+ Add to Cart</a>
            </article>
        @empty
            <p class="catalog-empty">Belum ada menu tersedia saat ini.</p>
        @endforelse
    </section>
    <div class="catalog-more">
        <a class="btn" href="{{ route('customer.daftar_menu') }}">Menu Selengkapnya</a>
    </div>
    <h2 id="kreasi">Event Terbaru dari Hamta Salad</h2>
    <section class="event"><div class="event-copy"><h3>Coba kreasi salad bareng teman-teman!</h3><p>Bingung mau ngapain bareng temen-temen? Mending kreasi Salad kalian dan makan bareng-bareng!</p><a class="btn" href="{{ route('customer.kreasikan_salad') }}">Coba Sekarang!</a></div><img class="event-img" src="{{ asset('images/salad-1-clean.webp') }}" alt="Salad segar Hamta"></section>
    <section class="store" id="promo"><div><h3>Penasaran cerita di balik usaha Hamta Salad?</h3><p>Cuss kepoin!</p><a class="btn" href="#promo">Kepoin sekarang!</a></div></section>
    <!-- ─── TESTIMONI CUSTOMER SECTION ─────────────────────────────────── -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
                <span class="text-xs font-extrabold uppercase tracking-wider text-[#65BD5E]">
                    Ulasan Nyata Pelanggan
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#010101] mt-1">
                    Apa Kata Customer Tercinta Kami?
                </h2>
            </div>
            <div class="flex items-center gap-1 text-amber-500 font-bold text-sm">
                <span>★★★★★</span>
                <span class="text-[#010101] text-xs ml-1">4.9 / 5.0 dari 500+ porsi terjual</span>
            </div>
        </div>

        <!-- Grid Testimoni 3 Kolom -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Testimoni 1 -->
            <div class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#0B412B] to-[#65BD5E] text-white flex items-center justify-center font-bold text-sm">
                                HH
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-[#010101]">Han Hyeong-jun</h4>
                                <span class="text-[11px] text-[#8A8A8A]">Pelanggan Cabang MERR</span>
                            </div>
                        </div>
                        <span class="text-amber-400 text-sm">★★★★★</span>
                    </div>
                    <p class="text-xs text-[#010101] leading-relaxed">
                        "Saladnya juara! Bahan-bahannya super fresh dan garing banget waktu dikunyah. Dressing-nya pas gak enek. Fix jadi langganan makan siang sehat di kantor."
                    </p>
                </div>
                <span class="mt-4 text-[10px] text-[#8A8A8A] font-semibold">Pesanan: Chef Salad & Jus Wortel</span>
            </div>

            <!-- Testimoni 2 -->
            <div class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#0B412B] to-[#72bbcc] text-white flex items-center justify-center font-bold text-sm">
                                SK
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-[#010101]">Siti Khadijah</h4>
                                <span class="text-[11px] text-[#8A8A8A]">Pelanggan Lidah Kulon</span>
                            </div>
                        </div>
                        <span class="text-amber-400 text-sm">★★★★★</span>
                    </div>
                    <p class="text-xs text-[#010101] leading-relaxed">
                        "Fitur kreasikan saladnya ngebantu banget! Saya yang lagi diet defisit kalori bisa atur takaran sayur dan protein sendiri tanpa takut kebanyakan saus."
                    </p>
                </div>
                <span class="mt-4 text-[10px] text-[#8A8A8A] font-semibold">Pesanan: Custom Salad Racik Sendiri</span>
            </div>

            <!-- Testimoni 3 -->
            <div class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#65BD5E] to-emerald-700 text-white flex items-center justify-center font-bold text-sm">
                                RA
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-[#010101]">Rian Anggoro</h4>
                                <span class="text-[11px] text-[#8A8A8A]">Pelanggan Pickup</span>
                            </div>
                        </div>
                        <span class="text-amber-400 text-sm">★★★★★</span>
                    </div>
                    <p class="text-xs text-[#010101] leading-relaxed">
                        "Chicken sandwich-nya padat dan ngenyangin! Harganya masuk akal banget untuk kualitas bahan fresh kayak gini. Checkout di website juga cepet langsung scan QRIS."
                    </p>
                </div>
                <span class="mt-4 text-[10px] text-[#8A8A8A] font-semibold">Pesanan: Chicken Sandwich & Jus Alpukat</span>
            </div>
        </div>
    </section>
</div>
@include('customer.partials.footer')
</body>
</html>
