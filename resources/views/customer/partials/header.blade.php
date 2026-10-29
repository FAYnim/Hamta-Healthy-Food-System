{{-- Partial header customer: base style halaman + navigasi. Include paling atas di setiap halaman customer. --}}
<style>
    :root { --cream:#f7f2e7; --green:#0b412b; --lime:#65bd5e; --ink:#101010; --page-pad:22px; }
    * { box-sizing:border-box; }
    html { scroll-behavior:smooth; }
    body { margin:0; background:var(--cream); color:var(--ink); font-family:'Plus Jakarta Sans', sans-serif; }
    .page { max-width:1180px; margin:auto; padding:26px var(--page-pad) 0; overflow:hidden; }
    .nav { display:flex; align-items:center; justify-content:space-between; padding:0 2px 24px; }
    .brand { width:108px; height:auto; filter:brightness(0); }
    .links { display:flex; align-items:center; gap:31px; font-size:15px; font-weight:600; }
    .links a { color:var(--ink); text-decoration:none; transition:.2s; }
    .links a:hover { color:var(--lime); }
    .bag { display:block; }
    @media (max-width:700px) { :root{--page-pad:12px}.page{padding:19px var(--page-pad) 0}.nav{padding-bottom:18px}.links{gap:13px;font-size:12px}.brand{width:83px} }
</style>
<nav class="nav">
    <a href="{{ route('home') }}#home"><img class="brand" width="108" src="{{ asset('images/hamta-logo-white.png') }}" alt="Hamta"></a>
    <div class="links">
        <a href="{{ route('home') }}#home">Beranda</a>
        <a href="{{ route('customer.daftar_menu') }}">Menu</a>
        <a href="{{ route('customer.kreasikan_salad') }}">Kreasi</a>
        <a href="{{ url('/') }}#promo">Promo</a>
        <a href="{{ route('customer.checkout') }}" aria-label="Keranjang"><svg class="bag" width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="#101010" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></a>
    </div>
</nav>
