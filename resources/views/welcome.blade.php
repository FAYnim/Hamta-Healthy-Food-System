<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hamta — Eat Good, Feel Good</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --cream:#f7f2e7; --green:#0b412b; --lime:#65bd5e; --ink:#101010; }
        * { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { margin:0; background:var(--cream); color:var(--ink); font-family:'Plus Jakarta Sans', sans-serif; }
        .page { max-width:1180px; margin:auto; padding:26px 22px 0; overflow:hidden; }
        .nav { display:flex; align-items:center; justify-content:space-between; padding:0 2px 24px; }
        .brand { width:108px; height:auto; filter:brightness(0); }
        .links { display:flex; align-items:center; gap:31px; font-size:15px; font-weight:600; }
        .links a { color:var(--ink); text-decoration:none; transition:.2s; }
        .links a:hover { color:var(--lime); }
        .brand { filter:brightness(0); }
        .bag { display:block; }
        .hero, .store { position:relative; border:3px solid var(--green); border-radius:13px; overflow:hidden; color:white; }
        .hero { min-height:calc(100vh - 110px); display:flex; align-items:center; padding:34px 9%; background:linear-gradient(90deg,rgba(5,46,31,.88),rgba(5,46,31,.35)), url('/images/hamta-background.png') center/cover; }
        .hero-row { position:relative; z-index:1; display:flex; align-items:center; gap:18px; }
        .hero h1 { width:390px; margin:0; font-size:25px; line-height:1.15; font-weight:700; }
        .hero-logo { width:225px; }
        h2 { margin:26px 8px 18px; font-size:29px; line-height:1.15; letter-spacing:-1px; }
        .catalog { border:3px solid #426a57; border-radius:14px; padding:17px 15px 22px; display:grid; grid-template-columns:repeat(4,1fr); gap:24px 20px; }
        .product { min-width:0; }
        .product-art { aspect-ratio:1.42; border-radius:9px; background:var(--lime); display:grid; place-items:center; position:relative; overflow:hidden; }
        .product-art:before { content:''; width:66%; height:66%; border-radius:50%; background:radial-gradient(circle at 35% 30%,#ffe49c 0 16%,transparent 17%), radial-gradient(circle at 62% 58%,#eb4d35 0 15%,transparent 16%), radial-gradient(circle at 42% 70%,#7dce59 0 18%,transparent 19%), #f2c17b; box-shadow:0 7px 0 #d49a5b; transform:rotate(-9deg); }
        .product:nth-child(2) .product-art:before { border-radius:48% 52% 42% 58%; background:linear-gradient(135deg,#efc779,#9c573d 52%,#f7e4ad); }
        .product:nth-child(3) .product-art:before { background:radial-gradient(circle,#dc5741 0 13%,transparent 14%),radial-gradient(circle at 70% 28%,#f2d36f 0 13%,transparent 14%),#f1e6a7; }
        .product:nth-child(n+5) .product-art:before { width:35%; height:70%; border-radius:12px 12px 28px 28px; background:linear-gradient(90deg,#e7a348,#fff0bc,#d58241); box-shadow:0 9px 0 #cb762f; }
        .product:nth-child(6) .product-art:before { background:linear-gradient(90deg,#efe2b6,#a9d85f,#734631); }
        .product:nth-child(7) .product-art:before { background:linear-gradient(90deg,#f08b39,#ffcf5f,#e75e30); }
        .product:nth-child(8) .product-art:before { background:linear-gradient(90deg,#e77791,#ffe1a3,#dc4d66); }
        .product-name { position:absolute; bottom:8px; left:9px; color:white; font-size:13px; font-weight:700; text-transform:uppercase; text-shadow:0 1px 2px #255c27; }
        .price { margin:10px 5px 7px; font-size:14px; font-weight:700; }
        .btn { display:inline-block; padding:9px 15px; border:0; border-radius:7px; background:var(--green); color:#fff; font-weight:700; font-size:13px; text-decoration:none; transition:transform .2s,background .2s; }
        .btn:hover { background:#16613f; transform:translateY(-2px); }
        .event { min-height:245px; border:3px solid #426a57; border-radius:13px; display:flex; align-items:center; padding:28px 30px; overflow:hidden; position:relative; }
        .event-copy { width:49%; z-index:1; }
        .event h3 { margin:0 0 7px; font-size:27px; line-height:1.05; }
        .event p { font-size:13px; line-height:1.25; font-weight:600; margin:0 0 17px; max-width:260px; }
        .event-img { position:absolute; width:50%; height:118%; right:-2%; object-fit:contain; }
        .store { min-height:260px; margin-top:58px; padding:38px 40px; background:linear-gradient(90deg,rgba(5,65,43,.93),rgba(5,65,43,.56)), url('/images/salad-1-clean.webp') center/cover; display:flex; align-items:center; }
        .store h3 { font-size:24px; line-height:1.1; margin:0 0 15px; max-width:300px; }
        .store p { margin:0 0 20px; font-size:14px; }
        .testimonials { display:flex; gap:30px; overflow:hidden; padding:0 0 34px; }
        .quote { flex:0 0 31%; min-height:205px; border:3px solid #426a57; border-radius:12px; padding:16px 14px; background:#fbf8f0; }
        .quote-top { display:flex; justify-content:space-between; align-items:center; }
        .avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#0b412b,#72bbcc); }
        .stars { color:#f2bf18; font-size:20px; letter-spacing:1px; margin:8px 0; }
        .quote p { margin:0; font-size:13px; line-height:1.25; }
        .quote small { display:block; margin-top:12px; font-size:11px; }
        .slider { display:flex; align-items:center; justify-content:center; gap:25px; margin-bottom:48px; color:var(--green); font-size:28px; }
        .track { width:235px; height:8px; background:#b9ddb2; border-radius:9px; overflow:hidden; }
        .track span { display:block; width:29%; height:100%; background:var(--green); border-radius:9px; }
        footer { margin:0 -22px; padding:72px 7% 32px; color:#fff; background:var(--green); display:grid; grid-template-columns:1.1fr 1fr 1fr; gap:25px; }
        .wave { display:block; width:100%; height:110px; margin:48px 0 0; }
        footer .brand { filter:brightness(0) invert(1); width:150px; margin-bottom:9px; }
        footer h4 { margin:0 0 8px; font-size:15px; }
        footer p { font-size:12px; line-height:1.25; margin:0; }
        .map { height:115px; background:linear-gradient(135deg,#e8e1d0,#c6d5c1); border:5px solid white; color:#55836b; display:grid; place-items:center; font-size:12px; }
        @media (max-width:700px) { .page{padding:19px 12px 0}.nav{padding-bottom:18px}.links{gap:13px;font-size:12px}.brand{width:83px}.hero{min-height:calc(100vh - 96px);padding:25px 7%;}.hero-row{flex-wrap:wrap;gap:10px}.hero-logo{width:150px}.hero h1{font-size:16px;width:190px}.hero h1{font-size:16px;width:190px}h2{font-size:22px;margin:20px 6px 12px}.catalog{padding:12px 9px 15px;gap:17px 9px}.product-name{font-size:9px;left:5px;bottom:5px}.price{font-size:11px;margin:7px 3px 5px}.btn{padding:7px 8px;font-size:10px}.event{min-height:180px;padding:20px 15px}.event-copy{width:58%}.event h3{font-size:20px}.event p{font-size:10px}.event-img{width:55%;right:-9%}.store{margin-top:35px;min-height:190px;padding:28px 24px}.store h3{font-size:19px}.quote{flex-basis:77%;min-height:190px}.testimonials{gap:12px}.slider{margin-bottom:35px}footer{margin:0 -12px;padding:48px 25px 28px;grid-template-columns:1fr 1fr;gap:25px 15px}.wave{height:55px;margin-top:28px}footer>div:first-child{grid-column:1/-1}footer .brand{width:120px}.map{height:85px} }
    </style>
</head>
<body>
<div class="page">
    <nav class="nav"><a href="#home"><img class="brand" width="108" src="{{ asset('images/hamta-logo-white.png') }}" alt="Hamta"></a><div class="links"><a href="#home">Beranda</a><a href="#menu">Menu</a><a href="#kreasi">Kreasi</a><a href="#promo">Promo</a><a href="#menu" aria-label="Keranjang"><svg class="bag" width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="#101010" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></a></div></nav>
    <section class="hero" id="home"><div class="hero-row"><h1>Mau hidup sehat tapi gak boncos banyak? Yuk beli di</h1><img class="hero-logo" src="{{ asset('images/hamta-logo-white.png') }}" alt="Hamta"></div></section>
    <h2 id="menu">Menu / Katalog</h2>
    <section class="catalog">
        @foreach ([['CHICKEN SANDWICH','sandwich'],['CAESAR WRAP','wrap'],['CHEF SALAD','salad'],['CHEF WRAP','wrap'],['JUS PIR','juice'],['JUS ALPUKAT','juice'],['JUS WORTEL','juice'],['JUS STROBERI','juice']] as [$name, $type])
            <article class="product"><div class="product-art"><span class="product-name">{{ $name }}</span></div><div class="price">Rp. 25.000</div><a class="btn" href="#menu">+ Add to Cart</a></article>
        @endforeach
    </section>
    <h2 id="kreasi">Event Terbaru dari Hamta Salad</h2>
    <section class="event"><div class="event-copy"><h3>Coba kreasi salad bareng teman-teman!</h3><p>Bingung mau ngapain bareng temen-temen? Mending kreasi Salad kalian dan makan bareng-bareng!</p><a class="btn" href="#kreasi">Coba Sekarang!</a></div><img class="event-img" src="{{ asset('images/salad-1-clean.webp') }}" alt="Salad segar Hamta"></section>
    <section class="store" id="promo"><div><h3>Penasaran cerita di balik usaha Hamta Salad?</h3><p>Cuss kepoin!</p><a class="btn" href="#promo">Kepoin sekarang!</a></div></section>
    <h2>Testimoni dari para<br>customer tercinta kami!</h2>
    <section class="testimonials">
        @foreach (['Saladnya juara! Bahan-bahannya super fresh dan garing banget. Dressingnya juga pas di lidah. Fix jadi langganan. Sangat direkomendasikan!','Lidah saya dimanjakan dengan rasa sayur yang fresh. Gak disangka bisa makan salad seenak ini. Hamta Salad porsinya pas dan menyenangkan.','Baru pertama kali coba dan langsung suka. Ternyata dressingnya bener-bener yang saya cari.'] as $quote)
            <article class="quote"><div class="quote-top"><span class="avatar"></span><span>◉</span></div><div class="stars">★★★★★</div><p>{{ $quote }}</p><small>Han Hyeong-jun (Korea Selatan)</small></article>
        @endforeach
    </section>
    <div class="slider"><span>‹</span><div class="track"><span></span></div><span>›</span></div>
</div>
<svg class="wave" viewBox="0 0 1440 320" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg"><path fill="#0b412b" d="M0,160L48,149.3C96,139,192,117,288,96C384,75,480,53,576,53.3C672,53,768,75,864,101.3C960,128,1056,160,1152,149.3C1248,139,1344,85,1392,58.7L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"/></svg>
<footer><div><img class="brand" src="{{ asset('images/hamta-logo-nobg.svg') }}" alt="Hamta"><p>Eat Good, Feel Good</p></div><div><h4>Lokasi Kita</h4><p>Lidah Kulon<br>Rumah Produksi,<br>Jl. Lidah Kulon Gg. 1, Lidah Kulon,<br>Kec. Lakarsantri,<br>Surabaya, Jawa Timur<br>60213</p></div><div><div class="map">Lidah Kulon • Surabaya</div></div><div><h4>Our Sosmed</h4></div><div><h4>Contact Us</h4></div></footer>
</body>
</html>
