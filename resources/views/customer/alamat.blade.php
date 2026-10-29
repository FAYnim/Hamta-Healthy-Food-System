<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hamta — Pilih Alamat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .al-head { display:flex; align-items:center; gap:30px; margin:14px 4px 34px; }
        .al-back { display:inline-flex; color:var(--green); text-decoration:none; flex:0 0 auto; }
        .al-head h1 { margin:0; font-size:34px; line-height:1.15; font-weight:700; letter-spacing:-1px; }
        .al-list { display:grid; gap:30px; }
        .al-card { display:flex; gap:18px; border:2px solid var(--green); border-radius:14px; padding:20px 22px 24px; background:#fff; }
        .al-radio { flex:0 0 auto; width:27px; height:27px; margin-top:2px; border:2px solid var(--green); border-radius:50%; display:grid; place-items:center; }
        .al-radio.is-selected:before { content:''; width:13px; height:13px; border-radius:50%; background:var(--green); }
        .al-body { flex:1 1 auto; min-width:0; }
        .al-top { display:flex; align-items:baseline; justify-content:space-between; gap:16px; }
        .al-name { margin:0; font-size:19px; line-height:1.3; font-weight:700; }
        .al-phone { font-weight:400; }
        .al-edit { flex:0 0 auto; color:var(--green); font-size:16px; font-weight:500; text-decoration:underline; text-underline-offset:3px; }
        .al-address { margin:9px 0 0; font-size:16px; line-height:1.45; }
        .al-region { margin:16px 0 0; font-size:15px; line-height:1.45; text-transform:uppercase; color:#4a5a52; }
        .al-add { display:flex; align-items:center; justify-content:center; gap:12px; width:100%; margin:38px 0 10px; padding:22px 18px; border:2px solid var(--green); border-radius:14px; background:transparent; color:var(--green); font-family:inherit; font-size:21px; font-weight:700; text-decoration:none; }
        @media (max-width:700px) {
            .al-head { gap:16px; margin:8px 2px 24px; }
            .al-head h1 { font-size:24px; }
            .al-back svg { width:30px; height:30px; }
            .al-list { gap:18px; }
            .al-card { gap:12px; padding:14px 14px 16px; }
            .al-radio { width:22px; height:22px; }
            .al-radio.is-selected:before { width:10px; height:10px; }
            .al-top { flex-wrap:wrap; gap:4px 12px; }
            .al-name { font-size:15px; }
            .al-edit { font-size:13px; }
            .al-address { font-size:13px; margin-top:7px; }
            .al-region { font-size:12px; margin-top:12px; }
            .al-add { margin:26px 0 6px; padding:16px 14px; font-size:16px; gap:9px; }
        }
    </style>
</head>
<body>
<div class="page">
    @include('customer.partials.header')

    <div class="al-head">
        <a class="al-back" href="{{ url('/') }}" aria-label="Kembali">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
        </a>
        <h1>Pilih Alamat</h1>
    </div>

    <div class="al-list">
        <article class="al-card">
            <span class="al-radio is-selected" aria-hidden="true"></span>
            <div class="al-body">
                <div class="al-top">
                    <p class="al-name">Brendata Najwa Firki Hid... <span class="al-phone">(+62) 823-3112-9819</span></p>
                    <a class="al-edit" href="#">Ubah</a>
                </div>
                <p class="al-address">Jalan Tangkis Turi Kecil C No .22, RT.1/RW.4,<br>Simomulyo, Sukomanunggal (Kos Ibu Maria)</p>
                <p class="al-region">Sukomanunggal, Kota Surabaya, Jawa Timur, ID 60187</p>
            </div>
        </article>

        <article class="al-card">
            <span class="al-radio" aria-hidden="true"></span>
            <div class="al-body">
                <div class="al-top">
                    <p class="al-name">Brendata Najwa Firki Hid... <span class="al-phone">(+62) 823-3112-9819</span></p>
                    <a class="al-edit" href="#">Ubah</a>
                </div>
                <p class="al-address">Jalan Petemon Barat Gang Mushola No. 210b, Petemon<br>(Masuk Gang)</p>
                <p class="al-region">Sukomanunggal, Kota Surabaya, Jawa Timur, ID 60187</p>
            </div>
        </article>
    </div>

    <a class="al-add" href="#">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
        Tambah Alamat Baru
    </a>
</div>
@include('customer.partials.footer')
</body>
</html>
