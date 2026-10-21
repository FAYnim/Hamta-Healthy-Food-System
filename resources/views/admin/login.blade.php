<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Hamta Healthy Food</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via Vite -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
    </style>
</head>
<body class="bg-[#F7F2E7] text-[#010101] antialiased min-h-screen relative overflow-x-hidden selection:bg-[#65BD5E] selection:text-white">

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
            <!-- Salad Bowl: positioned touching/overlapping the left edge just like in Figma -->
            <div class="relative w-72 sm:w-84 md:w-96 lg:w-[420px] xl:w-[460px] -ml-8 sm:-ml-12 lg:-ml-14 xl:-ml-16 select-none pointer-events-none">
                <img 
                    src="/images/salad-bowl.png" 
                    alt="Salad Bowl Hamta" 
                    class="w-full h-auto object-contain block"
                    onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80';"
                >
            </div>
        </div>

        <!-- ========================================== -->
        <!-- RIGHT SIDE: Dark Green Form Area           -->
        <!-- ========================================== -->
        <div class="w-full lg:w-[44%] bg-[#0B412B] lg:bg-transparent min-h-screen flex flex-col justify-center items-center px-6 sm:px-12 py-12 relative">

            <div class="w-full max-w-[300px] sm:max-w-[320px] mx-auto">

                <!-- Brand Wordmark Logo -->
                <div class="mb-7">
                    <img 
                        src="/images/hamta-logo-white.png" 
                        alt="hamtā" 
                        class="h-12 sm:h-14 lg:h-16 w-auto object-contain select-none"
                    >
                </div>

                <!-- Form -->
                <form class="space-y-4" onsubmit="return false;">
                    <!-- Nama Pengguna Field -->
                    <div>
                        <label class="block text-white text-sm sm:text-base font-medium mb-1.5">
                            Nama Pengguna
                        </label>
                        <input 
                            type="text" 
                            value="Admin" 
                            readonly 
                            class="w-full bg-white text-[#A0AEC0] placeholder-[#A0AEC0] rounded-xl px-4 py-2.5 sm:py-3 text-sm sm:text-base font-normal shadow-sm outline-none border border-transparent focus:border-[#65BD5E] transition"
                            placeholder="Admin"
                        >
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label class="block text-white text-sm sm:text-base font-medium mb-1.5">
                            Password
                        </label>
                        <input 
                            type="text" 
                            value="Admin" 
                            readonly 
                            class="w-full bg-white text-[#A0AEC0] placeholder-[#A0AEC0] rounded-xl px-4 py-2.5 sm:py-3 text-sm sm:text-base font-normal shadow-sm outline-none border border-transparent focus:border-[#65BD5E] transition"
                            placeholder="Admin"
                        >
                    </div>

                    <!-- reCAPTCHA v2 Box -->
                    <div class="bg-white rounded-xl px-4 py-2.5 sm:py-3 flex items-center justify-between shadow-sm border border-stone-200">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded border-2 border-stone-300 flex items-center justify-center bg-white">
                                <svg class="w-4 h-4 text-[#65BD5E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </div>
                            <span class="text-xs sm:text-sm font-medium text-[#010101]">I'm not a robot</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-7 h-7 flex items-center justify-center">
                                <svg class="w-6 h-6 text-[#1A73E8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                    <path d="M3 3v5h5"/>
                                    <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                                    <path d="M16 21h5v-5"/>
                                </svg>
                            </div>
                            <span class="text-[9px] text-[#8A8A8A] leading-tight">reCAPTCHA</span>
                        </div>
                    </div>

                    <!-- Submit Button: Masuk -->
                    <button 
                        type="button" 
                        class="w-full bg-[#65BD5E] hover:bg-[#58a851] text-white font-medium text-sm sm:text-base rounded-xl py-2.5 sm:py-3 shadow-sm hover:shadow transition cursor-pointer"
                    >
                        Masuk
                    </button>
                </form>

            </div>

        </div>

    </div>

</body>
</html>
