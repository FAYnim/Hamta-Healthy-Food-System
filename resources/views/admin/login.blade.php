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
<body class="bg-[#F7F2E7] text-[#010101] antialiased min-h-screen">

    <div class="min-h-screen flex flex-col lg:flex-row relative overflow-hidden">

        <!-- ========================================== -->
        <!-- LEFT SIDE: Cream Background with Salad     -->
        <!-- ========================================== -->
        <div class="w-full lg:w-1/2 bg-[#F7F2E7] min-h-[300px] lg:min-h-screen flex items-center justify-center p-8 relative">
            <!-- Salad Bowl Image Mockup / Visual -->
            <div class="relative w-64 h-64 sm:w-80 sm:h-80 md:w-96 md:h-96 rounded-full overflow-hidden shadow-2xl border-4 border-white/80 bg-white">
                <img 
                    src="/storage/salad-bowl.png" 
                    alt="Salad Bowl" 
                    class="w-full h-full object-cover"
                    onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80';"
                >
            </div>
        </div>

        <!-- ========================================== -->
        <!-- RIGHT SIDE: Dark Green Form Area           -->
        <!-- ========================================== -->
        <div class="w-full lg:w-1/2 bg-[#0B412B] min-h-screen flex flex-col justify-center px-8 sm:px-14 md:px-20 lg:px-24 py-12 relative lg:rounded-l-[55px]">

            <div class="w-full max-w-md mx-auto">
                <!-- Brand Wordmark Logo -->
                <div class="mb-10 text-center lg:text-left">
                    <h1 class="text-5xl font-black text-white tracking-tight flex items-center justify-center lg:justify-start gap-1">
                        hamtā
                        <span class="inline-block text-[#65BD5E]">
                            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24">
                                <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/>
                            </svg>
                        </span>
                    </h1>
                </div>

                <!-- Static Form -->
                <form class="space-y-6" onsubmit="return false;">
                    <!-- Nama Pengguna Field -->
                    <div>
                        <label class="block text-white text-base font-medium mb-2.5">
                            Nama Pengguna
                        </label>
                        <input 
                            type="text" 
                            value="Admin" 
                            readonly 
                            class="w-full bg-white text-[#010101] rounded-xl px-5 py-3.5 text-base font-normal shadow-sm outline-none border border-transparent focus:border-[#65BD5E] transition"
                            placeholder="Admin"
                        >
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label class="block text-white text-base font-medium mb-2.5">
                            Password
                        </label>
                        <input 
                            type="password" 
                            value="Admin" 
                            readonly 
                            class="w-full bg-white text-[#010101] rounded-xl px-5 py-3.5 text-base font-normal shadow-sm outline-none border border-transparent focus:border-[#65BD5E] transition"
                            placeholder="Admin"
                        >
                    </div>

                    <!-- reCAPTCHA v2 Mockup Box -->
                    <div class="bg-white rounded-xl px-5 py-3.5 flex items-center justify-between shadow-sm border border-stone-200">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded border-2 border-stone-300 flex items-center justify-center bg-white">
                                <x-lucide-check class="w-4 h-4 text-[#65BD5E] stroke-[3]" />
                            </div>
                            <span class="text-sm font-medium text-[#010101]">I'm not a robot</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 flex items-center justify-center">
                                <svg class="w-6 h-6 text-[#0B412B]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                        class="w-full bg-[#65BD5E] hover:bg-[#58a851] text-white font-bold text-lg rounded-xl py-3.5 shadow-md hover:shadow-lg transition cursor-pointer"
                    >
                        Masuk
                    </button>
                </form>
            </div>

        </div>

    </div>

</body>
</html>
