<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hamta — Daftar Menu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .menu-title { margin:6px 2px 18px; font-size:30px; line-height:1.15; letter-spacing:-1px; font-weight:700; }
        .menu-catalog { border:3px solid var(--green); border-radius:14px; padding:18px 16px 24px; display:grid; grid-template-columns:repeat(4,1fr); gap:24px 20px; margin-bottom:30px; }
        .menu-card { min-width:0; display:flex; flex-direction:column; align-items:flex-start; }
        .menu-art { aspect-ratio:1; width:100%; border-radius:12px; background:var(--lime); position:relative; overflow:hidden; }
        .menu-art:before { content:''; position:absolute; inset:0; margin:auto; width:62%; height:62%; border-radius:50%; background:radial-gradient(circle at 35% 30%,#ffe49c 0 16%,transparent 17%), radial-gradient(circle at 62% 58%,#eb4d35 0 15%,transparent 16%), radial-gradient(circle at 42% 70%,#7dce59 0 18%,transparent 19%), #f2c17b; box-shadow:0 7px 0 #d49a5b; transform:rotate(-9deg); }
        .menu-card:nth-child(2) .menu-art:before { border-radius:48% 52% 42% 58%; background:linear-gradient(135deg,#efc779,#9c573d 52%,#f7e4ad); }
        .menu-card:nth-child(3) .menu-art:before { background:radial-gradient(circle,#dc5741 0 13%,transparent 14%),radial-gradient(circle at 70% 28%,#f2d36f 0 13%,transparent 14%),#f1e6a7; }
        .menu-card:nth-child(4) .menu-art:before { width:38%; height:74%; border-radius:12px 12px 28px 28px; background:linear-gradient(90deg,#e7a348,#fff0bc,#d58241); box-shadow:0 9px 0 #cb762f; }
        .menu-catalog .menu-card--juice .menu-art:before { width:35%; height:72%; border-radius:12px 12px 28px 28px; background:linear-gradient(90deg,#e7a348,#fff0bc,#d58241); box-shadow:0 9px 0 #cb762f; }
        .menu-catalog .menu-card--jus-pir .menu-art:before { background:linear-gradient(90deg,#efe2b6,#c9e08a,#9fbf63); }
        .menu-catalog .menu-card--jus-alpukat .menu-art:before { background:linear-gradient(90deg,#efe2b6,#a9d85f,#734631); }
        .menu-catalog .menu-card--jus-wortel .menu-art:before { background:linear-gradient(90deg,#f08b39,#ffcf5f,#e75e30); }
        .menu-catalog .menu-card--jus-stroberi .menu-art:before { background:linear-gradient(90deg,#e77791,#ffe1a3,#dc4d66); }
        .menu-name { position:absolute; bottom:9px; left:10px; right:10px; color:#fff; font-size:13px; font-weight:700; text-transform:uppercase; line-height:1.2; text-shadow:0 1px 2px #255c27; }
        .menu-price { margin:12px 4px 9px; font-size:14px; font-weight:700; }
        .menu-btn { display:inline-block; padding:10px 20px; border-radius:999px; background:var(--green); color:#fff; font-size:13px; font-weight:700; text-decoration:none; transition:transform .2s, background .2s; }
        .menu-btn:hover { background:#16613f; transform:translateY(-2px); }
        @media (max-width:700px) {
            .menu-title { font-size:22px; margin:4px 2px 12px; }
            .menu-catalog { padding:12px 9px 16px; gap:17px 9px; margin-bottom:22px; grid-template-columns:repeat(2,1fr); }
            .menu-art { border-radius:9px; }
            .menu-name { font-size:9px; bottom:5px; left:6px; right:6px; }
            .menu-price { font-size:11px; margin:8px 3px 6px; }
            .menu-btn { padding:7px 12px; font-size:10px; }
        }
    </style>
</head>
<body>
<div class="page">
    @include('customer.partials.header')

    <h1 class="menu-title">Best Seller</h1>
    <section class="menu-catalog">
        @foreach ([['CHICKEN SANDWICH',''],['CAESAR WRAP',''],['CHEF SALAD',''],['CHEF WRAP','']] as [$name, $modifier])
            <article class="menu-card {{ $modifier }}">
                <div class="menu-art"><span class="menu-name">{{ $name }}</span></div>
                <div class="menu-price">Rp. 25.000</div>
                <a class="menu-btn" href="#">+ Add to Cart</a>
            </article>
        @endforeach
    </section>

    <h2 class="menu-title">Salad</h2>
    <section class="menu-catalog">
        @foreach ([['CHICKEN SANDWICH',''],['CAESAR WRAP',''],['CHEF SALAD',''],['CHEF WRAP','']] as [$name, $modifier])
            <article class="menu-card {{ $modifier }}">
                <div class="menu-art"><span class="menu-name">{{ $name }}</span></div>
                <div class="menu-price">Rp. 25.000</div>
                <a class="menu-btn" href="#">+ Add to Cart</a>
            </article>
        @endforeach
    </section>

    <h2 class="menu-title">Juice</h2>
    <section class="menu-catalog">
        @foreach ([['JUS PIR','menu-card--juice menu-card--jus-pir'],['JUS ALPUKAT','menu-card--juice menu-card--jus-alpukat'],['JUS WORTEL','menu-card--juice menu-card--jus-wortel'],['JUS STROBERI','menu-card--juice menu-card--jus-stroberi']] as [$name, $modifier])
            <article class="menu-card {{ $modifier }}">
                <div class="menu-art"><span class="menu-name">{{ $name }}</span></div>
                <div class="menu-price">Rp. 25.000</div>
                <a class="menu-btn" href="#">+ Add to Cart</a>
            </article>
        @endforeach
    </section>
</div>
@include('customer.partials.footer')
</body>
</html>
