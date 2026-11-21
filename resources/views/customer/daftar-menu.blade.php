<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hamta — Daftar Menu</title>
    @include('partials.favicon')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --hamta-green: #0b412b;
            --hamta-lime: #65bd5e;
            --hamta-lime-light: #eaf7e8;
            --hamta-cream: #f7f2e7;
            --hamta-ink: #101010;
            --hamta-gray: #667085;
            --hamta-border: #426a57;
        }

        .menu-title {
            margin: 12px 2px 18px;
            font-size: 28px;
            line-height: 1.2;
            letter-spacing: -0.5px;
            font-weight: 800;
            color: var(--hamta-green);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-title::after {
            content: '';
            flex: 1;
            height: 2px;
            background: linear-gradient(to right, rgba(66, 106, 87, 0.35), transparent);
            border-radius: 999px;
        }

        .catalog {
            border: 2px solid var(--hamta-border);
            border-radius: 18px;
            padding: 22px 18px 24px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px 18px;
            margin-bottom: 36px;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(4px);
        }

        /* Product Card */
        .product-card {
            background: #ffffff;
            border: 1.5px solid rgba(66, 106, 87, 0.22);
            border-radius: 16px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
            position: relative;
            cursor: pointer;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -6px rgba(11, 65, 43, 0.16);
            border-color: var(--hamta-lime);
        }

        /* Thumbnail Image Area */
        .product-thumb-wrap {
            aspect-ratio: 1 / 1;
            border-radius: 12px;
            background: #f0f7ef;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .product-thumb-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-thumb-photo {
            transform: scale(1.05);
        }

        .product-thumb-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 40% 40%, #eaf7e8 0%, #d8ebd5 100%);
            color: var(--hamta-green);
        }

        .badge-cabang {
            position: absolute;
            top: 8px;
            left: 8px;
            z-index: 2;
            background: rgba(11, 65, 43, 0.92);
            color: #ffffff;
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            backdrop-filter: blur(4px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.2px;
        }

        .product-cabang-tag {
            font-size: 11px;
            font-weight: 700;
            color: var(--hamta-green);
            background: #eef5ea;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        /* Product Card Text Content */
        .product-meta {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 12px;
            flex-grow: 1;
        }

        .product-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--hamta-ink);
            line-height: 1.3;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 42px;
        }

        .product-card:hover .product-name {
            color: var(--hamta-green);
        }

        .product-price-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 6px;
            border-top: 1px dashed rgba(66, 106, 87, 0.18);
        }

        .product-price {
            font-size: 17px;
            font-weight: 800;
            color: var(--hamta-green);
            letter-spacing: -0.3px;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        /* Action Buttons */
        .product-actions {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-view-detail {
            background: #ffffff;
            border: 1.5px solid var(--hamta-green);
            color: var(--hamta-green);
            padding: 8px 12px;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 700;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .btn-view-detail:hover {
            background: #edf5ed;
            color: var(--hamta-green);
        }

        .btn-quick-add {
            background: var(--hamta-green);
            border: 1.5px solid var(--hamta-green);
            color: #ffffff;
            padding: 8px 12px;
            border-radius: 9px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .btn-quick-add:hover {
            background: #145938;
            border-color: #145938;
            transform: translateY(-1px);
        }

        .catalog-empty {
            grid-column: 1 / -1;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            padding: 32px 0;
            color: var(--hamta-gray);
        }

        /* ======================================================== */
        /* MODAL POPUP (DI TENGAH LAYAR)                            */
        /* ======================================================== */
        .hamta-modal {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }

        .hamta-modal.is-active {
            opacity: 1;
            visibility: visible;
        }

        .hamta-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(11, 65, 43, 0.55);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 1;
        }

        .hamta-modal-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 860px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 30px 60px -15px rgba(11, 65, 43, 0.4);
            transform: scale(0.92) translateY(12px);
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
        }

        .hamta-modal.is-active .hamta-modal-container {
            transform: scale(1) translateY(0);
        }

        .hamta-modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #333333;
            transition: all 0.18s ease;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        .hamta-modal-close:hover {
            background: #f1f1f1;
            color: #000000;
            transform: scale(1.06);
        }

        .hamta-modal-body {
            padding: 32px 34px;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 32px;
            align-items: center;
        }

        /* Modal Photo / Media */
        .hamta-modal-media {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            background: #f0f7ef;
            aspect-ratio: 1 / 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(66, 106, 87, 0.18);
        }

        .hamta-modal-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Modal Info */
        .hamta-modal-info {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .hamta-modal-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--hamta-ink);
            line-height: 1.2;
            margin: 0;
            padding-right: 30px;
        }

        .hamta-modal-price-wrap {
            display: flex;
            align-items: baseline;
            gap: 6px;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .hamta-modal-price {
            font-size: 22px;
            font-weight: 800;
            color: var(--hamta-green);
            letter-spacing: -0.3px;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
            line-height: 1.2;
        }

        .hamta-modal-desc-box {
            background: #fbf9f4;
            border: 1px solid rgba(66, 106, 87, 0.15);
            border-radius: 12px;
            padding: 12px 14px;
        }

        .hamta-modal-desc-label {
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--hamta-green);
            margin: 0 0 4px;
        }

        .hamta-modal-desc {
            font-size: 13.5px;
            line-height: 1.5;
            color: #374151;
            margin: 0;
        }

        /* Quantity & Controls */
        .hamta-modal-qty-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
        }

        .hamta-modal-qty-label {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--hamta-ink);
        }

        .hamta-modal-qty-box {
            display: inline-flex;
            align-items: center;
            background: #ffffff;
            border: 1.5px solid var(--hamta-green);
            border-radius: 999px;
            padding: 2px;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: #f0f7ef;
            color: var(--hamta-green);
            font-size: 17px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .qty-btn:hover {
            background: var(--hamta-green);
            color: #ffffff;
        }

        .qty-num {
            width: 40px;
            text-align: center;
            font-size: 15px;
            font-weight: 800;
            color: var(--hamta-ink);
            border: none;
            outline: none;
            background: transparent;
        }

        /* Note Input */
        .hamta-modal-note-box {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .hamta-modal-note-label {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--hamta-ink);
        }

        .hamta-modal-note-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid rgba(66, 106, 87, 0.25);
            border-radius: 11px;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: #ffffff;
        }

        .hamta-modal-note-input:focus {
            border-color: var(--hamta-green);
            box-shadow: 0 0 0 3px rgba(11, 65, 43, 0.12);
        }

        /* Modal Footer Action */
        .hamta-modal-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 18px;
            border-top: 1.5px solid rgba(66, 106, 87, 0.15);
            margin-top: 6px;
            gap: 24px;
        }

        .hamta-modal-subtotal-meta {
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
            flex-shrink: 0;
        }

        .hamta-modal-subtotal-label {
            font-size: 12px;
            color: var(--hamta-gray);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            margin-bottom: 2px;
        }

        .hamta-modal-subtotal-val {
            font-size: 24px;
            font-weight: 800;
            color: var(--hamta-green);
            white-space: nowrap;
            letter-spacing: -0.5px;
            font-variant-numeric: tabular-nums;
            line-height: 1.2;
        }

        .hamta-modal-submit-btn {
            background: var(--hamta-green);
            color: #ffffff;
            border: none;
            border-radius: 14px;
            padding: 13px 28px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-shrink: 0;
            white-space: nowrap;
            transition: all 0.2s ease;
            box-shadow: 0 6px 18px -2px rgba(11, 65, 43, 0.32);
        }

        .modal-add-plus {
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
            display: inline-block;
            margin-right: 2px;
            transform: translateY(-1px);
        }

        .hamta-modal-submit-btn:hover {
            background: #145938;
            transform: translateY(-1.5px);
            box-shadow: 0 8px 20px -2px rgba(11, 65, 43, 0.38);
        }

        .hamta-modal-submit-btn:active {
            transform: translateY(0);
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .catalog {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .menu-title {
                font-size: 22px;
                margin: 6px 2px 14px;
            }

            .catalog {
                padding: 14px 10px 18px;
                gap: 14px 10px;
                margin-bottom: 24px;
                grid-template-columns: repeat(2, 1fr);
            }

            .product-card {
                padding: 9px;
                border-radius: 13px;
            }

            .product-name {
                font-size: 13.5px;
                min-height: 36px;
            }

            .product-desc-snippet {
                display: none;
            }

            .product-price {
                font-size: 14.5px;
            }

            .product-actions {
                grid-template-columns: 1fr;
                gap: 5px;
                margin-top: 6px;
            }

            .btn-view-detail,
            .btn-quick-add {
                padding: 6px 8px;
                font-size: 11px;
                border-radius: 8px;
            }

            @media (max-width: 420px) {
                .catalog {
                    gap: 10px 8px;
                    padding: 10px 8px 14px;
                }
                .product-card {
                    padding: 8px;
                    border-radius: 11px;
                }
                .product-name {
                    font-size: 12px;
                    min-height: 32px;
                }
                .product-price {
                    font-size: 13px;
                }
                .btn-view-detail,
                .btn-quick-add {
                    padding: 5px 6px;
                    font-size: 10.5px;
                    border-radius: 7px;
                }
            }

            /* Modal Mobile */
            .hamta-modal {
                overflow-y: auto;
            }

            .hamta-modal-container {
                max-width: 95vw;
                border-radius: 20px;
                margin: auto;
            }

            .hamta-modal-body {
                grid-template-columns: 1fr;
                gap: 16px;
                padding: 20px 18px;
            }

            .hamta-modal-media {
                max-height: 240px;
            }

            .hamta-modal-title {
                font-size: 22px;
            }

            .hamta-modal-price {
                font-size: 22px;
            }

            .hamta-modal-footer {
                flex-direction: column;
                gap: 12px;
                align-items: stretch;
            }

            .hamta-modal-subtotal-meta {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
            }

            .hamta-modal-subtotal-val {
                font-size: 22px;
                white-space: nowrap;
            }

            .hamta-modal-submit-btn {
                width: 100%;
                justify-content: center;
                padding: 13px 20px;
            }
        }
    </style>
</head>
<body>
<div class="page">
    @include('customer.partials.header')

    <!-- Section: Best Seller -->
    <h1 class="menu-title">
        <span>Best Seller</span>
    </h1>
    <section class="catalog">
        @forelse ($bestSellers as $menu)
            <article class="product-card" 
                     data-product-card
                     data-id="{{ $menu->id_produk }}" 
                     data-nama="{{ $menu->nama_produk }}" 
                     data-harga="{{ $menu->harga_online }}" 
                     data-kategori="{{ $menu->kategori }}" 
                     data-deskripsi="{{ $menu->deskripsi ?? 'Pilihan favorit pelanggan Hamta Salad dengan kombinasi bahan segar bernutrisi tinggi.' }}" 
                     data-foto="{{ $menu->foto ? asset('storage/' . $menu->foto) : '' }}"
                     data-cabang="{{ $menu->cabang ? $menu->cabang->nama_cabang : '' }}"
                     data-bahan="{{ $menu->bahan ? $menu->bahan->pluck('nama_bahan')->implode(', ') : '' }}"
                     data-alergen="{{ $menu->bahan ? $menu->bahan->whereNotNull('keterangan')->map(fn($b) => $b->nama_bahan . ' (' . $b->keterangan . ')')->implode(', ') : '' }}">
                <div class="product-thumb-wrap">
                    @if ($menu->cabang)
                        <span class="badge-cabang">Khusus {{ $menu->cabang->nama_cabang }}</span>
                    @endif
                    @if ($menu->foto)
                        <img class="product-thumb-photo" src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_produk }}" loading="lazy">
                    @else
                        <div class="product-thumb-fallback">
                            <svg class="w-12 h-12 opacity-40 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                            <span class="text-xs font-semibold">Hamta Salad</span>
                        </div>
                    @endif
                </div>
                <div class="product-meta">
                    <h3 class="product-name" title="{{ $menu->nama_produk }}">{{ $menu->nama_produk }}</h3>
                    <div class="product-price-row">
                        <span class="product-price">Rp {{ number_format($menu->harga_online, 0, ',', '.') }}</span>
                        @if ($menu->cabang)
                            <span class="product-cabang-tag" title="Hanya di Cabang {{ $menu->cabang->nama_cabang }}">📍 {{ $menu->cabang->nama_cabang }}</span>
                        @endif
                    </div>
                </div>
                <div class="product-actions">
                    <button type="button" class="btn-view-detail" data-open-modal-btn>Lihat Detail</button>
                    <button type="button" class="btn-quick-add" data-add-cart data-id="{{ $menu->id_produk }}" data-nama="{{ $menu->nama_produk }}" data-harga="{{ $menu->harga_online }}" @if($menu->foto) data-foto="{{ asset('storage/' . $menu->foto) }}" @endif>+ Tambah</button>
                </div>
            </article>
        @empty
            <p class="catalog-empty">Belum ada menu best seller saat ini.</p>
        @endforelse
    </section>

    <!-- Sections: Per Kategori (Salad, Sandwich, Juice) -->
    @foreach ($kategoriUrutan as $kategori)
        <h2 class="menu-title">
            <span>{{ $kategori }}</span>
        </h2>
        <section class="catalog">
            @forelse ($menusByKategori->get($kategori, collect()) as $menu)
                <article class="product-card" 
                         data-product-card
                         data-id="{{ $menu->id_produk }}" 
                         data-nama="{{ $menu->nama_produk }}" 
                         data-harga="{{ $menu->harga_online }}" 
                         data-kategori="{{ $menu->kategori }}" 
                         data-deskripsi="{{ $menu->deskripsi ?? 'Diracik dengan komposisi terbaik, higienis, dan sehat untuk menjaga kebugaran Anda.' }}" 
                         data-foto="{{ $menu->foto ? asset('storage/' . $menu->foto) : '' }}"
                         data-cabang="{{ $menu->cabang ? $menu->cabang->nama_cabang : '' }}"
                         data-bahan="{{ $menu->bahan ? $menu->bahan->pluck('nama_bahan')->implode(', ') : '' }}"
                         data-alergen="{{ $menu->bahan ? $menu->bahan->whereNotNull('keterangan')->map(fn($b) => $b->nama_bahan . ' (' . $b->keterangan . ')')->implode(', ') : '' }}">
                    <div class="product-thumb-wrap">
                        @if ($menu->cabang)
                            <span class="badge-cabang">Khusus {{ $menu->cabang->nama_cabang }}</span>
                        @endif
                        @if ($menu->foto)
                            <img class="product-thumb-photo" src="{{ asset('storage/' . $menu->foto) }}" alt="{{ $menu->nama_produk }}" loading="lazy">
                        @else
                            <div class="product-thumb-fallback">
                                <svg class="w-12 h-12 opacity-40 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                <span class="text-xs font-semibold">Hamta {{ $kategori }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="product-meta">
                        <h3 class="product-name" title="{{ $menu->nama_produk }}">{{ $menu->nama_produk }}</h3>
                        <div class="product-price-row">
                            <span class="product-price">Rp {{ number_format($menu->harga_online, 0, ',', '.') }}</span>
                            @if ($menu->cabang)
                                <span class="product-cabang-tag" title="Hanya di Cabang {{ $menu->cabang->nama_cabang }}">📍 {{ $menu->cabang->nama_cabang }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="product-actions">
                        <button type="button" class="btn-view-detail" data-open-modal-btn>Lihat Detail</button>
                        <button type="button" class="btn-quick-add" data-add-cart data-id="{{ $menu->id_produk }}" data-nama="{{ $menu->nama_produk }}" data-harga="{{ $menu->harga_online }}" @if($menu->foto) data-foto="{{ asset('storage/' . $menu->foto) }}" @endif>+ Tambah</button>
                    </div>
                </article>
            @empty
                <p class="catalog-empty">Belum ada menu {{ strtolower($kategori) }} saat ini.</p>
            @endforelse
        </section>
    @endforeach
</div>

@include('customer.partials.modal-detail-menu')

@include('customer.partials.footer')
</body>
</html>
