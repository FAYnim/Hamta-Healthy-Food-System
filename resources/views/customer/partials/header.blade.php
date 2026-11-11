{{-- Partial header customer: base style halaman + navigasi. Include paling atas di setiap halaman customer. --}}
<style>
    :root { 
        --cream: #f7f2e7; 
        --green: #0b412b; 
        --lime: #65bd5e; 
        --ink: #101010; 
        --grey: #8a8a8a;
        --page-pad: 22px; 
    }
    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { margin: 0; background: var(--cream); color: var(--ink); font-family: 'Plus Jakarta Sans', sans-serif; }
    body.hamta-drawer-open { overflow: hidden; touch-action: none; }

    .page { max-width: 1180px; margin: auto; padding: 26px var(--page-pad) 0; overflow: hidden; }

    /* Navigation Bar */
    .nav { display: flex; align-items: center; justify-content: space-between; padding: 0 2px 24px; }
    .brand { width: 108px; height: auto; filter: brightness(0); display: block; }
    .brand-link { display: inline-flex; align-items: center; text-decoration: none; }

    /* Desktop Navigation Links */
    .links { display: flex; align-items: center; gap: 31px; font-size: 15px; font-weight: 600; }
    .links a { color: var(--ink); text-decoration: none; transition: color .2s, border-color .2s; }
    .links a:hover { color: var(--lime); }
    .links a.nav-active { color: var(--green); font-weight: 700; border-bottom: 2px solid var(--lime); padding-bottom: 2px; }

    /* Bag / Cart Icon */
    .bag { display: block; }
    .bag-wrap { position: relative; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; color: inherit; }
    .hamta-cart-badge {
        position: absolute;
        top: -6px;
        right: -8px;
        min-width: 18px;
        height: 18px;
        padding: 0 4px;
        border-radius: 999px;
        background: var(--lime);
        color: #fff;
        font-size: 10.5px;
        font-weight: 800;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(11, 65, 43, 0.25);
        border: 2px solid var(--cream);
    }

    /* Mobile Header Action Buttons (Cart + Hamburger) */
    .mobile-actions { display: none; align-items: center; gap: 10px; }
    .mobile-btn {
        width: 42px;
        height: 42px;
        border-radius: 11px;
        border: 1.5px solid rgba(11, 65, 43, 0.15);
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--green);
        cursor: pointer;
        padding: 0;
        transition: background .18s, border-color .18s, transform .15s;
    }
    .mobile-btn:active { transform: scale(0.95); background: rgba(11, 65, 43, 0.08); }

    /* Animated Hamburger Button */
    .hamburger-btn { flex-direction: column; gap: 4.5px; }
    .hamburger-line {
        width: 20px;
        height: 2px;
        background: var(--green);
        border-radius: 2px;
        transition: transform .25s ease, opacity .2s ease;
        transform-origin: center;
    }
    .hamburger-btn.is-active .hamburger-line:nth-child(1) {
        transform: translateY(6.5px) rotate(45deg);
    }
    .hamburger-btn.is-active .hamburger-line:nth-child(2) {
        opacity: 0;
        transform: scaleX(0);
    }
    .hamburger-btn.is-active .hamburger-line:nth-child(3) {
        transform: translateY(-6.5px) rotate(-45deg);
    }

    /* Drawer Backdrop */
    .hamta-drawer-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(11, 65, 43, 0.45);
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        z-index: 99990;
        opacity: 0;
        visibility: hidden;
        transition: opacity .28s cubic-bezier(0.16, 1, 0.3, 1), visibility .28s;
    }
    .hamta-drawer-backdrop.is-open {
        opacity: 1;
        visibility: visible;
    }

    /* Off-Canvas Mobile Sidebar */
    .hamta-sidebar {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: min(320px, 84vw);
        height: 100%;
        height: 100dvh;
        background: var(--cream);
        z-index: 99999;
        box-shadow: -12px 0 36px rgba(11, 65, 43, 0.22);
        transform: translateX(100%);
        transition: transform .32s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        border-left: 2px solid rgba(11, 65, 43, 0.1);
        visibility: hidden;
    }
    .hamta-sidebar.is-open {
        transform: translateX(0);
        visibility: visible;
    }

    /* Sidebar Header */
    .sidebar-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 20px 16px;
        border-bottom: 1px solid rgba(11, 65, 43, 0.12);
        background: rgba(255, 255, 255, 0.4);
    }
    .sidebar-close {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: 1px solid rgba(11, 65, 43, 0.15);
        background: #fff;
        color: var(--green);
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: background .15s, transform .15s;
    }
    .sidebar-close:active { transform: scale(0.94); background: rgba(11, 65, 43, 0.08); }

    /* Sidebar Content / Links */
    .sidebar-body {
        flex: 1 1 auto;
        padding: 18px 16px;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    .sidebar-section-title {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: var(--grey);
        padding: 0 10px;
        margin-bottom: 10px;
    }
    .sidebar-nav-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .sidebar-nav-item { margin: 0; padding: 0; }
    .sidebar-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 16px;
        border-radius: 11px;
        text-decoration: none;
        color: var(--ink);
        font-size: 15px;
        font-weight: 600;
        transition: background .15s, color .15s, transform .12s;
    }
    .sidebar-link:active { transform: scale(0.99); }
    .sidebar-link:hover {
        background: rgba(101, 189, 94, 0.12);
        color: var(--green);
    }
    .sidebar-link.is-active {
        background: var(--green);
        color: #ffffff;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(11, 65, 43, 0.2);
    }
    .sidebar-cart-count {
        margin-left: auto;
        background: var(--lime);
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 999px;
    }
    .sidebar-link.is-active .sidebar-cart-count {
        background: #fff;
        color: var(--green);
    }

    /* Sidebar Footer */
    .sidebar-foot {
        padding: 16px 18px 22px;
        border-top: 1px solid rgba(11, 65, 43, 0.12);
        background: rgba(255, 255, 255, 0.35);
    }
    .sidebar-cta-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 12px 16px;
        border-radius: 11px;
        background: var(--green);
        color: #fff;
        font-size: 13.5px;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(11, 65, 43, 0.25);
        transition: background .2s, transform .15s;
    }
    .sidebar-cta-btn:hover { background: #072e1e; }
    .sidebar-cta-btn:active { transform: scale(0.98); }
    .sidebar-foot-note {
        margin: 11px 0 0;
        text-align: center;
        font-size: 11px;
        color: var(--grey);
        font-weight: 500;
    }

    /* Responsive Breakpoints */
    @media (max-width: 768px) {
        :root { --page-pad: 14px; }
        .page { padding: 18px var(--page-pad) 0; }
        .nav { padding-bottom: 16px; }
        .brand { width: 88px; }
        .links { display: none !important; }
        .mobile-actions { display: flex !important; }
    }

    @media (min-width: 769px) {
        .mobile-actions { display: none !important; }
        .hamta-drawer-backdrop,
        .hamta-sidebar { display: none !important; }
    }

    /* Toast Notification */
    .hamta-toast-wrap { position: fixed; top: 18px; right: 18px; z-index: 999999; display: flex; flex-direction: column; gap: 10px; max-width: min(340px, calc(100vw - 36px)); pointer-events: none; }
    .hamta-toast { pointer-events: auto; display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 10px; background: var(--green); color: #fff; font-size: 13px; font-weight: 600; box-shadow: 0 8px 24px rgba(11,65,43,.3); animation: hamta-toast-in .25s ease-out; }
    .hamta-toast svg { flex: 0 0 auto; }
    @keyframes hamta-toast-in { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: none; } }
    @media (max-width: 768px) { .hamta-toast-wrap { top: 12px; right: 12px; } .hamta-toast { font-size: 12px; padding: 10px 13px; } }
</style>

<div class="hamta-toast-wrap" id="hamta-toast-wrap" aria-live="polite"></div>

<nav class="nav">
    <a href="{{ route('home') }}#home" class="brand-link" aria-label="Hamta Salad Home">
        <img class="brand" width="108" src="{{ asset('images/hamta-logo-white.png') }}" alt="Hamta">
    </a>

    {{-- Desktop Links (Hidden on Mobile) --}}
    <div class="links">
        <a href="{{ route('home') }}#home" class="{{ request()->routeIs('home') ? 'nav-active' : '' }}">Beranda</a>
        <a href="{{ route('customer.daftar_menu') }}" class="{{ request()->routeIs('customer.daftar_menu') ? 'nav-active' : '' }}">Menu</a>
        <a href="{{ route('customer.kreasikan_salad') }}" class="{{ request()->routeIs('customer.kreasikan_salad') ? 'nav-active' : '' }}">Kreasi</a>
        <a href="{{ route('customer.cerita') }}" class="{{ request()->routeIs('customer.cerita') ? 'nav-active' : '' }}">Cerita</a>
        <a href="{{ route('customer.status_pesanan') }}" class="{{ request()->routeIs('customer.status_pesanan') ? 'nav-active' : '' }}">Status</a>
        <a href="{{ route('customer.checkout') }}" class="bag-wrap {{ request()->routeIs('customer.checkout') ? 'nav-active' : '' }}" aria-label="Keranjang">
            <svg class="bag" width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                <path d="M3 6h18"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <span class="hamta-cart-badge js-cart-badge" style="display:none">0</span>
        </a>
    </div>

    {{-- Mobile Action Buttons (Visible only on Mobile <= 768px) --}}
    <div class="mobile-actions">
        {{-- Quick Cart Button --}}
        <a href="{{ route('customer.checkout') }}" class="mobile-btn bag-wrap" aria-label="Keranjang Belanja">
            <svg class="bag" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                <path d="M3 6h18"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <span class="hamta-cart-badge js-cart-badge" style="display:none">0</span>
        </a>

        {{-- Hamburger Button --}}
        <button type="button" class="mobile-btn hamburger-btn" id="hamta-menu-toggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="hamta-sidebar">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
    </div>
</nav>

{{-- Mobile Sidebar Drawer & Overlay --}}
<div class="hamta-drawer-backdrop" id="hamta-drawer-backdrop" aria-hidden="true"></div>
<aside class="hamta-sidebar" id="hamta-sidebar" aria-label="Menu Navigasi Mobile" aria-hidden="true">
    <div class="sidebar-head">
        <a href="{{ route('home') }}#home" class="brand-link">
            <img width="84" src="{{ asset('images/hamta-logo-white.png') }}" alt="Hamta" style="filter: brightness(0);">
        </a>
        <button type="button" class="sidebar-close" id="hamta-menu-close" aria-label="Tutup menu navigasi">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <div class="sidebar-body">
        <div class="sidebar-section-title">Menu Navigasi</div>
        <ul class="sidebar-nav-list">
            <li class="sidebar-nav-item">
                <a href="{{ route('home') }}#home" class="sidebar-link {{ request()->routeIs('home') ? 'is-active' : '' }}">
                    <span>Beranda</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('customer.daftar_menu') }}" class="sidebar-link {{ request()->routeIs('customer.daftar_menu') ? 'is-active' : '' }}">
                    <span>Daftar Menu</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('customer.kreasikan_salad') }}" class="sidebar-link {{ request()->routeIs('customer.kreasikan_salad') ? 'is-active' : '' }}">
                    <span>Kreasi Salad</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('customer.cerita') }}" class="sidebar-link {{ request()->routeIs('customer.cerita') ? 'is-active' : '' }}">
                    <span>Cerita Kami</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('customer.status_pesanan') }}" class="sidebar-link {{ request()->routeIs('customer.status_pesanan') ? 'is-active' : '' }}">
                    <span>Status Pesanan</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('customer.checkout') }}" class="sidebar-link {{ request()->routeIs('customer.checkout') ? 'is-active' : '' }}">
                    <span>Keranjang</span>
                    <span class="sidebar-cart-count js-cart-badge" style="display:none">0</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-foot">
        <a href="{{ route('customer.kreasikan_salad') }}" class="sidebar-cta-btn">
            <span>Kreasikan Salad Sendiri</span>
        </a>
        <p class="sidebar-foot-note">Hamta Healthy Food • Eat Good, Feel Good</p>
    </div>
