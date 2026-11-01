<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat Pemesanan — Hamta Healthy Food</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .rw-wrap { max-width: 920px; margin: 0 auto; padding-bottom: 56px; }
        .rw-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin: 8px 0 24px; flex-wrap: wrap; }
        .rw-title-group { display: flex; align-items: center; gap: 14px; }
        .rw-back { display: grid; place-items: center; width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 1.5px solid rgba(11,65,43,.15); color: var(--green); text-decoration: none; transition: all .2s; }
        .rw-back:hover { background: #eaf5e7; transform: translateX(-2px); }
        .rw-head h1 { margin: 0; font-size: 27px; font-weight: 800; color: var(--green); letter-spacing: -.6px; }

        /* Stat Bar */
        .rw-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 24px; }
        .rw-stat-card { background: #fff; border: 1.5px solid rgba(11,65,43,.15); border-radius: 14px; padding: 14px 18px; box-shadow: 0 2px 10px rgba(11,65,43,.04); }
        .rw-stat-label { font-size: 11.5px; font-weight: 700; color: #6f6a5f; text-transform: uppercase; letter-spacing: .5px; }
        .rw-stat-val { font-size: 22px; font-weight: 800; color: var(--green); margin-top: 4px; }

        /* Filter Tabs & Search Bar */
        .rw-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
        .rw-tabs { display: flex; gap: 6px; background: rgba(11,65,43,.08); padding: 4px; border-radius: 12px; }
        .rw-tab { padding: 8px 16px; border-radius: 8px; border: none; background: transparent; font-size: 13px; font-weight: 700; color: #4a5a52; cursor: pointer; transition: all .2s; }
        .rw-tab.is-active { background: #fff; color: var(--green); box-shadow: 0 2px 8px rgba(0,0,0,.08); }

        .rw-search-box { position: relative; min-width: 260px; }
        .rw-search-input { width: 100%; padding: 9px 14px 9px 34px; border-radius: 10px; border: 1.5px solid rgba(11,65,43,.2); font-size: 13px; font-weight: 600; outline: none; background: #fff; }
        .rw-search-input:focus { border-color: var(--green); }
        .rw-search-icon { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #7f8a84; pointer-events: none; }

        /* Lookup By Phone Section */
        .rw-sync-box { background: rgba(101,189,94,.12); border: 1.5px dashed rgba(11,65,43,.25); border-radius: 14px; padding: 14px 18px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
        .rw-sync-info { display: flex; align-items: center; gap: 12px; }
        .rw-sync-form { display: flex; gap: 8px; flex: 1 1 auto; max-width: 400px; }
        .rw-sync-input { flex: 1 1 auto; padding: 8px 12px; border-radius: 8px; border: 1px solid rgba(11,65,43,.25); font-size: 12.5px; font-weight: 600; outline: none; }
        .rw-sync-btn { padding: 8px 14px; border-radius: 8px; background: var(--green); color: #fff; font-size: 12.5px; font-weight: 700; border: none; cursor: pointer; white-space: nowrap; }

        /* Order Cards */
        .rw-order-card { background: #fff; border: 2px solid rgba(11,65,43,.14); border-radius: 16px; margin-bottom: 18px; overflow: hidden; box-shadow: 0 4px 16px rgba(11,65,43,.04); transition: transform .2s, border-color .2s; }
        .rw-order-card:hover { border-color: var(--green); transform: translateY(-1px); }

        .rw-card-header { padding: 16px 20px; background: #faf8f3; border-bottom: 1px solid rgba(11,65,43,.08); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .rw-code-wrap { display: flex; align-items: center; gap: 8px; }
        .rw-code { font-family: monospace; font-size: 15px; font-weight: 800; color: var(--green); }
        .rw-copy-btn { padding: 2px 7px; border-radius: 5px; background: rgba(11,65,43,.08); border: none; font-size: 10px; font-weight: 700; color: var(--green); cursor: pointer; }
        .rw-copy-btn:hover { background: rgba(11,65,43,.15); }
        .rw-meta { font-size: 12px; color: #6f6a5f; margin-top: 2px; }

        .rw-badges { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .rw-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 999px; font-size: 11.5px; font-weight: 700; }
        .rw-badge-diproses { background: #e0f2fe; color: #0369a1; }
        .rw-badge-siap { background: #dcfce7; color: #15803d; }
        .rw-badge-selesai { background: #e9e6df; color: #374151; }
        .rw-badge-batal { background: #fee2e2; color: #b91c1c; }

        .rw-badge-pay { background: #fef3c7; color: #92400e; font-size: 11px; }
        .rw-badge-pay-ok { background: #dcfce7; color: #166534; font-size: 11px; }

        /* Items Section */
        .rw-items { padding: 16px 20px; }
        .rw-item { display: flex; align-items: center; gap: 14px; padding: 10px 0; border-bottom: 1px dashed rgba(11,65,43,.1); }
        .rw-item:last-child { border-bottom: none; }
        .rw-thumb { width: 56px; height: 56px; border-radius: 8px; background: #edf4eb; overflow: hidden; flex: 0 0 56px; position: relative; }
        .rw-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .rw-thumb-placeholder { width: 100%; height: 100%; display: grid; place-items: center; font-size: 22px; }
        .rw-item-info { flex: 1 1 auto; min-width: 0; }
        .rw-item-name { font-size: 14px; font-weight: 700; color: var(--ink); margin: 0; }
        .rw-item-sub { font-size: 11.5px; color: #6f6a5f; margin-top: 2px; }
        .rw-item-price { font-size: 13.5px; font-weight: 700; color: var(--ink); text-align: right; white-space: nowrap; }

        /* Card Footer */
        .rw-card-footer { padding: 14px 20px; background: #fff; border-top: 1px solid rgba(11,65,43,.08); display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
        .rw-total-wrap { display: flex; align-items: baseline; gap: 8px; }
        .rw-total-label { font-size: 13px; font-weight: 600; color: #6f6a5f; }
        .rw-total-val { font-size: 18px; font-weight: 800; color: var(--green); }

        .rw-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .rw-btn-track { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 9px; background: transparent; border: 1.5px solid var(--green); color: var(--green); font-size: 12.5px; font-weight: 700; text-decoration: none; transition: all .2s; }
        .rw-btn-track:hover { background: rgba(11,65,43,.06); }

        .rw-btn-reorder { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 9px; background: var(--lime); color: var(--green); font-size: 12.5px; font-weight: 800; border: none; cursor: pointer; transition: all .2s; }
        .rw-btn-reorder:hover { background: #56a750; transform: translateY(-1px); }

        /* Empty State */
        .rw-empty { background: #fff; border: 2px dashed rgba(11,65,43,.2); border-radius: 18px; padding: 54px 24px; text-align: center; }
        .rw-empty-icon { font-size: 48px; margin-bottom: 12px; }
        .rw-empty h3 { margin: 0 0 8px; font-size: 20px; font-weight: 800; color: var(--green); }
        .rw-empty p { margin: 0 auto 20px; font-size: 13.5px; color: #6f6a5f; max-width: 420px; }
        .rw-empty-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 12px; background: var(--green); color: #fff; font-size: 14px; font-weight: 800; text-decoration: none; }

        @media (max-width: 640px) {
            .rw-stats { grid-template-columns: 1fr; gap: 10px; }
            .rw-stat-card { padding: 12px 14px; }
            .rw-toolbar { flex-direction: column; align-items: stretch; }
            .rw-search-box { min-width: auto; width: 100%; }
            .rw-card-header, .rw-card-footer { padding: 12px 14px; }
            .rw-items { padding: 12px 14px; }
            .rw-actions { width: 100%; }
            .rw-btn-track, .rw-btn-reorder { flex: 1 1 auto; justify-content: center; }
        }
    </style>
</head>
<body>
<div class="page">
    @include('customer.partials.header')

    <div class="rw-wrap">
        <!-- Header -->
        <div class="rw-head">
            <div class="rw-title-group">
                <a class="rw-back" href="{{ route('home') }}" aria-label="Kembali ke Beranda">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1>Riwayat Pemesanan</h1>
                    <span style="font-size:12.5px;color:#6f6a5f">Lihat daftar menu sehat yang pernah Anda beli di Hamta</span>
                </div>
            </div>

            <a href="{{ route('customer.daftar_menu') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:700;color:var(--green);text-decoration:none">
                <span>+ Beli Menu Baru</span>
            </a>
        </div>

        <!-- Bar Ringkasan Statistik -->
        <div class="rw-stats" id="rw-stats-bar" style="display:none">
            <div class="rw-stat-card">
                <span class="rw-stat-label">Total Pesanan</span>
                <div class="rw-stat-val" id="rw-stat-total-orders">0</div>
            </div>
            <div class="rw-stat-card">
                <span class="rw-stat-label">Porsi Menu Dibeli</span>
                <div class="rw-stat-val" id="rw-stat-total-items">0</div>
            </div>
            <div class="rw-stat-card">
                <span class="rw-stat-label">Total Belanja</span>
                <div class="rw-stat-val" id="rw-stat-total-spent">Rp 0</div>
            </div>
        </div>

        <!-- Bar Pencarian & Filter Status -->
        <div class="rw-toolbar">
            <div class="rw-tabs">
                <button type="button" class="rw-tab is-active" data-filter="all">Semua</button>
                <button type="button" class="rw-tab" data-filter="diproses">Diproses</button>
                <button type="button" class="rw-tab" data-filter="siap">Siap</button>
                <button type="button" class="rw-tab" data-filter="selesai">Selesai</button>
                <button type="button" class="rw-tab" data-filter="batal">Batal</button>
            </div>

            <div class="rw-search-box">
                <svg class="rw-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" id="rw-search-input" class="rw-search-input" placeholder="Cari nama menu atau kode...">
            </div>
        </div>

        <!-- Box Sinkronisasi / Pencarian via WhatsApp (Bila berganti browser / HP) -->
        <div class="rw-sync-box">
            <div class="rw-sync-info">
                <span style="font-size:22px">📱</span>
                <div>
                    <strong style="font-size:13px;color:var(--green);display:block">Pernah memesan di perangkat lain?</strong>
                    <span style="font-size:11.5px;color:#556b60">Cari riwayat Anda dengan nomor WhatsApp atau kode pesanan:</span>
                </div>
            </div>

            <form id="rw-sync-form" class="rw-sync-form">
                <input type="text" id="rw-sync-keyword" class="rw-sync-input" placeholder="No. WhatsApp (08xxx) / Kode (ORD-xxx)" required>
                <button type="submit" class="rw-sync-btn" id="rw-sync-submit">Temukan</button>
            </form>
        </div>

        <!-- Kontainer Daftar Pesanan -->
        <div id="rw-orders-list">
            <!-- Loading Indicator Awal -->
            <div id="rw-loading" style="padding:40px;text-align:center;color:#6f6a5f;font-size:14px;font-weight:600">
                Memuat riwayat pemesanan Anda...
            </div>
        </div>

        <!-- Tampilan Kosong (Empty State) -->
        <div class="rw-empty" id="rw-empty-state" style="display:none">
            <div class="rw-empty-icon">🥗</div>
            <h3>Belum Ada Riwayat Pemesanan</h3>
            <p>Anda belum pernah melakukan pemesanan di Hamta Healthy Food melalui perangkat ini. Yuk mulai hidup sehat hari ini!</p>
            <a href="{{ route('customer.daftar_menu') }}" class="rw-empty-btn">
                <span>Eksplor Katalog Menu Sehat ➔</span>
            </a>
        </div>
    </div>
</div>

<script>
(function () {
    var CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var listEl = document.getElementById('rw-orders-list');
    var emptyEl = document.getElementById('rw-empty-state');
    var loadingEl = document.getElementById('rw-loading');
    var statsBar = document.getElementById('rw-stats-bar');
    var searchInput = document.getElementById('rw-search-input');
    var syncForm = document.getElementById('rw-sync-form');

    var allOrders = [];
    var activeFilter = 'all';
    var activeSearch = '';

    var rupiah = function (n) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
    };

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    // Ambil daftar kode pesanan dari browser
    function getSavedCodes() {
        var codes = [];
        try {
            var hist = JSON.parse(localStorage.getItem('hamta_order_history') || '[]');
            if (Array.isArray(hist)) {
                hist.forEach(function (it) {
                    var code = typeof it === 'string' ? it : (it && it.kode_pesanan ? it.kode_pesanan : null);
                    if (code && codes.indexOf(code) === -1) codes.push(code);
                });
            }
        } catch (e) {}

        try {
            var last = JSON.parse(localStorage.getItem('hamta_last_order') || 'null');
            if (last && last.order && last.order.kode_pesanan) {
                if (codes.indexOf(last.order.kode_pesanan) === -1) {
                    codes.unshift(last.order.kode_pesanan);
                }
            }
        } catch (e) {}

        return codes;
    }

    function saveCodes(newCodes) {
        try {
            var existing = getSavedCodes();
            newCodes.forEach(function (c) {
                if (existing.indexOf(c) === -1) existing.unshift(c);
            });
            localStorage.setItem('hamta_order_history', JSON.stringify(existing.slice(0, 50)));
        } catch (e) {}
    }

    // Fetch data pesanan dari backend
    function loadHistory(codes, phoneQuery, searchQuery) {
        if (loadingEl) loadingEl.style.display = 'block';

        fetch("{{ route('customer.pesanan.api_riwayat') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify({
                codes: codes || [],
                phone: phoneQuery || '',
                search: searchQuery || ''
            })
        })
        .then(function (res) { return res.json(); })
        .then(function (res) {
            if (loadingEl) loadingEl.style.display = 'none';
            if (res.ok && Array.isArray(res.pesanan)) {
                allOrders = res.pesanan;
                // Simpan kode yang ditemukan ke localStorage agar tersimpan di riwayat
                if (res.pesanan.length > 0) {
                    var loadedCodes = res.pesanan.map(function (p) { return p.kode_pesanan; });
                    saveCodes(loadedCodes);
                }
                updateStats();
                render();
            } else {
                render();
            }
        })
        .catch(function () {
            if (loadingEl) loadingEl.style.display = 'none';
            if (window.hamtaToast) window.hamtaToast('Gagal memuat riwayat. Coba lagi.');
            render();
        });
    }

    function updateStats() {
        if (!allOrders.length) {
            if (statsBar) statsBar.style.display = 'none';
            return;
        }

        var totalOrders = allOrders.length;
        var totalItems = allOrders.reduce(function (acc, p) { return acc + (p.total_item || 0); }, 0);
        var totalSpent = allOrders.reduce(function (acc, p) {
            return p.status_pesanan !== 'batal' ? acc + (p.total_bayar || 0) : acc;
        }, 0);

        var oEl = document.getElementById('rw-stat-total-orders');
        var iEl = document.getElementById('rw-stat-total-items');
        var sEl = document.getElementById('rw-stat-total-spent');

        if (oEl) oEl.textContent = totalOrders + ' Pesanan';
        if (iEl) iEl.textContent = totalItems + ' Porsi';
        if (sEl) sEl.textContent = rupiah(totalSpent);

        if (statsBar) statsBar.style.display = 'grid';
    }

    function reorderItems(order) {
        if (!order || !Array.isArray(order.items) || !order.items.length) return;

        var currentCart = window.hamtaGetCart ? window.hamtaGetCart() : [];

        var addedCount = 0;
        order.items.forEach(function (item) {
            if (item.status_item === 'dibatalkan') return;

            if (item.id_produk) {
                // Produk reguler
                var existing = currentCart.find(function (c) {
                    return c.tipe === 'produk' && parseInt(c.id_produk, 10) === parseInt(item.id_produk, 10);
                });
                if (existing) {
                    existing.jumlah = (parseInt(existing.jumlah, 10) || 1) + (parseInt(item.jumlah, 10) || 1);
                } else {
                    currentCart.push({
                        tipe: 'produk',
                        id_produk: parseInt(item.id_produk, 10),
                        nama: item.nama_item,
                        harga: parseFloat(item.harga_satuan) || 0,
                        foto: item.foto || '',
                        jumlah: parseInt(item.jumlah, 10) || 1
                    });
                }
                addedCount++;
            } else {
                // Custom salad
                currentCart.push({
                    tipe: 'custom',
                    id_produk: 0,
                    nama: item.nama_item || 'Kreasikan Salad Sendiri',
                    harga: parseFloat(item.harga_satuan) || 0,
                    foto: item.foto || '',
                    id_komponen: item.id_komponen || [],
                    komponen: (item.komponen || []).map(function (k) { return { nama: k }; }),
                    jumlah: parseInt(item.jumlah, 10) || 1
                });
                addedCount++;
            }
        });

        if (window.hamtaSaveCart) {
            window.hamtaSaveCart(currentCart);
        }

        if (window.hamtaToast) {
            window.hamtaToast(addedCount + ' menu pesanan ditambahkan ke keranjang!');
        }

        setTimeout(function () {
            window.location.href = "{{ route('customer.checkout') }}";
        }, 500);
    }

    function render() {
        if (!listEl) return;
        listEl.innerHTML = '';

        var filtered = allOrders.filter(function (p) {
            // Filter Status
            if (activeFilter !== 'all' && p.status_pesanan !== activeFilter) {
                return false;
            }
            // Filter Search Text
            if (activeSearch) {
                var q = activeSearch.toLowerCase();
                var inCode = (p.kode_pesanan || '').toLowerCase().indexOf(q) !== -1;
                var inItem = (p.items || []).some(function (it) {
                    return (it.nama_item || '').toLowerCase().indexOf(q) !== -1;
                });
                if (!inCode && !inItem) return false;
            }
            return true;
        });

        if (!filtered.length) {
            if (!allOrders.length) {
                if (emptyEl) emptyEl.style.display = 'block';
            } else {
                listEl.innerHTML = '<div style="padding:40px;text-align:center;color:#6f6a5f;font-size:13.5px;font-weight:600">Tidak ada pesanan yang cocok dengan filter atau kata kunci.</div>';
                if (emptyEl) emptyEl.style.display = 'none';
            }
            return;
        }

        if (emptyEl) emptyEl.style.display = 'none';

        filtered.forEach(function (order) {
            var card = document.createElement('article');
            card.className = 'rw-order-card';

            // Status label & badge
            var statusBadgeClass = 'rw-badge-' + (order.status_pesanan || 'diproses');
            var statusMap = {
                'diproses': 'Diproses di Resto',
                'siap': 'Siap ' + (order.metode_ambil === 'delivery' ? 'Diantar' : 'Diambil'),
                'selesai': 'Pesanan Selesai',
                'batal': 'Dibatalkan'
            };
            var statusText = statusMap[order.status_pesanan] || order.status_pesanan;

            var payBadge = '';
            if (order.metode_bayar === 'tunai') {
                payBadge = '<span class="rw-badge rw-badge-pay">💵 Bayar di Tempat</span>';
            } else if (order.status_pembayaran === 'terverifikasi') {
                payBadge = '<span class="rw-badge rw-badge-pay-ok">✅ QRIS Lunas</span>';
            } else if (order.status_pembayaran === 'ditolak') {
                payBadge = '<span class="rw-badge rw-badge-batal">❌ QRIS Ditolak</span>';
            } else {
                payBadge = '<span class="rw-badge rw-badge-pay">⏳ QRIS Menunggu Verifikasi</span>';
            }

            var takeText = order.metode_ambil === 'delivery' ? '🛵 Delivery' : '🏪 Pickup';

            // Items HTML
            var itemsHtml = '';
            (order.items || []).forEach(function (item) {
                var isCanceled = item.status_item === 'dibatalkan';
                var subPrice = (parseFloat(item.harga_satuan) || 0) * (parseInt(item.jumlah, 10) || 1);
                var compText = (item.komponen && item.komponen.length) ? '<div class="rw-item-sub">Komponen: ' + esc(item.komponen.join(', ')) + '</div>' : '';
                var canceledNote = isCanceled ? '<div style="font-size:11px;color:#c0392b;font-weight:700;margin-top:2px">⚠️ Dibatalkan resto</div>' : '';

                var thumbContent = item.foto
                    ? '<img src="' + esc(item.foto) + '" alt="' + esc(item.nama_item) + '" onerror="this.onerror=null;this.parentElement.innerHTML=\'<span class=\\\'rw-thumb-placeholder\\\'>🥗</span>\'">'
                    : '<span class="rw-thumb-placeholder">🥗</span>';

                itemsHtml += '' +
                    '<div class="rw-item' + (isCanceled ? ' opacity-50 line-through' : '') + '">' +
                        '<div class="rw-thumb">' + thumbContent + '</div>' +
                        '<div class="rw-item-info">' +
                            '<h4 class="rw-item-name">' + esc(item.nama_item) + '</h4>' +
                            compText +
                            canceledNote +
                            '<span style="font-size:12px;color:#6f6a5f">' + item.jumlah + 'x ' + rupiah(item.harga_satuan) + '</span>' +
                        '</div>' +
                        '<div class="rw-item-price">' + rupiah(subPrice) + '</div>' +
                    '</div>';
            });

            var trackingUrl = "{{ url('status-pesanan') }}/" + encodeURIComponent(order.kode_pesanan);

            card.innerHTML = '' +
                '<div class="rw-card-header">' +
                    '<div>' +
                        '<div class="rw-code-wrap">' +
                            '<span class="rw-code">#' + esc(order.kode_pesanan) + '</span>' +
                            '<button type="button" class="rw-copy-btn" data-copy="' + esc(order.kode_pesanan) + '">SALIN</button>' +
                        '</div>' +
                        '<div class="rw-meta">' +
                            '<span>' + esc(order.created_at_formatted) + '</span>' +
                            '<span style="margin:0 6px">•</span>' +
                            '<span>' + takeText + '</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="rw-badges">' +
                        payBadge +
                        '<span class="rw-badge ' + statusBadgeClass + '">' + esc(statusText) + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="rw-items">' + itemsHtml + '</div>' +
                '<div class="rw-card-footer">' +
                    '<div class="rw-total-wrap">' +
                        '<span class="rw-total-label">Total (' + (order.total_item || 0) + ' Menu):</span>' +
                        '<span class="rw-total-val">' + rupiah(order.total_bayar) + '</span>' +
                    '</div>' +
                    '<div class="rw-actions">' +
                        '<a href="' + trackingUrl + '" class="rw-btn-track">' +
                            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' +
                            '<span>Status Live</span>' +
                        '</a>' +
                        '<button type="button" class="rw-btn-reorder" data-reorder-idx="' + esc(order.kode_pesanan) + '">' +
                            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m17 2 4 4-4 4"/><path d="M3 11v-1a4 4 0 0 1 4-4h14"/><path d="m7 22-4-4 4-4"/><path d="M21 13v1a4 4 0 0 1-4 4H3"/></svg>' +
                            '<span>Pesan Lagi</span>' +
                        '</button>' +
                    '</div>' +
                '</div>';

            listEl.appendChild(card);
        });

        // Event listener Salin Kode
        listEl.querySelectorAll('[data-copy]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var code = this.dataset.copy;
                if (!code) return;
                try {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(code);
                    }
                    if (window.hamtaToast) window.hamtaToast('Kode pesanan #' + code + ' disalin');
                } catch (e) {}
            });
        });

        // Event listener Pesan Lagi (Re-order)
        listEl.querySelectorAll('[data-reorder-idx]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var code = this.dataset.reorderIdx;
                var found = allOrders.find(function (p) { return p.kode_pesanan === code; });
                if (found) reorderItems(found);
            });
        });
    }

    // Filter status tab klik
    document.querySelectorAll('.rw-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.rw-tab').forEach(function (t) { t.classList.remove('is-active'); });
            this.classList.add('is-active');
            activeFilter = this.dataset.filter || 'all';
            render();
        });
    });

    // Pencarian instan input
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            activeSearch = this.value.trim();
            render();
        });
    }

    // Form Cari Riwayat via WhatsApp / Kode
    if (syncForm) {
        syncForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var q = (document.getElementById('rw-sync-keyword') || {}).value || '';
            q = q.trim();
            if (!q) return;

            var submitBtn = document.getElementById('rw-sync-submit');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Mencari...';
            }

            var isCode = q.toUpperCase().indexOf('ORD-') !== -1;
            loadHistory(isCode ? [q.toUpperCase()] : [], !isCode ? q : '', isCode ? q : '');

            setTimeout(function () {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Temukan';
                }
            }, 1000);
        });
    }

    // Inisialisasi: Muat kode pesanan tersimpan di browser
    var saved = getSavedCodes();
    if (saved.length > 0) {
        loadHistory(saved);
    } else {
        if (loadingEl) loadingEl.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'block';
    }
})();
</script>

@include('customer.partials.footer')
</body>
</html>

