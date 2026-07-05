<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-zinc-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login - BuatJalan</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-zinc-950 text-white min-h-screen font-sans antialiased overflow-hidden flex items-center justify-center">
        <div class="flex h-screen w-full">
            {{-- Left Side: Workspace Graphic --}}
            <div class="w-1/2 hidden lg:block relative overflow-hidden h-full">
                <div class="absolute inset-0 bg-cover bg-center opacity-70" style="background-image: url('{{ asset('assets/background-images/background-login.jpg') }}');"></div>
                <div class="absolute inset-0 bg-linear-to-r from-transparent to-zinc-950"></div>
                {{-- Text Overlay --}}
                <div class="absolute bottom-16 left-16 max-w-md z-10">
                    <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">PRD & Dev Roadmap Generator</span>
                    <h2 class="text-4xl font-bold tracking-tight text-white mt-2 leading-tight">Mulai Buat Jalan Aplikasi Impianmu</h2>
                    <p class="text-zinc-300 mt-4 text-sm leading-relaxed">Masuk ke dashboard untuk generate roadmap dengan AI, rancang PRD, dan pantau progress belajarmu.</p>
                </div>
            </div>
        
            {{-- Right Side: Login Box --}}
            <div class="w-full lg:w-1/2 flex flex-col items-center justify-center bg-zinc-950 relative p-8">
                {{-- Glow effect --}}
                <div class="absolute top-0 right-0 -mr-16 -mt-16 h-96 w-96 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
                
                {{-- Back button --}}
                <a href="/" class="absolute top-8 left-8 inline-flex items-center gap-2 text-zinc-400 hover:text-white text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Kembali ke Beranda
                </a>

                <div class="w-full max-w-sm flex flex-col items-center justify-center">
                    {{-- Logo / Title --}}
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/icon-images/buatjalan-icon.png') }}" class="w-10 h-10 rounded-[8px]" alt="BuatJalan Logo">
                        <h2 class="text-3xl text-white font-bold tracking-tight">BuatJalan</h2>
                    </div>
                    <p class="text-sm text-zinc-400 mt-3 text-center">Masuk untuk memulai generate roadmap AI Anda</p>
         
                    @if(session('error'))
                        <div class="w-full mt-6 p-4 rounded-xl border border-red-500/20 bg-red-500/10 text-red-400 text-xs text-center">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- OAuth Buttons Container --}}
                    <div class="w-full mt-8 space-y-4">
                        {{-- Google OAuth Button --}}
                        <a href="/auth/google/redirect" class="w-full flex items-center justify-center gap-3 h-12 rounded-full border border-white/10 bg-white/5 text-white text-sm font-semibold transition-all hover:bg-white/10 hover:border-white/20 active:scale-[0.98] cursor-pointer">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#EA4335" d="M12 5.04c1.7 0 3.2.6 4.4 1.7l3.3-3.3C17.7 1.5 15 1 12 1 7.3 1 3.4 3.7 1.5 7.6l3.9 3C6.3 7.3 8.9 5.04 12 5.04z"/>
                                <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.4h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.7z"/>
                                <path fill="#FBBC05" d="M5.4 14.6c-.2-.6-.3-1.3-.3-2s.1-1.4.3-2L1.5 7.6C.5 9.5 0 11.7 0 14s.5 4.5 1.5 6.4l3.9-3c-.2-.6-.3-1.3-.3-2.1z"/>
                                <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-2.9l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3.1 0-5.7-2.3-6.6-5.4l-3.9 3C3.4 20.3 7.3 23 12 23z"/>
                            </svg>
                            Continue with Google
                        </a>
        
                        {{-- GitHub OAuth Button --}}
                        <a href="/auth/github/redirect" class="w-full flex items-center justify-center gap-3 h-12 rounded-full border border-white/10 bg-white/5 text-white text-sm font-semibold transition-all hover:bg-white/10 hover:border-white/20 active:scale-[0.98] cursor-pointer">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.5 2 2 6.5 2 12c0 4.4 2.9 8.2 6.8 9.5.5.1.7-.2.7-.5v-1.7c-2.8.6-3.4-1.3-3.4-1.3-.5-1.2-1.2-1.5-1.2-1.5-.9-.6.1-.6.1-.6 1 .1 1.5 1 1.5 1 .9 1.5 2.3 1.1 2.9.8.1-.6.4-1.1.6-1.3-2.2-.3-4.6-1.1-4.6-5 0-1.1.4-2 1-2.7-.1-.3-.4-1.3.1-2.7 0 0 .9-.3 2.8 1a9.8 9.8 0 0 1 5.2 0c1.9-1.3 2.8-1 2.8-1 .5 1.4.2 2.4.1 2.7.6.7 1 1.6 1 2.7 0 3.9-2.4 4.7-4.7 5 .4.3.7.9.7 1.8V21c0 .3.2.6.7.5C19.1 20.2 22 16.4 22 12c0-5.5-4.5-10-10-10z"/>
                            </svg>
                            Continue with GitHub
                        </a>
                    </div>

                    <p class="text-zinc-500 text-xs mt-8 text-center px-4 leading-relaxed">Dengan masuk, Anda menyetujui Ketentuan Layanan dan Kebijakan Privasi kami.</p>
                </div>
            </div>
        </div>
    </body>
</html>
