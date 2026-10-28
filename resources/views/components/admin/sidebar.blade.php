@php
    $logo_pengaturan = $logo_pengaturan ?? null;
    $menu_admin = [
        ['rute' => 'admin.dashboard', 'label' => 'Dashboard', 'ikon' => 'home'],
        ['rute' => 'admin.kasir', 'label' => 'Kasir', 'ikon' => 'calculator'],
        ['rute' => 'admin.daftar_pesanan', 'label' => 'Daftar Pesanan', 'ikon' => 'clipboard-list'],
        ['rute' => 'admin.kelola_menu', 'label' => 'Kelola Menu', 'ikon' => 'concierge-bell'],
        ['rute' => 'admin.promo.index', 'label' => 'Promo', 'ikon' => 'tag'],
        ['rute' => 'admin.laporan', 'label' => 'Laporan', 'ikon' => 'bar-chart-2'],
        ['rute' => 'admin.pengaturan', 'label' => 'Pengaturan', 'ikon' => 'store'],
    ];
@endphp

<aside class="w-full lg:w-72 bg-hamta-dark text-white flex flex-col shrink-0 lg:sticky lg:top-0 lg:h-screen lg:overflow-y-auto">
    <div class="px-7 pt-8 pb-6 lg:pb-8 flex items-center justify-between gap-4">
        @if ($logo_pengaturan)
            <img src="{{ Storage::url($logo_pengaturan) }}" alt="Logo Hamta" class="max-h-12 max-w-40 object-contain">
        @else
            <h1 class="text-2xl font-bold tracking-tight text-white">Hamta Panel</h1>
        @endif

        <div class="lg:hidden bg-white text-hamta-black text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm">
            {{ session('admin_nama', 'Admin') }}
        </div>
    </div>

    <nav class="flex-1 px-4 pb-6 space-y-1.5 overflow-x-auto lg:overflow-visible flex lg:flex-col gap-1 lg:gap-0">
        @foreach ($menu_admin as $item)
            @php
                $aktif = request()->routeIs($item['rute']);
            @endphp
            <a href="{{ route($item['rute']) }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-xl whitespace-nowrap transition {{ $aktif ? 'bg-hamta-green text-white font-semibold shadow-sm' : 'text-white/80 hover:text-white hover:bg-white/10 font-medium' }}"
               @if ($aktif) aria-current="page" @endif>
                <x-dynamic-component :component="'lucide-' . $item['ikon']" class="w-5 h-5 shrink-0" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3.5 px-4 py-3 rounded-xl text-white/80 hover:text-white hover:bg-white/10 font-medium transition whitespace-nowrap">
                <x-lucide-log-out class="w-5 h-5 shrink-0" />
                <span>Keluar</span>
            </button>
        </form>
    </nav>
</aside>
