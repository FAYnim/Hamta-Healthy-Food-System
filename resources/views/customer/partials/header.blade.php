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
    .bag-wrap { position:relative; display:inline-block; }
    @media (max-width:700px) { :root{--page-pad:12px}.page{padding:19px var(--page-pad) 0}.nav{padding-bottom:18px}.links{gap:13px;font-size:12px}.brand{width:83px} }
    .hamta-toast-wrap { position:fixed; top:18px; right:18px; z-index:9999; display:flex; flex-direction:column; gap:10px; max-width:min(340px, calc(100vw - 36px)); }
    .hamta-toast { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; background:var(--green); color:#fff; font-size:13px; font-weight:600; box-shadow:0 8px 24px rgba(11,65,43,.3); animation:hamta-toast-in .25s ease-out; }
    .hamta-toast svg { flex:0 0 auto; }
    @keyframes hamta-toast-in { from { opacity:0; transform:translateX(20px); } to { opacity:1; transform:none; } }
    @media (max-width:700px) { .hamta-toast-wrap { top:12px; right:12px; } .hamta-toast { font-size:12px; padding:10px 13px; } }
</style>
<div class="hamta-toast-wrap" id="hamta-toast-wrap" aria-live="polite"></div>
<script>
(function () {
    var KEY = 'hamta_cart';
    function getCart() {
        try {
            return JSON.parse(localStorage.getItem(KEY)) || [];
        } catch (e) {
            return [];
        }
    }
    function showToast(msg) {
        var wrap = document.getElementById('hamta-toast-wrap');
        if (!wrap) return;
        var el = document.createElement('div');
        el.className = 'hamta-toast';
        el.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg><span></span>';
        el.querySelector('span').textContent = msg;
        wrap.appendChild(el);
        setTimeout(function () {
            el.style.transition = 'opacity .3s, transform .3s';
            el.style.opacity = '0';
            el.style.transform = 'translateX(20px)';
            setTimeout(function () { el.remove(); }, 320);
        }, 2500);
    }
    window.hamtaToast = showToast;
    window.hamtaGetCart = getCart;
    window.hamtaSaveCart = function (cart) {
        try {
            localStorage.setItem(KEY, JSON.stringify(cart));
            return true;
        } catch (e) {
            return false;
        }
    };
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-add-cart]');
        if (!btn) return;
        e.preventDefault();
        var id = parseInt(btn.dataset.id, 10);
        if (!id) return;
        var cart = getCart();
        var existing = cart.find(function (item) { return item.tipe === 'produk' && parseInt(item.id_produk, 10) === id; });
        if (existing) {
            existing.jumlah = (parseInt(existing.jumlah, 10) || 1) + 1;
        } else {
            cart.push({
                tipe: 'produk',
                id_produk: id,
                nama: btn.dataset.nama || 'Produk',
                harga: parseFloat(btn.dataset.harga) || 0,
                foto: btn.dataset.foto || '',
                jumlah: 1,
            });
        }
        try {
            localStorage.setItem(KEY, JSON.stringify(cart));
        } catch (err) {
            showToast('Keranjang penuh, gagal menyimpan.');
            return;
        }
        showToast((btn.dataset.nama || 'Produk') + ' berhasil dimasukkan ke keranjang');
    });
})();
</script>
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
