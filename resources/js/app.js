// Kasir (POS) — cart state, filters, custom salad builder, form payload.
const root = document.getElementById('kasir-root');

if (root) {
    const form = document.getElementById('kasir-form');
    const searchInput = document.getElementById('kasir-search');
    const cards = Array.from(root.querySelectorAll('[data-produk-card]'));
    const emptyFilter = document.getElementById('kasir-empty-filter');
    const pills = Array.from(root.querySelectorAll('[data-filter-kategori]'));

    const cartList = document.getElementById('kasir-cart-list');
    const cartEmpty = document.getElementById('kasir-cart-empty');
    const subtotalEl = document.getElementById('kasir-subtotal');
    const totalEl = document.getElementById('kasir-total');
    const inputsWrap = document.getElementById('kasir-cart-inputs');
    const submitBtn = document.getElementById('kasir-submit');

    const customModal = document.getElementById('kasir-custom');
    const customOpenBtn = document.getElementById('kasir-buka-custom');
    const customBase = document.getElementById('kasir-custom-base');
    // Modal lives outside #kasir-root; classes are unique to this page.
    const customKomponen = Array.from(document.querySelectorAll('.kasir-komponen'));
    const customPrice = document.getElementById('kasir-custom-price');
    const customAdd = document.getElementById('kasir-custom-add');

    // {tipe, id_produk, nama, harga, jumlah, komponen: [{id, nama, harga}]}
    const cart = [];
    let filterKategori = '';

    const rupiah = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));

    function totalItem(item) {
        return item.harga * item.jumlah;
    }

    function subtotal() {
        return cart.reduce((sum, item) => sum + totalItem(item), 0);
    }

    function updateCartUI() {
        cartList.textContent = '';

        cart.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between gap-3';

            const left = document.createElement('div');
            left.className = 'flex items-center gap-3 min-w-0';

            const thumb = document.createElement('div');
            thumb.className = 'w-14 h-14 bg-[#D9D9D9] rounded-xl shrink-0 flex items-center justify-center overflow-hidden';
            if (item.foto) {
                const img = document.createElement('img');
                img.src = item.foto;
                img.alt = item.nama;
                img.className = 'w-full h-full object-cover';
                thumb.appendChild(img);
            }
            left.appendChild(thumb);

            const info = document.createElement('div');
            info.className = 'min-w-0';

            const nama = document.createElement('h4');
            nama.className = 'font-bold text-[#010101] text-sm truncate';
            nama.textContent = item.nama;
            nama.title = item.nama;
            info.appendChild(nama);

            const harga = document.createElement('p');
            harga.className = 'text-xs text-[#8A8A8A] font-medium mt-0.5';
            harga.textContent = rupiah(item.harga);
            info.appendChild(harga);

            if (item.tipe === 'custom' && item.komponen.length) {
                const detail = document.createElement('p');
                detail.className = 'text-[11px] text-[#8A8A8A] mt-0.5 leading-snug';
                detail.textContent = item.komponen.map((k) => k.nama).join(', ');
                info.appendChild(detail);
            }

            // Steppers
            const stepper = document.createElement('div');
            stepper.className = 'flex items-center gap-1.5 mt-1.5';

            const minus = document.createElement('button');
            minus.type = 'button';
            minus.className = 'w-6 h-6 rounded-md bg-[#65BD5E] hover:bg-[#58a851] text-white flex items-center justify-center transition';
            minus.setAttribute('aria-label', 'Kurangi jumlah ' + item.nama);
            minus.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true" class="w-3.5 h-3.5"><path d="M5 12h14"/></svg>';
            minus.addEventListener('click', () => {
                if (item.jumlah > 1) {
                    item.jumlah -= 1;
                    updateCartUI();
                }
            });

            const qty = document.createElement('span');
            qty.className = 'text-xs font-bold px-1.5 text-[#010101]';
            qty.textContent = item.jumlah;

            const plus = document.createElement('button');
            plus.type = 'button';
            plus.className = 'w-6 h-6 rounded-md bg-[#65BD5E] hover:bg-[#58a851] text-white flex items-center justify-center transition';
            plus.setAttribute('aria-label', 'Tambah jumlah ' + item.nama);
            plus.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true" class="w-3.5 h-3.5"><path d="M5 12h14M12 5v14"/></svg>';
            plus.addEventListener('click', () => {
                item.jumlah += 1;
                updateCartUI();
            });

            stepper.append(minus, qty, plus);
            info.appendChild(stepper);
            left.appendChild(info);

            const right = document.createElement('div');
            right.className = 'text-right shrink-0';

            const lineTotal = document.createElement('span');
            lineTotal.className = 'font-bold text-sm text-[#010101] block';
            lineTotal.textContent = rupiah(totalItem(item));
            right.appendChild(lineTotal);

            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'text-[11px] font-semibold text-[#E05C5C] hover:underline mt-1';
            remove.textContent = 'Hapus';
            remove.setAttribute('aria-label', 'Hapus ' + item.nama + ' dari keranjang');
            remove.addEventListener('click', () => {
                cart.splice(index, 1);
                updateCartUI();
            });
            right.appendChild(remove);

            row.append(left, right);
            cartList.appendChild(row);
        });

        const kosong = cart.length === 0;
        cartEmpty.classList.toggle('hidden', !kosong);
        cartList.classList.toggle('hidden', kosong);

        subtotalEl.textContent = rupiah(subtotal());
        totalEl.textContent = rupiah(subtotal());

        submitBtn.disabled = kosong;
        submitBtn.classList.toggle('opacity-50', kosong);
        submitBtn.classList.toggle('cursor-not-allowed', kosong);
    }

    function addProduk(data) {
        const id = parseInt(data.id, 10);
        const existing = cart.find((item) => item.tipe === 'produk' && item.id_produk === id);

        if (existing) {
            existing.jumlah += 1;
        } else {
            cart.push({
                tipe: 'produk',
                id_produk: id,
                nama: data.nama,
                harga: parseFloat(data.harga) || 0,
                foto: data.foto || '',
                jumlah: 1,
                komponen: [],
            });
        }
        updateCartUI();
    }

    // --- Product cards ---
    cards.forEach((card) => {
        card.addEventListener('click', () => {
            addProduk({
                id: card.dataset.id,
                nama: card.dataset.nama,
                harga: card.dataset.harga,
                foto: card.querySelector('img') ? card.querySelector('img').getAttribute('src') : '',
            });
        });
    });

    // --- Search + category filter ---
    function applyFilter() {
        const q = (searchInput.value || '').trim().toLowerCase();
        let visible = 0;

        cards.forEach((card) => {
            const cocokNama = !q || (card.dataset.nama || '').toLowerCase().includes(q);
            const cocokKategori = !filterKategori || card.dataset.kategori === filterKategori;
            const tampil = cocokNama && cocokKategori;
            card.classList.toggle('hidden', !tampil);
            if (tampil) visible += 1;
        });

        emptyFilter.classList.toggle('hidden', visible > 0 || cards.length === 0);
    }

    searchInput.addEventListener('input', applyFilter);

    pills.forEach((pill) => {
        pill.addEventListener('click', () => {
            filterKategori = pill.dataset.filterKategori || '';
            pills.forEach((p) => {
                const aktif = p === pill;
                p.classList.toggle('bg-[#65BD5E]', aktif);
                p.classList.toggle('text-white', aktif);
                p.classList.toggle('border-[#65BD5E]', aktif);
                p.classList.toggle('shadow-sm', aktif);
                p.classList.toggle('bg-white', !aktif);
                p.classList.toggle('text-[#010101]', !aktif);
                p.classList.toggle('border-[#010101]', !aktif);
                p.classList.toggle('hover:bg-stone-100', !aktif);
                p.setAttribute('aria-pressed', aktif ? 'true' : 'false');
            });
            applyFilter();
        });
    });

    // --- Custom salad ---
    const customHint = document.getElementById('kasir-custom-hint');

    function customState() {
        const opt = customBase ? customBase.selectedOptions[0] : null;
        const baseId = opt && opt.value ? parseInt(opt.value, 10) : null;
        const baseHarga = opt && opt.value ? parseFloat(opt.dataset.harga) || 0 : 0;
        const baseNama = opt && opt.value ? (opt.dataset.nama || '') : '';
        const baseFoto = opt && opt.value ? (opt.dataset.foto || '') : '';
        const komponen = customKomponen
            .filter((cb) => cb.checked)
            .map((cb) => ({
                id: parseInt(cb.value, 10),
                nama: cb.dataset.nama || '',
                harga: parseFloat(cb.dataset.harga) || 0,
            }));

        return {
            baseId,
            baseNama,
            baseFoto,
            harga: baseHarga + komponen.reduce((sum, k) => sum + k.harga, 0),
            komponen,
        };
    }

    function updateCustomPrice() {
        if (!customPrice || !customAdd) return;
        const state = customState();
        customPrice.textContent = rupiah(state.harga);

        const valid = state.baseId !== null && state.komponen.length > 0;
        customAdd.disabled = !valid;
        customAdd.classList.toggle('opacity-50', !valid);
        customAdd.classList.toggle('cursor-not-allowed', !valid);
        customHint.textContent = valid
            ? 'Siap ditambahkan ke keranjang.'
            : 'Pilih salad dasar dan minimal satu komponen.';
    }

    function bukaCustom() {
        customModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        updateCustomPrice();
    }

    function tutupCustom() {
        customModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    if (customOpenBtn) {
        customOpenBtn.addEventListener('click', bukaCustom);
        customModal.querySelectorAll('[data-tutup-custom]').forEach((el) => {
            el.addEventListener('click', tutupCustom);
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !customModal.classList.contains('hidden')) tutupCustom();
        });
    }

    // Builder controls only exist when there is at least one customisable salad.
    if (customOpenBtn && customBase && customAdd) {

        customBase.addEventListener('change', updateCustomPrice);
        customKomponen.forEach((cb) => cb.addEventListener('change', updateCustomPrice));

        customAdd.addEventListener('click', () => {
            const state = customState();
            if (state.baseId === null || state.komponen.length === 0) return;

            cart.push({
                tipe: 'custom',
                id_produk: state.baseId,
                nama: 'Kreasikan Salad Sendiri (' + state.baseNama + ')',
                harga: state.harga,
                foto: state.baseFoto,
                jumlah: 1,
                komponen: state.komponen,
            });

            customBase.value = '';
            customKomponen.forEach((cb) => (cb.checked = false));
            updateCustomPrice();
            updateCartUI();
            tutupCustom();
        });

        updateCustomPrice();
    }

    // --- Submit payload: cart[i][...] hidden inputs ---
    function buildCartInputs() {
        inputsWrap.textContent = '';

        const add = (name, value) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            inputsWrap.appendChild(input);
        };

        cart.forEach((item, i) => {
            add(`cart[${i}][tipe]`, item.tipe);
            add(`cart[${i}][id_produk]`, item.id_produk);
            add(`cart[${i}][jumlah]`, item.jumlah);
            item.komponen.forEach((k, j) => add(`cart[${i}][id_komponen][${j}]`, k.id));
        });
    }

    form.addEventListener('submit', (e) => {
        if (cart.length === 0) {
            e.preventDefault();
            return;
        }
        buildCartInputs();
    });

    // Restore cart after a failed validation round-trip (JSON injected by the Blade view).
    let oldCart = [];
    const oldCartEl = document.getElementById('kasir-old-cart');
    if (oldCartEl) {
        try {
            oldCart = JSON.parse(oldCartEl.textContent || '[]');
        } catch {
            oldCart = [];
        }
    }
    if (Array.isArray(oldCart) && oldCart.length) {
        oldCart.forEach((item) => {
            const id = parseInt(item.id_produk, 10);
            const jumlah = parseInt(item.jumlah, 10) || 1;
            const card = cards.find((c) => parseInt(c.dataset.id, 10) === id);
            const nama = card ? card.dataset.nama : 'Item';
            const harga = card ? parseFloat(card.dataset.harga) || 0 : 0;

            if (item.tipe === 'custom') {
                const ids = Object.values(item.id_komponen || {}).map((v) => parseInt(v, 10));
                const komponen = ids.map((kid) => {
                    const cb = customKomponen.find((c) => parseInt(c.value, 10) === kid);
                    return cb
                        ? { id: kid, nama: cb.dataset.nama || '', harga: parseFloat(cb.dataset.harga) || 0 }
                        : { id: kid, nama: 'Komponen', harga: 0 };
                });
                cart.push({
                    tipe: 'custom',
                    id_produk: id,
                    nama: 'Kreasikan Salad Sendiri' + (card ? ' (' + nama + ')' : ''),
                    harga: harga + komponen.reduce((sum, k) => sum + k.harga, 0),
                    foto: card && card.querySelector('img') ? card.querySelector('img').getAttribute('src') : '',
                    jumlah,
                    komponen,
                });
            } else if (card) {
                cart.push({
                    tipe: 'produk',
                    id_produk: id,
                    nama,
                    harga,
                    foto: card.querySelector('img') ? card.querySelector('img').getAttribute('src') : '',
                    jumlah,
                    komponen: [],
                });
            }
        });
    }

    updateCartUI();
    applyFilter();
}
