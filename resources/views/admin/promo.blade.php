<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo - Hamta Healthy Food</title>

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
                    <h2 class="text-2xl md:text-3xl font-bold text-[#010101] tracking-tight">Promo</h2>
                </div>

                <!-- Admin Profile Chip -->
                <div class="hidden lg:flex items-center">
                    <button type="button" class="bg-white hover:bg-stone-50 border border-stone-200/60 rounded-xl px-7 py-2.5 font-bold text-[#010101] text-base shadow-sm transition">
                        {{ session('admin_nama', 'Admin') }}
                    </button>
                </div>
            </header>

            @if (session('success'))
                <div class="bg-[#65BD5E]/15 border border-[#65BD5E] text-[#0B412B] text-sm font-semibold rounded-xl px-4 py-3 mb-6">
                    {{ session('success') }}
                </div>
            @elseif (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm font-semibold rounded-xl px-4 py-3 mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Top Action Bar -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 mb-6 flex items-center justify-end">
                <div class="flex items-center gap-3.5 w-full md:w-auto justify-end">
                    <!-- Search Input -->
                    <form method="GET" action="{{ route('admin.promo.index') }}" class="relative w-full md:w-64">
                        <input
                            type="text"
                            name="q"
                            value="{{ $q }}"
                            placeholder="Cari Promo"
                            aria-label="Cari promo"
                            class="w-full bg-white border border-stone-300 rounded-xl pl-4 pr-10 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                        <button type="submit" class="absolute inset-y-0 right-0 pr-3.5 flex items-center rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#65BD5E]" aria-label="Cari promo">
                            <x-lucide-search class="w-4 h-4 text-[#8A8A8A]" aria-hidden="true" />
                        </button>
                    </form>

                    <!-- Tambah Promo Button -->
                    <button type="button" id="btn-tambah-promo"
                       class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-sm transition shrink-0">
                        Tambah Promo
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
                                <th class="py-4 px-6">Promo</th>
                                <th class="py-4 px-6 text-center w-40">Kuota</th>
                                <th class="py-4 px-6 w-52">Periode</th>
                                <th class="py-4 px-6 text-center w-36">Status</th>
                                <th class="py-4 px-6 text-center w-32">Aksi</th>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody class="divide-y divide-stone-200 text-sm">
                            @forelse ($promo as $item)
                                @php
                                    // Ringkasan tipe potongan untuk baris tabel
                                    if ($item->tipe_potongan === 'persen') {
                                        $ringkas = 'Diskon ' . rtrim(rtrim(number_format((float) $item->nilai_potongan, 2, ',', '.'), '0'), ',') . '%';
                                    } elseif ($item->tipe_potongan === 'gratis_ongkir') {
                                        $ringkas = 'Gratis ongkir';
                                    } else {
                                        $ringkas = 'Potongan Rp ' . number_format((float) $item->nilai_potongan, 0, ',', '.');
                                    }
                                @endphp
                                <tr class="hover:bg-stone-50/50 transition {{ $item->status_aktif ? '' : 'opacity-60' }}"
                                    data-id="{{ $item->id_promo }}"
                                    data-nama="{{ $item->nama_promo }}"
                                    data-deskripsi="{{ $item->deskripsi }}"
                                    data-tipe="{{ $item->tipe_potongan }}"
                                    data-nilai="{{ $item->nilai_potongan }}"
                                    data-min-porsi="{{ $item->syarat_min_porsi }}"
                                    data-batas="{{ $item->batas_penggunaan }}"
                                    data-mulai="{{ $item->tanggal_mulai?->format('Y-m-d') }}"
                                    data-selesai="{{ $item->tanggal_selesai?->format('Y-m-d') }}"
                                    data-aktif="{{ $item->status_aktif ? '1' : '0' }}">
                                    <td class="py-5 px-6">
                                        <p class="font-bold text-[#010101] text-base">{{ $item->nama_promo }}</p>
                                        @if ($item->deskripsi)
                                            <p class="text-[#8A8A8A] text-xs mt-0.5">{{ $item->deskripsi }}</p>
                                        @endif
                                        <p class="text-[#8A8A8A] text-xs mt-1">
                                            {{ $ringkas }}
                                            @if ($item->syarat_min_porsi)
                                                &middot; min. {{ $item->syarat_min_porsi }} porsi
                                            @endif
                                        </p>
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <span class="font-semibold text-[#010101]">{{ $item->jumlah_terpakai }}/{{ $item->batas_penggunaan }}</span>
                                    </td>
                                    <td class="py-5 px-6 text-[#010101]">
                                        @if ($item->tanggal_mulai && $item->tanggal_selesai)
                                            {{ $item->tanggal_mulai->translatedFormat('d M Y') }}
                                            &ndash;
                                            {{ $item->tanggal_selesai->translatedFormat('d M Y') }}
                                        @elseif ($item->tanggal_mulai)
                                            Mulai {{ $item->tanggal_mulai->translatedFormat('d M Y') }}
                                        @elseif ($item->tanggal_selesai)
                                            Sampai {{ $item->tanggal_selesai->translatedFormat('d M Y') }}
                                        @else
                                            <span class="text-[#8A8A8A]">Selalu aktif</span>
                                        @endif
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <!-- Toggle Switch -->
                                        <form method="POST" action="{{ route('admin.promo.status', $item->id_promo) }}" class="inline-flex">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status_aktif" value="{{ $item->status_aktif ? '0' : '1' }}">
                                            <button type="submit"
                                                role="switch"
                                                aria-checked="{{ $item->status_aktif ? 'true' : 'false' }}"
                                                aria-label="Status promo {{ $item->nama_promo }}"
                                                title="{{ $item->status_aktif ? 'Klik untuk menonaktifkan' : 'Klik untuk mengaktifkan' }}"
                                                class="relative inline-flex h-7 w-12 shrink-0 items-center rounded-full transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0B412B] focus-visible:ring-offset-2 {{ $item->status_aktif ? 'bg-[#65BD5E] hover:bg-[#58a851]' : 'bg-stone-300 hover:bg-stone-400' }}">
                                                <span class="sr-only">{{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform {{ $item->status_aktif ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="py-5 px-6">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button"
                                               class="btn-edit-promo w-9 h-9 rounded-lg bg-[#E08A3C] hover:bg-[#d07b30] text-white flex items-center justify-center transition"
                                               aria-label="Edit promo {{ $item->nama_promo }}">
                                                <x-lucide-pencil class="w-4 h-4" />
                                            </button>
                                            <form method="POST" action="{{ route('admin.promo.destroy', $item->id_promo) }}"
                                                  onsubmit="return confirm('Yakin untuk menghapus promo &quot;{{ $item->nama_promo }}&quot;?');" class="inline-flex">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-9 h-9 rounded-lg bg-[#E05C5C] hover:bg-[#d04b4b] text-white flex items-center justify-center transition"
                                                        aria-label="Hapus promo {{ $item->nama_promo }}">
                                                    <x-lucide-trash-2 class="w-4 h-4" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 px-6 text-center text-[#8A8A8A]">
                                        @if ($q !== '')
                                            <p class="mb-3">Tidak ada promo yang cocok dengan pencarian "{{ $q }}".</p>
                                            <a href="{{ route('admin.promo.index') }}"
                                               class="inline-block bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2 rounded-xl transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0B412B] focus-visible:ring-offset-2">
                                                Reset pencarian
                                            </a>
                                        @else
                                            Belum ada promo. Klik "Tambah Promo" untuk menambahkan.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

    </div>

    @php
        // Isi form promo (dipakai bareng modal tambah & edit lewat partial kecil di bawah)
        $tipeOptions = ['nominal' => 'Nominal (Rp)', 'persen' => 'Persen (%)', 'gratis_ongkir' => 'Gratis Ongkir'];
    @endphp

    <!-- ========================================== -->
    <!-- MODAL TAMBAH PROMO                         -->
    <!-- ========================================== -->
    <div id="modal-tambah" data-modal-promo class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="judul-modal-tambah">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" data-tutup-modal></div>

        <!-- Panel Modal -->
        <div class="relative mx-auto mt-8 mb-8 w-[calc(100%-2rem)] max-w-[440px] bg-white rounded-2xl shadow-xl p-6 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <h3 id="judul-modal-tambah" class="text-lg font-bold text-[#010101] mb-4">
                Tambah Promo Baru
            </h3>

            @if ($errors->any() && !old('edit_id'))
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl px-4 py-3 mb-4">
                    <p class="mb-1">Ada data yang belum valid:</p>
                    <ul class="list-disc list-inside font-normal">
                        @foreach ($errors->all() as $pesan)
                            <li>{{ $pesan }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="form-tambah-promo" action="{{ route('admin.promo.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Promo -->
                <div>
                    <label for="nama_promo" class="block text-sm font-semibold text-[#010101] mb-1.5">Nama Promo</label>
                    <input
                        type="text"
                        id="nama_promo"
                        name="nama_promo"
                        value="{{ old('nama_promo') }}"
                        placeholder="Diskon Akhir Pekan"
                        required
                        class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                    >
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="deskripsi" class="block text-sm font-semibold text-[#010101] mb-1.5">Deskripsi <span class="text-[#8A8A8A] font-normal">(opsional)</span></label>
                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        rows="2"
                        placeholder="Potongan harga khusus hari Sabtu dan Minggu"
                        class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                    >{{ old('deskripsi') }}</textarea>
                </div>

                <!-- Tipe Potongan + Nilai Potongan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tipe_potongan" class="block text-sm font-semibold text-[#010101] mb-1.5">Tipe Potongan</label>
                        <select
                            id="tipe_potongan"
                            name="tipe_potongan"
                            required
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                            @foreach ($tipeOptions as $nilai => $label)
                                <option value="{{ $nilai }}" {{ old('tipe_potongan', 'nominal') === $nilai ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Disembunyikan saat tipe gratis_ongkir -->
                    <div data-area-nilai>
                        <label for="nilai_potongan" class="block text-sm font-semibold text-[#010101] mb-1.5">Nilai Potongan</label>
                        <div class="flex items-center bg-white border border-stone-300 rounded-xl focus-within:border-[#65BD5E] focus-within:ring-1 focus-within:ring-[#65BD5E]">
                            <span data-prefix-nilai class="pl-4 pr-1.5 py-2.5 text-sm font-semibold text-[#8A8A8A] select-none">Rp.</span>
                            <input
                                type="number"
                                id="nilai_potongan"
                                name="nilai_potongan"
                                value="{{ old('nilai_potongan') }}"
                                min="0"
                                step="1"
                                placeholder="5000"
                                class="w-full bg-transparent rounded-r-xl px-1 py-2.5 pr-4 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none"
                            >
                        </div>
                    </div>
                </div>

                <!-- Syarat Min Porsi + Batas Penggunaan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="syarat_min_porsi" class="block text-sm font-semibold text-[#010101] mb-1.5">Syarat Min. Porsi <span class="text-[#8A8A8A] font-normal">(opsional)</span></label>
                        <input
                            type="number"
                            id="syarat_min_porsi"
                            name="syarat_min_porsi"
                            value="{{ old('syarat_min_porsi') }}"
                            min="1"
                            step="1"
                            placeholder="2"
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                    </div>

                    <div>
                        <label for="batas_penggunaan" class="block text-sm font-semibold text-[#010101] mb-1.5">Batas Penggunaan</label>
                        <input
                            type="number"
                            id="batas_penggunaan"
                            name="batas_penggunaan"
                            value="{{ old('batas_penggunaan') }}"
                            min="1"
                            step="1"
                            placeholder="50"
                            required
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                    </div>
                </div>

                <!-- Periode -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-semibold text-[#010101] mb-1.5">Tanggal Mulai <span class="text-[#8A8A8A] font-normal">(opsional)</span></label>
                        <input
                            type="date"
                            id="tanggal_mulai"
                            name="tanggal_mulai"
                            value="{{ old('tanggal_mulai') }}"
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                    </div>

                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-semibold text-[#010101] mb-1.5">Tanggal Selesai <span class="text-[#8A8A8A] font-normal">(opsional)</span></label>
                        <input
                            type="date"
                            id="tanggal_selesai"
                            name="tanggal_selesai"
                            value="{{ old('tanggal_selesai') }}"
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                    </div>
                </div>

                <!-- Status Aktif -->
                <label class="flex items-start gap-3 border border-stone-200 rounded-xl px-4 py-3 cursor-pointer hover:bg-stone-50 transition">
                    {{-- Hidden "0" supaya checkbox yang tidak dicentang tetap terkirim ke server --}}
                    <input type="hidden" name="status_aktif" value="0">
                    <input
                        type="checkbox"
                        id="status_aktif"
                        name="status_aktif"
                        value="1"
                        {{ old('status_aktif', '1') ? 'checked' : '' }}
                        class="w-4 h-4 mt-0.5 shrink-0 accent-[#65BD5E]"
                    >
                    <span>
                        <span class="block text-sm font-semibold text-[#010101]">Promo aktif</span>
                        <span class="block text-xs text-[#8A8A8A] mt-0.5">Promo aktif langsung berlaku di kasir & checkout.</span>
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
    <!-- MODAL EDIT PROMO                           -->
    <!-- ========================================== -->
    <div id="modal-edit" data-modal-promo class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="judul-modal-edit">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" data-tutup-modal-edit></div>

        <!-- Panel Modal -->
        <div class="relative mx-auto mt-8 mb-8 w-[calc(100%-2rem)] max-w-[440px] bg-white rounded-2xl shadow-xl p-6 max-h-[calc(100vh-4rem)] overflow-y-auto">
            <h3 id="judul-modal-edit" class="text-lg font-bold text-[#010101] mb-4">
                Edit Promo
            </h3>

            @if ($errors->any() && old('edit_id'))
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl px-4 py-3 mb-4">
                    <p class="mb-1">Ada data yang belum valid:</p>
                    <ul class="list-disc list-inside font-normal">
                        @foreach ($errors->all() as $pesan)
                            <li>{{ $pesan }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Action form langsung dibentuk server: id promo lama dipakai kalau validasi gagal --}}
            <form id="form-edit-promo"
                  data-action="{{ url('/admin/promo') }}"
                  action="{{ old('edit_id') ? url('/admin/promo/' . old('edit_id')) : url('/admin/promo') }}"
                  method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="edit_id" id="edit_id" value="{{ old('edit_id') }}">

                <!-- Nama Promo -->
                <div>
                    <label for="edit-nama_promo" class="block text-sm font-semibold text-[#010101] mb-1.5">Nama Promo</label>
                    <input
                        type="text"
                        id="edit-nama_promo"
                        name="nama_promo"
                        value="{{ old('nama_promo') }}"
                        required
                        class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                    >
                </div>

                <!-- Deskripsi -->
                <div>
                    <label for="edit-deskripsi" class="block text-sm font-semibold text-[#010101] mb-1.5">Deskripsi <span class="text-[#8A8A8A] font-normal">(opsional)</span></label>
                    <textarea
                        id="edit-deskripsi"
                        name="deskripsi"
                        rows="2"
                        class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                    >{{ old('deskripsi') }}</textarea>
                </div>

                <!-- Tipe Potongan + Nilai Potongan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit-tipe_potongan" class="block text-sm font-semibold text-[#010101] mb-1.5">Tipe Potongan</label>
                        <select
                            id="edit-tipe_potongan"
                            name="tipe_potongan"
                            required
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                            @foreach ($tipeOptions as $nilai => $label)
                                <option value="{{ $nilai }}" {{ old('tipe_potongan') === $nilai ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Disembunyikan saat tipe gratis_ongkir -->
                    <div data-area-nilai>
                        <label for="edit-nilai_potongan" class="block text-sm font-semibold text-[#010101] mb-1.5">Nilai Potongan</label>
                        <div class="flex items-center bg-white border border-stone-300 rounded-xl focus-within:border-[#65BD5E] focus-within:ring-1 focus-within:ring-[#65BD5E]">
                            <span data-prefix-nilai class="pl-4 pr-1.5 py-2.5 text-sm font-semibold text-[#8A8A8A] select-none">Rp.</span>
                            <input
                                type="number"
                                id="edit-nilai_potongan"
                                name="nilai_potongan"
                                value="{{ old('nilai_potongan') }}"
                                min="0"
                                step="1"
                                placeholder="5000"
                                class="w-full bg-transparent rounded-r-xl px-1 py-2.5 pr-4 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none"
                            >
                        </div>
                    </div>
                </div>

                <!-- Syarat Min Porsi + Batas Penggunaan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit-syarat_min_porsi" class="block text-sm font-semibold text-[#010101] mb-1.5">Syarat Min. Porsi <span class="text-[#8A8A8A] font-normal">(opsional)</span></label>
                        <input
                            type="number"
                            id="edit-syarat_min_porsi"
                            name="syarat_min_porsi"
                            value="{{ old('syarat_min_porsi') }}"
                            min="1"
                            step="1"
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                    </div>

                    <div>
                        <label for="edit-batas_penggunaan" class="block text-sm font-semibold text-[#010101] mb-1.5">Batas Penggunaan</label>
                        <input
                            type="number"
                            id="edit-batas_penggunaan"
                            name="batas_penggunaan"
                            value="{{ old('batas_penggunaan') }}"
                            min="1"
                            step="1"
                            required
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] placeholder-[#8A8A8A] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                    </div>
                </div>

                <!-- Periode -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit-tanggal_mulai" class="block text-sm font-semibold text-[#010101] mb-1.5">Tanggal Mulai <span class="text-[#8A8A8A] font-normal">(opsional)</span></label>
                        <input
                            type="date"
                            id="edit-tanggal_mulai"
                            name="tanggal_mulai"
                            value="{{ old('tanggal_mulai') }}"
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                    </div>

                    <div>
                        <label for="edit-tanggal_selesai" class="block text-sm font-semibold text-[#010101] mb-1.5">Tanggal Selesai <span class="text-[#8A8A8A] font-normal">(opsional)</span></label>
                        <input
                            type="date"
                            id="edit-tanggal_selesai"
                            name="tanggal_selesai"
                            value="{{ old('tanggal_selesai') }}"
                            class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm text-[#010101] outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]"
                        >
                    </div>
                </div>

                <!-- Status Aktif -->
                <label class="flex items-start gap-3 border border-stone-200 rounded-xl px-4 py-3 cursor-pointer hover:bg-stone-50 transition">
                    {{-- Hidden "0" supaya checkbox yang tidak dicentang tetap terkirim ke server --}}
                    <input type="hidden" name="status_aktif" value="0">
                    <input
                        type="checkbox"
                        id="edit-status_aktif"
                        name="status_aktif"
                        value="1"
                        {{ old('status_aktif') ? 'checked' : '' }}
                        class="w-4 h-4 mt-0.5 shrink-0 accent-[#65BD5E]"
                    >
                    <span>
                        <span class="block text-sm font-semibold text-[#010101]">Promo aktif</span>
                        <span class="block text-xs text-[#8A8A8A] mt-0.5">Promo aktif langsung berlaku di kasir & checkout.</span>
                    </span>
                </label>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-end gap-2.5 pt-1">
                    <button type="button" data-tutup-modal-edit class="bg-[#E5E5E5] hover:bg-[#d9d9d9] text-[#010101] font-bold text-sm px-5 py-2 rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2 rounded-lg transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modalTambah = document.getElementById('modal-tambah');
        const modalEdit = document.getElementById('modal-edit');
        const btnTambah = document.getElementById('btn-tambah-promo');
        const formEdit = document.getElementById('form-edit-promo');

        function bukaModal(el) {
            el.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function tutupModal(el) {
            el.classList.add('hidden');
            if (modalTambah.classList.contains('hidden') && modalEdit.classList.contains('hidden')) {
                document.body.style.overflow = '';
            }
        }

        // Sembunyikan "Nilai Potongan" saat tipe gratis ongkir (per modal)
        function sinkronTipe(modal) {
            const tipe = modal.querySelector('[name="tipe_potongan"]');
            const areaNilai = modal.querySelector('[data-area-nilai]');
            const prefixNilai = modal.querySelector('[data-prefix-nilai]');
            const inputNilai = modal.querySelector('[name="nilai_potongan"]');
            if (!tipe || !areaNilai || !prefixNilai || !inputNilai) return;

            const gratis = tipe.value === 'gratis_ongkir';
            areaNilai.classList.toggle('hidden', gratis);
            inputNilai.required = !gratis;
            prefixNilai.textContent = tipe.value === 'persen' ? '%' : 'Rp.';
            inputNilai.placeholder = tipe.value === 'persen' ? '10' : '5000';
        }

        document.querySelectorAll('[data-modal-promo]').forEach(modal => {
            const tipe = modal.querySelector('[name="tipe_potongan"]');
            if (tipe) tipe.addEventListener('change', () => sinkronTipe(modal));
            sinkronTipe(modal);
        });

        btnTambah.addEventListener('click', () => bukaModal(modalTambah));

        modalTambah.querySelectorAll('[data-tutup-modal]').forEach(el => {
            el.addEventListener('click', () => tutupModal(modalTambah));
        });

        modalEdit.querySelectorAll('[data-tutup-modal-edit]').forEach(el => {
            el.addEventListener('click', () => tutupModal(modalEdit));
        });

        document.addEventListener('keydown', e => {
            if (e.key !== 'Escape') return;
            if (!modalEdit.classList.contains('hidden')) tutupModal(modalEdit);
            else if (!modalTambah.classList.contains('hidden')) tutupModal(modalTambah);
        });

        // Isi modal edit dari DataRow tabel / data lama hasil validasi gagal
        function isiEditPromo(d) {
            formEdit.action = formEdit.dataset.action + '/' + d.id;
            document.getElementById('edit_id').value = d.id || '';
            document.getElementById('edit-nama_promo').value = d.nama || '';
            document.getElementById('edit-deskripsi').value = d.deskripsi || '';
            document.getElementById('edit-tipe_potongan').value = d.tipe || 'nominal';
            document.getElementById('edit-nilai_potongan').value = d.nilai != null && d.nilai !== '' ? parseFloat(d.nilai) : '';
            document.getElementById('edit-syarat_min_porsi').value = d.min_porsi || '';
            document.getElementById('edit-batas_penggunaan').value = d.batas || '';
            document.getElementById('edit-tanggal_mulai').value = d.mulai || '';
            document.getElementById('edit-tanggal_selesai').value = d.selesai || '';
            document.getElementById('edit-status_aktif').checked = String(d.aktif) === '1';

            sinkronTipe(modalEdit);
            bukaModal(modalEdit);
        }

        // Tombol edit di baris tabel
        document.querySelectorAll('.btn-edit-promo').forEach(btn => {
            btn.addEventListener('click', () => {
                const row = btn.closest('tr');
                isiEditPromo({
                    id: row.dataset.id,
                    nama: row.dataset.nama,
                    deskripsi: row.dataset.deskripsi,
                    tipe: row.dataset.tipe,
                    nilai: row.dataset.nilai,
                    min_porsi: row.dataset.minPorsi,
                    batas: row.dataset.batas,
                    mulai: row.dataset.mulai,
                    selesai: row.dataset.selesai,
                    aktif: row.dataset.aktif,
                });
            });
        });

        // Auto buka modal setelah validasi gagal.
        // Field modal edit sudah diisi server-side pakai old(), jadi cukup buka modalnya.
        @if ($errors->any())
            @if (old('edit_id'))
                sinkronTipe(modalEdit);
                bukaModal(modalEdit);
            @else
                bukaModal(modalTambah);
            @endif
        @endif
    </script>

</body>
</html>
