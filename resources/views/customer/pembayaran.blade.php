<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hamta — Konfirmasi Pesanan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .by-head { display:flex; align-items:center; gap:16px; margin:6px 0 22px; }
        .by-back { display:grid; place-items:center; color:var(--ink); text-decoration:none; transition:transform .2s; }
        .by-back:hover { transform:translateX(-3px); }
        .by-head h1 { margin:0; font-size:31px; line-height:1.1; font-weight:700; letter-spacing:-1.2px; }
        .by-card { margin-bottom:16px; padding:18px 20px; border:2px solid var(--green); border-radius:13px; background:#fff; }
        .by-address { display:flex; align-items:flex-start; justify-content:space-between; gap:14px; }
        .by-address strong { display:block; margin-bottom:3px; font-size:17px; }
        .by-address span { display:block; font-size:13px; font-weight:600; }
        .by-address p { margin:5px 0 0; max-width:430px; font-size:13px; line-height:1.3; color:#3c3c3c; }
        .by-ubah { flex:0 0 auto; padding-top:2px; color:var(--green); font-size:14px; font-weight:700; text-decoration:none; }
        .by-ubah:hover { color:var(--lime); }
        .by-item { display:flex; align-items:center; gap:16px; }
        .by-item + .by-item { margin-top:18px; }
        .by-thumb { position:relative; flex:0 0 88px; width:88px; height:88px; overflow:hidden; border-radius:9px; background:var(--lime); }
        .by-thumb:before { content:''; position:absolute; inset:14%; border-radius:50%; background:radial-gradient(circle at 35% 30%,#ffe49c 0 16%,transparent 17%),radial-gradient(circle at 62% 58%,#eb4d35 0 15%,transparent 16%),radial-gradient(circle at 42% 70%,#7dce59 0 18%,transparent 19%),#f2c17b; box-shadow:0 5px 0 #d49a5b; }
        .by-item-info { flex:1 1 auto; min-width:0; }
        .by-item-name { display:block; font-size:16px; font-weight:700; letter-spacing:-.3px; }
        .by-item-price { display:block; margin-top:4px; font-size:14px; font-weight:600; }
        .by-qty { flex:0 0 auto; display:flex; align-items:center; gap:14px; padding:6px 13px; border-radius:999px; background:#e9e6df; }
        .by-qty-ctl { color:#6f6a5f; font-size:17px; line-height:1; }
        .by-qty-num { min-width:10px; font-size:14px; font-weight:700; text-align:center; }
        .by-divider { height:0; margin:20px 0 16px; border-top:1px solid #ddd7c8; }
        .by-sub { margin:0 0 14px; font-size:17px; font-weight:700; letter-spacing:-.3px; }
        .by-mini-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; }
        .by-mini { padding:9px 9px 11px; border:1.5px solid #426a57; border-radius:11px; background:#fff; }
        .by-mini .by-thumb { flex:0 0 auto; width:100%; height:auto; aspect-ratio:1; border-radius:7px; }
        .by-mini-name { display:block; margin-top:8px; font-size:11px; font-weight:700; line-height:1.2; letter-spacing:.2px; }
        .by-mini-foot { display:flex; align-items:center; justify-content:space-between; gap:4px; margin-top:5px; }
        .by-mini-price { font-size:11px; font-weight:600; white-space:nowrap; }
        .by-plus { flex:0 0 auto; display:grid; place-items:center; width:24px; height:24px; border-radius:50%; background:var(--green); color:#fff; font-size:17px; font-weight:700; line-height:1; }
        .by-pay { display:flex; align-items:center; gap:12px; margin-bottom:16px; padding:16px 20px; border:2px solid var(--green); border-radius:13px; background:#fff; }
        .by-pay-label { flex:1 1 auto; font-size:16px; font-weight:700; }
        .by-sum-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:4px 0; font-size:14px; font-weight:600; }
        .by-sum .by-divider { margin:12px 0; }
        .by-sum-total { display:flex; align-items:center; justify-content:space-between; gap:12px; font-size:19px; font-weight:700; }
        .by-sum-note { margin:3px 0 0; text-align:right; font-size:11px; font-weight:600; color:#5b5b5b; }
        .by-cta-wrap { margin:20px 0 8px; text-align:right; }
        .by-cta { display:inline-block; padding:13px 26px; border-radius:999px; background:var(--lime); color:var(--green); font-size:15px; font-weight:800; text-decoration:none; transition:transform .2s,background .2s; }
        .by-cta:hover { background:#57a94f; transform:translateY(-2px); }
        @media (max-width:700px) {
            .by-head { gap:10px; margin-bottom:16px; }
            .by-head h1 { font-size:22px; letter-spacing:-.8px; }
            .by-back svg { width:24px; height:24px; }
            .by-card { margin-bottom:12px; padding:13px; border-radius:11px; }
            .by-address strong { font-size:14px; }
            .by-address span, .by-address p { font-size:11px; }
            .by-ubah { font-size:12px; }
            .by-item { gap:10px; }
            .by-thumb { flex-basis:62px; width:62px; height:62px; }
            .by-item-name { font-size:12px; }
            .by-item-price { font-size:11px; }
            .by-qty { gap:9px; padding:5px 9px; }
            .by-qty-ctl { font-size:14px; }
            .by-qty-num { font-size:12px; }
            .by-divider { margin:15px 0 12px; }
            .by-sub { margin-bottom:10px; font-size:14px; }
            .by-mini-grid { grid-template-columns:repeat(2,1fr); gap:9px; }
            .by-mini { padding:7px 7px 9px; }
            .by-mini-name, .by-mini-price { font-size:10px; }
            .by-plus { width:20px; height:20px; font-size:14px; }
            .by-pay { margin-bottom:12px; padding:12px 13px; gap:9px; border-radius:11px; }
            .by-pay-label { font-size:13px; }
            .by-sum-row { font-size:12px; }
            .by-sum-total { font-size:15px; }
            .by-cta { padding:11px 20px; font-size:13px; }
        }
    </style>
</head>
<body>
<div class="page">
    @include('customer.partials.header')

    <div class="by-head">
        <a class="by-back" href="{{ url('/') }}" aria-label="Kembali">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        </a>
        <h1>Konfirmasi Pesanan</h1>
    </div>

    <section class="by-card by-address">
        <div>
            <strong>Brendata Najwa Firki Hid...</strong>
            <span>(+62) 823-3112-9819</span>
            <p>Jl. Lidah Kulon Gg. 1, Lidah Kulon, Kec. Lakarsantri, Surabaya</p>
        </div>
        <a class="by-ubah" href="#">Ubah</a>
    </section>

    <section class="by-card">
        <div id="by-cart-list"></div>
        <p id="by-cart-empty" style="margin:6px 0 0;font-size:13px;font-weight:600;color:#5b5b5b">Keranjang masih kosong. Yuk tambah menu dulu!</p>

        <div class="by-divider"></div>

        <h2 class="by-sub">Menu lain yang sering dipesan</h2>
        <div class="by-mini-grid">
            @foreach (['CHICKEN SANDWICH', 'CAESAR WRAP', 'JUS PIR', 'JUS ALPUKAT'] as $menu)
                <article class="by-mini">
                    <div class="by-thumb" aria-hidden="true"></div>
                    <span class="by-mini-name">{{ $menu }}</span>
                    <div class="by-mini-foot">
                        <span class="by-mini-price">Rp. 25.000</span>
                        <span class="by-plus" aria-hidden="true">+</span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <div class="by-pay">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="3"/><path d="M2 10h20"/><path d="M6 15h3"/></svg>
        <span class="by-pay-label">Pilih Pembayaran</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
    </div>

    <section class="by-card by-sum">
        <div class="by-sum-row"><span id="by-total-label">Total Pemesanan (0 Menu)</span><span id="by-subtotal">Rp. 0</span></div>
        <div class="by-sum-row"><span>Biaya Pengiriman</span><span id="by-ongkir">Rp. 10.000</span></div>
        <div class="by-sum-row"><span>Biaya Layanan</span><span id="by-layanan">Rp. 2.000</span></div>
        <div class="by-sum-row"><span>Biaya Pengemasan</span><span id="by-kemas">Rp. 1.000</span></div>
        <div class="by-divider"></div>
        <div class="by-sum-total"><span>Total Pembayaran</span><span id="by-total">Rp. 13.000</span></div>
        <p class="by-sum-note">Sudah termasuk pajak</p>
    </section>

    <div class="by-cta-wrap">
        <a class="by-cta" href="#" id="by-confirm">Konfirmasi Pemesanan</a>
    </div>
<script>
(function () {
    var list = document.getElementById('by-cart-list');
    var empty = document.getElementById('by-cart-empty');
    if (!list) return;
    var rupiah = function (n) { return 'Rp. ' + new Intl.NumberFormat('id-ID').format(Math.round(n)); };
    var escapeHtml = function (s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    };
    function render() {
        var cart = window.hamtaGetCart ? window.hamtaGetCart() : [];
        list.textContent = '';
        empty.style.display = cart.length ? 'none' : '';
        cart.forEach(function (item, index) {
            var row = document.createElement('div');
            row.className = 'by-item';
            var thumb = document.createElement('div');
            thumb.className = 'by-thumb';
            thumb.setAttribute('aria-hidden', 'true');
            if (item.foto) {
                var img = document.createElement('img');
                img.src = item.foto;
                img.alt = item.nama || 'Menu';
                img.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover';
                thumb.appendChild(img);
            }
            var info = document.createElement('div');
            info.className = 'by-item-info';
            var name = document.createElement('span');
            name.className = 'by-item-name';
            name.textContent = (item.nama || 'Menu').toUpperCase();
            var price = document.createElement('span');
            price.className = 'by-item-price';
            price.textContent = rupiah(item.harga) + ' x ' + item.jumlah;
            info.append(name, price);
            if (item.komponen && item.komponen.length) {
                var detail = document.createElement('span');
                detail.className = 'by-item-price';
                detail.style.cssText = 'display:block;margin-top:2px;font-size:11px;font-weight:500;color:#6f6a5f';
                detail.textContent = item.komponen.map(function (k) { return k.nama || k; }).join(', ');
                info.appendChild(detail);
            }
            var qty = document.createElement('div');
            qty.className = 'by-qty';
            qty.setAttribute('aria-label', 'Jumlah ' + item.jumlah);
            var minus = document.createElement('button');
            minus.type = 'button';
            minus.className = 'by-qty-ctl';
            minus.textContent = '-';
            minus.addEventListener('click', function () { step(index, -1); });
            var num = document.createElement('span');
            num.className = 'by-qty-num';
            num.textContent = item.jumlah;
            var plus = document.createElement('button');
            plus.type = 'button';
            plus.className = 'by-qty-ctl';
            plus.textContent = '+';
            plus.addEventListener('click', function () { step(index, 1); });
            var remove = document.createElement('button');
            remove.type = 'button';
            remove.textContent = 'Hapus';
            remove.style.cssText = 'border:0;background:transparent;color:#c0392b;font:inherit;font-size:11px;font-weight:700;cursor:pointer;padding:0';
            remove.addEventListener('click', function () {
                var c = window.hamtaGetCart();
                c.splice(index, 1);
                window.hamtaSaveCart(c);
                render();
            });
            qty.append(minus, num, plus, remove);
            row.append(thumb, info, qty);
            list.appendChild(row);
        });
        var subtotal = cart.reduce(function (s, it) { return s + (parseFloat(it.harga) || 0) * (parseInt(it.jumlah, 10) || 0); }, 0);
        var totalQty = cart.reduce(function (s, it) { return s + (parseInt(it.jumlah, 10) || 0); }, 0);
        var ongkir = cart.length ? 10000 : 0;
        var layanan = cart.length ? 2000 : 0;
        var kemas = cart.length ? 1000 : 0;
        document.getElementById('by-total-label').textContent = 'Total Pemesanan (' + totalQty + ' Menu)';
        document.getElementById('by-subtotal').textContent = rupiah(subtotal);
        document.getElementById('by-ongkir').textContent = rupiah(ongkir);
        document.getElementById('by-layanan').textContent = rupiah(layanan);
        document.getElementById('by-kemas').textContent = rupiah(kemas);
        document.getElementById('by-total').textContent = rupiah(subtotal + ongkir + layanan + kemas);
    }
    function step(index, delta) {
        var cart = window.hamtaGetCart();
        if (!cart[index]) return;
        cart[index].jumlah = (parseInt(cart[index].jumlah, 10) || 0) + delta;
        if (cart[index].jumlah < 1) cart.splice(index, 1);
        window.hamtaSaveCart(cart);
        render();
    }
    document.getElementById('by-confirm').addEventListener('click', function (e) {
        e.preventDefault();
        var cart = window.hamtaGetCart();
        if (!cart.length) {
            if (window.hamtaToast) window.hamtaToast('Keranjang masih kosong.');
            return;
        }
        window.location.href = "{{ route('customer.checkout') }}";
    });
    render();
})();
</script>
</div>
@include('customer.partials.footer')
</body>
</html>
