<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .co-addr-box { background:#fff; border:2px solid var(--green); border-radius:12px; padding:16px 18px; margin-top:14px; position:relative; }
        .co-addr-header { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:8px; flex-wrap:wrap; }
        .co-addr-title { display:flex; align-items:center; gap:8px; font-size:15px; font-weight:700; color:var(--ink); }
        .co-addr-phone { font-size:13px; font-weight:600; opacity:.75; }
        .co-addr-badge { font-size:11px; font-weight:700; padding:3px 9px; border-radius:999px; background:#eef5ea; color:var(--green); border:1px solid rgba(101,189,94,.5); }
        .co-addr-text { margin:6px 0 0; font-size:13.5px; line-height:1.45; color:#2c3e35; }
        .co-addr-note { margin:5px 0 0; font-size:12px; line-height:1.4; color:#5c7a68; display:flex; align-items:center; gap:4px; font-weight:500; }
        .co-addr-change { display:inline-flex; align-items:center; gap:5px; padding:6px 14px; border:1.5px solid var(--green); border-radius:8px; background:transparent; color:var(--green); font-family:inherit; font-size:12px; font-weight:700; text-decoration:none; transition:all .15s; }
        .co-addr-change:hover { background:var(--green); color:#fff; }

        .qris-card { background:#fff; border:2px solid var(--green); border-radius:14px; padding:20px; margin-top:14px; }
        .qris-header { display:flex; align-items:center; justify-content:space-between; border-bottom:2px solid #eef5ea; padding-bottom:12px; margin-bottom:14px; }
        .qris-badge { font-weight:900; font-size:22px; letter-spacing:1px; color:#c0392b; font-family:sans-serif; }
        .qris-sub { display:block; font-size:10px; color:#7f8c8d; font-weight:600; }
        .gpn-badge { font-weight:800; font-size:12px; color:#2980b9; padding:2px 8px; border:1.5px solid #2980b9; border-radius:6px; }
        .qris-merchant { text-align:center; margin-bottom:14px; }
        .qris-merchant-name { margin:0; font-size:16px; font-weight:800; letter-spacing:.5px; color:var(--ink); }
        .qris-merchant-sub { margin:2px 0 0; font-size:12px; color:#6f6a5f; }
        .qris-nmid { margin:4px 0 0; font-size:11px; font-weight:700; color:var(--green); letter-spacing:.5px; }
        .qris-code-wrap { display:flex; flex-direction:column; align-items:center; justify-content:center; margin:12px 0; padding:16px; background:#fbf8f0; border-radius:12px; border:1px dashed rgba(11,65,43,.25); }
        .qris-scan-tag { margin-top:10px; font-size:11px; font-weight:800; letter-spacing:1.5px; color:var(--green); background:#eef5ea; padding:4px 14px; border-radius:999px; }
        .qris-total-box { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:#eef5ea; border:1.5px solid rgba(101,189,94,.5); border-radius:10px; margin:14px 0; }
        .qris-total-label { font-size:13px; font-weight:700; color:var(--green); }
        .qris-total-val { font-size:18px; font-weight:900; color:var(--green); }
        .qris-steps { margin:12px 0; padding-left:20px; font-size:12px; line-height:1.6; color:#4a5a52; }
        .qris-steps li { margin-bottom:4px; }
        .qris-upload-box { margin-top:14px; padding-top:14px; border-top:1px solid rgba(11,65,43,.15); }
        .qris-upload-label { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; padding:16px; border:2px dashed #9bc894; border-radius:11px; background:#fbfbf9; cursor:pointer; text-align:center; transition:all .15s; color:var(--ink); }
        .qris-upload-label:hover { border-color:var(--green); background:#eef5ea; }
        .qris-preview { display:flex; align-items:center; gap:12px; padding:10px 14px; background:#fbf8f0; border:1.5px solid var(--green); border-radius:10px; margin-top:10px; }
        .qris-preview img { width:52px; height:52px; object-fit:cover; border-radius:7px; border:1px solid rgba(11,65,43,.2); }
        .tunai-card { background:#fff; border:2px solid #d8e2d2; border-radius:12px; padding:16px; margin-top:14px; }
        .co-submit-btn { display:flex; align-items:center; justify-content:center; gap:8px; width:100%; margin-top:16px; padding:14px 20px; border-radius:12px; border:0; background:var(--green); color:#fff; font-family:inherit; font-size:15px; font-weight:800; cursor:pointer; transition:all .15s; box-shadow:0 4px 12px rgba(11,65,43,.25); }
        .co-submit-btn:hover { background:#16613f; transform:translateY(-1px); }
        .co-submit-btn:disabled { opacity:.6; cursor:not-allowed; transform:none; }
        .co-success-card { background:#fff; border:2px solid var(--green); border-radius:14px; padding:24px; margin-bottom:20px; text-align:center; box-shadow:0 8px 24px rgba(11,65,43,.08); }
        .co-success-icon { width:64px; height:64px; border-radius:50%; background:#eef5ea; color:var(--green); display:grid; place-items:center; margin:0 auto 14px; }
        .co-success-title { margin:0; font-size:24px; font-weight:800; color:var(--ink); letter-spacing:-.5px; }
        .co-success-sub { margin:6px 0 0; font-size:13.5px; color:#5c7a68; }
        .co-order-meta { display:grid; grid-template-columns:1fr 1fr; gap:12px; text-align:left; background:#fbf8f0; border-radius:11px; padding:14px 16px; margin:18px 0; border:1px solid rgba(11,65,43,.12); }
        .co-order-meta-item { display:flex; flex-direction:column; gap:2px; }
        .co-order-meta-label { font-size:11px; color:#7a8a80; font-weight:600; text-transform:uppercase; letter-spacing:.5px; }
        .co-order-meta-val { font-size:13.5px; font-weight:700; color:var(--ink); word-break:break-word; }
        .co-badge-status { display:inline-flex; align-items:center; gap:4px; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:700; width:max-content; }
        .co-badge-pending { background:#fff3cd; color:#856404; border:1px solid #ffeeba; }
        .co-badge-verified { background:#eef5ea; color:var(--green); border:1px solid rgba(101,189,94,.5); }
        .co-wa-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:13px 18px; border-radius:11px; background:#25D366; color:#fff; font-size:14px; font-weight:700; text-decoration:none; transition:all .15s; margin-top:12px; }
        .co-wa-btn:hover { background:#1ebc59; }
        .co-new-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; width:100%; padding:11px 18px; border-radius:11px; background:transparent; border:1.5px solid var(--green); color:var(--green); font-size:13px; font-weight:700; text-decoration:none; transition:all .15s; margin-top:8px; cursor:pointer; }
        .co-new-btn:hover { background:#eef5ea; }

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

    <!-- ─── TAMPILAN 1: FORM CHECKOUT & PEMBAYARAN ──────────────────── -->
    <div id="co-form-view">
        <!-- Section Alamat -->
        <section class="co-card" id="co-address-section">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px">
                <div>
                    <h2>Alamat Pengiriman</h2>
                    <p class="co-sub" id="co-address-sub">Alamat tujuan untuk pengantaran pesanan</p>
                </div>
                <a class="co-addr-change" id="co-addr-change-btn" href="{{ route('customer.alamat') }}" style="display:none">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                    <span>Ganti Alamat</span>
                </a>
            </div>

            <!-- Box Tampilan Alamat Terpilih -->
            <div class="co-addr-box" id="co-address-selected" style="display:none">
                <div class="co-addr-header">
                    <div class="co-addr-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span id="co-addr-name">Nama Penerima</span>
                        <span class="co-addr-phone" id="co-addr-phone">(08xxx)</span>
                    </div>
                    <span class="co-addr-badge">✓ Terpilih</span>
                </div>
                <p class="co-addr-text" id="co-addr-detail">-</p>
                <p class="co-addr-note" id="co-addr-patokan" style="display:none">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    <span id="co-addr-patokan-text"></span>
                </p>
            </div>

            <!-- Tampilan Belum Ada Alamat -->
            <div id="co-address-empty" style="margin-top:12px">
                <a class="co-opt" href="{{ route('customer.alamat') }}" style="text-decoration:none;color:inherit">
                    <span class="co-opt-radio" aria-hidden="true"></span>
                    <span>Atur Alamat Pengiriman<small>Pilih atau tambah alamat pengiriman</small></span>
                </a>
            </div>
        </section>

        <!-- Section Keranjang -->
        <section class="co-card">
            <h2>Rincian Pesanan</h2>
            <div id="co-cart-list"></div>
            <p id="co-cart-empty" class="co-item-note">Keranjang masih kosong. Yuk tambah menu dulu!</p>
        </section>

        <!-- Section Metode Pengiriman -->
        <section class="co-card">
            <h2>Metode Pengiriman</h2>
            <div class="co-opt-group" role="radiogroup" aria-label="Metode pengiriman" style="margin-top:14px">
                <button type="button" class="co-opt is-selected" data-take="pickup" aria-pressed="true">
                    <span class="co-opt-radio" aria-hidden="true"></span>
                    <span>Pickup / Ambil di Tempat<small>Ambil sendiri pesananmu di outlet</small></span>
                </button>
                <button type="button" class="co-opt" data-take="delivery" aria-pressed="false">
                    <span class="co-opt-radio" aria-hidden="true"></span>
                    <span>Delivery / Diantar<small>Kurir mengantar langsung ke alamat tujuan</small></span>
                </button>
            </div>
        </section>

        <!-- Section Metode Pembayaran & QRIS Statis -->
        <section class="co-card" id="co-payment-section">
            <h2>Metode Pembayaran</h2>

            <!-- Opsi Pembayaran untuk Pickup (Muncul 2 Opsi: QRIS & Tunai) -->
            <div class="co-opt-group" id="co-pay-pickup-group" role="radiogroup" aria-label="Metode pembayaran" style="margin-top:14px">
                <button type="button" class="co-opt is-selected" data-pay="qris" aria-pressed="true">
                    <span class="co-opt-radio" aria-hidden="true"></span>
                    <span>QRIS<small>Bayar instan via GoPay, OVO, Dana, BCA &amp; m-Banking</small></span>
                </button>
                <button type="button" class="co-opt" data-pay="tunai" aria-pressed="false">
                    <span class="co-opt-radio" aria-hidden="true"></span>
                    <span>Bayar di Tempat (Tunai)<small>Bayar langsung saat mengambil pesanan di outlet</small></span>
                </button>
            </div>

            <!-- Tampilan Pembayaran untuk Delivery (Hanya QRIS, Langsung Terpilih, Tanpa Opsi) -->
            <div id="co-pay-delivery-badge" style="display:none;margin-top:14px;padding:14px 16px;background:#fbf8f0;border:2px solid var(--green);border-radius:12px;align-items:center;justify-content:space-between;gap:12px">
                <div style="display:flex;align-items:center;gap:12px">
                    <span class="qris-badge" style="font-size:16px">QRIS</span>
                    <div>
                        <strong style="display:block;font-size:13.5px;color:var(--ink)">QRIS (Pembayaran Instan)</strong>
                        <small style="display:block;font-size:11.5px;color:#4a5a52;margin-top:2px">Pesanan delivery otomatis menggunakan pembayaran QRIS instan</small>
                    </div>
                </div>
                <span style="font-size:11.5px;font-weight:700;color:var(--green);background:#eef5ea;padding:3px 10px;border-radius:20px;white-space:nowrap">✓ Terpilih</span>
            </div>

            <!-- Banner Fallback jika Midtrans gagal/down -->
            <div id="co-fallback-banner" style="display:none;margin-top:14px;padding:12px 14px;background:#fffbeb;border:1.5px solid #f59e0b;border-radius:12px;color:#92400e;font-size:12.5px;line-height:1.4">
                <strong>Pemberitahuan:</strong>
                <span id="co-fallback-text">Layanan QRIS otomatis sedang tidak dapat dijangkau. Silakan lakukan pembayaran via QRIS statis di bawah ini dan unggah bukti transfer.</span>
            </div>

            <!-- Card QRIS Statis Hamta Healthy Food -->
            <div class="qris-card" id="qris-box" style="display:none">
                <div class="qris-header">
                    <div class="qris-logo-wrap">
                        <span class="qris-badge">QRIS</span>
                        <span class="qris-sub">QR Code Standar Pembayaran Nasional</span>
                    </div>
                    <span class="gpn-badge">GPN</span>
                </div>

                <div class="qris-merchant">
                    <h3 class="qris-merchant-name">HAMTA HEALTHY FOOD</h3>
                    <p class="qris-merchant-sub">Hamta Salad &amp; Healthy Eatery Surabaya</p>
                    <p class="qris-nmid">NMID: ID1024398129038</p>
                </div>

                <div class="qris-code-wrap">
                    <!-- High Quality QRIS SVG -->
                    <svg width="220" height="220" viewBox="0 0 260 260" fill="none" xmlns="http://www.w3.org/2000/svg" style="border-radius:12px;background:#fff;box-shadow:0 4px 16px rgba(0,0,0,.08);padding:10px">
                        <rect x="20" y="20" width="56" height="56" rx="8" fill="#0B412B"/>
                        <rect x="28" y="28" width="40" height="40" rx="4" fill="#fff"/>
                        <rect x="36" y="36" width="24" height="24" rx="3" fill="#0B412B"/>

                        <rect x="184" y="20" width="56" height="56" rx="8" fill="#0B412B"/>
                        <rect x="192" y="28" width="40" height="40" rx="4" fill="#fff"/>
                        <rect x="200" y="36" width="24" height="24" rx="3" fill="#0B412B"/>

                        <rect x="20" y="184" width="56" height="56" rx="8" fill="#0B412B"/>
                        <rect x="28" y="192" width="40" height="40" rx="4" fill="#fff"/>
                        <rect x="36" y="200" width="24" height="24" rx="3" fill="#0B412B"/>

                        <rect x="84" y="44" width="8" height="8" fill="#0B412B"/>
                        <rect x="100" y="44" width="8" height="8" fill="#0B412B"/>
                        <rect x="116" y="44" width="8" height="8" fill="#0B412B"/>
                        <rect x="132" y="44" width="8" height="8" fill="#0B412B"/>
                        <rect x="148" y="44" width="8" height="8" fill="#0B412B"/>
                        <rect x="164" y="44" width="8" height="8" fill="#0B412B"/>

                        <rect x="44" y="84" width="8" height="8" fill="#0B412B"/>
                        <rect x="44" y="100" width="8" height="8" fill="#0B412B"/>
                        <rect x="44" y="116" width="8" height="8" fill="#0B412B"/>
                        <rect x="44" y="132" width="8" height="8" fill="#0B412B"/>
                        <rect x="44" y="148" width="8" height="8" fill="#0B412B"/>
                        <rect x="44" y="164" width="8" height="8" fill="#0B412B"/>

                        <rect x="88" y="24" width="12" height="12" fill="#0B412B"/>
                        <rect x="108" y="24" width="12" height="12" fill="#0B412B"/>
                        <rect x="136" y="24" width="12" height="12" fill="#0B412B"/>
                        <rect x="156" y="24" width="12" height="12" fill="#0B412B"/>

                        <rect x="84" y="64" width="16" height="8" fill="#0B412B"/>
                        <rect x="108" y="64" width="8" height="16" fill="#0B412B"/>
                        <rect x="124" y="64" width="16" height="8" fill="#0B412B"/>
                        <rect x="148" y="64" width="16" height="16" fill="#0B412B"/>

                        <rect x="20" y="88" width="12" height="12" fill="#0B412B"/>
                        <rect x="64" y="88" width="12" height="12" fill="#0B412B"/>
                        <rect x="88" y="88" width="12" height="12" fill="#0B412B"/>
                        <rect x="168" y="88" width="12" height="12" fill="#0B412B"/>
                        <rect x="188" y="88" width="12" height="12" fill="#0B412B"/>
                        <rect x="228" y="88" width="12" height="12" fill="#0B412B"/>

                        <rect x="24" y="108" width="12" height="12" fill="#0B412B"/>
                        <rect x="60" y="108" width="16" height="8" fill="#0B412B"/>
                        <rect x="84" y="108" width="12" height="12" fill="#0B412B"/>
                        <rect x="164" y="108" width="12" height="12" fill="#0B412B"/>
                        <rect x="196" y="108" width="12" height="12" fill="#0B412B"/>
                        <rect x="216" y="108" width="24" height="12" fill="#0B412B"/>

                        <rect x="20" y="128" width="12" height="12" fill="#0B412B"/>
                        <rect x="60" y="128" width="16" height="8" fill="#0B412B"/>
                        <rect x="164" y="128" width="16" height="16" fill="#0B412B"/>
                        <rect x="188" y="128" width="12" height="12" fill="#0B412B"/>
                        <rect x="216" y="128" width="16" height="12" fill="#0B412B"/>

                        <rect x="24" y="148" width="12" height="12" fill="#0B412B"/>
                        <rect x="64" y="148" width="12" height="12" fill="#0B412B"/>
                        <rect x="88" y="148" width="12" height="12" fill="#0B412B"/>
                        <rect x="164" y="148" width="12" height="12" fill="#0B412B"/>
                        <rect x="196" y="148" width="12" height="12" fill="#0B412B"/>
                        <rect x="228" y="148" width="12" height="12" fill="#0B412B"/>

                        <rect x="24" y="168" width="12" height="8" fill="#0B412B"/>
                        <rect x="60" y="168" width="16" height="12" fill="#0B412B"/>
                        <rect x="84" y="168" width="20" height="12" fill="#0B412B"/>
                        <rect x="120" y="168" width="16" height="12" fill="#0B412B"/>
                        <rect x="144" y="168" width="16" height="12" fill="#0B412B"/>
                        <rect x="176" y="168" width="24" height="12" fill="#0B412B"/>
                        <rect x="212" y="168" width="16" height="12" fill="#0B412B"/>

                        <rect x="88" y="188" width="12" height="12" fill="#0B412B"/>
                        <rect x="108" y="188" width="12" height="12" fill="#0B412B"/>
                        <rect x="128" y="188" width="12" height="12" fill="#0B412B"/>
                        <rect x="156" y="188" width="12" height="12" fill="#0B412B"/>
                        <rect x="180" y="188" width="28" height="28" rx="4" fill="#0B412B"/>
                        <rect x="188" y="196" width="12" height="12" rx="2" fill="#fff"/>
                        <rect x="192" y="200" width="4" height="4" fill="#0B412B"/>
                        <rect x="220" y="188" width="16" height="12" fill="#0B412B"/>

                        <rect x="88" y="208" width="12" height="16" fill="#0B412B"/>
                        <rect x="108" y="208" width="20" height="8" fill="#0B412B"/>
                        <rect x="136" y="208" width="12" height="16" fill="#0B412B"/>
                        <rect x="220" y="208" width="20" height="12" fill="#0B412B"/>

                        <rect x="88" y="228" width="24" height="12" fill="#0B412B"/>
                        <rect x="120" y="228" width="16" height="12" fill="#0B412B"/>
                        <rect x="144" y="228" width="28" height="12" fill="#0B412B"/>
                        <rect x="180" y="228" width="12" height="12" fill="#0B412B"/>
                        <rect x="204" y="228" width="20" height="12" fill="#0B412B"/>

                        <circle cx="130" cy="130" r="26" fill="#fff" stroke="#65BD5E" stroke-width="4"/>
                        <circle cx="130" cy="130" r="20" fill="#0B412B"/>
                        <path d="M124 135c0-6 6-11 12-11 0 6-6 11-12 11Z" fill="#65BD5E"/>
                        <path d="M136 135c0-6-6-11-12-11 0 6 6 11 12 11Z" fill="#A8E6A1"/>
                        <path d="M122 136q8-1 16-8" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    <div class="qris-scan-tag">SCAN &amp; BAYAR DI SINI</div>
                </div>

                <div class="qris-total-box">
                    <span class="qris-total-label">Total yang Harus Dibayar:</span>
                    <span class="qris-total-val" id="qris-nominal-display">Rp. 0</span>
                </div>

                <ol class="qris-steps">
                    <li>Buka aplikasi <strong>BCA Mobile, Mandiri Livin, BRImo, BNI, GoPay, OVO, ShopeePay, DANA</strong>, atau perbankan lainnya.</li>
                    <li>Pilih menu <strong>Scan QRIS</strong> dan arahkan kamera ke barcode di atas.</li>
                    <li>Pastikan nama merchant tertera <strong>HAMTA HEALTHY FOOD</strong> dan masukkan nominal persis <strong id="qris-steps-nominal">Rp. 0</strong>.</li>
                    <li>Selesaikan pembayaran, simpan screenshot bukti transfer, lalu unggah bukti di bawah <strong style="color:#c0392b">(WAJIB)</strong>.</li>
                </ol>

                <!-- Area Upload Bukti Transfer -->
                <div class="qris-upload-box">
                    <label for="co-bukti-input" class="qris-upload-label" id="co-bukti-upload-area">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--green)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                        <span style="font-weight:700;font-size:13px;color:var(--green)">+ Unggah Bukti Transfer / Screenshot QRIS <span style="color:#c0392b;font-weight:800;font-size:11px">*(Wajib)</span></span>
                        <small style="color:#7a8a80">Format JPG, PNG, WEBP (Maksimal 5MB)</small>
                    </label>
                    <input type="file" id="co-bukti-input" accept="image/png,image/jpeg,image/webp" style="display:none">
                    <div id="co-bukti-preview" class="qris-preview" style="display:none">
                        <img id="co-bukti-img" src="" alt="Bukti Transfer">
                        <div style="flex:1 1 auto;min-width:0">
                            <span id="co-bukti-filename" style="display:block;font-weight:700;font-size:12px;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"></span>
                            <span style="font-size:11px;color:var(--green);font-weight:600">✓ Bukti pembayaran siap dikirim</span>
                        </div>
                        <button type="button" id="co-bukti-remove" style="border:0;background:none;color:#c0392b;font-size:12px;font-weight:700;cursor:pointer">Hapus</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section Rincian Pembayaran & Submit -->
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

            <p class="co-sub" id="co-order-msg" role="status" style="display:none;margin-top:10px"></p>

            <button type="button" class="co-submit-btn" id="co-submit">
                <span id="co-submit-label">Bayar via QRIS &amp; Buat Pesanan</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </button>
        </section>
    </div>

    <!-- ─── TAMPILAN 2: HASIL PESANAN YANG SUDAH DIBAYAR / DIPESAN ───── -->
    <div id="co-success-view" style="display:none">
        <!-- Hero Sukses -->
        <section class="co-success-card">
            <div class="co-success-icon">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <h1 class="co-success-title flex items-center justify-center gap-2"><span>Hore! Pesanan Berhasil Dibuat</span> <x-lucide-party-popper class="w-7 h-7 text-emerald-700" /></h1>
            <p class="co-success-sub">Terima kasih telah memesan menu sehat di Hamta Healthy Food Surabaya.</p>
        </section>

        <!-- Status Pesanan (Step Progress Bar) -->
        <section class="co-card">
            <h2>Status Pesanan</h2>
            <p class="co-sub" id="success-status-note">Pesanan telah kami terima dan sedang disiapkan di dapur Hamta.</p>
            <ol class="co-steps">
                <li class="co-step is-done">
                    <span class="co-dot">
                        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
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
                    <span class="co-label">Siap Diambil / Diantar</span>
                    <span class="co-bar"><i></i></span>
                </li>
            </ol>
        </section>

        <!-- Informasi Pesanan yang Sudah Dipesan -->
        <section class="co-card">
            <h2>Rincian Pesanan Anda</h2>
            <div class="co-order-meta">
                <div class="co-order-meta-item">
                    <span class="co-order-meta-label">Kode Pesanan</span>
                    <div style="display:flex;align-items:center;gap:8px">
                        <span class="co-order-meta-val" id="success-order-code">—</span>
                        <button type="button" class="co-copy" id="success-copy-btn">SALIN</button>
                    </div>
                </div>
                <div class="co-order-meta-item">
                    <span class="co-order-meta-label">Waktu Pemesanan</span>
                    <span class="co-order-meta-val" id="success-order-time">—</span>
                </div>
                <div class="co-order-meta-item">
                    <span class="co-order-meta-label">Status Pembayaran</span>
                    <span id="success-order-pay" class="co-badge-status co-badge-pending">QRIS — Menunggu Verifikasi</span>
                </div>
                <div class="co-order-meta-item">
                    <span class="co-order-meta-label">Metode Pengambilan</span>
                    <span class="co-order-meta-val" id="success-order-take">Pickup di Tempat</span>
                </div>
            </div>

            <!-- List Item yang Dibeli -->
            <div id="success-items-list" style="margin-top:14px;border-top:1px solid rgba(11,65,43,.15);padding-top:10px"></div>

            <div style="margin-top:14px;padding-top:12px;border-top:1px solid rgba(11,65,43,.15);display:flex;justify-content:space-between;align-items:baseline">
                <span style="font-size:15px;font-weight:700">Total Pembayaran:</span>
                <span id="success-order-total" style="font-size:20px;font-weight:800;color:var(--green)">Rp. 0</span>
            </div>

            <!-- QRIS Midtrans (Jika Ada) -->
            <div id="success-midtrans-qr-box" style="display:none;margin:16px 0;padding:20px 16px;background:#fff;border:2px solid var(--green);border-radius:12px;text-align:center">
                <span style="display:block;font-size:12px;font-weight:800;color:var(--green);letter-spacing:.6px;margin-bottom:12px">SCAN QRIS UNTUK MEMBAYAR</span>
                
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;margin:0 auto">
                    <!-- Countdown Timer -->
                    <div id="success-expiry-badge" style="display:inline-flex;align-items:center;gap:6px;background:#fef2f2;border:1px solid #fecaca;padding:6px 16px;border-radius:20px;font-size:12px;font-weight:700;color:#991b1b">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Sisa Waktu Pembayaran: <span id="success-expiry-countdown">--:--</span></span>
                    </div>

                    <div style="display:inline-block;padding:12px;background:#fbf8f0;border-radius:14px;border:1px dashed rgba(11,65,43,.3);box-shadow:0 2px 8px rgba(0,0,0,.04)">
                        <img id="success-midtrans-qr-img" src="" alt="QRIS Midtrans" style="width:200px;height:200px;display:block;margin:0 auto;object-fit:contain" />
                    </div>
                </div>

                <p style="margin:12px 0 0;font-size:12px;color:#4a5a52">Scan dengan aplikasi e-wallet atau m-Banking Anda. Pembayaran akan terverifikasi secara otomatis.</p>
            </div>

            <!-- Wadah Jika QRIS Expired di Checkout -->
            <div id="success-midtrans-expired-box" style="display:none;margin:16px 0;padding:18px 16px;background:#fff;border:2px dashed #ef4444;border-radius:12px;text-align:center">
                <div style="width:40px;height:40px;border-radius:50%;background:#fee2e2;color:#dc2626;display:inline-flex;align-items:center;justify-content:center;margin-bottom:8px">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <h4 style="margin:0 0 4px;font-size:14px;font-weight:800;color:#991b1b">Waktu Pembayaran Telah Habis</h4>
                <p style="margin:0 0 12px;font-size:12px;color:#6f6a5f">Kode QRIS ini telah kedaluwarsa. Silakan buat pesanan baru.</p>
            </div>

            <!-- Tombol Pantau Status Live -->
            <a href="#" class="co-submit-btn" id="success-tracker-btn" style="text-decoration:none;margin-top:16px;background:var(--green);color:#fff">
                <span>Pantau Status Pesanan Live (Realtime Tracker) ➔</span>
            </a>

            <!-- Tombol WhatsApp & Pesan Baru -->
            <a href="#" target="_blank" class="co-wa-btn" id="success-wa-btn" style="margin-top:10px">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.41 8.86 7.65C8.63 7.9 7.99 8.5 7.99 9.71C7.99 10.93 8.88 12.1 9 12.27C9.13 12.44 10.74 14.92 13.2 15.97C13.78 16.22 14.24 16.38 14.6 16.49C15.19 16.68 15.72 16.65 16.14 16.59C16.61 16.51 17.58 16 17.79 15.41C17.99 14.83 17.99 14.33 17.93 14.22C17.87 14.12 17.7 14.05 17.44 13.93C17.19 13.8 15.93 13.18 15.7 13.1C15.47 13.01 15.3 12.97 15.13 13.22C14.96 13.47 14.47 14.05 14.33 14.22C14.18 14.38 14.04 14.4 13.79 14.27C13.54 14.15 12.72 13.88 11.75 13.02C10.99 12.35 10.48 11.51 10.33 11.26C10.19 11.01 10.31 10.88 10.44 10.75C10.55 10.64 10.69 10.46 10.82 10.31C10.95 10.16 11 10.05 11.08 9.89C11.16 9.72 11.12 9.57 11.06 9.45C11 9.33 10.5 8.12 10.3 7.62C10.1 7.15 9.9 7.21 9.74 7.21C9.6 7.21 9.43 7.21 9.26 7.21L9.53 7.34Z"/></svg>
                <span>Konfirmasi Pesanan ke WhatsApp Kasir</span>
            </a>

            <button type="button" class="co-new-btn" id="success-new-order-btn">
                <span>+ Buat Pesanan Baru</span>
            </button>
        </section>
    </div>
<script>
(function () {
    function getActiveAddress() {
        try {
            var active = JSON.parse(localStorage.getItem('hamta_address') || 'null');
            if (active && (active.alamat || active.nama)) return active;

            // Fallback: jika hamta_address belum di-set, periksa hamta_addresses
            var list = JSON.parse(localStorage.getItem('hamta_addresses') || '[]');
            if (Array.isArray(list) && list.length > 0) {
                var first = list[0];
                try { localStorage.setItem('hamta_address', JSON.stringify(first)); } catch (e) {}
                return first;
            }
        } catch (e) {}
        return null;
    }

    function renderAddress() {
        var addr = getActiveAddress();
        var selectedBox = document.getElementById('co-address-selected');
        var emptyBox = document.getElementById('co-address-empty');
        var changeBtn = document.getElementById('co-addr-change-btn');
        var subText = document.getElementById('co-address-sub');
        var nameEl = document.getElementById('co-addr-name');
        var phoneEl = document.getElementById('co-addr-phone');
        var detailEl = document.getElementById('co-addr-detail');
        var patokanEl = document.getElementById('co-addr-patokan');
        var patokanText = document.getElementById('co-addr-patokan-text');

        if (addr && (addr.alamat || addr.nama)) {
            if (selectedBox) selectedBox.style.display = 'block';
            if (emptyBox) emptyBox.style.display = 'none';
            if (changeBtn) changeBtn.style.display = 'inline-flex';
            if (subText) subText.textContent = 'Alamat tujuan pengantaran pesanan';
            if (nameEl) nameEl.textContent = addr.nama || 'Penerima';
            if (phoneEl) phoneEl.textContent = addr.telepon ? '(' + addr.telepon + ')' : '';
            if (detailEl) detailEl.textContent = addr.alamat || '';
            if (patokanEl && patokanText) {
                if (addr.patokan) {
                    patokanText.textContent = 'Patokan: ' + addr.patokan;
                    patokanEl.style.display = 'flex';
                } else {
                    patokanEl.style.display = 'none';
                }
            }
        } else {
            if (selectedBox) selectedBox.style.display = 'none';
            if (emptyBox) emptyBox.style.display = 'block';
            if (changeBtn) changeBtn.style.display = 'none';
            if (subText) subText.textContent = 'Belum ada alamat dipilih. Atur alamat pengirimanmu dulu.';
        }
    }
    renderAddress();
    var list = document.getElementById('co-cart-list');
    var empty = document.getElementById('co-cart-empty');
    var rupiah = function (n) { return 'Rp. ' + new Intl.NumberFormat('id-ID').format(Math.round(n)); };

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function currentTake() {
        var sel = document.querySelector('[data-take].is-selected');
        return sel ? sel.dataset.take : 'pickup';
    }

    var isFallbackMode = false;
    var FLAT_ONGKIR = {{ (float) ($tarifOngkir ?? 2000) }};

    function currentPay() {
        var take = currentTake();
        if (take === 'delivery') {
            return 'qris';
        }
        var sel = document.querySelector('#co-pay-pickup-group [data-pay].is-selected');
        return sel ? sel.dataset.pay : 'qris';
    }

    function updatePaymentDisplay() {
        var take = currentTake();
        var pay = currentPay();
        var payPickupGroup = document.getElementById('co-pay-pickup-group');
        var payDeliveryBadge = document.getElementById('co-pay-delivery-badge');
        var fallbackBanner = document.getElementById('co-fallback-banner');
        var qrisBox = document.getElementById('qris-box');
        var submitLabel = document.getElementById('co-submit-label');

        if (take === 'delivery') {
            // Delivery: hanya muncul QRIS, langsung terpilih tanpa opsi
            if (payPickupGroup) payPickupGroup.style.display = 'none';
            if (payDeliveryBadge) payDeliveryBadge.style.display = 'flex';

            if (isFallbackMode) {
                if (fallbackBanner) fallbackBanner.style.display = 'block';
                if (qrisBox) qrisBox.style.display = 'block';
                if (submitLabel) submitLabel.textContent = 'Unggah Bukti & Buat Pesanan (QRIS Statis)';
            } else {
                if (fallbackBanner) fallbackBanner.style.display = 'none';
                if (qrisBox) qrisBox.style.display = 'none';
                if (submitLabel) submitLabel.textContent = 'Bayar via QRIS & Buat Pesanan';
            }
        } else {
            // Pickup: muncul 2 opsi (QRIS & Tunai)
            if (payPickupGroup) payPickupGroup.style.display = 'grid';
            if (payDeliveryBadge) payDeliveryBadge.style.display = 'none';

            if (pay === 'qris') {
                if (isFallbackMode) {
                    if (fallbackBanner) fallbackBanner.style.display = 'block';
                    if (qrisBox) qrisBox.style.display = 'block';
                    if (submitLabel) submitLabel.textContent = 'Unggah Bukti & Buat Pesanan (QRIS Statis)';
                } else {
                    if (fallbackBanner) fallbackBanner.style.display = 'none';
                    if (qrisBox) qrisBox.style.display = 'none';
                    if (submitLabel) submitLabel.textContent = 'Bayar via QRIS & Buat Pesanan';
                }
            } else {
                // Tunai
                if (fallbackBanner) fallbackBanner.style.display = 'none';
                if (qrisBox) qrisBox.style.display = 'none';
                if (submitLabel) submitLabel.textContent = 'Buat Pesanan (Bayar di Tempat)';
            }
        }
    }

    // Bukti Transfer File Upload
    var buktiInput = document.getElementById('co-bukti-input');
    var buktiPreview = document.getElementById('co-bukti-preview');
    var buktiImg = document.getElementById('co-bukti-img');
    var buktiFilename = document.getElementById('co-bukti-filename');
    var buktiRemove = document.getElementById('co-bukti-remove');
    var selectedBuktiFile = null;

    if (buktiInput) {
        buktiInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                var file = this.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    if (window.hamtaToast) window.hamtaToast('Ukuran file maksimal 5MB.');
                    this.value = '';
                    return;
                }
                selectedBuktiFile = file;
                if (buktiFilename) buktiFilename.textContent = file.name;
                var reader = new FileReader();
                reader.onload = function (e) {
                    if (buktiImg) buktiImg.src = e.target.result;
                    if (buktiPreview) buktiPreview.style.display = 'flex';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (buktiRemove) {
        buktiRemove.addEventListener('click', function () {
            selectedBuktiFile = null;
            if (buktiInput) buktiInput.value = '';
            if (buktiPreview) buktiPreview.style.display = 'none';
            if (buktiImg) buktiImg.src = '';
        });
    }

    function render() {
        var cart = window.hamtaGetCart ? window.hamtaGetCart() : [];
        if (list) list.textContent = '';
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
            if (list) list.appendChild(row);
        });

        var subtotal = cart.reduce(function (s, it) { return s + (parseFloat(it.harga) || 0) * (parseInt(it.jumlah, 10) || 0); }, 0);
        var totalQty = cart.reduce(function (s, it) { return s + (parseInt(it.jumlah, 10) || 0); }, 0);
        var isDelivery = currentTake() === 'delivery';
        var ongkir = !cart.length ? 0 : (isDelivery ? FLAT_ONGKIR : 0);
        var ppn = Math.round((subtotal + ongkir) * 0.11);
        var grandTotal = subtotal + ongkir + ppn;

        var totalLabel = document.getElementById('co-total-label');
        var subtotalEl = document.getElementById('co-subtotal');
        var ongkirEl = document.getElementById('co-ongkir');
        var ppnEl = document.getElementById('co-ppn');
        var totalEl = document.getElementById('co-total');

        if (totalLabel) totalLabel.textContent = 'Total Pemesanan (' + totalQty + ' Menu)';
        if (subtotalEl) subtotalEl.textContent = rupiah(subtotal);
        if (ongkirEl) ongkirEl.textContent = rupiah(ongkir);
        if (ppnEl) ppnEl.textContent = rupiah(ppn);
        if (totalEl) totalEl.textContent = rupiah(grandTotal);

        var qrisNominal = document.getElementById('qris-nominal-display');
        var qrisStepsNominal = document.getElementById('qris-steps-nominal');
        if (qrisNominal) qrisNominal.textContent = rupiah(grandTotal);
        if (qrisStepsNominal) qrisStepsNominal.textContent = rupiah(grandTotal);

        updatePaymentDisplay();
    }

    function step(index, delta) {
        var cart = window.hamtaGetCart();
        if (!cart[index]) return;
        cart[index].jumlah = (parseInt(cart[index].jumlah, 10) || 0) + delta;
        if (cart[index].jumlah < 1) cart.splice(index, 1);
        window.hamtaSaveCart(cart);
        render();
    }

    document.querySelectorAll('[data-pay]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('[data-pay]').forEach(function (b) {
                var on = b === btn;
                b.classList.toggle('is-selected', on);
                b.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
            try { localStorage.setItem('hamta_pay', btn.dataset.pay); } catch (e) {}
            updatePaymentDisplay();
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

            // Jika memilih delivery, otomatis set metode bayar ke QRIS
            if (btn.dataset.take === 'delivery') {
                var qrisBtn = document.querySelector('[data-pay="qris"]');
                var tunaiBtn = document.querySelector('[data-pay="tunai"]');
                if (qrisBtn) {
                    qrisBtn.classList.add('is-selected');
                    qrisBtn.setAttribute('aria-pressed', 'true');
                }
                if (tunaiBtn) {
                    tunaiBtn.classList.remove('is-selected');
                    tunaiBtn.setAttribute('aria-pressed', 'false');
                }
                try { localStorage.setItem('hamta_pay', 'qris'); } catch (e) {}
            }

            render();
        });
    });

    try {
        var savedTake = localStorage.getItem('hamta_take');
        var savedPay = localStorage.getItem('hamta_pay');
        if (savedTake && (savedTake === 'pickup' || savedTake === 'delivery')) {
            var takeBtn = document.querySelector('[data-take="' + savedTake + '"]');
            if (takeBtn) takeBtn.click();
        }
        if (savedTake === 'delivery') {
            var qrisBtn = document.querySelector('[data-pay="qris"]');
            if (qrisBtn) qrisBtn.click();
        } else if (savedPay && (savedPay === 'qris' || savedPay === 'tunai')) {
            var payBtn = document.querySelector('[data-pay="' + savedPay + '"]');
            if (payBtn) payBtn.click();
        }
    } catch (e) {}

    function showMsg(text, isError) {
        var el = document.getElementById('co-order-msg');
        if (!el) return;
        el.style.display = '';
        el.style.color = isError ? '#c0392b' : '';
        el.textContent = text;
    }

    // Tampilkan View Pesanan yang Berhasil Dibuat
    function showSuccessOrder(order, items) {
        var formView = document.getElementById('co-form-view');
        var successView = document.getElementById('co-success-view');
        if (formView) formView.style.display = 'none';
        if (successView) successView.style.display = 'block';

        var codeEl = document.getElementById('success-order-code');
        var timeEl = document.getElementById('success-order-time');
        var payEl = document.getElementById('success-order-pay');
        var takeEl = document.getElementById('success-order-take');
        var totalEl = document.getElementById('success-order-total');
        var itemsList = document.getElementById('success-items-list');
        var waBtn = document.getElementById('success-wa-btn');

        if (codeEl) codeEl.textContent = order.kode_pesanan || '—';
        if (timeEl) timeEl.textContent = order.created_at || 'Baru saja';

        if (payEl) {
            if (order.metode_bayar === 'midtrans') {
                if (order.status_pembayaran === 'terverifikasi' || order.status_pembayaran === 'settlement') {
                    payEl.className = 'co-badge-status co-badge-verified';
                    payEl.textContent = 'QRIS Otomatis — Lunas';
                } else {
                    payEl.className = 'co-badge-status co-badge-pending';
                    payEl.textContent = 'QRIS Otomatis — Menunggu Pembayaran';
                }
            } else if (order.metode_bayar === 'qris' || order.metode_bayar === 'qris_statis') {
                payEl.className = 'co-badge-status co-badge-pending';
                payEl.textContent = 'QRIS Manual — Menunggu Verifikasi';
            } else {
                payEl.className = 'co-badge-status co-badge-verified';
                payEl.textContent = 'Bayar di Tempat (Tunai)';
            }
        }

        var qrSuccessBox = document.getElementById('success-midtrans-qr-box');
        var qrSuccessImg = document.getElementById('success-midtrans-qr-img');
        var qrExpiredBox = document.getElementById('success-midtrans-expired-box');
        var successCountdownEl = document.getElementById('success-expiry-countdown');
        var successCountdownTimer = null;

        function startSuccessCountdown(expiryIso) {
            if (!expiryIso) return;
            var targetMs = new Date(expiryIso).getTime();
            if (successCountdownTimer) clearInterval(successCountdownTimer);

            function updateTimer() {
                var now = Date.now();
                var diff = targetMs - now;

                if (diff <= 0) {
                    if (successCountdownEl) successCountdownEl.textContent = '00:00';
                    if (qrSuccessBox) qrSuccessBox.style.display = 'none';
                    if (qrExpiredBox) qrExpiredBox.style.display = 'block';
                    if (successCountdownTimer) clearInterval(successCountdownTimer);
                    return;
                }

                var m = Math.floor(diff / 60000);
                var s = Math.floor((diff % 60000) / 1000);
                var mStr = (m < 10 ? '0' : '') + m;
                var sStr = (s < 10 ? '0' : '') + s;

                if (successCountdownEl) successCountdownEl.textContent = mStr + ':' + sStr;
            }

            updateTimer();
            successCountdownTimer = setInterval(updateTimer, 1000);
        }

        if (qrSuccessBox && qrSuccessImg) {
            if (order.metode_bayar === 'midtrans' && order.qr_url) {
                qrSuccessImg.src = order.qr_url;
                qrSuccessBox.style.display = 'block';
                if (qrExpiredBox) qrExpiredBox.style.display = 'none';
                if (order.expiry_time) {
                    startSuccessCountdown(order.expiry_time);
                }
            } else {
                qrSuccessBox.style.display = 'none';
            }
        }

        if (takeEl) {
            if (order.metode_ambil === 'delivery') {
                takeEl.textContent = 'Delivery ke ' + (order.alamat_delivery || 'Alamat');
            } else {
                takeEl.textContent = 'Pickup di Cabang Hamta';
            }
        }

        if (totalEl) totalEl.textContent = rupiah(order.total_bayar || 0);

        if (itemsList) {
            itemsList.innerHTML = '';
            (items || []).forEach(function (it) {
                var row = document.createElement('div');
                row.style.cssText = 'display:flex;justify-content:space-between;align-items:center;padding:7px 0;font-size:13px;border-bottom:1px dashed rgba(11,65,43,.12)';
                var itemSub = (parseFloat(it.harga) || 0) * (parseInt(it.jumlah, 10) || 0);
                row.innerHTML = '<div><strong style="color:var(--ink)">' + esc(it.nama) + '</strong> <span style="font-weight:600;color:#6f6a5f">x' + it.jumlah + '</span></div><div style="font-weight:700">' + rupiah(itemSub) + '</div>';
                itemsList.appendChild(row);
            });
        }

        if (waBtn) {
            var msg = 'Halo Admin Hamta Healthy Food, saya sudah membuat pesanan di website!\n\n' +
                '• No. Pesanan: ' + (order.kode_pesanan || '') + '\n' +
                '• Nama: ' + (order.nama_pelanggan || '-') + '\n' +
                '• Metode: ' + (order.metode_ambil === 'delivery' ? 'Delivery' : 'Pickup') + '\n' +
                '• Pembayaran: ' + (order.metode_bayar === 'qris' ? 'QRIS (Sudah Bayar)' : 'Bayar di Tempat') + '\n' +
                '• Total Tagihan: ' + rupiah(order.total_bayar || 0) + '\n\n' +
                'Pantau status pesanan live: ' + "{{ url('status-pesanan') }}/" + encodeURIComponent(order.kode_pesanan || '') + '\n\n' +
                'Mohon segera diproses ya. Terima kasih!';
            waBtn.href = 'https://wa.me/6281234567890?text=' + encodeURIComponent(msg);
        }

        var trackerBtn = document.getElementById('success-tracker-btn');
        if (trackerBtn && order.kode_pesanan) {
            trackerBtn.href = "{{ url('status-pesanan') }}/" + encodeURIComponent(order.kode_pesanan);
        }

        // Live Polling ke Resto saat tampilan sukses terbuka
        if (order.kode_pesanan) {
            var pollOrderCode = order.kode_pesanan;
            var pollCheckoutInterval = setInterval(function () {
                fetch("{{ url('api/pesanan') }}/" + encodeURIComponent(pollOrderCode) + "/status")
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (!data || !data.ok) return;

                        if (data.is_expired || data.status_pembayaran === 'expired' || data.status_pembayaran === 'ditolak') {
                            if (qrSuccessBox) qrSuccessBox.style.display = 'none';
                            if (qrExpiredBox && order.metode_bayar === 'midtrans') qrExpiredBox.style.display = 'block';
                        }
                        if (payEl) {
                            if (data.metode_bayar === 'tunai') {
                                payEl.className = 'co-badge-status co-badge-verified';
                                payEl.textContent = 'Bayar di Tempat (Tunai)';
                            } else if (data.status_pembayaran === 'terverifikasi') {
                                payEl.className = 'co-badge-status co-badge-verified';
                                payEl.textContent = 'QRIS — Terverifikasi (Lunas)';
                            } else if (data.status_pembayaran === 'ditolak') {
                                payEl.className = 'co-badge-status';
                                payEl.style.background = '#fee2e2';
                                payEl.style.color = '#b91c1c';
                                payEl.textContent = 'Pembayaran Ditolak';
                            } else {
                                payEl.className = 'co-badge-status co-badge-pending';
                                payEl.textContent = 'QRIS — Menunggu Verifikasi Admin';
                            }
                        }
                        var stepNote = document.getElementById('success-status-note');
                        var steps = document.querySelectorAll('.co-step');
                        if (data.status_pesanan === 'diproses') {
                            if (stepNote) stepNote.textContent = 'Pesanan telah kami terima dan sedang disiapkan di dapur Hamta.';
                            if (steps[0]) steps[0].className = 'co-step is-done';
                            if (steps[1]) steps[1].className = 'co-step is-active';
                            if (steps[2]) steps[2].className = 'co-step';
                        } else if (data.status_pesanan === 'siap') {
                            if (stepNote) stepNote.textContent = data.metode_ambil === 'delivery' ? 'Pesanan Anda sudah siap & kurir bersiap mengantar!' : 'Pesanan Anda sudah siap diambil di outlet resto!';
                            if (steps[0]) steps[0].className = 'co-step is-done';
                            if (steps[1]) steps[1].className = 'co-step is-done';
                            if (steps[2]) steps[2].className = 'co-step is-active';
                        } else if (data.status_pesanan === 'selesai') {
                            if (stepNote) stepNote.textContent = 'Pesanan telah selesai. Selamat menikmati menu sehat dari Hamta!';
                            if (steps[0]) steps[0].className = 'co-step is-done';
                            if (steps[1]) steps[1].className = 'co-step is-done';
                            if (steps[2]) steps[2].className = 'co-step is-done';
                        } else if (data.status_pesanan === 'batal') {
                            if (stepNote) stepNote.textContent = 'Pesanan dibatalkan oleh pihak resto. Hubungi kasir kami untuk info lebih lanjut.';
                        }
                    })
                    .catch(function () {});
            }, 4000);
        }

        var copyBtn = document.getElementById('success-copy-btn');
        if (copyBtn) {
            copyBtn.onclick = function () {
                var code = (document.getElementById('success-order-code') || {}).textContent || '';
                if (!code || code === '—') return;
                try {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(code);
                    }
                    if (window.hamtaToast) window.hamtaToast('Kode pesanan disalin');
                } catch (e) {}
            };
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    var newOrderBtn = document.getElementById('success-new-order-btn');
    if (newOrderBtn) {
        newOrderBtn.addEventListener('click', function () {
            try { localStorage.removeItem('hamta_last_order'); } catch (e) {}
            var formView = document.getElementById('co-form-view');
            var successView = document.getElementById('co-success-view');
            if (formView) formView.style.display = 'block';
            if (successView) successView.style.display = 'none';
            render();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Submit Checkout
    var submitBtn = document.getElementById('co-submit');
    var submitLabel = document.getElementById('co-submit-label');
    if (submitBtn) {
        submitBtn.addEventListener('click', function () {
            var cart = window.hamtaGetCart ? window.hamtaGetCart() : [];
            if (!cart.length) {
                showMsg('Keranjang masih kosong. Yuk tambah menu dulu!', true);
                if (window.hamtaToast) window.hamtaToast('Keranjang masih kosong.');
                return;
            }
            // Validasi 1: Alamat wajib ada
            var savedAddr = getActiveAddress();
            if (!savedAddr || !savedAddr.alamat || !savedAddr.nama) {
                showMsg('Silakan atur dan pilih alamat pengiriman terlebih dahulu sebelum membuat pesanan.', true);
                if (window.hamtaToast) window.hamtaToast('Tambahkan alamat pengiriman terlebih dahulu.');
                var addrSec = document.getElementById('co-address-section');
                if (addrSec) {
                    addrSec.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    addrSec.style.boxShadow = '0 0 0 3px #c0392b';
                    setTimeout(function () { addrSec.style.boxShadow = ''; }, 3000);
                }
                return;
            }

            // Validasi 2: Bukti transfer QRIS manual wajib ada jika dalam mode fallback QRIS statis
            var take = currentTake();
            var pay = currentPay();
            if (pay === 'qris' && isFallbackMode && !selectedBuktiFile) {
                showMsg('Bukti pembayaran QRIS statis wajib diunggah sebelum membuat pesanan.', true);
                if (window.hamtaToast) window.hamtaToast('Unggah bukti pembayaran QRIS terlebih dahulu.');
                var buktiArea = document.getElementById('co-bukti-upload-area');
                if (buktiArea) {
                    buktiArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    buktiArea.style.borderColor = '#c0392b';
                    buktiArea.style.backgroundColor = '#fef2f2';
                    setTimeout(function () {
                        buktiArea.style.borderColor = '';
                        buktiArea.style.backgroundColor = '';
                    }, 3000);
                }
                return;
            }

            var token = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
            var payloadCart = cart.map(function (it) {
                var ids = it.id_komponen || (it.komponen || []).map(function (k) { return k.id; });
                return {
                    tipe: it.tipe === 'custom' ? 'custom' : 'produk',
                    id_produk: parseInt(it.id_produk, 10),
                    id_komponen: (ids || []).map(function (id) { return parseInt(id, 10); }).filter(Boolean),
                    jumlah: parseInt(it.jumlah, 10) || 1,
                };
            });

            var formData = new FormData();
            formData.append('nama_pelanggan', (savedAddr && savedAddr.nama) || '');
            formData.append('no_whatsapp', (savedAddr && savedAddr.telepon) || '');
            formData.append('metode_ambil', take);
            if (take === 'delivery' && savedAddr) {
                formData.append('alamat_delivery', [savedAddr.alamat, savedAddr.patokan].filter(Boolean).join(' — '));
            }
            // Jika dalam mode fallback QRIS statis, kirim metode_bayar='qris_statis'
            var sendPay = (pay === 'qris' && isFallbackMode) ? 'qris_statis' : pay;
            formData.append('metode_bayar', sendPay);
            formData.append('cart', JSON.stringify(payloadCart));
            if (selectedBuktiFile) {
                formData.append('bukti_bayar', selectedBuktiFile);
            }

            submitBtn.disabled = true;
            if (submitLabel) submitLabel.textContent = 'Memproses Pesanan...';

            var cartSnapshot = cart.slice();

            fetch("{{ route('customer.checkout.store') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: formData
            }).then(function (res) {
                return res.json().then(function (body) { return { ok: res.ok, body: body }; });
            }).then(function (out) {
                submitBtn.disabled = false;

                // Cek apakah server meminta fallback ke QRIS statis (misal Midtrans down/timeout/gagal)
                if (out.body && out.body.fallback_required) {
                    isFallbackMode = true;
                    updatePaymentDisplay();
                    var fallbackBanner = document.getElementById('co-fallback-banner');
                    var fallbackText = document.getElementById('co-fallback-text');
                    if (fallbackBanner) fallbackBanner.style.display = 'block';
                    if (fallbackText && out.body.message) fallbackText.textContent = out.body.message;

                    var qrisBox = document.getElementById('qris-box');
                    if (qrisBox) {
                        qrisBox.style.display = 'block';
                        qrisBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    if (window.hamtaToast) window.hamtaToast('Layanan QRIS otomatis dialihkan ke QRIS statis.');
                    showMsg('Layanan QRIS otomatis sedang tidak dapat dijangkau. Silakan transfer via barcode QRIS dan unggah bukti transfer di bawah.', true);
                    return;
                }

                updatePaymentDisplay();
                if (!out.ok) {
                    var firstErr = out.body && out.body.errors
                        ? Object.values(out.body.errors).flat()[0]
                        : null;
                    showMsg(firstErr || (out.body && out.body.message) || 'Gagal membuat pesanan. Coba lagi.', true);
                    return;
                }

                // Berhasil membuat pesanan!
                var orderData = out.body;
                try {
                    localStorage.setItem('hamta_last_order', JSON.stringify({
                        order: orderData,
                        items: cartSnapshot,
                        timestamp: Date.now()
                    }));
                } catch (e) {}

                window.hamtaSaveCart([]);
                if (window.hamtaToast) window.hamtaToast('Pesanan ' + orderData.kode_pesanan + ' berhasil dibuat!');
                showSuccessOrder(orderData, cartSnapshot);
            }).catch(function () {
                submitBtn.disabled = false;
                updatePaymentDisplay();
                showMsg('Gagal membuat pesanan. Periksa koneksi lalu coba lagi.', true);
            });
        });
    }

    // Periksa apakah ada pesanan terakhir saat keranjang kosong (maks 30 menit)
    try {
        var lastOrderData = JSON.parse(localStorage.getItem('hamta_last_order') || 'null');
        var currentCart = window.hamtaGetCart ? window.hamtaGetCart() : [];
        var orderAge = lastOrderData && lastOrderData.timestamp ? (Date.now() - lastOrderData.timestamp) : 0;
        if (lastOrderData && lastOrderData.order && currentCart.length === 0 && orderAge < 30 * 60 * 1000) {
            showSuccessOrder(lastOrderData.order, lastOrderData.items);
        } else {
            render();
        }
    } catch (e) {
        render();
    }
})();
</script>
</div>
@include('customer.partials.footer')
</body>
</html>
