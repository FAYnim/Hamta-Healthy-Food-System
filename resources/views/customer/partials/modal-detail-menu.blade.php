<!-- ======================================================== -->
<!-- MODAL DETAIL MAKANAN (TAMPIL DI TENGAH LAYAR)            -->
<!-- ======================================================== -->
<style>
    .hamta-modal {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }

    .hamta-modal.is-active {
        opacity: 1;
        visibility: visible;
    }

    .hamta-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(11, 65, 43, 0.55);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: 1;
    }

    .hamta-modal-container {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 860px;
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 30px 60px -15px rgba(11, 65, 43, 0.4);
        transform: scale(0.92) translateY(12px);
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
    }

    .hamta-modal.is-active .hamta-modal-container {
        transform: scale(1) translateY(0);
    }

    .hamta-modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #333333;
        transition: all 0.18s ease;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .hamta-modal-close:hover {
        background: #f1f1f1;
        color: #000000;
        transform: scale(1.06);
    }

    .hamta-modal-body {
        padding: 32px 34px;
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 32px;
        align-items: center;
    }

    .hamta-modal-media {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        background: #f0f7ef;
        aspect-ratio: 1 / 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(66, 106, 87, 0.18);
    }

    .hamta-modal-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hamta-modal-info {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .hamta-modal-title {
        font-size: 24px;
        font-weight: 800;
        color: var(--hamta-ink, #101010);
        line-height: 1.2;
        margin: 0;
        padding-right: 30px;
    }

    .hamta-modal-price-wrap {
        display: flex;
        align-items: baseline;
        gap: 6px;
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    .hamta-modal-price {
        font-size: 22px;
        font-weight: 800;
        color: var(--hamta-green, #0b412b);
        letter-spacing: -0.3px;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
        line-height: 1.2;
    }

    .hamta-modal-desc-box {
        background: #fbf9f4;
        border: 1px solid rgba(66, 106, 87, 0.15);
        border-radius: 12px;
        padding: 12px 14px;
    }

    .hamta-modal-desc-label {
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--hamta-green, #0b412b);
        margin: 0 0 4px;
    }

    .hamta-modal-desc {
        font-size: 13.5px;
        line-height: 1.5;
        color: #374151;
        margin: 0;
    }

    .hamta-modal-qty-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 0;
    }

    .hamta-modal-qty-label {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--hamta-ink, #101010);
    }

    .hamta-modal-qty-box {
        display: inline-flex;
        align-items: center;
        background: #ffffff;
        border: 1.5px solid var(--hamta-green, #0b412b);
        border-radius: 999px;
        padding: 2px;
    }

    .qty-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: none;
        background: #f0f7ef;
        color: var(--hamta-green, #0b412b);
        font-size: 17px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
    }

    .qty-btn:hover {
        background: var(--hamta-green, #0b412b);
        color: #ffffff;
    }

    .qty-num {
        width: 40px;
        text-align: center;
        font-size: 15px;
        font-weight: 800;
        color: var(--hamta-ink, #101010);
        border: none;
        outline: none;
        background: transparent;
    }

    .hamta-modal-note-box {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .hamta-modal-note-label {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--hamta-ink, #101010);
    }

    .hamta-modal-note-input {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid rgba(66, 106, 87, 0.25);
        border-radius: 11px;
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        background: #ffffff;
    }

    .hamta-modal-note-input:focus {
        border-color: var(--hamta-green, #0b412b);
        box-shadow: 0 0 0 3px rgba(11, 65, 43, 0.12);
    }

    .hamta-modal-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 18px;
        border-top: 1.5px solid rgba(66, 106, 87, 0.15);
        margin-top: 6px;
        gap: 24px;
    }

    .hamta-modal-subtotal-meta {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 0;
        flex-shrink: 0;
    }

    .hamta-modal-subtotal-label {
        font-size: 12px;
        color: var(--hamta-gray, #667085);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        margin-bottom: 2px;
    }

    .hamta-modal-subtotal-val {
        font-size: 24px;
        font-weight: 800;
        color: var(--hamta-green, #0b412b);
        white-space: nowrap;
        letter-spacing: -0.5px;
        font-variant-numeric: tabular-nums;
        line-height: 1.2;
    }

    .hamta-modal-submit-btn {
        background: var(--hamta-green, #0b412b);
        color: #ffffff;
        border: none;
        border-radius: 14px;
        padding: 13px 28px;
        font-size: 15px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-shrink: 0;
        white-space: nowrap;
        transition: all 0.2s ease;
        box-shadow: 0 6px 18px -2px rgba(11, 65, 43, 0.32);
    }

    .hamta-modal-submit-btn:hover {
        background: #145938;
        transform: translateY(-1.5px);
        box-shadow: 0 8px 20px -2px rgba(11, 65, 43, 0.38);
    }

    .hamta-modal-submit-btn:active {
        transform: translateY(0);
    }

    .modal-add-plus {
        font-size: 20px;
        font-weight: 800;
        line-height: 1;
        display: inline-block;
        margin-right: 2px;
        transform: translateY(-1px);
    }

    /* Tablet Responsive (769px - 1024px) */
    @media (min-width: 769px) and (max-width: 1024px) {
        .hamta-modal-container {
            max-width: 90vw;
            margin: auto;
        }

        .hamta-modal-body {
            grid-template-columns: 260px 1fr;
            gap: 24px;
            padding: 26px 28px;
        }

        .hamta-modal-title {
            font-size: 22px;
        }

        .hamta-modal-price {
            font-size: 20px;
        }
    }

    /* Mobile Responsive (<= 768px) */
    @media (max-width: 768px) {
        .hamta-modal {
            overflow-y: auto;
            padding: 12px;
        }

        .hamta-modal-container {
            max-width: 96vw;
            max-height: 92vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 20px;
            margin: auto;
        }

        .hamta-modal-body {
            grid-template-columns: 1fr;
            gap: 16px;
            padding: 20px 18px;
        }

        .hamta-modal-media {
            max-height: 240px;
        }

        .hamta-modal-title {
            font-size: 22px;
        }

        .hamta-modal-price {
            font-size: 22px;
        }

        .hamta-modal-footer {
            flex-direction: column;
            gap: 12px;
            align-items: stretch;
        }

        .hamta-modal-subtotal-meta {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .hamta-modal-subtotal-val {
            font-size: 22px;
            white-space: nowrap;
        }

        .hamta-modal-submit-btn {
            width: 100%;
            justify-content: center;
            padding: 13px 20px;
        }
    }
</style>

<div id="hamta-food-modal" class="hamta-modal" aria-hidden="true">
    <div class="hamta-modal-backdrop" id="hamta-modal-backdrop"></div>
    <div class="hamta-modal-container" role="dialog" aria-modal="true" aria-labelledby="modal-food-title">
        <!-- Close Button (X) -->
        <button type="button" class="hamta-modal-close" id="hamta-modal-close" aria-label="Tutup">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <div class="hamta-modal-body">
            <!-- Left: Foto Produk -->
            <div class="hamta-modal-media">
                <img id="modal-food-img" src="" alt="Foto Makanan" class="hamta-modal-photo">
                <div id="modal-food-fallback" class="hamta-modal-fallback" style="display:none; width:100%; height:100%; flex-direction:column; align-items:center; justify-content:center; background:#eaf7e8; color:var(--hamta-green, #0b412b);">
                    <svg class="w-16 h-16 opacity-40 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    <span class="text-sm font-bold">Hamta Healthy Food</span>
                </div>
            </div>

            <!-- Right: Info, Kuantitas, Catatan & Tombol Tambah -->
            <div class="hamta-modal-info">
                <div>
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap">
                        <h2 id="modal-food-title" class="hamta-modal-title">Nama Makanan</h2>
                        <span id="modal-food-cabang" class="badge-cabang" style="display:none;position:static;font-size:11px"></span>
                    </div>
                    <div class="hamta-modal-price-wrap mt-2">
                        <span id="modal-food-price" class="hamta-modal-price">Rp 0</span>
                        <span class="text-xs font-semibold text-gray-500">/ porsi</span>
                    </div>
                </div>

                <!-- Deskripsi Makanan (HANYA MUNCUL DI MODAL DETAIL) -->
                <div class="hamta-modal-desc-box">
                    <h4 class="hamta-modal-desc-label">Deskripsi Menu</h4>
                    <p id="modal-food-desc" class="hamta-modal-desc">Deskripsi makanan...</p>
                </div>

                <!-- Komposisi Bahan & Alergen (PRD F1 / A2) -->
                <div id="modal-food-bahan-box" class="hamta-modal-desc-box" style="display:none;background:#f0fdf4;border-color:rgba(101,189,94,.3);">
                    <h4 class="hamta-modal-desc-label" style="color:var(--hamta-green, #0b412b)">Komposisi Bahan</h4>
                    <p id="modal-food-bahan" style="font-size:12.5px;color:#374151;margin:0 0 4px">-</p>
                    <div id="modal-food-alergen-wrap" style="display:none;margin-top:4px;padding-top:4px;border-top:1px dashed rgba(11,65,43,.15)">
                        <span style="font-size:11px;font-weight:700;color:#b91c1c">Perhatian Alergen:</span>
                        <p id="modal-food-alergen" style="font-size:12px;color:#b91c1c;margin:0"></p>
                    </div>
                </div>

                <!-- Pengatur Kuantitas (Jumlah) -->
                <div class="hamta-modal-qty-row">
                    <span class="hamta-modal-qty-label">Pilih Jumlah</span>
                    <div class="hamta-modal-qty-box">
                        <button type="button" class="qty-btn" id="modal-qty-minus" aria-label="Kurangi jumlah">−</button>
                        <input type="text" class="qty-num" id="modal-qty-num" value="1" readonly>
                        <button type="button" class="qty-btn" id="modal-qty-plus" aria-label="Tambah jumlah">+</button>
                    </div>
                </div>

                <!-- Catatan Khusus -->
                <div class="hamta-modal-note-box">
                    <label for="modal-food-note" class="hamta-modal-note-label">Catatan Tambahan (Opsional)</label>
                    <input type="text" id="modal-food-note" class="hamta-modal-note-input" placeholder="Contoh: dressing dipisah, jangan pakai bawang, dll.">
                </div>

                <!-- Footer: Total & Tombol Tambahkan Menu -->
                <div class="hamta-modal-footer">
                    <div class="hamta-modal-subtotal-meta">
                        <span class="hamta-modal-subtotal-label">Total Harga</span>
                        <span id="modal-food-subtotal" class="hamta-modal-subtotal-val">Rp 0</span>
                    </div>
                    <button type="button" id="modal-food-add-btn" class="hamta-modal-submit-btn">
                        <span class="modal-add-plus">+</span>
                        <span>Tambahkan Menu</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var modal = document.getElementById('hamta-food-modal');
    if (!modal) return;

    var backdrop = document.getElementById('hamta-modal-backdrop');
    var closeBtn = document.getElementById('hamta-modal-close');
    var modalImg = document.getElementById('modal-food-img');
    var modalImgFallback = document.getElementById('modal-food-fallback');
    var modalTitle = document.getElementById('modal-food-title');
    var modalPrice = document.getElementById('modal-food-price');
    var modalDesc = document.getElementById('modal-food-desc');
    var modalSubtotal = document.getElementById('modal-food-subtotal');
    var modalNote = document.getElementById('modal-food-note');
    var qtyNum = document.getElementById('modal-qty-num');
    var qtyMinus = document.getElementById('modal-qty-minus');
    var qtyPlus = document.getElementById('modal-qty-plus');
    var addBtn = document.getElementById('modal-food-add-btn');

    var currentItem = null;
    var currentQty = 1;

    function formatRupiah(val) {
        return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
    }

    function updateModalSubtotal() {
        if (!currentItem) return;
        var subtotal = (parseFloat(currentItem.harga) || 0) * currentQty;
        modalSubtotal.textContent = formatRupiah(subtotal);
        qtyNum.value = currentQty;
    }

    function openModal(data) {
        currentItem = data;
        currentQty = 1;
        if (modalNote) modalNote.value = '';

        if (modalTitle) modalTitle.textContent = data.nama || 'Menu Hamta';
        if (modalPrice) modalPrice.textContent = formatRupiah(data.harga);
        if (modalDesc) modalDesc.textContent = data.deskripsi || 'Menu lezat dan sehat dari Hamta Salad dengan bahan pilihan berkualitas segar.';

        var modalCabang = document.getElementById('modal-food-cabang');
        if (modalCabang) {
            if (data.cabang && data.cabang.trim()) {
                modalCabang.textContent = 'Khusus ' + data.cabang;
                modalCabang.style.display = 'inline-block';
            } else {
                modalCabang.style.display = 'none';
            }
        }

        var modalBahanBox = document.getElementById('modal-food-bahan-box');
        var modalBahan = document.getElementById('modal-food-bahan');
        var modalAlergenWrap = document.getElementById('modal-food-alergen-wrap');
        var modalAlergen = document.getElementById('modal-food-alergen');
        if (modalBahanBox && modalBahan) {
            if (data.bahan && data.bahan.trim()) {
                modalBahan.textContent = data.bahan;
                modalBahanBox.style.display = 'block';
                if (data.alergen && data.alergen.trim() && modalAlergen && modalAlergenWrap) {
                    modalAlergen.textContent = data.alergen;
                    modalAlergenWrap.style.display = 'block';
                } else if (modalAlergenWrap) {
                    modalAlergenWrap.style.display = 'none';
                }
            } else {
                modalBahanBox.style.display = 'none';
            }
        }

        if (data.foto) {
            if (modalImg) {
                modalImg.src = data.foto;
                modalImg.alt = data.nama || 'Foto Menu';
                modalImg.style.display = 'block';
            }
            if (modalImgFallback) modalImgFallback.style.display = 'none';
        } else {
            if (modalImg) modalImg.style.display = 'none';
            if (modalImgFallback) modalImgFallback.style.display = 'flex';
        }

        updateModalSubtotal();

        modal.classList.add('is-active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('is-active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        currentItem = null;
    }

    // Attach click listener ke setiap kartu produk
    document.querySelectorAll('[data-product-card]').forEach(function(card) {
        card.addEventListener('click', function(e) {
            // Jika yang diklik adalah tombol quick add atau di dalam product-actions (selain tombol detail), abaikan
            if (e.target.closest('[data-add-cart]') || (e.target.closest('.product-actions') && !e.target.closest('[data-open-modal-btn]'))) return;

            var data = {
                id: parseInt(card.dataset.id, 10),
                nama: card.dataset.nama || '',
                harga: parseFloat(card.dataset.harga) || 0,
                kategori: card.dataset.kategori || 'Menu',
                deskripsi: card.dataset.deskripsi || '',
                foto: card.dataset.foto || '',
                cabang: card.dataset.cabang || '',
                bahan: card.dataset.bahan || '',
                alergen: card.dataset.alergen || ''
            };
            openModal(data);
        });

        // Tombol "Lihat Detail"
        var detailBtn = card.querySelector('[data-open-modal-btn]');
        if (detailBtn) {
            detailBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                var data = {
                    id: parseInt(card.dataset.id, 10),
                    nama: card.dataset.nama || '',
                    harga: parseFloat(card.dataset.harga) || 0,
                    kategori: card.dataset.kategori || 'Menu',
                    deskripsi: card.dataset.deskripsi || '',
                    foto: card.dataset.foto || '',
                    cabang: card.dataset.cabang || '',
                    bahan: card.dataset.bahan || '',
                    alergen: card.dataset.alergen || ''
                };
                openModal(data);
            });
        }

        // Tombol Quick Add (+ Tambah) pada kartu
        var quickAddBtn = card.querySelector('[data-add-cart]');
        if (quickAddBtn) {
            quickAddBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                var id = parseInt(quickAddBtn.dataset.id, 10);
                if (!id) return;
                if (window.hamtaAddToCart) {
                    window.hamtaAddToCart({
                        id_produk: id,
                        nama: quickAddBtn.dataset.nama || 'Produk',
                        harga: parseFloat(quickAddBtn.dataset.harga) || 0,
                        foto: quickAddBtn.dataset.foto || '',
                        jumlah: 1
                    });
                }
            });
        }
    });

    // Qty minus & plus
    if (qtyMinus) {
        qtyMinus.addEventListener('click', function(e) {
            e.stopPropagation();
            if (currentQty > 1) {
                currentQty--;
                updateModalSubtotal();
            }
        });
    }

    if (qtyPlus) {
        qtyPlus.addEventListener('click', function(e) {
            e.stopPropagation();
            if (currentQty < 99) {
                currentQty++;
                updateModalSubtotal();
            }
        });
    }

    // Close button & backdrop click
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (backdrop) backdrop.addEventListener('click', closeModal);

    // Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('is-active')) {
            closeModal();
        }
    });

    // Tombol "Tambahkan Menu" dari dalam modal
    if (addBtn) {
        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (!currentItem) return;

            var id = parseInt(currentItem.id, 10);
            if (!id) return;

            var note = modalNote ? modalNote.value.trim() : '';

            if (window.hamtaAddToCart) {
                window.hamtaAddToCart({
                    id_produk: id,
                    nama: currentItem.nama || 'Produk',
                    harga: parseFloat(currentItem.harga) || 0,
                    foto: currentItem.foto || '',
                    jumlah: currentQty,
                    catatan: note || null
                });
            } else {
                var cart = window.hamtaGetCart ? window.hamtaGetCart() : [];

                // Cari apakah item yang sama (dengan catatan yang sama) sudah ada
                var existing = cart.find(function(it) {
                    return it.tipe === 'produk' && 
                           parseInt(it.id_produk, 10) === id && 
                           (it.catatan || '') === note;
                });

                if (existing) {
                    existing.jumlah = (parseInt(existing.jumlah, 10) || 1) + currentQty;
                } else {
                    cart.push({
                        tipe: 'produk',
                        id_produk: id,
                        nama: currentItem.nama || 'Produk',
                        harga: parseFloat(currentItem.harga) || 0,
                        foto: currentItem.foto || '',
                        jumlah: currentQty,
                        catatan: note || null
                    });
                }

                if (window.hamtaSaveCart) {
                    window.hamtaSaveCart(cart);
                } else {
                    try {
                        localStorage.setItem('hamta_cart', JSON.stringify(cart));
                    } catch (err) {}
                }

                if (window.hamtaToast) {
                    var message = currentItem.nama + ' (' + currentQty + 'x) berhasil ditambahkan ke keranjang';
                    window.hamtaToast(message);
                }
            }

            closeModal();
        });
    }

    // Auto-open modal jika URL memuat parameter ?modal=ID
    try {
        var urlParams = new URLSearchParams(window.location.search);
        var autoModalId = urlParams.get('modal');
        if (autoModalId) {
            var targetCard = document.querySelector('[data-product-card][data-id="' + autoModalId + '"]');
            if (targetCard) {
                var btn = targetCard.querySelector('[data-open-modal-btn]') || targetCard;
                btn.click();
            }
        }
    } catch (e) {}
})();
</script>

