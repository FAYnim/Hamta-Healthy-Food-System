<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Status Pesanan — Hamta Healthy Food</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .tr-wrap { width: 100%; padding-bottom: 48px; }
        .tr-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin: 8px 0 24px; flex-wrap: wrap; }
        .tr-title-group { display: flex; align-items: center; gap: 14px; }
        .tr-back { display: grid; place-items: center; width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 1.5px solid rgba(11,65,43,.15); color: var(--green); text-decoration: none; transition: all .2s; }
        .tr-back:hover { background: #eaf5e7; transform: translateX(-2px); }
        .tr-head h1 { margin: 0; font-size: 26px; font-weight: 800; color: var(--green); letter-spacing: -.5px; }

        /* Live Indicator Badge */
        .tr-live-badge { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 999px; background: rgba(101,189,94,.15); border: 1.5px solid rgba(101,189,94,.4); font-size: 12px; font-weight: 700; color: var(--green); }
        .tr-live-dot { width: 8px; height: 8px; border-radius: 50%; background: #25D366; box-shadow: 0 0 0 0 rgba(37,211,102,.7); animation: trPulse 1.8s infinite; }
        @keyframes trPulse { 0% { box-shadow: 0 0 0 0 rgba(37,211,102,.7); } 70% { box-shadow: 0 0 0 8px rgba(37,211,102,0); } 100% { box-shadow: 0 0 0 0 rgba(37,211,102,0); } }

        /* Card Container */
        .tr-card { background: #fff; border: 2px solid var(--green); border-radius: 18px; padding: 24px; margin-bottom: 20px; box-shadow: 0 4px 16px rgba(11,65,43,.06); }
        .tr-hero { background: linear-gradient(135deg, #0B412B 0%, #175a3e 100%); color: #fff; border: none; }
        .tr-code-pill { display: inline-block; padding: 4px 12px; border-radius: 8px; background: rgba(255,255,255,.15); font-family: monospace; font-size: 16px; font-weight: 800; letter-spacing: 1px; color: #A8E6A1; }

        /* Stepper */
        .tr-stepper { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 24px 0 10px; position: relative; }
        .tr-step { display: flex; flex-direction: column; align-items: center; text-align: center; position: relative; z-index: 1; }
        .tr-step-circle { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 800; border: 2px solid #ddd; background: #fff; color: #888; transition: all .3s; }
        .tr-step.is-done .tr-step-circle { background: var(--lime); border-color: var(--lime); color: var(--green); }
        .tr-step.is-active .tr-step-circle { background: var(--green); border-color: var(--lime); color: #fff; box-shadow: 0 0 0 4px rgba(101,189,94,.3); }
        .tr-step-label { font-size: 11.5px; font-weight: 700; margin-top: 8px; color: #777; line-height: 1.25; }
        .tr-step.is-done .tr-step-label { color: var(--green); }
        .tr-step.is-active .tr-step-label { color: #fff; font-weight: 800; }
        .tr-hero .tr-step-label { color: rgba(255,255,255,.65); }
        .tr-hero .tr-step.is-done .tr-step-label { color: #A8E6A1; }

        /* Stepper connector lines */
        .tr-stepper-line { position: absolute; top: 19px; left: 12%; right: 12%; height: 3px; background: rgba(255,255,255,.2); z-index: 0; }
        .tr-stepper-progress { height: 100%; background: var(--lime); transition: width .4s ease; }

        /* Badge Status Tag */
        .tr-status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .tr-pill-diproses { background: #e0f2fe; color: #0369a1; }
        .tr-pill-siap { background: #dcfce7; color: #15803d; }
        .tr-pill-selesai { background: #e9e6df; color: #374151; }
        .tr-pill-batal { background: #fee2e2; color: #b91c1c; }

        .tr-pill-pay-terverifikasi { background: #dcfce7; color: #15803d; }
        .tr-pill-pay-menunggu { background: #fef3c7; color: #b45309; }
        .tr-pill-pay-ditolak { background: #fee2e2; color: #b91c1c; }

        /* Detail Grid */
        .tr-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 14px; }
        .tr-box { padding: 14px 16px; border-radius: 12px; background: #f9f7f2; border: 1px solid rgba(11,65,43,.08); }
        .tr-box-label { font-size: 11px; font-weight: 700; color: #6f6a5f; text-transform: uppercase; letter-spacing: .5px; }
        .tr-box-val { font-size: 14px; font-weight: 700; color: var(--ink); margin-top: 4px; }

        /* Item Row */
        .tr-item-row { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; padding: 10px 0; border-bottom: 1px dashed rgba(11,65,43,.12); font-size: 13.5px; }
        .tr-item-canceled { opacity: .55; text-decoration: line-through; }

        /* Action Buttons */
        .tr-wa-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 13px 20px; border-radius: 12px; background: #25D366; color: #fff; font-size: 14px; font-weight: 800; text-decoration: none; transition: all .2s; }
        .tr-wa-btn:hover { background: #20bd5a; transform: translateY(-1px); }

        .tr-refresh-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 10px 18px; border-radius: 10px; background: transparent; border: 1.5px solid var(--green); color: var(--green); font-size: 13px; font-weight: 700; cursor: pointer; transition: all .2s; }
        .tr-refresh-btn:hover { background: rgba(11,65,43,.06); }

        @media (max-width: 640px) {
            .tr-grid { grid-template-columns: 1fr; }
            .tr-head h1 { font-size: 21px; }
            .tr-stepper { gap: 4px; }
            .tr-step-circle { width: 32px; height: 32px; font-size: 12px; }
            .tr-step-label { font-size: 10px; }
        }
    </style>
</head>
<body>
<div class="page">
    @include('customer.partials.header')

    <div class="tr-wrap">
        <div class="tr-head">
            <div class="tr-title-group">
                <div>
                    <h1>Status Pesanan</h1>
                    <span style="font-size:12.5px;color:#6f6a5f">Pantau perkembangan pesanan Anda secara live</span>
                </div>
            </div>
        </div>

        @if (! $pesanan)
            <!-- Form Cari Kode Pesanan jika belum ada di URL -->
            <section class="tr-card" style="text-align:center;padding:36px 20px">
                <div class="flex justify-center mb-3">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100/60 flex items-center justify-center text-[#0B412B]">
                        <x-lucide-salad class="w-8 h-8" />
                    </div>
                </div>
                <h2 style="font-size:20px;font-weight:800;color:var(--green);margin:0 0 6px">Cari Pesanan Anda</h2>
                <p style="font-size:13.5px;color:#6f6a5f;max-width:440px;margin:0 auto 20px">
                    Masukkan nomor/kode pesanan yang Anda terima setelah checkout (contoh: <code>ORD-xxxxxx</code>) untuk melihat status terkini.
                </p>

                <form method="GET" action="{{ route('customer.status_pesanan') }}" style="max-width:380px;margin:0 auto;display:flex;gap:8px" onsubmit="var k=this.kode.value.trim();if(k){this.action='{{ url('status-pesanan') }}/'+encodeURIComponent(k);return true;}return false;">
                    <input type="text" name="kode" value="{{ $kode }}" placeholder="Contoh: ORD-20261101-001" required
                           style="flex:1 1 auto;padding:12px 16px;border-radius:12px;border:1.5px solid rgba(11,65,43,.25);font-size:14px;font-weight:700;outline:none">
                    <button type="submit" style="padding:12px 20px;border-radius:12px;background:var(--green);color:#fff;font-weight:800;font-size:14px;border:none;cursor:pointer">
                        Lacak
                    </button>
                </form>

                <div id="tr-recent-order-box" style="margin-top:24px;display:none">
                    <span style="font-size:12px;color:#888">Atau buka pesanan terakhir dari perangkat ini:</span>
                    <div style="margin-top:8px">
                        <a id="tr-recent-order-link" href="#" style="font-size:13px;font-weight:700;color:var(--green);text-decoration:underline"></a>
                    </div>
                </div>
            </section>
        @else
            @php
                $rp = fn($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
                $isBatal = $pesanan->status_pesanan === 'batal';

                // Hitung langkah stepper
                // 1: Diterima, 2: Diproses, 3: Siap, 4: Selesai
                $stepIndex = 1;
                if ($pesanan->status_pesanan === 'diproses') {
                    $stepIndex = 2;
                } elseif ($pesanan->status_pesanan === 'siap') {
                    $stepIndex = 3;
                } elseif ($pesanan->status_pesanan === 'selesai') {
                    $stepIndex = 4;
                }

                $progressPct = ($stepIndex - 1) / 3 * 100;

                // Teks status besar
                $statusMap = [
                    'diproses' => ['title' => 'Pesanan Sedang Diproses', 'sub' => 'Chef Hamta sedang meracik bahan segar untuk pesanan Anda.'],
                    'siap' => ['title' => ($pesanan->metode_ambil === 'delivery' ? 'Pesanan Siap & Diantar' : 'Pesanan Siap Diambil'), 'sub' => ($pesanan->metode_ambil === 'delivery' ? 'Kurir sedang bersiap/mengantar pesanan ke alamat tujuan.' : 'Pesanan sudah siap di outlet, silakan ambil di kasir.')],
                    'selesai' => ['title' => 'Pesanan Telah Selesai', 'sub' => 'Terima kasih telah menikmati makanan sehat dari Hamta Healthy Food!'],
                    'batal' => ['title' => 'Pesanan Dibatalkan', 'sub' => 'Pesanan ini telah dibatalkan oleh pihak resto. Hubungi kasir untuk info lebih lanjut.'],
                ];
                $curStatusInfo = $statusMap[$pesanan->status_pesanan] ?? ['title' => 'Pesanan Diterima', 'sub' => 'Pesanan telah masuk ke sistem kami.'];
            @endphp

            <!-- Hero Status Card (Live Tracking Banner) -->
            <section class="tr-card tr-hero" id="tr-hero-card">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
                    <div>
                        <span style="font-size:12px;color:#A8E6A1;font-weight:700;text-transform:uppercase;letter-spacing:.8px">Kode Pesanan</span>
                        <div style="margin-top:4px">
                            <span class="tr-code-pill" id="tr-code">{{ $pesanan->kode_pesanan }}</span>
                        </div>
                    </div>
                    <div style="text-align:right">
                        <span style="font-size:11px;color:rgba(255,255,255,.65)">Waktu Pemesanan:</span>
                        <div style="font-size:13px;font-weight:700;color:#fff" id="tr-time">
                            {{ $pesanan->created_at ? $pesanan->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : 'Baru saja' }}
                        </div>
                    </div>
                </div>

                <div style="margin-top:20px">
                    <h2 id="tr-status-title" style="margin:0;font-size:22px;font-weight:800;letter-spacing:-.3px">{{ $curStatusInfo['title'] }}</h2>
                    <p id="tr-status-sub" style="margin:6px 0 0;font-size:13.5px;color:rgba(255,255,255,.85);line-height:1.4">{{ $curStatusInfo['sub'] }}</p>
                </div>

                @if (! $isBatal)
                    <!-- Stepper Visual -->
                    <div class="tr-stepper" id="tr-stepper-box">
                        <div class="tr-stepper-line">
                            <div class="tr-stepper-progress" id="tr-stepper-bar" style="width:{{ $progressPct }}%"></div>
                        </div>

                        <!-- Step 1 -->
                        <div class="tr-step {{ $stepIndex >= 1 ? ($stepIndex === 1 ? 'is-active' : 'is-done') : '' }}" id="tr-step-1">
                            <div class="tr-step-circle">1</div>
                            <span class="tr-step-label">Diterima</span>
                        </div>
                        <!-- Step 2 -->
                        <div class="tr-step {{ $stepIndex >= 2 ? ($stepIndex === 2 ? 'is-active' : 'is-done') : '' }}" id="tr-step-2">
                            <div class="tr-step-circle">2</div>
                            <span class="tr-step-label">Diproses</span>
                        </div>
                        <!-- Step 3 -->
                        <div class="tr-step {{ $stepIndex >= 3 ? ($stepIndex === 3 ? 'is-active' : 'is-done') : '' }}" id="tr-step-3">
                            <div class="tr-step-circle">3</div>
                            <span class="tr-step-label">Siap</span>
                        </div>
                        <!-- Step 4 -->
                        <div class="tr-step {{ $stepIndex >= 4 ? ($stepIndex === 4 ? 'is-active' : 'is-done') : '' }}" id="tr-step-4">
                            <div class="tr-step-circle">4</div>
                            <span class="tr-step-label">Selesai</span>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2" style="margin-top:16px;padding:10px 14px;border-radius:10px;background:rgba(239,68,68,.2);border:1px solid rgba(239,68,68,.4);font-size:13px;color:#fecaca">
                        <x-lucide-alert-triangle class="w-4 h-4 shrink-0 text-red-300" />
                        <span>Pesanan ini berstatus <strong>Dibatalkan</strong>.</span>
                    </div>
                @endif
            </section>

            <!-- Card Informasi Pengantaran & Pembayaran -->
            <section class="tr-card">
                <h3 style="margin:0 0 12px;font-size:16px;font-weight:800;color:var(--green)">Rincian Status &amp; Pengambilan</h3>

                <div class="tr-grid">
                    <div class="tr-box">
                        <span class="tr-box-label">Status Pembayaran</span>

                        @php
                            $qrUrl = null;
                            if (! empty($pesanan->midtrans_response['qr_url'])) {
                                $qrUrl = $pesanan->midtrans_response['qr_url'];
                            } elseif (! empty($pesanan->midtrans_response['actions'])) {
                                foreach ($pesanan->midtrans_response['actions'] as $act) {
                                    if (($act['name'] ?? '') === 'generate-qr-code') {
                                        $qrUrl = $act['url'] ?? null;
                                        break;
                                    }
                                }
                            }
                            $expiryTime = $pesanan->midtrans_response['expiry_time'] ?? null;
                            $isExpiredInitial = false;
                            if ($expiryTime && in_array($pesanan->status_pembayaran, ['menunggu_verifikasi', 'pending'])) {
                                try {
                                    $isExpiredInitial = now()->greaterThanOrEqualTo(\Carbon\Carbon::parse($expiryTime));
                                } catch (\Throwable) {
                                    $isExpiredInitial = false;
                                }
                            }

                            $isPendingOrMenunggu = in_array($pesanan->status_pembayaran, ['menunggu_verifikasi', 'pending']);
                            $isMidtrans = $pesanan->metode_bayar === 'midtrans';
                            $showQr = $isPendingOrMenunggu && $isMidtrans && ! empty($qrUrl) && ! $isExpiredInitial;
                            $showExpired = ($isExpiredInitial || in_array($pesanan->status_pembayaran, ['ditolak', 'expired'])) && $isMidtrans;
                            $needUploadBukti = $pesanan->status_pembayaran === 'menunggu_verifikasi'
                                && in_array($pesanan->metode_bayar, ['qris_statis', 'qris'])
                                && empty($pesanan->bukti_bayar);
                        @endphp

                        <!-- Wadah QRIS Otomatis Midtrans -->
                        <div id="tr-midtrans-qr-wrap" style="display: {{ $showQr ? 'block' : 'none' }}; margin: 12px 0 16px; text-align: center; padding: 18px 16px; background: #fff; border: 2px solid var(--green); border-radius: 14px; box-shadow: 0 4px 12px rgba(11,65,43,.08);">
                            <span style="display:block;font-size:12px;font-weight:800;color:var(--green);letter-spacing:.6px;margin-bottom:12px">SCAN UNTUK MEMBAYAR (QRIS)</span>
                            
                            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;margin:0 auto">
                                <!-- Countdown Badge -->
                                <div id="tr-expiry-badge" style="display:inline-flex;align-items:center;gap:6px;background:#fef2f2;border:1px solid #fecaca;padding:6px 16px;border-radius:20px;font-size:12px;font-weight:700;color:#991b1b">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <span>Kedaluwarsa dalam: <span id="tr-expiry-countdown">--:--</span></span>
                                </div>

                                <div style="display:inline-block;padding:12px;background:#fbf8f0;border-radius:14px;border:1px dashed rgba(11,65,43,.3);box-shadow:0 2px 8px rgba(0,0,0,.04)">
                                    <img id="tr-midtrans-qr-img" src="{{ $qrUrl ?? '' }}" alt="QRIS Midtrans" style="width:200px;height:200px;display:block;margin:0 auto;object-fit:contain;" />
                                </div>
                            </div>

                            <p style="margin:12px 0 0;font-size:11.5px;color:#4a5a52;line-height:1.4">
                                Buka aplikasi e-wallet (GoPay, OVO, Dana) atau m-Banking Anda, lalu scan QR di atas.<br>
                                <strong>Status pembayaran akan diverifikasi otomatis tanpa perlu upload bukti transfer.</strong>
                            </p>
                        </div>

                        <!-- Wadah Jika QRIS Expired / Kedaluwarsa -->
                        <div id="tr-midtrans-expired-wrap" style="display: {{ $showExpired ? 'block' : 'none' }}; margin: 12px 0 16px; text-align: center; padding: 18px 16px; background: #fff; border: 2px dashed #ef4444; border-radius: 14px;">
                            <div style="width:44px;height:44px;border-radius:50%;background:#fee2e2;color:#dc2626;display:inline-flex;align-items:center;justify-content:center;margin-bottom:8px">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            </div>
                            <h4 style="margin:0 0 4px;font-size:14px;font-weight:800;color:#991b1b">Batas Waktu Pembayaran Habis</h4>
                            <p style="margin:0 0 12px;font-size:12px;color:#6f6a5f;line-height:1.4">
                                Kode QRIS ini telah kedaluwarsa. Silakan lakukan pemesanan ulang untuk mendapatkan kode pembayaran baru.
                            </p>
                            <a href="{{ route('customer.daftar_menu') }}" style="display:inline-block;padding:8px 16px;background:var(--green);color:#fff;border-radius:8px;font-size:12px;font-weight:700;text-decoration:none">
                                Pesan Ulang Menu
                            </a>
                        </div>

                        <!-- Wadah Form Upload Bukti QRIS Statis (Jika Belum Upload) -->
                        <div id="tr-qris-statis-upload-wrap" style="display: {{ $needUploadBukti ? 'block' : 'none' }}; margin: 12px 0 16px; padding: 16px; background: #fff; border: 2px dashed #f59e0b; border-radius: 14px; text-align: center;">
                            <span style="display:block;font-size:12px;font-weight:800;color:#b45309;letter-spacing:.6px;margin-bottom:8px">PEMBAYARAN QRIS MANUAL (STATIS)</span>
                            <p style="margin:0 0 12px;font-size:12px;color:#4a5a52;line-height:1.4">
                                Silakan scan QRIS Hamta di bawah ini dan unggah foto/screenshot bukti transfer agar pesanan dapat segera diproses:
                            </p>

                            <div style="display:inline-block;padding:10px;background:#fbf8f0;border-radius:12px;border:1px solid rgba(11,65,43,.2);margin-bottom:12px">
                                <svg width="180" height="180" viewBox="0 0 260 260" fill="none" xmlns="http://www.w3.org/2000/svg" style="border-radius:8px;background:#fff;padding:8px">
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
                                    <rect x="44" y="84" width="8" height="8" fill="#0B412B"/>
                                    <rect x="44" y="100" width="8" height="8" fill="#0B412B"/>
                                    <rect x="88" y="24" width="12" height="12" fill="#0B412B"/>
                                    <rect x="108" y="24" width="12" height="12" fill="#0B412B"/>
                                    <rect x="136" y="24" width="12" height="12" fill="#0B412B"/>
                                    <rect x="20" y="88" width="12" height="12" fill="#0B412B"/>
                                    <rect x="64" y="88" width="12" height="12" fill="#0B412B"/>
                                    <rect x="88" y="88" width="12" height="12" fill="#0B412B"/>
                                    <rect x="168" y="88" width="12" height="12" fill="#0B412B"/>
                                    <rect x="228" y="88" width="12" height="12" fill="#0B412B"/>
                                    <rect x="24" y="108" width="12" height="12" fill="#0B412B"/>
                                    <rect x="60" y="108" width="16" height="8" fill="#0B412B"/>
                                    <rect x="84" y="108" width="12" height="12" fill="#0B412B"/>
                                    <rect x="164" y="108" width="12" height="12" fill="#0B412B"/>
                                    <rect x="216" y="108" width="24" height="12" fill="#0B412B"/>
                                    <rect x="20" y="128" width="12" height="12" fill="#0B412B"/>
                                    <rect x="60" y="128" width="16" height="8" fill="#0B412B"/>
                                    <rect x="164" y="128" width="16" height="16" fill="#0B412B"/>
                                    <rect x="24" y="148" width="12" height="12" fill="#0B412B"/>
                                    <rect x="64" y="148" width="12" height="12" fill="#0B412B"/>
                                    <rect x="88" y="148" width="12" height="12" fill="#0B412B"/>
                                    <rect x="164" y="148" width="12" height="12" fill="#0B412B"/>
                                    <rect x="228" y="148" width="12" height="12" fill="#0B412B"/>
                                    <circle cx="130" cy="130" r="24" fill="#0B412B"/>
                                    <path d="M124 135c0-6 6-11 12-11 0 6-6 11-12 11Z" fill="#65BD5E"/>
                                    <path d="M136 135c0-6-6-11-12-11 0 6 6 11 12 11Z" fill="#A8E6A1"/>
                                </svg>
                                <span style="display:block;font-size:11px;font-weight:700;color:var(--green);margin-top:4px">NMID: ID1024398129038</span>
                            </div>

                            <form id="tr-upload-bukti-form" method="POST" action="{{ route('customer.status_pesanan.upload_bukti', $pesanan->kode_pesanan) }}" enctype="multipart/form-data">
                                @csrf
                                <div style="margin-bottom:10px">
                                    <input type="file" name="bukti_bayar" id="tr-bukti-file" accept="image/png,image/jpeg,image/webp" required style="font-size:12px;max-width:240px;margin:0 auto;display:block" />
                                    <small style="color:#7a8a80;font-size:11px">Format: JPG, PNG, WEBP (maks. 5MB)</small>
                                </div>
                                <button type="submit" id="tr-submit-bukti-btn" style="padding:8px 16px;background:var(--green);color:#fff;border-radius:10px;font-size:12px;font-weight:700;border:none;cursor:pointer">
                                    Unggah Bukti Transfer
                                </button>
                            </form>
                            <div id="tr-upload-bukti-msg" style="display:none;margin-top:10px;font-size:12px;font-weight:700;color:var(--green)"></div>
                        </div>

                        <!-- Notifikasi Bukti Sudah Diunggah -->
                        <div id="tr-bukti-uploaded-wrap" style="display: {{ (!empty($pesanan->bukti_bayar) && $pesanan->status_pembayaran === 'menunggu_verifikasi') ? 'block' : 'none' }}; margin: 10px 0 14px; padding: 12px; border-radius: 10px; background: #ecfdf5; border: 1px solid #a7f3d0; font-size: 12px; color: #065f46;">
                            ✓ <strong>Bukti transfer telah diunggah:</strong> Menunggu verifikasi tim kasir Hamta.
                            @if (!empty($pesanan->bukti_bayar))
                                <div style="margin-top:6px">
                                    <a href="{{ asset('storage/'.$pesanan->bukti_bayar) }}" target="_blank" style="color:var(--green);font-weight:700;text-decoration:underline">Lihat Foto Bukti Transfer ↗</a>
                                </div>
                            @endif
                        </div>

                        <div style="margin-top:6px" id="tr-pay-badge-wrap">
                            @if ($pesanan->metode_bayar === 'tunai')
                                <span class="tr-status-pill tr-pill-siap inline-flex items-center gap-1.5"><x-lucide-banknote class="w-3.5 h-3.5" /> <span>Bayar di Tempat (Tunai)</span></span>
                            @elseif (in_array($pesanan->status_pembayaran, ['terverifikasi', 'settlement']))
                                <span class="tr-status-pill tr-pill-pay-terverifikasi inline-flex items-center gap-1.5"><x-lucide-check-circle class="w-3.5 h-3.5" /> <span>{{ $pesanan->metode_bayar === 'midtrans' ? 'QRIS Otomatis — Lunas' : 'QRIS — Terverifikasi (Lunas)' }}</span></span>
                            @elseif (in_array($pesanan->status_pembayaran, ['ditolak', 'expired', 'gagal']))
                                <span class="tr-status-pill tr-pill-pay-ditolak inline-flex items-center gap-1.5"><x-lucide-x-circle class="w-3.5 h-3.5" /> <span>Pembayaran Ditolak / Kedaluwarsa</span></span>
                            @elseif ($pesanan->metode_bayar === 'midtrans')
                                <span class="tr-status-pill tr-pill-pay-menunggu inline-flex items-center gap-1.5"><x-lucide-clock class="w-3.5 h-3.5" /> <span>QRIS Otomatis — Menunggu Pembayaran</span></span>
                            @else
                                <span class="tr-status-pill tr-pill-pay-menunggu inline-flex items-center gap-1.5"><x-lucide-clock class="w-3.5 h-3.5" /> <span>QRIS — Menunggu Verifikasi Admin</span></span>
                            @endif
                        </div>
                        <div style="margin-top:6px;font-size:12px;color:#6f6a5f">
                            Metode: {{ $pesanan->metode_bayar === 'midtrans' ? 'QRIS Otomatis (Midtrans)' : ($pesanan->metode_bayar === 'qris_statis' || $pesanan->metode_bayar === 'qris' ? 'QRIS Manual (Statis)' : 'Tunai') }}
                        </div>
                    </div>

                    <div class="tr-box">
                        <span class="tr-box-label">Metode Pengambilan</span>
                        <div class="tr-box-val flex items-center gap-1.5" id="tr-take-val">
                            @if ($pesanan->metode_ambil === 'delivery')
                                <x-lucide-truck class="w-4 h-4 text-emerald-700" />
                                <span>Delivery (Diantar Kurir)</span>
                            @else
                                <x-lucide-store class="w-4 h-4 text-emerald-700" />
                                <span>Pickup (Ambil di Outlet)</span>
                            @endif
                        </div>
                        <div style="margin-top:6px;font-size:12.5px;color:#4a5a52" id="tr-take-dest">
                            @if ($pesanan->metode_ambil === 'delivery')
                                Alamat: {{ $pesanan->alamat_delivery ?: 'Sesuai pesanan' }}
                            @else
                                Cabang: {{ $pesanan->cabang?->nama_cabang ?? 'Hamta Eatery' }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="tr-grid">
                    <div class="tr-box">
                        <span class="tr-box-label">Nama Pelanggan</span>
                        <div class="tr-box-val">{{ $pesanan->nama_pelanggan ?: 'Tamu Hamta' }}</div>
                        <div style="margin-top:4px;font-size:12px;color:#6f6a5f">{{ $pesanan->no_whatsapp ?: '-' }}</div>
                    </div>

                    <div class="tr-box">
                        <span class="tr-box-label">Total Tagihan</span>
                        <div class="tr-box-val" id="tr-total-val" style="font-size:17px;color:var(--green)">
                            {{ $rp($pesanan->total_bayar) }}
                        </div>
                        <div style="margin-top:4px;font-size:11.5px;color:#6f6a5f">Sudah termasuk PPN</div>
                    </div>
                </div>
            </section>

            <!-- Card Rincian Item Menu -->
            <section class="tr-card">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                    <h3 style="margin:0;font-size:16px;font-weight:800;color:var(--green)">Menu yang Dipesan</h3>
                    <span style="font-size:12px;color:#6f6a5f">{{ $pesanan->detailPesanan->count() }} Item Menu</span>
                </div>

                <div id="tr-items-list">
                    @foreach ($pesanan->detailPesanan as $item)
                        @php $isItemCanceled = $item->status_item === 'dibatalkan'; @endphp
                        <div class="tr-item-row {{ $isItemCanceled ? 'tr-item-canceled' : '' }}">
                            <div>
                                <strong style="color:var(--ink)">{{ $item->nama_item }}</strong>
                                <span style="margin-left:6px;font-size:12px;color:#6f6a5f">x{{ $item->jumlah }}</span>
                                @if ($item->komponenSalad->isNotEmpty())
                                    <div style="font-size:11.5px;color:#6f6a5f;margin-top:2px">
                                        Komponen: {{ $item->komponenSalad->pluck('nama_komponen')->implode(', ') }}
                                    </div>
                                @endif
                                @if ($isItemCanceled)
                                    <div class="flex items-center gap-1" style="font-size:11px;color:#c0392b;font-weight:700;margin-top:2px">
                                        <x-lucide-alert-circle class="w-3.5 h-3.5 text-red-600 shrink-0" />
                                        <span>Dibatalkan resto (stok habis)</span>
                                    </div>
                                @endif
                            </div>
                            <div style="font-weight:700">
                                {{ $rp($item->subtotal_item) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top:14px;padding-top:10px;border-top:1.5px solid rgba(11,65,43,.1);display:flex;justify-content:space-between;align-items:baseline">
                    <span style="font-size:15px;font-weight:800">Total Pembayaran:</span>
                    <span id="tr-bottom-total" style="font-size:18px;font-weight:800;color:var(--green)">{{ $rp($pesanan->total_bayar) }}</span>
                </div>
            </section>

            <!-- Card Kontak / Bantuan Kasir -->
            <section class="tr-card" style="text-align:center">
                <h3 style="margin:0 0 6px;font-size:16px;font-weight:800;color:var(--green)">Butuh Bantuan dengan Pesanan Ini?</h3>
                <p style="margin:0 0 16px;font-size:13px;color:#6f6a5f">
                    Hubungi tim kasir atau admin Hamta Healthy Food jika ingin menanyakan status kurir, konfirmasi pembayaran, atau info lainnya.
                </p>

                @php
                    $waMsg = "Halo Admin Hamta Healthy Food, saya ingin menanyakan pesanan saya:\n" .
                             "• Kode Pesanan: " . $pesanan->kode_pesanan . "\n" .
                             "• Nama: " . ($pesanan->nama_pelanggan ?: 'Pelanggan') . "\n" .
                             "• Total: " . $rp($pesanan->total_bayar) . "\n\nTerima kasih!";
                    $waAdminUrl = 'https://wa.me/6281234567890?text=' . rawurlencode($waMsg);
                @endphp

                <div style="max-width:360px;margin:0 auto;display:flex;flex-direction:column;gap:10px">
                    <a href="{{ $waAdminUrl }}" target="_blank" class="tr-wa-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67M9.53 7.34C9.36 7.34 9.09 7.41 8.86 7.65C8.63 7.9 7.99 8.5 7.99 9.71C7.99 10.93 8.88 12.1 9 12.27C9.13 12.44 10.74 14.92 13.2 15.97C13.78 16.22 14.24 16.38 14.6 16.49C15.19 16.68 15.72 16.65 16.14 16.59C16.61 16.51 17.58 16 17.79 15.41C17.99 14.83 17.99 14.33 17.93 14.22C17.87 14.12 17.7 14.05 17.44 13.93C17.19 13.8 15.93 13.18 15.7 13.1C15.47 13.01 15.3 12.97 15.13 13.22C14.96 13.47 14.47 14.05 14.33 14.22C14.18 14.38 14.04 14.4 13.79 14.27C13.54 14.15 12.72 13.88 11.75 13.02C10.99 12.35 10.48 11.51 10.33 11.26C10.19 11.01 10.31 10.88 10.44 10.75C10.55 10.64 10.69 10.46 10.82 10.31C10.95 10.16 11 10.05 11.08 9.89C11.16 9.72 11.12 9.57 11.06 9.45C11 9.33 10.5 8.12 10.3 7.62C10.1 7.15 9.9 7.21 9.74 7.21C9.6 7.21 9.43 7.21 9.26 7.21L9.53 7.34Z"/></svg>
                        <span>Chat WhatsApp Admin Kasir</span>
                    </a>

                    <button type="button" class="tr-refresh-btn" id="tr-manual-refresh">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>
                        <span>Segarkan Status Sekarang</span>
                    </button>
                </div>
            </section>
        @endif
    </div>
</div>

@if ($pesanan)
<script>
(function () {
    var orderCode = @json($pesanan->kode_pesanan);
    var curStatus = @json($pesanan->status_pesanan);
    var curPayStatus = @json($pesanan->status_pembayaran);
    var initialExpiry = @json($expiryTime ? \Carbon\Carbon::parse($expiryTime)->toISOString() : null);
    var countdownTimer = null;

    function startCountdown(expiryIso) {
        if (!expiryIso) return;
        var targetDate = new Date(expiryIso).getTime();
        var countdownEl = document.getElementById('tr-expiry-countdown');
        var qrWrap = document.getElementById('tr-midtrans-qr-wrap');
        var expWrap = document.getElementById('tr-midtrans-expired-wrap');

        if (countdownTimer) clearInterval(countdownTimer);

        function tick() {
            var now = Date.now();
            var distance = targetDate - now;

            if (distance <= 0) {
                if (countdownEl) countdownEl.textContent = '00:00';
                if (qrWrap) qrWrap.style.display = 'none';
                if (expWrap && (curPayStatus === 'pending' || curPayStatus === 'menunggu_verifikasi')) {
                    expWrap.style.display = 'block';
                }
                if (countdownTimer) clearInterval(countdownTimer);
                return;
            }

            var m = Math.floor(distance / (1000 * 60));
            var s = Math.floor((distance % (1000 * 60)) / 1000);
            var mStr = (m < 10 ? '0' : '') + m;
            var sStr = (s < 10 ? '0' : '') + s;

            if (countdownEl) countdownEl.textContent = mStr + ':' + sStr;
        }

        tick();
        countdownTimer = setInterval(tick, 1000);
    }

    if (initialExpiry) {
        startCountdown(initialExpiry);
    }

    var rupiah = function (n) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));
    };

    function updateView(data) {
        if (!data || !data.ok) return;

        var changed = (data.status_pesanan !== curStatus) || (data.status_pembayaran !== curPayStatus);
        curStatus = data.status_pesanan;
        curPayStatus = data.status_pembayaran;

        var titleEl = document.getElementById('tr-status-title');
        var subEl = document.getElementById('tr-status-sub');
        var barEl = document.getElementById('tr-stepper-bar');
        var heroEl = document.getElementById('tr-hero-card');

        // Status Map
        var statusMap = {
            'diproses': {
                step: 2,
                title: 'Pesanan Sedang Diproses',
                sub: 'Chef Hamta sedang meracik bahan segar untuk pesanan Anda.'
            },
            'siap': {
                step: 3,
                title: data.metode_ambil === 'delivery' ? 'Pesanan Siap & Diantar' : 'Pesanan Siap Diambil',
                sub: data.metode_ambil === 'delivery' ? 'Kurir sedang bersiap / mengantar pesanan ke alamat Anda.' : 'Pesanan sudah siap di outlet, silakan ambil di kasir.'
            },
            'selesai': {
                step: 4,
                title: 'Pesanan Telah Selesai',
                sub: 'Terima kasih telah menikmati makanan sehat dari Hamta Healthy Food!'
            },
            'batal': {
                step: 0,
                title: 'Pesanan Dibatalkan',
                sub: 'Pesanan ini telah dibatalkan oleh pihak resto. Hubungi kasir untuk info lebih lanjut.'
            }
        };

        var cur = statusMap[data.status_pesanan] || { step: 1, title: 'Pesanan Diterima', sub: 'Pesanan telah masuk ke sistem kami.' };

        if (titleEl) titleEl.textContent = cur.title;
        if (subEl) subEl.textContent = cur.sub;

        if (cur.step > 0 && barEl) {
            var pct = (cur.step - 1) / 3 * 100;
            barEl.style.width = pct + '%';

            for (var i = 1; i <= 4; i++) {
                var st = document.getElementById('tr-step-' + i);
                if (!st) continue;
                st.className = 'tr-step ' + (cur.step >= i ? (cur.step === i ? 'is-active' : 'is-done') : '');
            }
        }

        // Update QRIS Midtrans
        var qrWrap = document.getElementById('tr-midtrans-qr-wrap');
        var qrImg = document.getElementById('tr-midtrans-qr-img');
        var expWrap = document.getElementById('tr-midtrans-expired-wrap');

        var isPendingPay = (data.status_pembayaran === 'menunggu_verifikasi' || data.status_pembayaran === 'pending');
        var isMidtransPay = (data.metode_bayar === 'midtrans');

        if (data.expiry_time) {
            startCountdown(data.expiry_time);
        }

        if (qrWrap) {
            if (isPendingPay && isMidtransPay && data.qr_url && !data.is_expired) {
                if (qrImg) qrImg.src = data.qr_url;
                qrWrap.style.display = 'block';
                if (expWrap) expWrap.style.display = 'none';
            } else {
                qrWrap.style.display = 'none';
                if (isMidtransPay && (data.is_expired || data.status_pembayaran === 'expired' || data.status_pembayaran === 'ditolak')) {
                    if (expWrap) expWrap.style.display = 'block';
                }
            }
        }

        // Update Wadah Upload QRIS Statis
        var uploadWrap = document.getElementById('tr-qris-statis-upload-wrap');
        var uploadedWrap = document.getElementById('tr-bukti-uploaded-wrap');
        var isQrisStatis = (data.metode_bayar === 'qris_statis' || data.metode_bayar === 'qris');
        if (uploadWrap && uploadedWrap) {
            if (data.status_pembayaran === 'menunggu_verifikasi' && isQrisStatis && !data.bukti_bayar) {
                uploadWrap.style.display = 'block';
                uploadedWrap.style.display = 'none';
            } else if (data.status_pembayaran === 'menunggu_verifikasi' && isQrisStatis && data.bukti_bayar) {
                uploadWrap.style.display = 'none';
                uploadedWrap.style.display = 'block';
            } else {
                uploadWrap.style.display = 'none';
                if (data.status_pembayaran === 'terverifikasi' || data.status_pembayaran === 'settlement') {
                    uploadedWrap.style.display = 'none';
                }
            }
        }

        // Update Badge Pembayaran
        var payWrap = document.getElementById('tr-pay-badge-wrap');
        if (payWrap) {
            if (data.metode_bayar === 'tunai') {
                payWrap.innerHTML = '<span class="tr-status-pill tr-pill-siap inline-flex items-center gap-1.5"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/></svg><span>Bayar di Tempat (Tunai)</span></span>';
            } else if (data.status_pembayaran === 'terverifikasi' || data.status_pembayaran === 'settlement') {
                var labelPay = data.metode_bayar === 'midtrans' ? 'QRIS Otomatis — Lunas' : 'QRIS — Terverifikasi (Lunas)';
                payWrap.innerHTML = '<span class="tr-status-pill tr-pill-pay-terverifikasi inline-flex items-center gap-1.5"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><span>' + labelPay + '</span></span>';
            } else if (data.status_pembayaran === 'ditolak' || data.status_pembayaran === 'expired' || data.status_pembayaran === 'gagal') {
                payWrap.innerHTML = '<span class="tr-status-pill tr-pill-pay-ditolak inline-flex items-center gap-1.5"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg><span>Pembayaran Ditolak / Kedaluwarsa</span></span>';
            } else if (data.metode_bayar === 'midtrans') {
                payWrap.innerHTML = '<span class="tr-status-pill tr-pill-pay-menunggu inline-flex items-center gap-1.5"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>QRIS Otomatis — Menunggu Pembayaran</span></span>';
            } else {
                payWrap.innerHTML = '<span class="tr-status-pill tr-pill-pay-menunggu inline-flex items-center gap-1.5"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>QRIS — Menunggu Verifikasi Admin</span></span>';
            }
        }

        // Update Total
        var totVal = document.getElementById('tr-total-val');
        var botTot = document.getElementById('tr-bottom-total');
        if (totVal) totVal.textContent = rupiah(data.total_bayar);
        if (botTot) botTot.textContent = rupiah(data.total_bayar);

        if (changed && window.hamtaToast) {
            window.hamtaToast('Status pesanan diperbarui: ' + cur.title);
        }
    }

    function pollStatus() {
        if (!orderCode) return;
        fetch("{{ url('api/pesanan') }}/" + encodeURIComponent(orderCode) + "/status")
            .then(function (res) { return res.json(); })
            .then(function (data) {
                updateView(data);
            })
            .catch(function () {});
    }

    // Polling setiap 4 detik untuk koneksi live ke admin
    var intervalId = setInterval(pollStatus, 4000);

    var uploadForm = document.getElementById('tr-upload-bukti-form');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = document.getElementById('tr-submit-bukti-btn');
            var msg = document.getElementById('tr-upload-bukti-msg');
            var fileInput = document.getElementById('tr-bukti-file');
            if (!fileInput || !fileInput.files || !fileInput.files[0]) {
                alert('Pilih file bukti transfer terlebih dahulu.');
                return;
            }
            var formData = new FormData(uploadForm);
            if (btn) { btn.disabled = true; btn.textContent = 'Mengunggah...'; }
            fetch(uploadForm.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.ok) {
                    uploadForm.style.display = 'none';
                    if (msg) {
                        msg.style.display = 'block';
                        msg.textContent = 'Bukti berhasil diunggah, menunggu verifikasi kasir.';
                    }
                    var uploadedWrap = document.getElementById('tr-bukti-uploaded-wrap');
                    if (uploadedWrap) uploadedWrap.style.display = 'block';
                    if (window.hamtaToast) window.hamtaToast('Bukti transfer berhasil diunggah!');
                } else {
                    alert(data.message || 'Gagal mengunggah bukti.');
                    if (btn) { btn.disabled = false; btn.textContent = 'Unggah Bukti Transfer'; }
                }
            })
            .catch(function () {
                alert('Terjadi kesalahan koneksi.');
                if (btn) { btn.disabled = false; btn.textContent = 'Unggah Bukti Transfer'; }
            });
        });
    }

    var refreshBtn = document.getElementById('tr-manual-refresh');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            this.disabled = true;
            this.textContent = 'Memeriksa...';
            var self = this;
            fetch("{{ url('api/pesanan') }}/" + encodeURIComponent(orderCode) + "/status")
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    updateView(data);
                    if (window.hamtaToast) window.hamtaToast('Status berhasil disegarkan.');
                })
                .finally(function () {
                    self.disabled = false;
                    self.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg><span>Segarkan Status Sekarang</span>';
                });
        });
    }
})();
</script>
@else
<script>
(function () {
    try {
        var last = JSON.parse(localStorage.getItem('hamta_last_order') || 'null');
        if (last && last.order && last.order.kode_pesanan) {
            var box = document.getElementById('tr-recent-order-box');
            var link = document.getElementById('tr-recent-order-link');
            if (box && link) {
                box.style.display = 'block';
                link.href = "{{ url('status-pesanan') }}/" + encodeURIComponent(last.order.kode_pesanan);
                link.textContent = 'Pesanan #' + last.order.kode_pesanan + ' (' + (last.order.nama_pelanggan || 'Saya') + ') ➔';
            }
        }
    } catch (e) {}
})();
</script>
@endif

@include('customer.partials.footer')
</body>
</html>

