<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hamta — Kreasikan Salad</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .kreasi-head { text-align:center; }
        .kreasi-head h1 { margin:6px 0 4px; font-size:40px; line-height:1.1; letter-spacing:-1px; font-weight:800; }
        .kreasi-head p { margin:0; font-size:17px; font-weight:600; }

        .kreasi-hero { display:grid; place-items:center; padding:26px 0 34px; }
        .kreasi-hero-img { width:min(470px,78vw); aspect-ratio:1; border-radius:50%; object-fit:cover; display:block;
            background:radial-gradient(circle at 34% 32%,#d94b2c 0 12%,transparent 13%), radial-gradient(circle at 68% 40%,#7b3f91 0 14%,transparent 15%),
                        radial-gradient(circle at 42% 66%,#7ec94a 0 16%,transparent 17%), radial-gradient(circle at 64% 72%,#f2cf6a 0 11%,transparent 12%),
                        #efe6d2; }

        .kreasi-block { margin-bottom:30px; }
        .kreasi-title { display:flex; align-items:center; gap:9px; margin:0 0 12px; font-size:24px; line-height:1.15; font-weight:700; letter-spacing:-.4px; }
        .kreasi-title svg { width:26px; height:26px; flex:0 0 auto; }

        .kreasi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
        .kreasi-opt { margin:0; min-width:0; border:1px solid #e2ddcf; border-radius:9px; background:#fff; overflow:hidden; }
        .kreasi-tile { position:relative; aspect-ratio:1.75; background:#e9e9e9; display:grid; place-items:center; overflow:hidden; }
        .kreasi-label { margin:0; padding:9px 6px; text-align:center; font-size:14px; font-weight:600; }

        .kreasi-check { position:absolute; top:7px; right:7px; width:22px; height:22px; border-radius:50%; background:#fff; border:1.6px solid #4a4a4a; color:transparent; display:grid; place-items:center; }
        .kreasi-check svg { width:14px; height:14px; }
        .kreasi-opt.is-checked .kreasi-check { background:var(--lime); border-color:var(--lime); color:#fff; }

        .kreasi-food { display:block; width:56%; height:72%; }
        .f-lettuce { background:radial-gradient(circle at 32% 30%,#b3e77c 0 24%,transparent 25%), radial-gradient(circle at 68% 38%,#86cf4f 0 26%,transparent 27%), radial-gradient(circle at 46% 72%,#5aa838 0 28%,transparent 29%), #4d9633; border-radius:50% 44% 56% 46%; }
        .f-romaine { background:linear-gradient(160deg,#d7eb9d,#79bd40 48%,#3f8327); border-radius:48% 48% 32% 32% / 60% 60% 30% 30%; width:44%; height:84%; }
        .f-purple-cabbage { background:radial-gradient(circle at 50% 46%,#edd0ee 0 22%,#b478c6 23% 52%,#8a4b9e 53% 74%,#6f3a83 75%); border-radius:50%; height:66%; }
        .f-tomato { background:radial-gradient(circle at 36% 30%,#ff8f79,#d92c1d 68%); border-radius:50% 50% 46% 46%; height:60%; }
        .f-cucumber { background:radial-gradient(circle at 22% 62%,#d8eeb4 0 12%,#5f9c34 13% 16%,transparent 17%), radial-gradient(circle at 46% 48%,#d8eeb4 0 13%,#5f9c34 14% 17%,transparent 18%), radial-gradient(circle at 74% 58%,#d8eeb4 0 12%,#5f9c34 13% 16%,transparent 17%); }
        .f-green-peas { background:radial-gradient(circle,#3f8124 0 22%,transparent 23%) 0 0/17px 17px, radial-gradient(circle,#79c94b 0 20%,transparent 21%) 8px 8px/17px 17px, #efeadd; border-radius:14px 14px 30px 30px; height:64%; }
        .f-mixed { background:radial-gradient(circle at 20% 40%,#e0533a 0 14%,transparent 15%), radial-gradient(circle at 52% 26%,#f0c34d 0 13%,transparent 14%), radial-gradient(circle at 80% 46%,#7ec94a 0 14%,transparent 15%), radial-gradient(circle at 34% 74%,#7ec94a 0 13%,transparent 14%), radial-gradient(circle at 66% 72%,#e0533a 0 13%,transparent 14%); }
        .f-chicken { background:radial-gradient(circle at 38% 32%,#fbdf94,#dd9c3c 72%); border-radius:52% 48% 44% 56%; height:58%; }
        .f-beef { background:radial-gradient(circle at 36% 30%,#8a5a3c,#4a2a1a 72%); border-radius:52% 48% 44% 56%; height:58%; }
        .f-ayam { background:repeating-linear-gradient(115deg,#a9622c 0 5px,#e9a85c 5px 13px); border-radius:44% 56% 40% 60% / 62% 42% 58% 38%; width:42%; height:80%; }
        .f-telur { background:radial-gradient(circle at 38% 42%,#f4c43a 0 18%,#fff 19% 100%); border-radius:50% 50% 48% 48%; height:58%; }

        .kreasi-summary { display:flex; align-items:center; justify-content:flex-end; gap:22px; margin:6px 0 10px; }
        .kreasi-count { display:flex; align-items:center; gap:8px; font-size:16px; font-weight:600; }
        .kreasi-count svg { width:22px; height:22px; }
        .kreasi-add { display:inline-block; padding:9px 18px; border-radius:7px; background:var(--lime); color:#fff; font-size:15px; font-weight:700; text-decoration:none; transition:transform .2s, filter .2s; }
        .kreasi-add:hover { filter:brightness(.94); transform:translateY(-2px); }

        @media (max-width:700px) {
            .kreasi-head h1 { font-size:26px; letter-spacing:-.6px; }
            .kreasi-head p { font-size:13px; }
            .kreasi-hero { padding:16px 0 22px; }
            .kreasi-hero-img { width:min(260px,64vw); }
            .kreasi-block { margin-bottom:22px; }
            .kreasi-title { font-size:17px; gap:6px; margin-bottom:9px; }
            .kreasi-title svg { width:19px; height:19px; }
            .kreasi-grid { grid-template-columns:repeat(3,1fr); gap:9px; }
            .kreasi-label { font-size:10px; padding:6px 3px; }
            .kreasi-check { width:16px; height:16px; top:4px; right:4px; }
            .kreasi-check svg { width:10px; height:10px; }
            .kreasi-summary { gap:12px; }
            .kreasi-count { font-size:12px; gap:5px; }
            .kreasi-count svg { width:16px; height:16px; }
            .kreasi-add { padding:7px 12px; font-size:11px; }
        }
        @media (max-width:430px) {
            .kreasi-grid { grid-template-columns:repeat(2,1fr); }
        }
    </style>
</head>
<body>
<div class="page">
    @include('customer.partials.header')

    <section class="kreasi-head">
        <h1>Isi Kreasikan Saladmu</h1>
        <p>Buat salad sesuai seleramu!</p>
    </section>

    <div class="kreasi-hero">
        <img class="kreasi-hero-img" src="{{ asset('images/salad-1-clean.webp') }}" alt="Salad segar Hamta">
    </div>

    @php
        $sayuran = [
            ['Lettuce', 'f-lettuce', true],
            ['Romaine', 'f-romaine', false],
            ['Purple Cabbage', 'f-purple-cabbage', false],
            ['Tomato', 'f-tomato', false],
            ['Cucumber', 'f-cucumber', true],
            ['Green Peas', 'f-green-peas', false],
            ['Mixed Vegetable', 'f-mixed', false],
        ];
        $protein = [
            ['Chicken Patty', 'f-chicken', true],
            ['Beef Patty', 'f-beef', false],
            ['Ayam Panggang', 'f-ayam', false],
            ['Telur Rebus', 'f-telur', false],
        ];
    @endphp

    <section class="kreasi-block">
        <h2 class="kreasi-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12h18"/><path d="M5 12a7 7 0 0 0 14 0"/><path d="M8.5 8.5c0-1.5 1-2 1.5-3 .6 1 1.5 1.5 1.5 3"/><path d="M13.5 8.5c0-1.5.9-2 1.5-3 .6 1 1.5 1.5 1.5 3"/></svg>
            Pilih Sayuran
        </h2>
        <div class="kreasi-grid">
            @foreach ($sayuran as [$name, $food, $checked])
                <article class="kreasi-opt {{ $checked ? 'is-checked' : '' }}">
                    <div class="kreasi-tile">
                        <span class="kreasi-food {{ $food }}"></span>
                        <span class="kreasi-check" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>
                    </div>
                    <p class="kreasi-label">{{ $name }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="kreasi-block">
        <h2 class="kreasi-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 9v6"/><path d="M20 9v6"/><path d="M7 12h10"/><path d="M2.5 8v8"/><path d="M21.5 8v8"/><path d="M7 5.5c0 2.5 1.5 3 1.5 5"/><path d="M17 5.5c0 2.5-1.5 3-1.5 5"/></svg>
            Pilih Protein
        </h2>
        <div class="kreasi-grid">
            @foreach ($protein as [$name, $food, $checked])
                <article class="kreasi-opt {{ $checked ? 'is-checked' : '' }}">
                    <div class="kreasi-tile">
                        <span class="kreasi-food {{ $food }}"></span>
                        <span class="kreasi-check" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>
                    </div>
                    <p class="kreasi-label">{{ $name }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <div class="kreasi-summary">
        <span class="kreasi-count">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 4 7v6c0 4 3.4 7 8 8 4.6-1 8-4 8-8V7Z"/><path d="M9 12l2 2 4-4"/></svg>
            2 bahan dipilih
        </span>
        <a class="kreasi-add" href="#">+ Add to Cart</a>
    </div>
</div>
@include('customer.partials.footer')
</body>
</html>
