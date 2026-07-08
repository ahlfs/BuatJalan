<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-zinc-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>Akses Ditolak (403) - BuatJalan</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-zinc-950 text-white min-h-screen font-sans antialiased overflow-hidden flex items-center justify-center relative p-6">
        
        {{-- Glow effects --}}
        <div class="absolute top-0 right-0 -mr-16 -mt-16 h-96 w-96 rounded-full bg-amber-500/5 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-16 -mb-16 h-96 w-96 rounded-full bg-zinc-900/10 blur-3xl pointer-events-none"></div>

        <div class="w-full max-w-md flex flex-col items-center justify-center text-center relative z-10 space-y-8 animate-in fade-in zoom-in-95 duration-300">
            
            {{-- Logo / Header --}}
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/icon-images/buatjalan-icon.png') }}" class="w-9 h-9 rounded-[8px]" alt="BuatJalan Logo">
                <span class="text-xl text-white font-bold tracking-tight">BuatJalan</span>
            </div>

            {{-- Main Error Box --}}
            <div class="bg-zinc-900/30 border border-white/10 p-8 rounded-2xl shadow-2xl w-full flex flex-col items-center space-y-6">
                {{-- Huge Status Code --}}
                <div class="text-7xl font-extrabold tracking-widest text-amber-400 font-mono select-none drop-shadow-[0_0_15px_rgba(251,191,36,0.15)]">
                    403
                </div>

                {{-- Status Text & Description --}}
                <div class="space-y-2">
                    <h2 class="text-lg font-bold text-white leading-snug">Akses Ditolak (Terlarang)</h2>
                    <p class="text-xs text-zinc-400 leading-relaxed max-w-xs mx-auto">
                        Maaf, Anda tidak memiliki izin atau otoritas yang cukup untuk mengakses halaman ini. Silakan hubungi pemilik workspace Anda.
                    </p>
                </div>
            </div>

            {{-- Back Button --}}
            <div class="w-full">
                <a href="/" class="inline-flex items-center justify-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-2.5 px-6 rounded-full text-xs transition-colors border border-white/5 cursor-pointer w-full max-w-[200px] shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>

        </div>
    </body>
</html>
