<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Hamta Healthy Food</title>


    <!-- Tailwind CSS via Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <!-- Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        @keyframes slideInFromLeft {
            0% {
                transform: translateX(-120%);
                scale: 1;
                opacity: 0;
            }
            100% {
                transform: translateX(0);
                scale: 1.5;
                opacity: 1;
            }
        }

        @keyframes infiniteSpin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .animate-slide-in-left {
            animation: slideInFromLeft 1.2s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .animate-spin-infinite {
            animation: infiniteSpin 35s linear infinite;
            transform-origin: center center;
        }
    </style>
</head>
<body class="bg-hamta-cream text-hamta-black antialiased min-h-screen relative overflow-x-hidden selection:bg-hamta-green selection:text-white">

    <!-- ======================================================== -->
    <!-- DESKTOP BACKGROUND SVG: EXACT CURVED DIVIDER             -->
    <!-- ======================================================== -->
    <div class="fixed inset-0 pointer-events-none z-0 hidden lg:block overflow-hidden">
        <svg class="w-full h-full block" viewBox="0 0 100 100" preserveAspectRatio="none">
            <!-- 
                Curved divider matched directly to Figma:
                Starts top at 48.5%, bows outward to 58.1% in the middle, and ends at 48.6% at the bottom.
                Everything to the right is filled with Hamta Dark Green #0B412B.
            -->
            <path d="M 48.5 0 C 58.1 30, 58.1 70, 48.6 100 L 100 100 L 100 0 Z" fill="#0B412B" />
        </svg>
    </div>

    <!-- ======================================================== -->
    <!-- MAIN CONTENT LAYOUT                                      -->
    <!-- ======================================================== -->
    <div class="min-h-screen relative z-10 flex flex-col lg:flex-row">

        <!-- ========================================== -->
        <!-- LEFT SIDE: Cream Background with Salad     -->
        <!-- ========================================== -->
        <div class="w-full lg:w-[56%] flex items-center justify-start py-8 px-4 sm:px-8 lg:p-0 relative min-h-[340px] lg:min-h-screen">
            <!-- Salad Bowl: positioned touching/overlapping the left edge with slide-in and continuous rotation -->
            <div class="animate-slide-in-left relative w-72 sm:w-84 md:w-96 lg:w-[420px] xl:w-[460px] -ml-8 sm:-ml-12 lg:-ml-14 xl:-ml-16 select-none pointer-events-none">
                <div class="animate-spin-infinite">
                    <img 
                        src="/images/salad-1-clean.webp" 
                        alt="Salad Bowl Hamta" 
                        class="w-full h-auto object-contain block"
                        onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80';"
                    >
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- RIGHT SIDE: Dark Green Form Area           -->
        <!-- ========================================== -->
        <div class="w-full lg:w-[44%] bg-hamta-dark lg:bg-transparent min-h-screen flex flex-col justify-center items-center px-6 sm:px-12 py-12 relative">

            <div class="w-full max-w-[320px] sm:max-w-[340px] mx-auto">

                <!-- Brand Wordmark Logo -->
                <div class="mb-7 flex justify-center">
                    <img 
                        src="/images/hamta-logo-white.png" 
                        alt="hamtā" 
                        class="h-16 sm:h-20 lg:h-24 w-auto object-contain select-none"
                    >
                </div>

                <!-- Flash Messages & Validation Errors -->
                @if (session('error'))
                    <div class="mb-4 bg-red-100 border border-red-300 text-red-700 text-sm rounded-xl px-4 py-2.5"> 
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-300 text-green-700 text-sm rounded-xl px-4 py-2.5"> 
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-300 text-red-700 text-xs sm:text-sm rounded-xl px-4 py-2.5 space-y-1">
                        @foreach ($errors->all() as $err)
                            <p>• {{ $err }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
                    @csrf

                    <!-- Nama Pengguna Field -->
                    <div>
                        <label class="block text-white text-sm sm:text-base font-medium mb-1.5">
                            Nama Pengguna
                        </label>
                        <input 
                            type="text" 
                            name="username"
                            value="{{ old('username') }}"
                            class="w-full bg-white text-[#A0AEC0] placeholder-[#A0AEC0] rounded-xl px-4 py-2.5 sm:py-3 text-sm sm:text-base font-normal shadow-sm outline-none border border-transparent focus:border-hamta-green transition"
                            placeholder="Admin"
                        >
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label class="block text-white text-sm sm:text-base font-medium mb-1.5">
                            Password
                        </label>
                        <input 
                            type="password" 
                            name="password"
                            class="w-full bg-white text-[#A0AEC0] placeholder-[#A0AEC0] rounded-xl px-4 py-2.5 sm:py-3 text-sm sm:text-base font-normal shadow-sm outline-none border border-transparent focus:border-hamta-green transition"
                            placeholder="••••••"
                        >
                    </div>

                    <!-- Google reCAPTCHA v2 Widget -->
                    <div class="flex flex-col items-center justify-center my-2">
                        <div class="g-recaptcha flex justify-center overflow-hidden rounded-lg shadow-sm" data-sitekey="{{ config('services.recaptcha.site_key') ?? env('RECAPTCHA_SITE_KEY') }}"></div>
                    </div>

                    <!-- Submit Button: Masuk -->
                    <button 
                        type="submit" 
                        class="w-full bg-hamta-green hover:bg-[#58a851] text-white font-medium text-sm sm:text-base rounded-xl py-2.5 sm:py-3 shadow-sm hover:shadow transition cursor-pointer"
                    >
                        Masuk
                    </button>
                </form>

            </div>

        </div>

    </div>

</body>
</html>
