<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hamta — Eat Good, Feel Good</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --hamta-green: #0b412b;
            --hamta-lime: #65bd5e;
            --hamta-cream: #f7f2e7;
            --hamta-ink: #101010;
            --hamta-gray: #667085;
            --hamta-border: #426a57;
        }

        .store { position:relative; border:3px solid var(--green, #0b412b); border-radius:13px; overflow:hidden; color:white; }
        h2:not(.tracking-tight) { margin:26px 8px 18px; font-size:29px; line-height:1.15; letter-spacing:-1px; }

        .catalog {
            border: 2px solid var(--hamta-border);
            border-radius: 18px;
            padding: 22px 18px 24px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px 18px;
            margin-bottom: 24px;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(4px);
        }

        /* Product Card */
        .product-card {
            background: #ffffff;
            border: 1.5px solid rgba(66, 106, 87, 0.22);
            border-radius: 16px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
            position: relative;
            cursor: pointer;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -6px rgba(11, 65, 43, 0.16);
            border-color: var(--hamta-lime);
        }

        .product-thumb-wrap {
            aspect-ratio: 1 / 1;
            border-radius: 12px;
            background: #f0f7ef;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .product-thumb-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-thumb-photo {
            transform: scale(1.05);
        }

        .product-thumb-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 40% 40%, #eaf7e8 0%, #d8ebd5 100%);
            color: var(--hamta-green);
        }

        .badge-cabang {
            position: absolute;
            top: 8px;
            left: 8px;
            z-index: 2;
            background: rgba(11, 65, 43, 0.92);
            color: #ffffff;
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            backdrop-filter: blur(4px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.2px;
        }

        .product-cabang-tag {
            font-size: 11px;
            font-weight: 700;
            color: var(--hamta-green);
            background: #eef5ea;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        .product-meta {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 12px;
            flex-grow: 1;
        }

        .product-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--hamta-ink);
            line-height: 1.3;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 42px;
        }

        .product-card:hover .product-name {
            color: var(--hamta-green);
        }

        .product-price-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 6px;
            border-top: 1px dashed rgba(66, 106, 87, 0.18);
        }

        .product-price {
            font-size: 17px;
            font-weight: 800;
            color: var(--hamta-green);
            letter-spacing: -0.3px;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .product-actions {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-view-detail {
            background: #ffffff;
            border: 1.5px solid var(--hamta-green);
            color: var(--hamta-green);
            padding: 8px 12px;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 700;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .btn-view-detail:hover {
            background: #edf5ed;
            color: var(--hamta-green);
        }

        .btn-quick-add {
            background: var(--hamta-green);
            border: 1.5px solid var(--hamta-green);
            color: #ffffff;
            padding: 8px 12px;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .btn-quick-add:hover {
            background: #145938;
            border-color: #145938;
            transform: translateY(-1px);
        }

        .catalog-more { display:flex; justify-content:center; margin:18px 0 6px; }
        .catalog-empty { grid-column:1 / -1; text-align:center; font-size:14px; font-weight:600; padding:24px 0; }
        .btn { display:inline-block; padding:9px 15px; border:0; border-radius:7px; background:var(--green, #0b412b); color:#fff; font-weight:700; font-size:13px; text-decoration:none; transition:transform .2s,background .2s; }
        .btn:hover { background:#16613f; transform:translateY(-2px); }
        .event { min-height:260px; border:3px solid #426a57; border-radius:13px; display:flex; align-items:center; padding:36px 44px; overflow:hidden; position:relative; }
        .event-copy { width:49%; z-index:1; }
        .event h3 { margin:0 0 7px; font-size:27px; line-height:1.05; }
        .event p { font-size:13px; line-height:1.25; font-weight:600; margin:0 0 17px; max-width:260px; }
        .event-img { position:absolute; width:50%; height:118%; right:-2%; object-fit:contain; }
        .store { min-height:260px; margin-top:58px; padding:38px 40px; background:linear-gradient(90deg,rgba(5,65,43,.93),rgba(5,65,43,.56)), url('/images/salad-1-clean.webp') center/cover; display:flex; align-items:center; }
        .store h3 { font-size:24px; line-height:1.1; margin:0 0 15px; max-width:300px; }
        .store p { margin:0 0 20px; font-size:14px; }

        /* Tablet Responsive (769px - 1024px) */
        @media (min-width: 769px) and (max-width: 1024px) {
            .catalog {
                grid-template-columns: repeat(3, 1fr);
                gap: 16px 14px;
                padding: 18px 14px 20px;
            }
            .event { min-height: 220px; padding: 28px 30px; }
            .event-copy { width: 55%; }
            .event h3 { font-size: 22px; }
            .event-img { width: 50%; right: -5%; }
            .store { min-height: 220px; padding: 30px 32px; }
        }

        /* Mobile Responsive (<= 768px) */
        @media (max-width: 768px) {
            h2:not(.tracking-tight) { font-size: 22px; margin: 20px 6px 12px; }
            .catalog {
                grid-template-columns: repeat(2, 1fr);
                padding: 14px 10px 18px;
                gap: 14px 10px;
                margin-bottom: 22px;
            }
            .product-card {
                padding: 9px;
                border-radius: 13px;
            }
            .product-name {
                font-size: 13.5px;
                min-height: 36px;
            }
            .product-price {
                font-size: 14.5px;
            }
            .product-actions {
                grid-template-columns: 1fr;
                gap: 5px;
                margin-top: 6px;
            }
            .btn-view-detail,
            .btn-quick-add {
                padding: 6px 8px;
                font-size: 11px;
                border-radius: 8px;
            }
            .event { min-height: 190px; padding: 24px 20px; }
            .event-copy { width: 58%; }
            .event h3 { font-size: 20px; }
            .event p { font-size: 10px; }
            .event-img { width: 55%; right: -9%; }
            .store { margin-top: 35px; min-height: 190px; padding: 28px 24px; }
            .store h3 { font-size: 19px; }
        }

        /* Small Phones (<= 420px) */
        @media (max-width: 420px) {
            .catalog {
                gap: 10px 8px;
                padding: 10px 8px 14px;
            }
            .product-card {
                padding: 8px;
                border-radius: 11px;
            }
            .product-name {
                font-size: 12px;
                min-height: 32px;
            }
            .product-price {
                font-size: 13px;
            }
            .btn-view-detail,
            .btn-quick-add {
                padding: 5px 6px;
                font-size: 10.5px;
                border-radius: 7px;
            }
        }
    </style>
</head>
<body>
<div class="page">
    @include('customer.partials.header')
    <!-- ─── HERO SECTION ────────────────────────────────────────────── -->
    <section id="home" class="w-full pt-4 pb-12">
        <div class="relative rounded-3xl overflow-hidden shadow-lg border-2 border-[#65BD5E]/40 min-h-[380px] sm:min-h-[440px] md:min-h-[500px] lg:min-h-[520px] flex items-center bg-gradient-to-r from-[#0B412B] via-[#0B412B]/85 to-[#0B412B]/40">
            <!-- Background Image Banner / Pattern -->
            <img 
                src="{{ asset('images/hamta-background.png') }}" 
                alt="Hero Background" 
                class="absolute inset-0 w-full h-full object-cover object-center mix-blend-overlay opacity-60 pointer-events-none"
            >

            <div class="relative z-10 max-w-2xl px-5 sm:px-10 md:px-12 py-8 sm:py-10 text-white">
                <!-- Main Title -->
                <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold leading-[1.15] tracking-tight">
                    Mau hidup sehat tapi gak boncos banyak? <br class="hidden sm:inline">
                    <span class="text-[#65BD5E]">Yuk pesan di Hamta!</span>
                </h1>

                <!-- CTA Buttons -->
                <div class="mt-8 flex flex-wrap items-center gap-3.5">
                    <a 
                        href="#menu" 
                        class="px-6 py-3 rounded-xl bg-[#65BD5E] hover:bg-[#52a64c] active:scale-95 text-[#0B412B] font-extrabold text-sm sm:text-base transition-all duration-200 shadow-md flex items-center gap-2"
                    >
                        <span>Eksplor Katalog</span>
                    </a>
                    <a 
                        href="{{ route('customer.kreasikan_salad') }}" 
                        class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-white border border-white/25 font-bold text-sm sm:text-base transition-all duration-200 backdrop-blur-xs flex items-center gap-2"
                    >
                        <span>Kreasikan Salad Sendiri</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
    <h2 id="menu">Menu / Katalog</h2>
    <section class="catalog">
        @forelse ($menus->take(8) as $menu)
            <article class="product-card" 
                     data-product-card 
                     data-id="{{ $menu->id_produk }}" 
                     data-nama="{{ $menu->nama_produk }}" 
                     data-harga="{{ $menu->harga_online }}" 
                     data-kategori="{{ $menu->kategori ?? 'Salad' }}"
                     data-deskripsi="{{ $menu->deskripsi ?? '' }}"
                     data-foto="{{ $menu->foto ? asset('storage/' . $menu->foto) : '' }}"
                     data-cabang="{{ $menu->cabang ? $menu->cabang->nama_cabang : '' }}"
                     data-bahan="{{ $menu->bahan ? $menu->bahan->pluck('nama_bahan')->implode(', ') : '' }}"
                     data-alergen="{{ $menu->bahan ? $menu->bahan->whereNotNull('keterangan')->map(fn($b) => $b->nama_bahan . ' (' . $b->keterangan . ')')->implode(', ') : '' }}">
                <div class="product-thumb-wrap">
                    @if ($menu->cabang)
                        <span class="badge-cabang">Khusus {{ $menu->cabang->nama_cabang }}</span>
                    @endif
                    @if ($menu->foto)
                        <img class="product-thumb-photo" src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_produk }}" loading="lazy">
                    @else
                        <div class="product-thumb-fallback">
                            <svg class="w-12 h-12 opacity-40 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                            <span class="text-xs font-semibold">Hamta {{ $menu->kategori ?? 'Salad' }}</span>
                        </div>
                    @endif
                </div>
                <div class="product-meta">
                    <h3 class="product-name" title="{{ $menu->nama_produk }}">{{ $menu->nama_produk }}</h3>
                    <div class="product-price-row">
                        <span class="product-price">Rp {{ number_format($menu->harga_online, 0, ',', '.') }}</span>
                        @if ($menu->cabang)
                            <span class="product-cabang-tag" title="Hanya di Cabang {{ $menu->cabang->nama_cabang }}">📍 {{ $menu->cabang->nama_cabang }}</span>
                        @endif
                    </div>
                </div>
                <div class="product-actions">
                    <button type="button" class="btn-view-detail" data-open-modal-btn>Lihat Detail</button>
                    <button type="button" class="btn-quick-add" data-add-cart data-id="{{ $menu->id_produk }}" data-nama="{{ $menu->nama_produk }}" data-harga="{{ $menu->harga_online }}" @if($menu->foto) data-foto="{{ asset('storage/' . $menu->foto) }}" @endif>+ Tambah</button>
                </div>
            </article>
        @empty
            <p class="catalog-empty">Belum ada menu tersedia saat ini.</p>
        @endforelse
    </section>
    <div class="catalog-more">
        <a class="btn" href="{{ route('customer.daftar_menu') }}">Menu Selengkapnya</a>
    </div>
    <h2 id="kreasi">Event Terbaru dari Hamta Salad</h2>
    <section class="event"><div class="event-copy"><h3>Coba kreasi salad bareng teman-teman!</h3><p>Bingung mau ngapain bareng temen-temen? Mending kreasi Salad kalian dan makan bareng-bareng!</p><a class="btn" href="{{ route('customer.kreasikan_salad') }}">Coba Sekarang!</a></div><img class="event-img" src="{{ asset('images/event-salad-kreasi.webp') }}" alt="Kreasi Salad Hamta"></section>
    <section class="store" id="cerita">
        <div>
            <h3>Penasaran cerita di balik usaha Hamta Salad?</h3>
            <p>Dari impian makan sehat terjangkau hingga menghadirkan salad segar higienis untuk Surabaya. Cuss kepoin kisah kami!</p>
            <a class="btn" href="{{ route('customer.cerita') }}">Kepoin Sekarang ➔</a>
        </div>
    </section>
    <!-- ─── TESTIMONI CUSTOMER SECTION ─────────────────────────────────── -->
    <section class="w-full py-12">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#010101] mt-1">
                    Apa Kata Customer Tercinta Kami?
                </h2>
            </div>
            <div class="flex items-center gap-1 text-amber-500 font-bold text-sm">
                <span class="flex items-center gap-0.5 text-amber-400">
                    @for ($i = 0; $i < 5; $i++)
                        <x-lucide-star class="w-4 h-4 fill-current text-amber-400" />
                    @endfor
                </span>
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
                            </div>
                        </div>
                        <span class="flex items-center gap-0.5 text-amber-400">
                            @for ($i = 0; $i < 5; $i++)
                                <x-lucide-star class="w-3.5 h-3.5 fill-current text-amber-400" />
                            @endfor
                        </span>
                    </div>
                    <p class="text-xs text-[#010101] leading-relaxed">
                        "Saladnya juara! Bahan-bahannya super fresh dan garing banget waktu dikunyah. Dressing-nya pas gak enek. Fix jadi langganan makan siang sehat di kantor."
                    </p>
                </div>
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
                            </div>
                        </div>
                        <span class="flex items-center gap-0.5 text-amber-400">
                            @for ($i = 0; $i < 5; $i++)
                                <x-lucide-star class="w-3.5 h-3.5 fill-current text-amber-400" />
                            @endfor
                        </span>
                    </div>
                    <p class="text-xs text-[#010101] leading-relaxed">
                        "Fitur kreasikan saladnya ngebantu banget! Saya yang lagi diet defisit kalori bisa atur takaran sayur dan protein sendiri tanpa takut kebanyakan saus."
                    </p>
                </div>
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
                            </div>
                        </div>
                        <span class="flex items-center gap-0.5 text-amber-400">
                            @for ($i = 0; $i < 5; $i++)
                                <x-lucide-star class="w-3.5 h-3.5 fill-current text-amber-400" />
                            @endfor
                        </span>
                    </div>
                    <p class="text-xs text-[#010101] leading-relaxed">
                        "Chicken sandwich-nya padat dan ngenyangin! Harganya masuk akal banget untuk kualitas bahan fresh kayak gini. Checkout di website juga cepet langsung scan QRIS."
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
@include('customer.partials.modal-detail-menu')
@include('customer.partials.footer')
</body>
</html>
