<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hamta — Daftar Menu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .menu-title { margin:6px 2px 18px; font-size:30px; line-height:1.15; letter-spacing:-1px; font-weight:700; }
        .catalog { border:3px solid #426a57; border-radius:14px; padding:17px 15px 22px; display:grid; grid-template-columns:repeat(4,1fr); gap:24px 20px; margin-bottom:30px; }
        .product { min-width:0; }
        .product-art { aspect-ratio:1/1; border-radius:9px; background:var(--lime); display:grid; place-items:center; position:relative; overflow:hidden; }
        .product-art:before { content:''; width:66%; height:66%; border-radius:50%; background:radial-gradient(circle at 35% 30%,#ffe49c 0 16%,transparent 17%), radial-gradient(circle at 62% 58%,#eb4d35 0 15%,transparent 16%), radial-gradient(circle at 42% 70%,#7dce59 0 18%,transparent 19%), #f2c17b; box-shadow:0 7px 0 #d49a5b; transform:rotate(-9deg); }
        .product-name { position:absolute; bottom:8px; left:9px; right:9px; color:white; font-size:17px; line-height:1.2; font-weight:700; text-transform:uppercase; text-shadow:0 1px 2px #255c27; }
        .price { margin:10px 5px 7px; font-size:18px; font-weight:700; }
        .product-photo { width:100%; height:100%; object-fit:contain; position:relative; z-index:1; }
        .product .btn { display:block; width:100%; text-align:center; }
        .catalog-empty { grid-column:1 / -1; text-align:center; font-size:14px; font-weight:600; padding:24px 0; }
        .btn { display:inline-block; padding:9px 15px; border:0; border-radius:7px; background:var(--green); color:#fff; font-weight:700; font-size:13px; text-decoration:none; transition:transform .2s,background .2s; }
        .btn:hover { background:#16613f; transform:translateY(-2px); }
        @media (max-width:700px) {
            .menu-title { font-size:22px; margin:4px 2px 12px; }
            .catalog { padding:12px 9px 15px; gap:17px 9px; margin-bottom:22px; grid-template-columns:repeat(2,1fr); }
            .product-name { font-size:12px; left:5px; right:5px; bottom:5px; }
            .price { font-size:14px; margin:7px 3px 5px; }
            .btn { padding:7px 8px; font-size:10px; }
        }
    </style>
</head>
<body>
<div class="page">
    @include('customer.partials.header')

    <h1 class="menu-title">Best Seller</h1>
    <section class="catalog">
        @forelse ($bestSellers as $menu)
            <article class="product">
                <div class="product-art">
                    @if ($menu->foto)
                        <img class="product-photo" src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_produk }}" loading="lazy">
                    @endif
                    <span class="product-name">{{ strtoupper($menu->nama_produk) }}</span>
                </div>
                <div class="price">Rp. {{ number_format($menu->harga_online, 0, ',', '.') }}</div>
                <a class="btn" href="#" data-add-cart data-id="{{ $menu->id_produk }}" data-nama="{{ $menu->nama_produk }}" data-harga="{{ $menu->harga_online }}" @if($menu->foto) data-foto="{{ asset('storage/' . $menu->foto) }}" @endif>+ Add to Cart</a>
            </article>
        @empty
            <p class="catalog-empty">Belum ada menu best seller saat ini.</p>
        @endforelse
    </section>

    @foreach ($kategoriUrutan as $kategori)
        <h2 class="menu-title">{{ $kategori }}</h2>
        <section class="catalog">
            @forelse ($menusByKategori->get($kategori, collect()) as $menu)
                <article class="product">
                    <div class="product-art">
                        @if ($menu->foto)
                            <img class="product-photo" src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_produk }}" loading="lazy">
                        @endif
                        <span class="product-name">{{ strtoupper($menu->nama_produk) }}</span>
                    </div>
                    <div class="price">Rp. {{ number_format($menu->harga_online, 0, ',', '.') }}</div>
                    <a class="btn" href="#" data-add-cart data-id="{{ $menu->id_produk }}" data-nama="{{ $menu->nama_produk }}" data-harga="{{ $menu->harga_online }}" @if($menu->foto) data-foto="{{ asset('storage/' . $menu->foto) }}" @endif>+ Add to Cart</a>
                </article>
            @empty
                <p class="catalog-empty">Belum ada menu {{ strtolower($kategori) }} saat ini.</p>
            @endforelse
        </section>
    @endforeach
</div>
@include('customer.partials.footer')
</body>
</html>
