{{-- Partial footer customer: wave + kolom footer + switcher cabang. --}}
<!-- ─── FOOTER WITH WAVE & BRANCH SWITCHER ─────────────────────────── -->
<div class="relative w-full overflow-hidden leading-none mt-16">
    <!-- SVG Wave Transisi -->
    <svg class="block w-full h-12 md:h-20 text-[#0B412B] fill-current" viewBox="0 0 1440 120" preserveAspectRatio="none">
        <path d="M0,32L60,42.7C120,53,240,75,360,74.7C480,75,600,53,720,48C840,43,960,53,1080,64C1200,75,1320,85,1380,90.7L1440,96L1440,120L1380,120C1320,120,1200,120,1080,120C960,120,840,120,720,120C600,120,480,120,360,120C240,120,120,120,60,120L0,120Z"></path>
    </svg>
</div>

<footer id="lokasi" class="bg-[#0B412B] text-white pt-6 pb-14 px-4 sm:px-6">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12">
        <!-- Kolom 1: Brand & Identitas -->
        <div class="md:col-span-4 flex flex-col justify-between">
            <div>
                <a href="{{ route('home') }}" class="inline-block">
                    <img 
                        src="{{ asset('images/hamta-logo-white.png') }}" 
                        alt="Brand Logo" 
                        class="h-10 w-auto brightness-0 invert"
                    >
                </a>
                <p class="text-emerald-300 font-bold text-sm tracking-wide mt-2">
                    Eat Good, Feel Good
                </p>
                <p class="text-gray-300 text-xs mt-3 leading-relaxed">
                    Menyajikan aneka salad segar, wrap, sandwich sehat, dan fresh juice berkualitas dengan harga terjangkau di Surabaya.
                </p>
            </div>

            <div class="mt-6 pt-6 border-t border-white/10">
                <p class="text-[11px] text-gray-400">
                    &copy; {{ date('Y') }} Hamta Healthy Food. All rights reserved.
                </p>
            </div>
        </div>

        <!-- Kolom 2: Cabang dengan Switcher Tab Interaktif -->
        <div class="md:col-span-5">
            <h4 class="text-sm font-bold uppercase tracking-wider text-emerald-400 mb-3">
                Cabang & Lokasi Kami
            </h4>
            
            <!-- Tombol Switcher Tab -->
            <div class="flex gap-2 p-1 bg-white/10 rounded-xl mb-4 text-xs font-bold">
                <button 
                    type="button" 
                    id="btn-cabang-1"
                    onclick="pilihCabangFooter(1)" 
                    class="flex-1 py-1.5 px-3 rounded-lg bg-[#65BD5E] text-[#0B412B] transition-all cursor-pointer"
                >
                    Lidah Kulon
                </button>
                <button 
                    type="button" 
                    id="btn-cabang-2"
                    onclick="pilihCabangFooter(2)" 
                    class="flex-1 py-1.5 px-3 rounded-lg text-white hover:bg-white/10 transition-all cursor-pointer"
                >
                    MERR (Bale Hinggil)
                </button>
            </div>

            <!-- Detail Cabang 1: Lidah Kulon -->
            <div id="content-cabang-1" class="space-y-2 text-xs leading-relaxed text-gray-200">
                <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
                    <p class="font-bold text-white text-[13px]">Cabang Lidah Kulon</p>
                    <p class="text-gray-300 mt-1">
                        Rumah Produksi, Jl. Lidah Kulon Gg. 1, Lakarsantri, Surabaya, Jawa Timur 60213
                    </p>
                    <p class="text-[11px] text-emerald-200 mt-2 font-medium flex items-center gap-1.5">
                        <x-lucide-clock class="w-3.5 h-3.5 text-emerald-300 shrink-0" />
                        <span>Buka: 09.00 - 21.00 WIB (Pickup &amp; Delivery)</span>
                    </p>
                </div>
            </div>

            <!-- Detail Cabang 2: MERR -->
            <div id="content-cabang-2" class="hidden space-y-2 text-xs leading-relaxed text-gray-200">
                <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
                    <p class="font-bold text-white text-[13px]">Cabang MERR (Apartemen Bale Hinggil)</p>
                    <p class="text-gray-300 mt-1">
                        Bale Hinggil Apartment (Jl. Dr. Ir. H. Soekarno / MERR), Rungkut, Surabaya
                    </p>
                    <p class="text-[11px] text-emerald-200 mt-2 font-medium flex items-center gap-1.5">
                        <x-lucide-clock class="w-3.5 h-3.5 text-emerald-300 shrink-0" />
                        <span>Buka: 09.00 - 21.00 WIB setiap hari (Dine-in &amp; Pickup)</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Kolom 3: Kontak & Media Sosial -->
        <div class="md:col-span-3 space-y-4">
            <div>
                <h4 class="text-sm font-bold uppercase tracking-wider text-emerald-400 mb-2">
                    Hubungi Kami
                </h4>
                <p class="text-xs text-gray-300">
                    Punya pertanyaan seputar katering sehat atau pesanan partai besar?
                </p>
                <div class="mt-3 flex flex-col gap-2">
                    <a 
                        href="{{ route('customer.status_pesanan') }}" 
                        class="inline-flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-white/10 hover:bg-[#65BD5E] hover:text-[#0B412B] text-xs font-bold transition-all text-white"
                    >
                        <x-lucide-search class="w-4 h-4 text-emerald-300" />
                        <span>Lacak Status Pesanan</span>
                    </a>
                    <a 
                        href="https://wa.me/6281234567890" 
                        target="_blank" 
                        class="inline-flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-white/10 hover:bg-[#65BD5E] hover:text-[#0B412B] text-xs font-bold transition-all text-white"
                    >
                        <x-lucide-message-circle class="w-4 h-4 text-emerald-300" />
                        <span>WhatsApp: 0812-3456-7890</span>
                    </a>
                    <a 
                        href="https://instagram.com/hamta.sby" 
                        target="_blank" 
                        class="inline-flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-white/10 hover:bg-[#65BD5E] hover:text-[#0B412B] text-xs font-bold transition-all text-white"
                    >
                        <x-lucide-instagram class="w-4 h-4 text-emerald-300" />
                        <span>Instagram: @hamta.sby</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- JavaScript Switcher Cabang Footer -->
<script>
    function pilihCabangFooter(id) {
        const btn1 = document.getElementById('btn-cabang-1');
        const btn2 = document.getElementById('btn-cabang-2');
        const content1 = document.getElementById('content-cabang-1');
        const content2 = document.getElementById('content-cabang-2');

        if (!btn1 || !btn2 || !content1 || !content2) return;

        if (id === 1) {
            btn1.className = 'flex-1 py-1.5 px-3 rounded-lg bg-[#65BD5E] text-[#0B412B] transition-all cursor-pointer';
            btn2.className = 'flex-1 py-1.5 px-3 rounded-lg text-white hover:bg-white/10 transition-all cursor-pointer';
            content1.classList.remove('hidden');
            content2.classList.add('hidden');
        } else {
            btn2.className = 'flex-1 py-1.5 px-3 rounded-lg bg-[#65BD5E] text-[#0B412B] transition-all cursor-pointer';
            btn1.className = 'flex-1 py-1.5 px-3 rounded-lg text-white hover:bg-white/10 transition-all cursor-pointer';
            content2.classList.remove('hidden');
            content1.classList.add('hidden');
        }
    }
</script>