</aside>

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

    function updateCartBadges() {
        var cart = getCart();
        var totalQty = cart.reduce(function (sum, item) {
            return sum + (parseInt(item.jumlah, 10) || 1);
        }, 0);

        var badges = document.querySelectorAll('.js-cart-badge');
        badges.forEach(function (badge) {
            if (totalQty > 0) {
                badge.textContent = totalQty > 99 ? '99+' : totalQty;
                badge.style.display = badge.classList.contains('sidebar-cart-count') ? 'inline-block' : 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
        });
    }

    window.hamtaToast = showToast;
    window.hamtaGetCart = getCart;
    window.hamtaUpdateCartBadge = updateCartBadges;
    window.hamtaSaveCart = function (cart) {
        try {
            localStorage.setItem(KEY, JSON.stringify(cart));
            updateCartBadges();
            return true;
        } catch (e) {
            return false;
        }
    };

    // Mobile Sidebar Logic
    var toggleBtn = document.getElementById('hamta-menu-toggle');
    var closeBtn = document.getElementById('hamta-menu-close');
    var sidebar = document.getElementById('hamta-sidebar');
    var backdrop = document.getElementById('hamta-drawer-backdrop');

    // Move sidebar & backdrop to body so no parent container (like .page with overflow:hidden) ever traps or clips them
    if (sidebar && sidebar.parentElement !== document.body) {
        document.body.appendChild(backdrop);
        document.body.appendChild(sidebar);
    }

    function openSidebar() {
        if (!sidebar || !backdrop) return;
        sidebar.classList.add('is-open');
        backdrop.classList.add('is-open');
        sidebar.setAttribute('aria-hidden', 'false');
        backdrop.setAttribute('aria-hidden', 'false');
        if (toggleBtn) {
            toggleBtn.classList.add('is-active');
            toggleBtn.setAttribute('aria-expanded', 'true');
        }
        document.body.classList.add('hamta-drawer-open');
    }

    function closeSidebar() {
        if (!sidebar || !backdrop) return;
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        sidebar.setAttribute('aria-hidden', 'true');
        backdrop.setAttribute('aria-hidden', 'true');
        if (toggleBtn) {
            toggleBtn.classList.remove('is-active');
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
        document.body.classList.remove('hamta-drawer-open');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (sidebar && sidebar.classList.contains('is-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('is-open')) {
            closeSidebar();
        }
    });

    if (sidebar) {
        sidebar.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                closeSidebar();
            });
        });
    }

    // Add to cart listener
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
            updateCartBadges();
        } catch (err) {
            showToast('Keranjang penuh, gagal menyimpan.');
            return;
        }
        showToast((btn.dataset.nama || 'Produk') + ' berhasil dimasukkan ke keranjang');
    });

    // Initialize badges on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateCartBadges);
    } else {
        updateCartBadges();
    }
})();
</script>