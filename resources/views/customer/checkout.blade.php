<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hamta — Rincian Pesananmu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .co-head { display:flex; align-items:center; gap:16px; margin:2px 0 22px; }
        .co-back { display:grid; place-items:center; width:42px; height:42px; border-radius:50%; color:var(--green); text-decoration:none; transition:background .2s; }
        .co-back:hover { background:rgba(11,65,43,.08); }
        .co-head h1 { margin:0; font-size:29px; line-height:1.15; letter-spacing:-.5px; }

        .co-card { background:#fbf8f0; border:2px solid #426a57; border-radius:13px; padding:22px 24px; margin-bottom:20px; }
        .co-card h2 { margin:0; font-size:18px; line-height:1.2; }
        .co-sub { margin:5px 0 0; font-size:13px; opacity:.7; }

        .co-steps { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; list-style:none; margin:22px 0 0; padding:0; }
        .co-step { min-width:0; }
        .co-dot { display:grid; place-items:center; width:42px; height:42px; border-radius:50%; background:#e4eddd; color:#5c7a68; }
        .co-step.is-done .co-dot { background:var(--green); color:#fff; }
        .co-step.is-active .co-dot { background:var(--lime); color:#fff; }
        .co-label { display:block; margin:9px 0 10px; font-size:13px; line-height:1.2; font-weight:700; }
        .co-step:not(.is-done) .co-label { font-weight:600; opacity:.62; }
        .co-bar { display:block; height:9px; border-radius:9px; background:#dfe8d8; overflow:hidden; }
        .co-bar i { display:block; height:100%; border-radius:9px; }
        .co-step.is-done .co-bar i { width:100%; background:var(--green); }
        .co-step.is-active .co-bar i { width:100%; background:var(--lime); }

        .co-item { display:grid; grid-template-columns:92px 1fr auto; gap:18px; padding:18px 0; border-top:1px solid rgba(11,65,43,.16); }
        .co-item:first-of-type { border-top:0; padding-top:6px; }
        .co-item:last-of-type { padding-bottom:4px; }
        .co-thumb { width:92px; aspect-ratio:1; border-radius:10px; background:var(--lime); box-shadow:inset 0 0 0 1px rgba(11,65,43,.12); position:relative; overflow:hidden; }
        .co-thumb:before { content:''; position:absolute; inset:26%; border-radius:50%; background:radial-gradient(circle at 38% 32%,#fff6d4 0 18%,transparent 19%), radial-gradient(circle at 64% 60%,#5f9e42 0 20%,transparent 21%), #f0c37c; }
        .co-item-name { margin:2px 0 0; font-size:17px; line-height:1.2; text-transform:uppercase; letter-spacing:-.2px; }
        .co-item-note { margin:7px 0 0; font-size:12.5px; line-height:1.4; max-width:520px; }
        .co-item-price { font-size:16px; font-weight:700; white-space:nowrap; }
        .co-qty { display:flex; align-items:center; gap:12px; margin-top:10px; padding:5px 12px; border-radius:999px; background:#e9e6df; width:max-content; }
        .co-qty-ctl { border:0; background:transparent; color:#6f6a5f; font-size:17px; line-height:1; cursor:pointer; padding:0 2px; }
        .co-qty-ctl:hover { color:var(--green); }
        .co-qty-num { min-width:10px; font-size:14px; font-weight:700; text-align:center; }
        .co-remove { border:0; background:transparent; color:#c0392b; font:inherit; font-size:11px; font-weight:700; cursor:pointer; padding:0; margin-left:4px; }

        .co-pay-row { display:flex; justify-content:space-between; gap:14px; padding:6px 0; font-size:15px; }
        .co-pay-row span:last-child { white-space:nowrap; }
        .co-divider { height:1px; margin:12px 0; background:rgba(11,65,43,.22); }
        .co-pay-total { display:flex; justify-content:space-between; gap:14px; font-size:18px; font-weight:800; }
        .co-pay-note { margin:6px 0 0; text-align:right; font-size:11.5px; opacity:.7; }
        .co-opt-group { display:grid; gap:10px; margin-top:12px; }
        .co-opt { display:flex; align-items:center; gap:12px; padding:12px 14px; border:2px solid #d8e2d2; border-radius:11px; background:#fff; cursor:pointer; font-family:inherit; font-size:14px; font-weight:600; text-align:left; transition:border-color .15s, background .15s; }
        .co-opt:hover { border-color:var(--green); }
        .co-opt.is-selected { border-color:var(--green); background:#eef5ea; }
        .co-opt-radio { flex:0 0 auto; width:20px; height:20px; border-radius:50%; border:2px solid var(--green); display:grid; place-items:center; }
        .co-opt.is-selected .co-opt-radio:before { content:''; width:10px; height:10px; border-radius:50%; background:var(--green); }
        .co-opt small { display:block; font-size:11.5px; font-weight:500; opacity:.7; }

        .co-info { display:grid; gap:14px; }
        .co-info-label { display:block; font-size:12px; opacity:.68; margin-bottom:3px; }
        .co-info-value { display:flex; align-items:center; justify-content:space-between; gap:12px; font-size:15px; font-weight:700; }
        .co-info-value span { word-break:break-all; }
        .co-copy { flex:0 0 auto; padding:4px 9px; border:1px solid var(--green); border-radius:6px; background:transparent; color:var(--green); font-family:inherit; font-size:10px; font-weight:800; letter-spacing:.6px; cursor:pointer; }
        .co-copy:hover { background:var(--green); color:#fff; }

        @media (max-width:700px) {
            .co-head { gap:10px; margin-bottom:16px; }
            .co-back { width:34px; height:34px; }
            .co-head h1 { font-size:21px; }
            .co-card { padding:16px 14px; margin-bottom:14px; border-width:2px; }
            .co-card h2 { font-size:15px; }
            .co-sub { font-size:11px; }
            .co-steps { gap:8px; margin-top:16px; }
            .co-dot { width:32px; height:32px; }
            .co-dot svg { width:17px; height:17px; }
            .co-label { font-size:10px; margin:7px 0 8px; }
            .co-bar { height:7px; }
            .co-item { grid-template-columns:62px 1fr; gap:11px; padding:13px 0; }
            .co-thumb { width:62px; }
            .co-item-name { font-size:13px; }
            .co-item-note { font-size:10.5px; margin-top:5px; }
            .co-item-price { grid-column:2; font-size:13px; }
            .co-pay-row { font-size:12.5px; padding:5px 0; }
            .co-pay-total { font-size:15px; }
            .co-pay-note { font-size:10px; }
            .co-info-value { font-size:12.5px; }
            .co-info-label { font-size:10.5px; }
        }
    </style>
</head>
<body>
<div class="page">
    @include('customer.partials.header')

    <div class="co-head">
        <a class="co-back" href="{{ url('/') }}" aria-label="Kembali">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        </a>
        <h1>Rincian Pesananmu</h1>
    </div>

    <section class="co-card">
        <h2>Status Pesanan</h2>
        <p class="co-sub">Pesanan sedang disiapkan</p>
        <ol class="co-steps">
            <li class="co-step is-done">
                <span class="co-dot">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                </span>
                <span class="co-label">Pesanan Diterima</span>
                <span class="co-bar"><i></i></span>
            </li>
            <li class="co-step is-active">
                <span class="co-dot">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11h16a8 8 0 0 1-8 8 8 8 0 0 1-8-8Z"/><path d="M7 7c0-1 .7-1.5 1.4-2"/><path d="M12 6c0-1.2.8-1.8 1.5-2.4"/><path d="M17 7c0-1 .6-1.4 1.2-1.8"/></svg>
                </span>
                <span class="co-label">Menyiapkan Salad</span>
                <span class="co-bar"><i></i></span>
            </li>
            <li class="co-step">
                <span class="co-dot">
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                </span>
                <span class="co-label">Selesai &amp; Siap Diambil</span>
                <span class="co-bar"><i></i></span>
            </li>
        </ol>
    </section>

    <section class="co-card">
        <h2>Rincian Pesanan</h2>
        <div id="co-cart-list"></div>
        <p id="co-cart-empty" class="co-item-note">Keranjang masih kosong. Yuk tambah menu dulu!</p>
    </section>

    <section class="co-card">
        <h2>Metode Pembayaran</h2>
        <div class="co-opt-group" role="radiogroup" aria-label="Metode pembayaran" style="margin-top:14px">
            <button type="button" class="co-opt is-selected" data-pay="qris" aria-pressed="true">
                <span class="co-opt-radio" aria-hidden="true"></span>
                <span>QRIS<small>Bayar sekarang via QRIS</small></span>
            </button>
            <button type="button" class="co-opt" data-pay="tunai" aria-pressed="false">
                <span class="co-opt-radio" aria-hidden="true"></span>
                <span>Bayar di Tempat<small>Bayar tunai saat pickup</small></span>
            </button>
        </div>
    </section>

    <section class="co-card">
        <h2>Metode Pengiriman</h2>
        <div class="co-opt-group" role="radiogroup" aria-label="Metode pengiriman" style="margin-top:14px">
            <button type="button" class="co-opt is-selected" data-take="pickup" aria-pressed="true">
                <span class="co-opt-radio" aria-hidden="true"></span>
                <span>Pickup / Ambil di Tempat<small>Ambil sendiri pesananmu</small></span>
            </button>
            <button type="button" class="co-opt" data-take="delivery" aria-pressed="false">
                <span class="co-opt-radio" aria-hidden="true"></span>
                <span>Delivery / Diantar<small>Ongkir dikonfirmasi admin via WhatsApp</small></span>
            </button>
        </div>
    </section>

    <section class="co-card">
        <h2>Rincian Pembayaran</h2>
        <div style="margin-top:12px">
            <div class="co-pay-row"><span id="co-total-label">Total Pemesanan (0 Menu)</span><span id="co-subtotal">Rp. 0</span></div>
            <div class="co-pay-row"><span>Biaya Pengiriman</span><span id="co-ongkir">Rp. 0</span></div>
            <div class="co-pay-row"><span>PPN 11%</span><span id="co-ppn">Rp. 0</span></div>
            <div class="co-divider"></div>
            <div class="co-pay-total"><span>Total Pembayaran</span><span id="co-total">Rp. 0</span></div>
            <p class="co-pay-note">Sudah termasuk pajak</p>
        </div>
    </section>

    <section class="co-card">
        <h2>Informasi Pemesanan</h2>
        <div class="co-info" style="margin-top:14px">
            <div>
                <span class="co-info-label">No. Pesanan</span>
                <span class="co-info-value"><span>1234567890123456789</span><span class="co-copy">SALIN</span></span>
            </div>
            <div>
                <span class="co-info-label">Waktu Pemesanan</span>
                <span class="co-info-value"><span>18 Oktober 2026 14:04</span></span>
            </div>
        </div>
    </section>
<script>
(function () {
    var list = document.getElementById('co-cart-list');
    var empty = document.getElementById('co-cart-empty');
    if (!list) return;
    var rupiah = function (n) { return 'Rp. ' + new Intl.NumberFormat('id-ID').format(Math.round(n)); };
    function render() {
        var cart = window.hamtaGetCart ? window.hamtaGetCart() : [];
        list.textContent = '';
        if (empty) empty.style.display = cart.length ? 'none' : '';
        cart.forEach(function (item, index) {
            var row = document.createElement('div');
            row.className = 'co-item';
            var thumb = document.createElement('div');
            thumb.className = 'co-thumb';
            if (item.foto) {
                var img = document.createElement('img');
                img.src = item.foto;
                img.alt = item.nama || 'Menu';
                img.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover';
                thumb.appendChild(img);
            }
            var mid = document.createElement('div');
            var name = document.createElement('p');
            name.className = 'co-item-name';
            name.textContent = (item.nama || 'Menu').toUpperCase();
            mid.appendChild(name);
            if (item.komponen && item.komponen.length) {
                var detail = document.createElement('p');
                detail.className = 'co-item-note';
                detail.style.marginTop = '2px';
                detail.textContent = item.komponen.map(function (k) { return k.nama || k; }).join(', ');
                mid.appendChild(detail);
            }
            var qty = document.createElement('div');
            qty.className = 'co-qty';
            qty.setAttribute('aria-label', 'Jumlah ' + item.jumlah);
            var minus = document.createElement('button');
            minus.type = 'button';
            minus.className = 'co-qty-ctl';
            minus.textContent = '-';
            minus.setAttribute('aria-label', 'Kurangi jumlah');
            minus.addEventListener('click', function () { step(index, -1); });
            var num = document.createElement('span');
            num.className = 'co-qty-num';
            num.textContent = item.jumlah;
            var plus = document.createElement('button');
            plus.type = 'button';
            plus.className = 'co-qty-ctl';
            plus.textContent = '+';
            plus.setAttribute('aria-label', 'Tambah jumlah');
            plus.addEventListener('click', function () { step(index, 1); });
            var remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'co-remove';
            remove.textContent = 'Hapus';
            remove.setAttribute('aria-label', 'Hapus item dari keranjang');
            remove.addEventListener('click', function () {
                var c = window.hamtaGetCart();
                c.splice(index, 1);
                window.hamtaSaveCart(c);
                render();
            });
            qty.append(minus, num, plus, remove);
            mid.appendChild(qty);
            var price = document.createElement('div');
            price.className = 'co-item-price';
            price.textContent = rupiah((parseFloat(item.harga) || 0) * (parseInt(item.jumlah, 10) || 0));
            row.append(thumb, mid, price);
            list.appendChild(row);
        });
        var subtotal = cart.reduce(function (s, it) { return s + (parseFloat(it.harga) || 0) * (parseInt(it.jumlah, 10) || 0); }, 0);
        var totalQty = cart.reduce(function (s, it) { return s + (parseInt(it.jumlah, 10) || 0); }, 0);
        var isDelivery = currentTake() === 'delivery';
        var ongkir = !cart.length ? 0 : (isDelivery ? 10000 : 0);
        var ppn = Math.round((subtotal + ongkir) * 0.11);
        document.getElementById('co-total-label').textContent = 'Total Pemesanan (' + totalQty + ' Menu)';
        document.getElementById('co-subtotal').textContent = rupiah(subtotal);
        document.getElementById('co-ongkir').textContent = rupiah(ongkir);
        document.getElementById('co-ppn').textContent = rupiah(ppn);
        document.getElementById('co-total').textContent = rupiah(subtotal + ongkir + ppn);
    }
    function step(index, delta) {
        var cart = window.hamtaGetCart();
        if (!cart[index]) return;
        cart[index].jumlah = (parseInt(cart[index].jumlah, 10) || 0) + delta;
        if (cart[index].jumlah < 1) cart.splice(index, 1);
        window.hamtaSaveCart(cart);
        render();
    }
    function currentTake() {
        var sel = document.querySelector('[data-take].is-selected');
        return sel ? sel.dataset.take : 'pickup';
    }
    document.querySelectorAll('[data-pay]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('[data-pay]').forEach(function (b) {
                var on = b === btn;
                b.classList.toggle('is-selected', on);
                b.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
            try { localStorage.setItem('hamta_pay', btn.dataset.pay); } catch (e) {}
        });
    });
    document.querySelectorAll('[data-take]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('[data-take]').forEach(function (b) {
                var on = b === btn;
                b.classList.toggle('is-selected', on);
                b.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
            try { localStorage.setItem('hamta_take', btn.dataset.take); } catch (e) {}
            render();
        });
    });
    try {
        var savedPay = localStorage.getItem('hamta_pay');
        var savedTake = localStorage.getItem('hamta_take');
        if (savedPay) {
            var payBtn = document.querySelector('[data-pay="' + savedPay + '"]');
            if (payBtn) payBtn.click();
        }
        if (savedTake) {
            var takeBtn = document.querySelector('[data-take="' + savedTake + '"]');
            if (takeBtn) takeBtn.click();
        }
    } catch (e) {}
    render();
})();
</script>
@include('customer.partials.footer')
</body>
</html>
