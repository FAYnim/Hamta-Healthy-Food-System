<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Hamta Healthy Food</title>

    <!-- Tailwind CSS via Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

</head>
<body class="bg-hamta-cream text-hamta-black antialiased min-h-screen">

    <div id="kasir-root" class="min-h-screen flex flex-col lg:flex-row">

        <x-admin.sidebar />

        <!-- ========================================== -->
        <!-- 2. MAIN CONTENT AREA (Cream: #F7F2E7)      -->
        <!-- ========================================== -->
        <main class="flex-1 p-6 md:p-8 lg:p-10">

            <!-- Topbar Section -->
            <header class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-hamta-black tracking-tight">Kasir</h2>
                </div>

                <!-- Admin Profile Chip -->
                <div class="hidden lg:flex items-center">
                    <button type="button" class="bg-white hover:bg-stone-50 border border-stone-200/60 rounded-xl px-7 py-2.5 font-bold text-hamta-black text-base shadow-sm transition">
                        {{ session('admin_nama', 'Admin') }}
                    </button>
                </div>
            </header>

            <!-- Flash & Validation Messages -->
            @if (session('success'))
                <div class="bg-hamta-green/15 border border-hamta-green/40 text-[#2f6b2a] text-sm font-semibold rounded-xl px-4 py-3 mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-hamta-red/15 border border-hamta-red/40 text-[#a13c3c] text-sm font-semibold rounded-xl px-4 py-3 mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-hamta-red/15 border border-hamta-red/40 text-[#a13c3c] text-sm rounded-xl px-4 py-3 mb-4">
                    <p class="font-bold mb-1">Transaksi belum bisa disimpan:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $pesan)
                            <li>{{ $pesan }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 2 Columns Layout: Menu Catalog & Shopping Cart -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

                <!-- Left Column (Menu Catalog): 8 Cols -->
                <div class="xl:col-span-7 space-y-6">

                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-lucide-search class="w-5 h-5 text-hamta-gray" />
                        </div>
                        <input
                            type="text"
                            id="kasir-search"
                            placeholder="Cari Item"
                            autocomplete="off"
                            class="w-full bg-white border border-stone-300 rounded-xl pl-12 pr-4 py-3 text-base text-hamta-black placeholder-hamta-gray shadow-sm outline-none focus:border-hamta-green focus:ring-1 focus:ring-hamta-green"
                        >
                    </div>

                    <!-- Category Filter Pills -->
                    <div class="flex items-center gap-3 overflow-x-auto pb-2">
                        <button type="button" data-filter-kategori="" class="px-5 py-2 rounded-full bg-hamta-green text-white font-semibold text-sm shadow-sm shrink-0 transition">
                            Semua
                        </button>
                        <button type="button" data-filter-kategori="Salad" class="px-5 py-2 rounded-full border border-hamta-black bg-white text-hamta-black hover:bg-stone-100 font-semibold text-sm transition shrink-0">
                            Salad
                        </button>
                        <button type="button" data-filter-kategori="Sandwich" class="px-5 py-2 rounded-full border border-hamta-black bg-white text-hamta-black hover:bg-stone-100 font-semibold text-sm transition shrink-0">
                            Sandwich
                        </button>
                        <button type="button" data-filter-kategori="Juice" class="px-5 py-2 rounded-full border border-hamta-black bg-white text-hamta-black hover:bg-stone-100 font-semibold text-sm transition shrink-0">
                            Juice
                        </button>
                        <button
                            type="button"
                            id="kasir-buka-custom"
                            class="px-5 py-2 rounded-full border border-hamta-dark bg-white text-hamta-dark hover:bg-[#CDE9C7] font-semibold text-sm transition shrink-0"
                        >
                            Kreasikan
                        </button>
                    </div>

                    <!-- Product Grid (2 Columns) -->
                    <div id="kasir-produk-grid" class="grid grid-cols-2 gap-4">
                        @forelse ($produk as $item)
                            <button
                                type="button"
                                data-produk-card
                                data-id="{{ $item->id_produk }}"
                                data-nama="{{ $item->nama_produk }}"
                                data-harga="{{ (float) $item->harga_offline }}"
                                data-kategori="{{ $item->kategori }}"
                                class="kasir-produk-card bg-white rounded-2xl p-4 shadow-sm border border-stone-100 hover:shadow-md hover:border-hamta-green/50 transition cursor-pointer text-left w-full focus:outline-none focus-visible:ring-2 focus-visible:ring-hamta-green"
                            >
                                <div class="w-full aspect-square bg-[#D9D9D9] rounded-xl mb-3 flex items-center justify-center overflow-hidden">
                                    @if ($item->foto)
                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_produk }}" class="w-full h-full object-contain" loading="lazy">
                                    @else
                                        <x-lucide-image class="w-8 h-8 text-stone-400" />
                                    @endif
                                </div>
                                <h4 class="font-bold text-hamta-black text-base truncate">{{ $item->nama_produk }}</h4>
                                <p class="font-bold text-hamta-black text-base mt-1">Rp {{ number_format($item->harga_offline, 0, ',', '.') }}</p>
                            </button>
                        @empty
                            <div class="col-span-full bg-white rounded-2xl p-10 text-center text-hamta-gray border border-stone-100">
                                Belum ada menu yang tersedia.
                            </div>
                        @endforelse
                    </div>

                    <!-- No Search Result -->
                    <div id="kasir-empty-filter" class="hidden bg-white rounded-2xl p-10 text-center text-hamta-gray border border-stone-100">
                        Tidak ada menu yang cocok dengan pencarian / kategori ini.
                    </div>

                    <!-- Promo yang sedang berlaku (dihitung otomatis oleh PromoService) -->
                    @if ($promoAktif->isNotEmpty())
                        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100">
                            <div class="flex items-center gap-2.5 mb-3">
                                <x-lucide-tag class="w-5 h-5 text-hamta-green" />
                                <h3 class="text-base font-bold text-hamta-black">Promo Berjalan</h3>
                            </div>

                            <ul class="space-y-2">
                                @foreach ($promoAktif as $item)
                                    <li class="flex items-start justify-between gap-3 text-sm">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-hamta-black truncate">{{ $item->nama_promo }}</p>
                                            <p class="text-xs text-hamta-gray mt-0.5">
                                                @if ($item->tipe_potongan === 'persen')
                                                    Diskon {{ rtrim(rtrim(number_format((float) $item->nilai_potongan, 2, ',', '.'), '0'), ',') }}%
                                                @elseif ($item->tipe_potongan === 'gratis_ongkir')
                                                    Gratis ongkir
                                                @else
                                                    Potongan Rp {{ number_format((float) $item->nilai_potongan, 0, ',', '.') }}
                                                @endif
                                                @if ($item->syarat_min_porsi)
                                                    &middot; min. {{ $item->syarat_min_porsi }} porsi
                                                @endif
                                            </p>
                                        </div>
                                        <span class="shrink-0 text-xs font-semibold text-hamta-gray bg-stone-100 rounded-lg px-2 py-1">
                                            sisa {{ (int) $item->batas_penggunaan - (int) $item->jumlah_terpakai }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            <p class="text-xs text-hamta-gray mt-3 pt-3 border-t border-stone-100">
                                Promo otomatis terpakai saat transaksi disimpan, sesuai syarat &amp; kuota.
                            </p>
                        </div>
                    @endif

                </div>

                <!-- Right Column (Shopping Cart / Keranjang): 4 Cols -->
                <div class="xl:col-span-5 bg-white rounded-2xl p-6 shadow-sm border border-stone-100">

                    <form id="kasir-form" method="POST" action="{{ route('admin.kasir.store') }}" class="flex flex-col">
                        @csrf

                        <!-- Cart Header -->
                        <div class="flex items-center gap-2.5 mb-6">
                            <x-lucide-shopping-cart class="w-6 h-6 text-hamta-black" />
                            <h3 class="text-xl font-bold text-hamta-black">Keranjang</h3>
                        </div>

                        <!-- Cart Items List (rendered by JS) -->
                        <div id="kasir-cart-list" class="space-y-4"></div>

                        <!-- Empty State -->
                        <div id="kasir-cart-empty" class="py-10 text-center text-hamta-gray text-sm">
                            Belum ada item di keranjang.
                        </div>

                        <!-- Cart Summary -->
                        <div class="mt-6 pt-4 border-t border-stone-200 space-y-3">
                            <div class="flex justify-between text-sm text-hamta-black">
                                <span>Sub-total</span>
                                <span id="kasir-subtotal" class="font-semibold">Rp 0</span>
                            </div>

                            <div class="border-t border-stone-200 pt-3 flex justify-between text-base font-bold text-hamta-black">
                                <span>Total</span>
                                <span id="kasir-total">Rp 0</span>
                            </div>
                        </div>

                        <!-- Customer Data -->
                        <div class="mt-4 pt-4 border-t border-stone-200 space-y-3">
                            <div>
                                <label for="nama_pelanggan" class="block text-xs font-semibold text-hamta-black mb-1.5">Nama Pelanggan <span class="font-normal text-hamta-gray">(opsional)</span></label>
                                <input
                                    type="text"
                                    id="nama_pelanggan"
                                    name="nama_pelanggan"
                                    value="{{ old('nama_pelanggan') }}"
                                    placeholder="Walk-in"
                                    maxlength="100"
                                    class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-hamta-black placeholder-hamta-gray outline-none focus:border-hamta-green focus:ring-1 focus:ring-hamta-green"
                                >
                            </div>

                            <div>
                                <label for="no_whatsapp" class="block text-xs font-semibold text-hamta-black mb-1.5">No. WhatsApp <span class="font-normal text-hamta-gray">(opsional)</span></label>
                                <input
                                    type="text"
                                    id="no_whatsapp"
                                    name="no_whatsapp"
                                    value="{{ old('no_whatsapp') }}"
                                    placeholder="08xxxxxxxxxx"
                                    maxlength="20"
                                    inputmode="tel"
                                    class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-hamta-black placeholder-hamta-gray outline-none focus:border-hamta-green focus:ring-1 focus:ring-hamta-green"
                                >
                            </div>

                            <div>
                                <label for="id_cabang" class="block text-xs font-semibold text-hamta-black mb-1.5">Cabang</label>
                                <select
                                    id="id_cabang"
                                    name="id_cabang"
                                    required
                                    class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-hamta-black outline-none focus:border-hamta-green focus:ring-1 focus:ring-hamta-green"
                                >
                                    <option value="" disabled {{ old('id_cabang') ? '' : 'selected' }} hidden>Pilih cabang</option>
                                    @foreach ($cabang as $item)
                                        <option value="{{ $item->id_cabang }}" {{ (string) old('id_cabang') === (string) $item->id_cabang ? 'selected' : '' }}>
                                            {{ $item->nama_cabang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <span class="block text-xs font-semibold text-hamta-black mb-1.5">Metode Bayar</span>
                                <div class="flex gap-2">
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="metode_bayar" value="tunai" class="peer sr-only" {{ old('metode_bayar', 'tunai') === 'tunai' ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center gap-2 rounded-xl border border-stone-300 py-2.5 text-sm font-semibold text-hamta-black transition peer-checked:bg-hamta-green peer-checked:text-white peer-checked:border-hamta-green peer-focus-visible:ring-2 peer-focus-visible:ring-hamta-dark">
                                            <x-lucide-banknote class="w-4 h-4" /> Tunai
                                        </span>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="metode_bayar" value="qris" class="peer sr-only" {{ old('metode_bayar') === 'qris' ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center gap-2 rounded-xl border border-stone-300 py-2.5 text-sm font-semibold text-hamta-black transition peer-checked:bg-hamta-green peer-checked:text-white peer-checked:border-hamta-green peer-focus-visible:ring-2 peer-focus-visible:ring-hamta-dark">
                                            <x-lucide-qr-code class="w-4 h-4" /> QRIS
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden cart inputs generated by JS on submit -->
                        <div id="kasir-cart-inputs"></div>

                        <button
                            type="submit"
                            id="kasir-submit"
                            disabled
                            class="w-full bg-hamta-green hover:bg-[#58a851] disabled:hover:bg-hamta-green text-white font-bold text-base rounded-xl py-3.5 shadow-sm transition mt-5 opacity-50 cursor-not-allowed"
                        >
                            Tambah Pesanan
                        </button>
                    </form>

                </div>

            </div>

        </main>

    </div>

    <!-- ========================================== -->
    <!-- MODAL KREASIKAN SALAD SENDIRI               -->
    <!-- ========================================== -->
    <div id="kasir-custom" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="judul-kasir-custom">
        <div class="absolute inset-0 bg-black/40" data-tutup-custom></div>

        <div class="relative mx-auto mt-8 mb-8 w-[calc(100%-2rem)] max-w-[560px] bg-white rounded-2xl shadow-xl p-6 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 id="judul-kasir-custom" class="text-lg font-bold text-hamta-black">Kreasikan Salad Sendiri</h3>
                    <p class="text-xs text-hamta-gray mt-0.5">Pilih salad dasar, lalu tambahkan sayuran &amp; protein.</p>
                </div>
                <button type="button" data-tutup-custom class="w-8 h-8 rounded-lg hover:bg-stone-100 flex items-center justify-center text-hamta-gray transition" aria-label="Tutup">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            @php
                $saladDasar = $produk->where('bisa_dikustom', true);
            @endphp

            @if ($saladDasar->isEmpty())
                <p class="text-sm text-hamta-gray py-6 text-center">Belum ada salad yang bisa dikustom.</p>
            @else
                <!-- Base Product -->
                <div class="mb-4">
                    <label for="kasir-custom-base" class="block text-sm font-semibold text-hamta-black mb-1.5">Salad Dasar</label>
                    <select
                        id="kasir-custom-base"
                        class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-hamta-black outline-none focus:border-hamta-green focus:ring-1 focus:ring-hamta-green"
                    >
                        <option value="" data-harga="0" data-nama="" selected>Pilih salad dasar</option>
                        @foreach ($saladDasar as $item)
                            <option value="{{ $item->id_produk }}" data-harga="{{ (float) $item->harga_offline }}" data-nama="{{ $item->nama_produk }}" data-foto="{{ $item->foto ? asset('storage/' . $item->foto) : '' }}">
                                {{ $item->nama_produk }} — Rp {{ number_format($item->harga_offline, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sayuran -->
                <div class="mb-4">
                    <p class="text-sm font-semibold text-hamta-black mb-2">Sayuran</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @forelse ($sayuran as $item)
                            <label class="flex items-center justify-between gap-2 border border-stone-200 rounded-xl px-3 py-2.5 cursor-pointer hover:bg-stone-50 transition">
                                <span class="flex items-center gap-2 min-w-0">
                                    <input type="checkbox" value="{{ $item->id_komponen }}" data-harga="{{ (float) $item->harga_tambahan }}" data-nama="{{ $item->nama_komponen }}" class="kasir-komponen w-4 h-4 shrink-0 accent-hamta-green">
                                    <span class="text-sm text-hamta-black truncate">{{ $item->nama_komponen }}</span>
                                </span>
                                <span class="text-xs font-semibold text-hamta-gray shrink-0">+ Rp {{ number_format($item->harga_tambahan, 0, ',', '.') }}</span>
                            </label>
                        @empty
                            <p class="text-xs text-hamta-gray">Belum ada sayuran.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Protein -->
                <div class="mb-4">
                    <p class="text-sm font-semibold text-hamta-black mb-2">Protein</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @forelse ($protein as $item)
                            <label class="flex items-center justify-between gap-2 border border-stone-200 rounded-xl px-3 py-2.5 cursor-pointer hover:bg-stone-50 transition">
                                <span class="flex items-center gap-2 min-w-0">
                                    <input type="checkbox" value="{{ $item->id_komponen }}" data-harga="{{ (float) $item->harga_tambahan }}" data-nama="{{ $item->nama_komponen }}" class="kasir-komponen w-4 h-4 shrink-0 accent-hamta-green">
                                    <span class="text-sm text-hamta-black truncate">{{ $item->nama_komponen }}</span>
                                </span>
                                <span class="text-xs font-semibold text-hamta-gray shrink-0">+ Rp {{ number_format($item->harga_tambahan, 0, ',', '.') }}</span>
                            </label>
                        @empty
                            <p class="text-xs text-hamta-gray">Belum ada protein.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Footer: price + add -->
                <div class="flex items-center justify-between gap-3 pt-4 border-t border-stone-200">
                    <div>
                        <p class="text-xs text-hamta-gray font-medium">Harga</p>
                        <p id="kasir-custom-price" class="text-lg font-bold text-hamta-black">Rp 0</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button type="button" data-tutup-custom class="bg-[#E5E5E5] hover:bg-[#d9d9d9] text-hamta-black font-bold text-sm px-5 py-2.5 rounded-lg transition">
                            Batal
                        </button>
                        <button type="button" id="kasir-custom-add" disabled class="bg-hamta-green hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2.5 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Tambah ke Keranjang
                        </button>
                    </div>
                </div>
                <p id="kasir-custom-hint" class="text-xs text-hamta-gray mt-2 text-right">Pilih salad dasar dan minimal satu komponen.</p>
            @endif
        </div>
    </div>

    {{-- Keranjang lama dipulihkan JS setelah validasi gagal --}}
    {{-- json_encode langsung: @json() memecah argumen lewat explode(','), koma di dalam array bikin compile rusak --}}
    <script type="application/json" id="kasir-old-cart">{!! json_encode(old('cart', []), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

</body>
</html>
