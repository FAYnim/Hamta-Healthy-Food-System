<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Hamta Healthy Food</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif; }
    </style>
</head>
<body class="bg-[#F7F2E7] text-[#010101] antialiased min-h-screen">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <x-admin.sidebar />

        <main class="flex-1 p-6 md:p-8 lg:p-10 max-w-7xl">
            <header class="flex items-center justify-between mb-8">
                <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Pengaturan</h2>
                <div class="hidden lg:flex items-center">
                    <span class="bg-white border border-stone-200/60 rounded-xl px-7 py-2.5 font-bold text-base shadow-sm">
                        {{ session('admin_nama', 'Admin') }}
                    </span>
                </div>
            </header>

            @if (session('success'))
                <div role="status" class="bg-[#65BD5E]/15 border border-[#65BD5E]/40 text-[#0B412B] text-sm font-semibold rounded-xl px-4 py-3 mb-6">
                    {{ session('success') }}
                </div>
            @elseif (session('error'))
                <div role="alert" class="bg-red-50 border border-red-200 text-red-700 text-sm font-semibold rounded-xl px-4 py-3 mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <section class="bg-white rounded-2xl p-6 md:p-7 shadow-sm border border-stone-100">
                    <h3 class="text-lg font-bold mb-5">Logo Usaha</h3>
                    @if ($errors->has('logo'))
                        <p class="text-sm text-red-600 mb-3">{{ $errors->first('logo') }}</p>
                    @endif
                    <form id="form-logo" method="POST" action="{{ route('admin.pengaturan.identitas') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="w-36 h-24 bg-[#E5E5E5] rounded-2xl flex items-center justify-center overflow-hidden">
                            <img id="preview-logo" src="{{ $pengaturan->logo ? Storage::url($pengaturan->logo) : '' }}" alt="Pratinjau logo usaha" class="{{ $pengaturan->logo ? '' : 'hidden' }} w-full h-full object-contain">
                            <x-lucide-image id="placeholder-logo" class="{{ $pengaturan->logo ? 'hidden' : '' }} w-10 h-10 text-stone-500" aria-hidden="true" />
                        </div>
                        <label for="logo" class="block text-sm font-semibold mt-4 mb-1.5">Pilih logo <span class="font-normal text-[#8A8A8A]">(PNG, JPG, WebP · maks. 2 MB)</span></label>
                        <input id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/webp" class="w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-[#E5E5E5] file:px-3 file:py-2 file:font-semibold">
                        <div class="flex items-center justify-between gap-3 mt-6">
                            @if ($pengaturan->logo)
                                <button type="submit" form="form-hapus-logo" class="text-sm font-semibold text-red-700 hover:underline" onclick="return confirm('Hapus logo usaha?')">Hapus logo</button>
                            @else
                                <span></span>
                            @endif
                            <div class="flex items-center gap-3">
                                <button type="reset" class="bg-[#8A8A8A] hover:bg-stone-500 text-white font-semibold text-sm px-5 py-2 rounded-xl transition">Batal</button>
                                <button type="submit" class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-semibold text-sm px-5 py-2 rounded-xl shadow-sm transition">Simpan</button>
                            </div>
                        </div>
                    </form>
                    @if ($pengaturan->logo)
                        <form id="form-hapus-logo" method="POST" action="{{ route('admin.pengaturan.identitas') }}" class="hidden">
                            @csrf
                            <input type="hidden" name="hapus_logo" value="1">
                        </form>
                    @endif
                </section>

                <section class="bg-white rounded-2xl p-6 md:p-7 shadow-sm border border-stone-100">
                    <h3 class="text-lg font-bold mb-4">Kontak dan Media Sosial</h3>
                    @if ($errors->has('whatsapp') || $errors->has('instagram'))
                        <div role="alert" class="text-sm text-red-600 mb-3">{{ $errors->first('whatsapp') ?: $errors->first('instagram') }}</div>
                    @endif
                    <form id="form-kontak" method="POST" action="{{ route('admin.pengaturan.kontak') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="whatsapp" class="block text-sm font-semibold mb-1.5">WhatsApp</label>
                                <input id="whatsapp" name="whatsapp" type="text" maxlength="20" value="{{ old('whatsapp', $pengaturan->whatsapp) }}" class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]">
                            </div>
                            <div>
                                <label for="instagram" class="block text-sm font-semibold mb-1.5">Instagram</label>
                                <input id="instagram" name="instagram" type="text" maxlength="50" value="{{ old('instagram', $pengaturan->instagram) }}" class="w-full bg-white border border-stone-300 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]">
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3 mt-6">
                            <button type="reset" class="bg-[#8A8A8A] hover:bg-stone-500 text-white font-semibold text-sm px-5 py-2 rounded-xl transition">Batal</button>
                            <button type="submit" class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-semibold text-sm px-5 py-2 rounded-xl shadow-sm transition">Simpan</button>
                        </div>
                    </form>
                </section>
            </div>

            <section class="bg-white rounded-2xl p-6 md:p-7 shadow-sm border border-stone-100">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <h3 class="text-xl font-bold">Cabang</h3>
                    <button type="button" id="btn-tambah-cabang" class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2.5 rounded-xl shadow-sm transition shrink-0">Tambah Cabang</button>
                </div>

                @if ($errors->any() && old('form_cabang'))
                    <div role="alert" class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5">
                        <p class="font-semibold mb-1">Periksa kembali data cabang:</p>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $pesan)
                                <li>{{ $pesan }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @forelse ($cabang as $item)
                    <article class="border border-stone-300 rounded-2xl p-5 md:p-6 mb-5 last:mb-0">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <h4 class="text-lg font-bold">{{ $item->nama_cabang }}</h4>
                                <p class="text-sm text-[#8A8A8A] font-medium mt-1">{{ $item->alamat }}</p>
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @if ($item->layani_pickup)
                                        <span class="bg-[#65BD5E]/15 text-[#0B412B] text-xs font-bold px-2.5 py-1 rounded-full">Pickup</span>
                                    @endif
                                    @if ($item->layani_dinein)
                                        <span class="bg-[#65BD5E]/15 text-[#0B412B] text-xs font-bold px-2.5 py-1 rounded-full">Dine-in</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex shrink-0 gap-2">
                                <button type="button" class="btn-edit-cabang rounded-lg p-2 hover:bg-stone-100" aria-label="Edit {{ $item->nama_cabang }}"
                                    data-id="{{ $item->id_cabang }}" data-nama="{{ $item->nama_cabang }}" data-alamat="{{ $item->alamat }}"
                                    data-buka="{{ substr($item->jam_buka, 0, 5) }}" data-tutup="{{ substr($item->jam_tutup, 0, 5) }}"
                                    data-buka-weekend="{{ $item->jam_buka_weekend ? substr($item->jam_buka_weekend, 0, 5) : '' }}"
                                    data-tutup-weekend="{{ $item->jam_tutup_weekend ? substr($item->jam_tutup_weekend, 0, 5) : '' }}"
                                    data-pickup="{{ $item->layani_pickup ? '1' : '0' }}" data-dinein="{{ $item->layani_dinein ? '1' : '0' }}">
                                    <x-lucide-pencil class="w-5 h-5" aria-hidden="true" />
                                </button>
                                <form method="POST" action="{{ route('admin.pengaturan.cabang.destroy', $item->id_cabang) }}" onsubmit="return confirm('Hapus cabang {{ $item->nama_cabang }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg p-2 text-red-700 hover:bg-red-50" aria-label="Hapus {{ $item->nama_cabang }}">
                                        <x-lucide-trash-2 class="w-5 h-5" aria-hidden="true" />
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h5 class="text-sm font-bold mb-2.5">Jam Operasional</h5>
                            <div class="bg-[#E5E5E5] rounded-xl p-4 space-y-2 text-sm">
                                <div class="flex items-center justify-between gap-3"><span class="font-semibold">Senin - Jumat</span><span>{{ substr($item->jam_buka, 0, 5) }} - {{ substr($item->jam_tutup, 0, 5) }}</span></div>
                                <div class="flex items-center justify-between gap-3"><span class="font-semibold">Sabtu - Minggu</span><span>{{ $item->jam_buka_weekend && $item->jam_tutup_weekend ? substr($item->jam_buka_weekend, 0, 5) . ' - ' . substr($item->jam_tutup_weekend, 0, 5) : 'Tutup' }}</span></div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="border border-dashed border-stone-300 rounded-2xl px-6 py-12 text-center">
                        <p class="font-semibold">Belum ada cabang.</p>
                        <p class="text-sm text-[#8A8A8A] mt-1">Pilih Tambah Cabang untuk menambahkan lokasi pertama.</p>
                    </div>
                @endforelse
            </section>
        </main>
    </div>

    <div id="modal-cabang" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="judul-modal-cabang">
        <div class="absolute inset-0 bg-black/40" data-tutup-modal></div>
        <div class="relative mx-auto my-6 w-[calc(100%-2rem)] max-w-2xl max-h-[calc(100vh-3rem)] overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
            <h3 id="judul-modal-cabang" class="text-lg font-bold mb-5">Tambah Cabang</h3>
            <form id="form-cabang" method="POST" action="{{ route('admin.pengaturan.cabang.store') }}" class="space-y-4">
                @csrf
                <input id="_method-cabang" type="hidden" name="_method" value="">
                <input type="hidden" name="form_cabang" value="1">
                <input id="cabang-id" type="hidden" name="cabang_id" value="{{ old('cabang_id') }}">
                <div>
                    <label for="nama_cabang" class="block text-sm font-semibold mb-1.5">Nama cabang</label>
                    <input id="nama_cabang" name="nama_cabang" value="{{ old('nama_cabang') }}" required maxlength="100" class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]">
                </div>
                <div>
                    <label for="alamat" class="block text-sm font-semibold mb-1.5">Alamat</label>
                    <textarea id="alamat" name="alamat" required maxlength="255" rows="2" class="w-full border border-stone-300 rounded-xl px-4 py-2.5 text-sm focus:border-[#65BD5E] focus:ring-1 focus:ring-[#65BD5E]">{{ old('alamat') }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Jam Senin - Jumat</label>
                        <div class="flex items-center gap-2"><input type="time" name="jam_buka" value="{{ old('jam_buka') }}" required class="min-w-0 flex-1 border border-stone-300 rounded-xl px-3 py-2.5 text-sm"><span>-</span><input type="time" name="jam_tutup" value="{{ old('jam_tutup') }}" required class="min-w-0 flex-1 border border-stone-300 rounded-xl px-3 py-2.5 text-sm"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5">Jam Sabtu - Minggu</label>
                        <div class="flex items-center gap-2"><input id="jam_buka_weekend" type="time" name="jam_buka_weekend" value="{{ old('jam_buka_weekend') }}" class="min-w-0 flex-1 border border-stone-300 rounded-xl px-3 py-2.5 text-sm"><span>-</span><input id="jam_tutup_weekend" type="time" name="jam_tutup_weekend" value="{{ old('jam_tutup_weekend') }}" class="min-w-0 flex-1 border border-stone-300 rounded-xl px-3 py-2.5 text-sm"></div>
                        <div class="flex items-center justify-between gap-3 mt-2"><p class="text-xs text-[#8A8A8A]">Kosongkan keduanya jika tutup.</p><button type="button" id="salin-jam-weekend" class="text-xs font-semibold text-[#0B412B] hover:underline">Samakan dengan hari kerja</button></div>
                    </div>
                </div>
                <fieldset>
                    <legend class="text-sm font-semibold mb-2">Layanan (pilih minimal satu)</legend>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="layani_pickup" value="1" {{ (old('form_cabang') ? old('layani_pickup') : '1') ? 'checked' : '' }} class="accent-[#65BD5E]">Pickup</label>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="layani_dinein" value="1" {{ (old('form_cabang') ? old('layani_dinein') : '') ? 'checked' : '' }} class="accent-[#65BD5E]">Dine-in</label>
                    </div>
                    @error('layanan')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </fieldset>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" data-tutup-modal class="bg-[#E5E5E5] hover:bg-[#d9d9d9] font-bold text-sm px-5 py-2 rounded-lg transition">Batal</button>
                    <button type="submit" class="bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-sm px-5 py-2 rounded-lg transition">Simpan Cabang</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modalCabang = document.getElementById('modal-cabang');
        const formCabang = document.getElementById('form-cabang');
        const urlCabangUpdate = @js(route('admin.pengaturan.cabang.update', ['cabang' => '__CABANG__']));
        const previewLogo = document.getElementById('preview-logo');
        const placeholderLogo = document.getElementById('placeholder-logo');
        const logoInput = document.getElementById('logo');

        function urlUpdateCabang(id) {
            return urlCabangUpdate.replace('__CABANG__', encodeURIComponent(id));
        }

        function bukaModalCabang() {
            modalCabang.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            document.getElementById('nama_cabang').focus();
        }

        function tutupModalCabang() {
            modalCabang.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function resetFormCabang() {
            formCabang.reset();
            formCabang.action = @js(route('admin.pengaturan.cabang.store'));
            document.getElementById('_method-cabang').value = '';
            document.getElementById('cabang-id').value = '';
            document.getElementById('judul-modal-cabang').textContent = 'Tambah Cabang';
            formCabang.querySelector('[name="layani_pickup"]').checked = true;
        }

        document.getElementById('btn-tambah-cabang').addEventListener('click', () => {
            resetFormCabang();
            bukaModalCabang();
        });

        document.querySelectorAll('.btn-edit-cabang').forEach((button) => {
            button.addEventListener('click', () => {
                resetFormCabang();
                const data = button.dataset;
                formCabang.action = urlUpdateCabang(data.id);
                document.getElementById('_method-cabang').value = 'PATCH';
                document.getElementById('cabang-id').value = data.id;
                document.getElementById('judul-modal-cabang').textContent = 'Edit Cabang';
                formCabang.querySelector('[name="nama_cabang"]').value = data.nama;
                formCabang.querySelector('[name="alamat"]').value = data.alamat;
                formCabang.querySelector('[name="jam_buka"]').value = data.buka;
                formCabang.querySelector('[name="jam_tutup"]').value = data.tutup;
                formCabang.querySelector('[name="jam_buka_weekend"]').value = data.bukaWeekend;
                formCabang.querySelector('[name="jam_tutup_weekend"]').value = data.tutupWeekend;
                formCabang.querySelector('[name="layani_pickup"]').checked = data.pickup === '1';
                formCabang.querySelector('[name="layani_dinein"]').checked = data.dinein === '1';
                bukaModalCabang();
            });
        });

        document.querySelectorAll('[data-tutup-modal]').forEach((element) => {
            element.addEventListener('click', tutupModalCabang);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modalCabang.classList.contains('hidden')) {
                tutupModalCabang();
            }
        });

        document.getElementById('salin-jam-weekend').addEventListener('click', () => {
            formCabang.querySelector('[name="jam_buka_weekend"]').value = formCabang.querySelector('[name="jam_buka"]').value;
            formCabang.querySelector('[name="jam_tutup_weekend"]').value = formCabang.querySelector('[name="jam_tutup"]').value;
        });

        logoInput.addEventListener('change', () => {
            const file = logoInput.files[0];
            if (!file) return;
            previewLogo.src = URL.createObjectURL(file);
            previewLogo.classList.remove('hidden');
            placeholderLogo.classList.add('hidden');
        });

        document.getElementById('form-logo').addEventListener('reset', () => {
            window.setTimeout(() => {
                previewLogo.src = @js($pengaturan->logo ? Storage::url($pengaturan->logo) : '');
                previewLogo.classList.toggle('hidden', !previewLogo.src);
                placeholderLogo.classList.toggle('hidden', !!previewLogo.src);
            });
        });

        @if ($errors->any() && old('form_cabang'))
            formCabang.querySelector('[name="nama_cabang"]').value = @js(old('nama_cabang', ''));
            formCabang.querySelector('[name="alamat"]').value = @js(old('alamat', ''));
            formCabang.querySelector('[name="jam_buka"]').value = @js(old('jam_buka', ''));
            formCabang.querySelector('[name="jam_tutup"]').value = @js(old('jam_tutup', ''));
            formCabang.querySelector('[name="jam_buka_weekend"]').value = @js(old('jam_buka_weekend', ''));
            formCabang.querySelector('[name="jam_tutup_weekend"]').value = @js(old('jam_tutup_weekend', ''));
            formCabang.querySelector('[name="layani_pickup"]').checked = @js((bool) old('layani_pickup', false));
            formCabang.querySelector('[name="layani_dinein"]').checked = @js((bool) old('layani_dinein', false));
            @if (old('cabang_id'))
                formCabang.action = urlUpdateCabang(@js(old('cabang_id')));
                document.getElementById('_method-cabang').value = 'PATCH';
                document.getElementById('cabang-id').value = @js(old('cabang_id'));
                document.getElementById('judul-modal-cabang').textContent = 'Edit Cabang';
            @endif
            bukaModalCabang();
        @endif
    </script>
</body>
</html>
