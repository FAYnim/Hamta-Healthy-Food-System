<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Menu - Hamta Healthy Food</title>

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
                    <h2 class="text-2xl md:text-3xl font-bold text-[#010101] tracking-tight">Kelola Menu</h2>
                </div>

                <!-- Admin Profile Chip -->
                <div class="hidden lg:flex items-center">
                    <button type="button" class="bg-white hover:bg-stone-50 border border-stone-200/60 rounded-xl px-7 py-2.5 font-bold text-[#010101] text-base shadow-sm transition">
                        {{ session('admin_nama', 'Admin') }}
                    </button>
                </div>
            </header>

            @if (session('success'))
                <div class="bg-[#65BD5E]/15 border border-[#65BD5E]/40 text-[#2f6b2a] text-sm font-semibold rounded-xl px-4 py-3 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Top Action / Filter Bar -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
                
                <!-- Category Tabs -->
                <div class="flex items-center gap-3 w-full md:w-auto overflow-x-auto">
                    @php
                        // List of pairs, bukan array asosiatif: PHP mengubah key null
                        // menjadi "" sehingga $kategori === $nilai tak pernah cocok.
                        $tabs = [
                            ['nilai' => null,       'label' => 'Semua'],
                            ['nilai' => 'Salad',    'label' => 'Salad'],
                            ['nilai' => 'Sandwich', 'label' => 'Sandwich'],
                            ['nilai' => 'Juice',    'label' => 'Juice'],
                        ];
                    @endphp

                    @foreach ($tabs as ['nilai' => $nilai, 'label' => $label])
                        <a href="{{ route('admin.kelola_menu', array_filter(['kategori' => $nilai, 'cari' => $cari], fn ($v) => $v !== null)) }}"
                           class="px-5 py-2 rounded-xl text-sm shrink-0 transition {{ $kategori === $nilai ? 'bg-[#65BD5E] text-white font-bold shadow-sm' : 'text-[#010101] hover:bg-stone-100 font-semibold' }}"
                           @if ($kategori === $nilai) aria-current="page" @endif>
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <!-- Search Input + Tambah Menu Button -->
                <div class="flex items-center gap-3.5 w-full md:w-auto justify-end">
                    <!-- Search Input -->
                    <form method="GET" action="{{ route('admin.kelola_menu') }}" class="relative w-full md:w-64">
                        @if ($kategori)
                            <input type="hidden" name="kategori" value="{{ $kategori }}">
                        @endif
                        <input
                            type="text"
                            name="cari"
                            value="{{ $cari }}"
                            placeholder="Cari Menu"
                            aria-label="Cari menu"
                            class="w-full bg-white border border-stone-300 rounded-xl pl-4 pr-10 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                        <button type="submit" class="absolute inset-y-0 right-0 pr-3.5 flex items-center rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#65BD5E]" aria-label="Cari menu">
                            <x-lucide-search class="w-4 h-4 text-[#8A8A8A]" aria-hidden="true" />
                        </button>
                    </form>

                    <!-- Tambah Menu Button -->
                    <button 
                        type="button" 
                        id="btn-tambah-menu"
                        class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-sm transition shrink-0"
                    >
                        Tambah Menu
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
                                <th class="py-4 px-6 w-32">Foto</th>
                                <th class="py-4 px-6">Menu</th>
                                <th class="py-4 px-6">Kategori</th>
                                <th class="py-4 px-6 text-center w-32">Status</th>
                                <th class="py-4 px-6">Harga</th>
                                <th class="py-4 px-6 text-center w-28">Aksi</th>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-stone-200 text-sm">
                            @forelse ($produk as $item)
                                <tr class="hover:bg-stone-50/50 transition {{ $item->status_aktif ? '' : 'opacity-60' }}">
                                    <td class="py-4 px-6">
                                        <div class="w-20 h-14 bg-[#D9D9D9] rounded-xl flex items-center justify-center overflow-hidden">
                                            @if ($item->foto)
                                                <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_produk }}" class="w-full h-full object-cover">
                                            @else
                                                <x-lucide-image class="w-6 h-6 text-stone-500" />
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-[#010101] text-base">
                                        {{ $item->nama_produk }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-block bg-[#65BD5E] text-white font-semibold text-xs px-4 py-1 rounded-full">
                                            {{ $item->kategori }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <form method="POST" action="{{ route('admin.kelola_menu.status', $item->id_produk) }}" class="inline-flex">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                role="switch"
                                                aria-checked="{{ $item->status_aktif ? 'true' : 'false' }}"
                                                aria-label="Status ketersediaan {{ $item->nama_produk }}"
                                                title="{{ $item->status_aktif ? 'Klik untuk menandai sold-out' : 'Klik untuk mengaktifkan kembali' }}"
                                                class="relative inline-flex h-7 w-12 shrink-0 items-center rounded-full transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0B412B] focus-visible:ring-offset-2 {{ $item->status_aktif ? 'bg-[#65BD5E] hover:bg-[#58a851]' : 'bg-stone-300 hover:bg-stone-400' }}">
                                                <span class="sr-only">{{ $item->status_aktif ? 'Tersedia' : 'Sold-out' }}</span>
                                                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform {{ $item->status_aktif ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-[#010101] text-base">
                                        Rp {{ number_format($item->harga_offline, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" class="btn-edit-menu w-9 h-9 rounded-lg bg-[#E08A3C] hover:bg-[#d07b30] text-white flex items-center justify-center transition"
                                                data-id="{{ $item->id_produk }}"
                                                data-nama="{{ $item->nama_produk }}"
                                                data-deskripsi="{{ $item->deskripsi }}"
                                                data-kategori="{{ $item->kategori }}"
                                                data-harga="{{ (int) round((float) $item->harga_offline) }}"
                                                data-bisa-dikustom="{{ $item->bisa_dikustom ? '1' : '0' }}"
                                                data-foto="{{ $item->foto ? asset('storage/' . $item->foto) : '' }}">
                                                <x-lucide-pencil class="w-4 h-4" />
                                            </button>
                                            <button type="button" class="btn-hapus-menu w-9 h-9 rounded-lg bg-[#E05C5C] hover:bg-[#d04b4b] text-white flex items-center justify-center transition"
                                                data-id="{{ $item->id_produk }}"
                                                data-nama="{{ $item->nama_produk }}">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 px-6 text-center text-[#8A8A8A]">
                                        @if ($produk->total() > 0)
                                            <p class="mb-3">Halaman ini tidak memiliki menu.</p>
                                            <a href="{{ $produk->url(1) }}"
                                               class="inline-block bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2 rounded-xl transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0B412B] focus-visible:ring-offset-2">
                                                Ke halaman pertama
                                            </a>
                                        @elseif ($kategori !== null || $cari !== null)
                                            <p class="mb-3">Tidak ada menu yang cocok dengan filter.</p>
                                            <a href="{{ route('admin.kelola_menu') }}"
                                               class="inline-block bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2 rounded-xl transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0B412B] focus-visible:ring-offset-2">
                                                Reset filter
                                            </a>
                                        @else
                                            Belum ada menu. Klik "Tambah Menu" untuk menambahkan.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($produk->hasPages())
                    <div class="px-6 py-4 border-t border-stone-100">
                        {{ $produk->links() }}
                    </div>
                @endif
            </div>

        </main>

    </div>

    <!-- ========================================== -->
    <!-- MODAL TAMBAH MENU BARU                     -->
    <!-- ========================================== -->
    <div id="modal-tambah" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="judul-modal-tambah">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" data-tutup-modal></div>

        <!-- Panel Modal -->
        <div class="relative mx-auto mt-8 mb-8 w-[calc(100%-2rem)] max-w-[440px] bg-white rounded-2xl shadow-xl p-6 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <h3 id="judul-modal-tambah" class="text-lg font-bold text-[#010101] mb-4">
                Tambah Menu Baru
            </h3>

            <form id="form-tambah-menu" action="{{ route('admin.kelola_menu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Upload Gambar -->
                <div>
                    <label for="foto" class="flex flex-col items-center justify-center gap-1 bg-[#CDE9C7] hover:bg-[#c2e3bb] rounded-xl py-8 cursor-pointer transition" id="area-foto">
                        <div id="foto-placeholder" class="flex flex-col items-center gap-1">
                            <x-lucide-cloud-upload class="w-8 h-8 text-[#58a851]" />
                            <span class="text-sm font-semibold text-[#3d7a37]">Upload Gambar</span>
                        </div>
                        <img id="foto-preview" src="" alt="Preview menu" class="hidden max-h-40 rounded-lg object-contain">
                    </label>
                    <input type="file" id="foto" name="foto" accept="image/*" class="hidden">
                    @error('foto')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Menu -->
                <div>
                    <label for="nama_produk" class="block text-sm font-semibold text-[#010101] mb-1.5">Nama Menu</label>
                    <input 
                        type="text" 
                        id="nama_produk" 
                        name="nama_produk" 
                        value="{{ old('nama_produk') }}" 
                        placeholder="Salad Sayur" 
                        required
                        class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                    >
                    @error('nama_produk')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-semibold text-[#010101] mb-1.5">Deskripsi</label>
                    <input 
                        type="text" 
                        id="deskripsi" 
                        name="deskripsi" 
                        value="{{ old('deskripsi') }}" 
                        placeholder="Salad sayur yang enak"
                        class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                    >
                    @error('deskripsi')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori + Harga -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="kategori" class="block text-sm font-semibold text-[#010101] mb-1.5">Kategori</label>
                        <select 
                            id="kategori" 
                            name="kategori" 
                            required
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                            <option value="" disabled selected hidden>Salad</option>
                            <option value="Salad" {{ old('kategori') == 'Salad' ? 'selected' : '' }}>Salad</option>
                            <option value="Sandwich" {{ old('kategori') == 'Sandwich' ? 'selected' : '' }}>Sandwich</option>
                            <option value="Juice" {{ old('kategori') == 'Juice' ? 'selected' : '' }}>Juice</option>
                        </select>
                        @error('kategori')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="harga" class="block text-sm font-semibold text-[#010101] mb-1.5">Harga</label>
                        <div class="flex items-center bg-white border border-stone-300 rounded-xl focus-within:border-[#65BD5E] focus-within:ring-1 focus-within:ring-[#65BD5E]">
                            <span class="pl-4 pr-1.5 py-2.5 text-sm font-semibold text-[#8A8A8A] select-none">Rp.</span>
                            <input 
                                type="text" 
                                id="harga" 
                                name="harga" 
                                value="{{ old('harga') !== null ? number_format((float) old('harga'), 0, ',', '.') : '' }}" 
                                placeholder="15.000" 
                                inputmode="numeric" 
                                required
                                class="w-full bg-transparent rounded-r-xl px-1 py-2.5 pr-4 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none"
                            >
                        </div>
                        @error('harga')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bisa Dikustom -->
                <label class="flex items-start gap-3 border border-stone-200 rounded-xl px-4 py-3 cursor-pointer hover:bg-stone-50 transition">
                    <input type="checkbox" id="bisa_dikustom" name="bisa_dikustom" value="1" {{ old('bisa_dikustom') ? 'checked' : '' }} class="w-4 h-4 mt-0.5 shrink-0 accent-[#65BD5E]">
                    <span>
                        <span class="block text-sm font-semibold text-[#010101]">Bisa dikreasikan</span>
                        <span class="block text-xs text-[#8A8A8A] mt-0.5">Muncul sebagai salad dasar di menu "Kreasikan Salad Sendiri" pada kasir.</span>
                    </span>
                </label>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-end gap-2.5 pt-1">
                    <button type="button" data-tutup-modal class="bg-[#E5E5E5] hover:bg-[#d9d9d9] text-[#010101] font-bold text-sm px-5 py-2 rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2 rounded-lg transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL EDIT MENU                            -->
    <!-- ========================================== -->
    <div id="modal-edit" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="judul-modal-edit">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" data-tutup-modal-edit></div>

        <!-- Panel Modal -->
        <div class="relative mx-auto mt-8 mb-8 w-[calc(100%-2rem)] max-w-[440px] bg-white rounded-2xl shadow-xl p-6 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <h3 id="judul-modal-edit" class="text-lg font-bold text-[#010101] mb-4">
                Edit Menu
            </h3>

            <form id="form-edit-menu" data-action="{{ route('admin.kelola_menu') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="edit_id" id="edit_id" value="">
                <input type="hidden" name="modal_edit" value="1">
                <input type="hidden" name="foto_lama" id="edit-foto-lama" value="">

                <!-- Upload Gambar -->
                <div>
                    <label for="edit-foto" class="flex flex-col items-center justify-center gap-1 bg-[#CDE9C7] hover:bg-[#c2e3bb] rounded-xl py-8 cursor-pointer transition" id="edit-area-foto">
                        <div id="edit-foto-placeholder" class="flex flex-col items-center gap-1">
                            <x-lucide-cloud-upload class="w-8 h-8 text-[#58a851]" />
                            <span class="text-sm font-semibold text-[#3d7a37]">Upload Gambar Baru</span>
                        </div>
                        <img id="edit-foto-preview" src="" alt="Preview menu" class="hidden max-h-40 rounded-lg object-contain">
                        <span id="edit-foto-hint" class="hidden text-xs font-semibold text-[#3d7a37] mt-1">Klik untuk upload gambar baru</span>
                    </label>
                    <input type="file" id="edit-foto" name="foto" accept="image/*" class="hidden">
                    @error('foto')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Menu -->
                <div>
                    <label for="edit-nama_produk" class="block text-sm font-semibold text-[#010101] mb-1.5">Nama Menu</label>
                    <input 
                        type="text" 
                        id="edit-nama_produk" 
                        name="nama_produk" 
                        value="{{ old('nama_produk') }}" 
                        placeholder="Salad Sayur" 
                        required
                        class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                    >
                    @error('nama_produk')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="edit-deskripsi" class="block text-sm font-semibold text-[#010101] mb-1.5">Deskripsi</label>
                    <input 
                        type="text" 
                        id="edit-deskripsi" 
                        name="deskripsi" 
                        value="{{ old('deskripsi') }}" 
                        placeholder="Salad sayur yang enak"
                        class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                    >
                    @error('deskripsi')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori + Harga -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="edit-kategori" class="block text-sm font-semibold text-[#010101] mb-1.5">Kategori</label>
                        <select 
                            id="edit-kategori" 
                            name="kategori" 
                            required
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                            <option value="Salad" {{ old('kategori') == 'Salad' ? 'selected' : '' }}>Salad</option>
                            <option value="Sandwich" {{ old('kategori') == 'Sandwich' ? 'selected' : '' }}>Sandwich</option>
                            <option value="Juice" {{ old('kategori') == 'Juice' ? 'selected' : '' }}>Juice</option>
                        </select>
                        @error('kategori')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="edit-harga" class="block text-sm font-semibold text-[#010101] mb-1.5">Harga</label>
                        <div class="flex items-center bg-white border border-stone-300 rounded-xl focus-within:border-[#65BD5E] focus-within:ring-1 focus-within:ring-[#65BD5E]">
                            <span class="pl-4 pr-1.5 py-2.5 text-sm font-semibold text-[#8A8A8A] select-none">Rp.</span>
                            <input 
                                type="text" 
                                id="edit-harga" 
                                name="harga" 
                                value="" 
                                placeholder="15.000" 
                                inputmode="numeric" 
                                required
                                class="w-full bg-transparent rounded-r-xl px-1 py-2.5 pr-4 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none"
                            >
                        </div>
                        @error('harga')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bisa Dikustom -->
                <label class="flex items-start gap-3 border border-stone-200 rounded-xl px-4 py-3 cursor-pointer hover:bg-stone-50 transition">
                    <input type="checkbox" id="edit-bisa_dikustom" name="bisa_dikustom" value="1" {{ old('bisa_dikustom') ? 'checked' : '' }} class="w-4 h-4 mt-0.5 shrink-0 accent-[#65BD5E]">
                    <span>
                        <span class="block text-sm font-semibold text-[#010101]">Bisa dikreasikan</span>
                        <span class="block text-xs text-[#8A8A8A] mt-0.5">Muncul sebagai salad dasar di menu "Kreasikan Salad Sendiri" pada kasir.</span>
                    </span>
                </label>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-end gap-2.5 pt-1">
                    <button type="button" data-tutup-modal-edit class="bg-[#E5E5E5] hover:bg-[#d9d9d9] text-[#010101] font-bold text-sm px-5 py-2 rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2 rounded-lg transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL KONFIRMASI HAPUS MENU                 -->
    <!-- ========================================== -->
    <div id="modal-hapus" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="judul-modal-hapus">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" data-tutup-modal-hapus></div>

        <!-- Panel Modal -->
        <div class="relative mx-auto mt-24 mb-8 w-[calc(100%-2rem)] max-w-[400px] bg-white rounded-2xl shadow-xl p-8 text-center">
            <div class="mx-auto w-14 h-14 rounded-full bg-[#E05C5C]/15 flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#E05C5C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>

            <p id="judul-modal-hapus" class="text-base font-semibold text-[#010101] mb-6">
                Yakin untuk menghapus menu ini?
            </p>

            <form id="form-hapus-menu" data-action="{{ route('admin.kelola_menu') }}" method="POST" class="flex items-center justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" data-tutup-modal-hapus class="bg-white border border-stone-300 hover:bg-stone-50 text-[#010101] font-bold text-sm px-5 py-2 rounded-lg transition">
                    Batal
                </button>
                <button type="submit" class="bg-[#E05C5C] hover:bg-[#d04b4b] text-white font-bold text-sm px-5 py-2 rounded-lg transition">
                    Yakin, Hapus
                </button>
            </form>
        </div>
    </div>

    <script>
        const modalTambah = document.getElementById('modal-tambah');
        const modalEdit = document.getElementById('modal-edit');
        const modalHapus = document.getElementById('modal-hapus');
        const formHapus = document.getElementById('form-hapus-menu');
        const btnTambah = document.getElementById('btn-tambah-menu');
        const formTambah = document.getElementById('form-tambah-menu');
        const formEdit = document.getElementById('form-edit-menu');
        const inputHarga = document.getElementById('harga');
        const inputFoto = document.getElementById('foto');
        const fotoPreview = document.getElementById('foto-preview');
        const fotoPlaceholder = document.getElementById('foto-placeholder');
        const editHarga = document.getElementById('edit-harga');
        const editFoto = document.getElementById('edit-foto');
        const editFotoPreview = document.getElementById('edit-foto-preview');
        const editFotoPlaceholder = document.getElementById('edit-foto-placeholder');
        const editFotoHint = document.getElementById('edit-foto-hint');
        const editFotoLama = document.getElementById('edit-foto-lama');

        function bukaModal(el) {
            el.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function tutupModal(el) {
            el.classList.add('hidden');
            if (modalTambah.classList.contains('hidden') && modalEdit.classList.contains('hidden') && modalHapus.classList.contains('hidden')) {
                document.body.style.overflow = '';
            }
        }

        btnTambah.addEventListener('click', () => bukaModal(modalTambah));

        modalTambah.querySelectorAll('[data-tutup-modal]').forEach(el => {
            el.addEventListener('click', () => tutupModal(modalTambah));
        });
        modalEdit.querySelectorAll('[data-tutup-modal-edit]').forEach(el => {
            el.addEventListener('click', () => tutupModal(modalEdit));
        });
        modalHapus.querySelectorAll('[data-tutup-modal-hapus]').forEach(el => {
            el.addEventListener('click', () => tutupModal(modalHapus));
        });

        document.addEventListener('keydown', e => {
            if (e.key !== 'Escape') return;
            if (!modalHapus.classList.contains('hidden')) tutupModal(modalHapus);
            else if (!modalEdit.classList.contains('hidden')) tutupModal(modalEdit);
            else if (!modalTambah.classList.contains('hidden')) tutupModal(modalTambah);
        });

        // Buka modal hapus + set action form delete
        document.querySelectorAll('.btn-hapus-menu').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                formHapus.action = formHapus.dataset.action + '/' + id;
                bukaModal(modalHapus);
            });
        });

        // Format harga ribuan saat diketik; strip titik saat submit
        function pasangFormatHarga(input) {
            input.addEventListener('input', () => {
                const angka = input.value.replace(/\D/g, '');
                input.value = angka ? parseInt(angka, 10).toLocaleString('id-ID') : '';
            });
        }
        function stripHargaSubmit(form, input) {
            form.addEventListener('submit', () => {
                input.value = input.value.replace(/\D/g, '');
            });
        }
        pasangFormatHarga(inputHarga);
        stripHargaSubmit(formTambah, inputHarga);
        pasangFormatHarga(editHarga);
        stripHargaSubmit(formEdit, editHarga);

        // Preview foto sebelum diupload
        function pasangPreviewFoto(input, img, placeholder, hint) {
            input.addEventListener('change', () => {
                const file = input.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    if (hint) hint.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });
        }
        pasangPreviewFoto(inputFoto, fotoPreview, fotoPlaceholder);
        pasangPreviewFoto(editFoto, editFotoPreview, editFotoPlaceholder, editFotoHint);

        // Parse harga: "15000.00" (DECIMAL DB) vs "15.000" (format UI)
        function parseHarga(harga) {
            const s = String(harga ?? '').trim();
            if (!s) return 0;
            if (/^\d{1,3}(\.\d{3})+$/.test(s)) return parseInt(s.replace(/\./g, ''), 10);
            return parseFloat(s) || 0;
        }

        // Buka modal edit + isi data dari tombol baris
        function isiEditModal(d) {
            document.getElementById('edit_id').value = d.id || '';
            formEdit.action = formEdit.dataset.action + '/' + d.id;
            document.getElementById('edit-nama_produk').value = d.nama || '';
            document.getElementById('edit-deskripsi').value = d.deskripsi || '';
            document.getElementById('edit-kategori').value = d.kategori || 'Salad';
            const angkaHarga = parseHarga(d.harga);
            editHarga.value = angkaHarga ? angkaHarga.toLocaleString('id-ID') : '';
            editFoto.value = '';
            editFotoLama.value = d.foto || '';
            document.getElementById('edit-bisa_dikustom').checked = String(d.bisaDikustom) === '1';
            if (d.foto) {
                editFotoPreview.src = d.foto;
                editFotoPreview.classList.remove('hidden');
                editFotoPlaceholder.classList.add('hidden');
                editFotoHint.classList.remove('hidden');
            } else {
                editFotoPreview.classList.add('hidden');
                editFotoPlaceholder.classList.remove('hidden');
                editFotoHint.classList.add('hidden');
            }
            bukaModal(modalEdit);
        }

        document.querySelectorAll('.btn-edit-menu').forEach(btn => {
            btn.addEventListener('click', () => isiEditModal(btn.dataset));
        });

        // Auto buka modal jika validasi gagal (data lama tetap ada)
        @if ($errors->any())
            @if (old('modal_edit'))
                isiEditModal({
                    id: @js(old('edit_id')),
                    nama: @js(old('nama_produk')),
                    deskripsi: @js(old('deskripsi')),
                    kategori: @js(old('kategori')),
                    harga: @js(old('harga')),
                    bisaDikustom: @js(old('bisa_dikustom') ? '1' : '0'),
                    foto: @js(old('foto_lama', '')),
                });
            @else
                bukaModal(modalTambah);
            @endif
        @endif
    </script>

</body>
</html>
