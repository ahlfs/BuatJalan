@php
    $clients = [
        ['name' => 'Vibecoders', 'icon' => 'ghost'],
        ['name' => 'Beginner Devs', 'icon' => 'hexagon'],
        ['name' => 'Indie Hackers', 'icon' => 'command'],
        ['name' => 'Solo Founders', 'icon' => 'triangle'],
        ['name' => 'Student Creators', 'icon' => 'gem'],
        ['name' => 'Tech Enthusiasts', 'icon' => 'cpu'],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-zinc-950 scroll-smooth custom-scrollbar">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- SEO Meta Tags Dasar -->
        <title>BuatJalan - AI PRD & Dev Roadmap Generator</title>
        <meta name="description" content="BuatJalan membantu developer pemula & vibecoders merancang PRD (Product Requirement Document) serta Roadmap Development lengkap menggunakan AI. Mulai ngoding tanpa tersesat!">
        <meta name="keywords" content="AI PRD Generator, AI Dev Roadmap, Pembuat Alur Kerja Aplikasi, Tech Stack Generator, Pembuat Aplikasi AI, BuatJalan">
        <meta name="author" content="BuatJalan">
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Open Graph / Facebook / LinkedIn / WhatsApp -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="BuatJalan - AI PRD & Dev Roadmap Generator">
        <meta property="og:description" content="Rancang PRD dan Roadmap Development aplikasi Anda secara instan dengan kecerdasan AI. Langsung ngoding tanpa pusing alur!">
        <meta property="og:image" content="{{ asset('assets/icon-images/buatjalan-icon.png') }}">

        <!-- Twitter / X -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="BuatJalan - AI PRD & Dev Roadmap Generator">
        <meta name="twitter:description" content="Rancang PRD dan Roadmap Development aplikasi Anda secara instan dengan AI.">
        <meta name="twitter:image" content="{{ asset('assets/icon-images/buatjalan-icon.png') }}">

        <!-- Fonts & Favicon -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/icon-images/buatjalan-icon.png') }}">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Structured Data (Schema.org JSON-LD) -->
        <script type="application/ld+json">
        {
          "@@context": "https://schema.org",
          "@@type": "WebApplication",
          "name": "BuatJalan",
          "alternateName": "BuatJalan AI",
          "url": "{{ url('/') }}",
          "logo": "{{ asset('assets/icon-images/buatjalan-icon.png') }}",
          "description": "Platform AI untuk merancang Product Requirement Document (PRD) dan roadmap pengembangan software untuk developer pemula dan profesional.",
          "applicationCategory": "DeveloperApplication",
          "operatingSystem": "All",
          "offers": {
            "@@type": "Offer",
            "price": "0",
            "priceCurrency": "IDR"
          }
        }
        </script>
    </head>
    <body class="bg-zinc-950 text-white min-h-screen font-sans antialiased overflow-x-hidden">
        <main class="w-full">

            <div class="relative w-full bg-zinc-950 text-white overflow-hidden font-sans">
                {{-- SCOPED ANIMATIONS --}}
                <style>
                    @keyframes fadeSlideIn {
                        from {
                            opacity: 0;
                            transform: translateY(20px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                    @keyframes marquee {
                        from {
                            transform: translateX(0);
                        }
                        to {
                            transform: translateX(-50%);
                        }
                    }
                    .animate-fade-in {
                        animation: fadeSlideIn 0.8s ease-out forwards;
                        opacity: 0;
                    }
                    .animate-marquee {
                        animation: marquee 40s linear infinite;
                    }
                    .delay-100 {
                        animation-delay: 0.1s;
                    }
                    .delay-200 {
                        animation-delay: 0.2s;
                    }
                    .delay-300 {
                        animation-delay: 0.3s;
                    }
                    .delay-400 {
                        animation-delay: 0.4s;
                    }
                    .delay-500 {
                        animation-delay: 0.5s;
                    }
                </style>

                {{-- Background Image with Gradient Mask --}}
                <div class="absolute inset-0 z-0 bg-[url(https://hoirqrkdgbmvpwutwuwj.supabase.co/storage/v1/object/public/assets/assets/a72ca2f3-9dd1-4fe4-84ba-fe86468a5237_3840w.webp?w=800&q=80)] bg-cover bg-center opacity-40"
                     style="mask-image: linear-gradient(180deg, transparent, black 0%, black 70%, transparent);
                            -webkit-mask-image: linear-gradient(180deg, transparent, black 0%, black 70%, transparent);">
                </div>

                {{-- Header / Navbar --}}
                <header class="relative z-20 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('assets/icon-images/buatjalan-icon.png') }}" class="w-7 h-7 rounded-[6px]" alt="BuatJalan Logo">
                        <span class="text-xl font-bold tracking-tight text-transparent bg-clip-text bg-linear-to-r from-white to-zinc-400">BuatJalan</span>
                    </div>
                    <div>
                        <a href="/login" class="inline-flex items-center justify-center rounded-full border border-white/10 bg-white/5 px-6 py-2 text-sm font-semibold text-white backdrop-blur-sm transition-colors hover:bg-white/10 hover:border-white/20 cursor-pointer">
                            Sign In
                        </a>
                    </div>
                </header>

                <div class="relative z-10 mx-auto max-w-7xl px-4 pt-12 pb-12 sm:px-6 md:pt-16 md:pb-20 lg:px-8">
                    <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:gap-8 items-start">
                        {{-- --- LEFT COLUMN --- --}}
                        <div class="lg:col-span-7 flex flex-col justify-center space-y-8 pt-8">
                            {{-- Badge --}}
                            <div class="animate-fade-in delay-100">
                                <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 backdrop-blur-md transition-colors hover:bg-white/10">
                                    <span class="text-[10px] sm:text-xs font-semibold uppercase tracking-wider text-zinc-300 flex items-center gap-2">
                                        AI-Powered App Builder
                                        <svg class="w-3.5 h-3.5 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            {{-- Heading --}}
                            <div class="animate-fade-in delay-100">
                                <span class="text-xs font-bold tracking-wider uppercase text-yellow-500/90 bg-yellow-500/10 border border-yellow-500/20 px-3 py-1 rounded-full">
                                    #1 Generator PRD & Roadmap Developer Berbasis AI
                                </span>
                            </div>
                            <h1 class="animate-fade-in delay-200 text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-medium tracking-tighter leading-[0.9]"
                                style="mask-image: linear-gradient(180deg, black 0%, black 80%, transparent 100%);
                                       -webkit-mask-image: linear-gradient(180deg, black 0%, black 80%, transparent 100%);">
                                Dari Ide ke Aplikasi,<br />
                                <span class="bg-linear-to-br from-white via-white to-[#ffcd75] bg-clip-text text-transparent">
                                    Tanpa Pusing
                                </span><br />
                                Alur & Tech Stack.
                            </h1>

                            {{-- Description --}}
                            <p class="animate-fade-in delay-300 max-w-xl text-lg text-zinc-400 leading-relaxed">
                                BuatJalan membantu developer pemula & vibecoder merancang PRD (Product Requirement Document) serta Roadmap Development lengkap menggunakan AI. Langsung ngoding tanpa tersesat!
                            </p>

                            {{-- CTA Buttons --}}
                            <div class="animate-fade-in delay-400 flex flex-col sm:flex-row gap-4">
                                <button class="group inline-flex items-center justify-center gap-2 rounded-full bg-white px-8 py-4 text-sm font-semibold text-zinc-950 transition-all hover:scale-[1.02] hover:bg-zinc-200 active:scale-[0.98] cursor-pointer">
                                    Buat Roadmap Gratis
                                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </button>
                                <a href="#how-it-works" class="group inline-flex items-center justify-center gap-2 rounded-full border border-white/10 bg-white/5 px-8 py-4 text-sm font-semibold text-white backdrop-blur-sm transition-colors hover:bg-white/10 hover:border-white/20 cursor-pointer">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                    </svg>
                                    Cara Kerja
                                </a>
                            </div>
                        </div>

                        {{-- --- RIGHT COLUMN --- --}}
                        <div class="lg:col-span-5 space-y-6 lg:mt-12">
                            {{-- Stats Card --}}
                            <div class="animate-fade-in delay-500 relative overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-8 backdrop-blur-xl shadow-2xl">
                                {{-- Card Glow Effect --}}
                                <div class="absolute top-0 right-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
                                <div class="relative z-10">
                                    <div class="flex items-center gap-4 mb-8">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/20">
                                            <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <circle cx="12" cy="12" r="6"></circle>
                                                <circle cx="12" cy="12" r="2"></circle>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-3xl font-bold tracking-tight text-white">1,500+</div>
                                            <div class="text-sm text-zinc-400">Roadmap Terbuat</div>
                                        </div>
                                    </div>

                                    {{-- Progress Bar Section --}}
                                    <div class="space-y-3 mb-8">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-zinc-400">Kesesuaian Tech Stack AI</span>
                                            <span class="text-white font-medium">99%</span>
                                        </div>
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-zinc-800/50">
                                            <div class="h-full w-[99%] rounded-full bg-linear-to-r from-white to-zinc-400"></div>
                                        </div>
                                    </div>

                                    <div class="h-px w-full bg-white/10 mb-6"></div>

                                    {{-- Mini Stats Grid --}}
                                    <div class="grid grid-cols-3 gap-4 text-center">
                                        {{-- StatItem 1 --}}
                                        <div class="flex flex-col items-center justify-center transition-transform hover:-translate-y-1 cursor-default">
                                            <span class="text-xl font-bold text-white sm:text-2xl">1-Click</span>
                                            <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-medium sm:text-xs">PRD Gen</span>
                                        </div>
                                        <div class="w-px h-full bg-white/10 mx-auto"></div>
                                        {{-- StatItem 2 --}}
                                        <div class="flex flex-col items-center justify-center transition-transform hover:-translate-y-1 cursor-default">
                                            <span class="text-xl font-bold text-white sm:text-2xl">AI</span>
                                            <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-medium sm:text-xs">Roadmap</span>
                                        </div>
                                        <div class="w-px h-full bg-white/10 mx-auto"></div>
                                        {{-- StatItem 3 --}}
                                        <div class="flex flex-col items-center justify-center transition-transform hover:-translate-y-1 cursor-default">
                                            <span class="text-xl font-bold text-white sm:text-2xl">100%</span>
                                            <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-medium sm:text-xs">Praktis</span>
                                        </div>
                                    </div>

                                    {{-- Tag Pills --}}
                                    <div class="mt-8 flex flex-wrap gap-2">
                                        <div class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[10px] font-medium tracking-wide text-zinc-300">
                                            <span class="relative flex h-2 w-2">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                            </span>
                                            BETA LIVE
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-[10px] font-medium tracking-wide text-zinc-300">
                                            <svg class="w-3 h-3 text-yellow-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"></path>
                                            </svg>
                                            SOCIALLY LOVED
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Marquee Card --}}
                            <div class="animate-fade-in delay-500 relative overflow-hidden rounded-3xl border border-white/10 bg-white/5 py-8 backdrop-blur-xl">
                                <h3 class="mb-6 px-8 text-sm font-medium text-zinc-400">Dirancang khusus untuk</h3>
                                <div class="relative flex overflow-hidden"
                                     style="mask-image: linear-gradient(to right, transparent, black 20%, black 80%, transparent);
                                            -webkit-mask-image: linear-gradient(to right, transparent, black 20%, black 80%, transparent);">
                                    <div class="animate-marquee flex gap-12 whitespace-nowrap px-4">
                                        {{-- Triple list for seamless loop --}}
                                        @for ($j = 0; $j < 3; $j++)
                                            @foreach ($clients as $client)
                                                <div class="flex items-center gap-2 opacity-50 transition-all hover:opacity-100 hover:scale-105 cursor-default grayscale hover:grayscale-0">
                                                    {{-- Brand Icon --}}
                                                    @if ($client['icon'] === 'hexagon')
                                                        <svg class="h-6 w-6 text-white fill-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                                        </svg>
                                                    @elseif ($client['icon'] === 'triangle')
                                                        <svg class="h-6 w-6 text-white fill-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                                                        </svg>
                                                    @elseif ($client['icon'] === 'command')
                                                        <svg class="h-6 w-6 text-white fill-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M18 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3H6a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3V6a3 3 0 0 0-3-3 3 3 0 0 0-3 3 3 3 0 0 0 3 3h12a3 3 0 0 0 3-3 3 3 0 0 0-3-3z"></path>
                                                        </svg>
                                                    @elseif ($client['icon'] === 'ghost')
                                                        <svg class="h-6 w-6 text-white fill-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M9 10h.01M15 10h.01M12 2a8 8 0 0 0-8 8v12l3-3 2.5 2.5L12 19l2.5 2.5L17 19l3 3V10a8 8 0 0 0-8-8z"></path>
                                                        </svg>
                                                    @elseif ($client['icon'] === 'gem')
                                                        <svg class="h-6 w-6 text-white fill-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M6 3h12l4 6-10 13L2 9z"></path>
                                                            <path d="M11 3 8 9l4 13 4-13-3-6"></path>
                                                            <path d="M2 9h20"></path>
                                                        </svg>
                                                    @elseif ($client['icon'] === 'cpu')
                                                        <svg class="h-6 w-6 text-white fill-current" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                                                            <rect x="9" y="9" width="6" height="6"></rect>
                                                            <path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 15h3M1 9h3M1 15h3"></path>
                                                        </svg>
                                                    @endif
                                                    {{-- Brand Name --}}
                                                    <span class="text-lg font-bold text-white tracking-tight">
                                                        {{ $client['name'] }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HOW IT WORKS SECTION --}}
            @php
                $stepsData = [
                    [
                        'icon' => 'search',
                        'title' => 'Isi Ide & Kebutuhan App',
                        'description' => 'Tuliskan deskripsi ide aplikasi Anda dan fitur utama yang ingin dibuat dalam kalimat sederhana.',
                        'benefits' => [
                            'Proses input instan & santai',
                            'Tidak perlu paham istilah teknis',
                            'Bisa pakai bahasa Indonesia sehari-hari'
                        ]
                    ],
                    [
                        'icon' => 'layers',
                        'title' => 'AI Memahami Kebutuhan Anda',
                        'description' => 'AI cerdas BuatJalan akan menganalisis alur bisnis, merekomendasikan tech stack, dan menyusun PRD detail.',
                        'benefits' => [
                            'Pemilihan database & library optimal',
                            'Analisis dependensi & alur kerja',
                            'Rekomendasi disesuaikan level belajar Anda'
                        ]
                    ],
                    [
                        'icon' => 'zap',
                        'title' => 'PRD & Roadmap Siap Pakai!',
                        'description' => 'Unduh PRD & roadmap terstruktur. Ikuti panduan langkah demi langkah dan langsung mulai coding.',
                        'benefits' => [
                            'Roadmap langkah demi langkah yang rapi',
                            'PRD siap pakai untuk acuan coding',
                            'Tautan referensi tutorial/dokumentasi terkait'
                        ]
                    ],
                ];
            @endphp

            <section id="how-it-works" class="w-full bg-zinc-950 py-16 sm:py-24 border-t border-white/5 scroll-mt-6">
                <div class="container mx-auto px-4 max-w-7xl">
                    {{-- Section Header --}}
                    <div class="mx-auto mb-16 max-w-4xl text-center">
                        <h2 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">
                            Bagaimana Cara Kerjanya?
                        </h2>
                        <p class="mt-4 text-lg text-zinc-400">
                            BuatJalan menggunakan kecerdasan buatan untuk merancang alur pembuatan aplikasi dari nol secara instan.
                        </p>
                    </div>

                    {{-- Step Indicators with Connecting Line --}}
                    <div class="relative mx-auto mb-8 w-full max-w-4xl">
                        <div aria-hidden="true" class="absolute left-[16.6667%] top-1/2 h-0.5 w-[66.6667%] -translate-y-1/2 bg-white/10"></div>
                        {{-- Use grid to align numbers with the card grid below --}}
                        <div class="relative grid grid-cols-3">
                            @foreach ($stepsData as $index => $step)
                                <div class="flex h-8 w-8 items-center justify-center justify-self-center rounded-full bg-zinc-900 border border-white/10 font-semibold text-white ring-4 ring-zinc-950">
                                    {{ $index + 1 }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Steps Grid --}}
                    <div class="mx-auto grid max-w-4xl grid-cols-1 gap-8 md:grid-cols-3">
                        @foreach ($stepsData as $step)
                            <div class="relative rounded-2xl border border-white/10 bg-white/5 p-6 text-white backdrop-blur-xl transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-lg hover:border-white/30 hover:bg-white/10">
                                {{-- Icon --}}
                                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-white/10 text-white">
                                    @if ($step['icon'] === 'search')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                    @elseif ($step['icon'] === 'layers')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                            <polyline points="2 17 12 22 22 17"></polyline>
                                            <polyline points="2 12 12 17 22 12"></polyline>
                                        </svg>
                                    @elseif ($step['icon'] === 'zap')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                                        </svg>
                                    @endif
                                </div>
                                {{-- Title and Description --}}
                                <h3 class="mb-2 text-xl font-semibold">{{ $step['title'] }}</h3>
                                <p class="mb-6 text-zinc-400 text-sm leading-relaxed">{{ $step['description'] }}</p>
                                {{-- Benefits List --}}
                                <ul class="space-y-3">
                                    @foreach ($step['benefits'] as $benefit)
                                        <li class="flex items-center gap-3">
                                            <div class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-white/10">
                                                <div class="h-2 w-2 rounded-full bg-white"></div>
                                            </div>
                                            <span class="text-zinc-400 text-xs">{{ $benefit }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </main>

        {{-- Footer Section --}}
        <footer class="border-t border-white/10 bg-zinc-950 py-12 relative overflow-hidden">
            {{-- Subtle Ambient Glow --}}
            <div class="absolute bottom-0 right-1/4 h-64 w-64 rounded-full bg-primary/5 blur-3xl pointer-events-none"></div>
            
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6 relative z-10 text-center md:text-left">
                {{-- Logo and Copyright --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-center md:justify-start gap-3">
                        <img src="{{ asset('assets/icon-images/buatjalan-icon.png') }}" class="w-7 h-7 rounded-[6px]" alt="BuatJalan Logo">
                        <span class="text-sm font-bold tracking-tight text-white">BuatJalan</span>
                    </div>
                    <p class="text-xs text-zinc-500">
                        &copy; {{ date('Y') }} BuatJalan. Developed by <strong class="text-zinc-300">Ahlfs</strong>. All rights reserved.
                    </p>
                </div>

                {{-- Secure & Premium Badges --}}
                <div class="flex flex-col items-center md:items-end gap-2">
                    <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Platform Integrity</span>
                    <div class="flex flex-wrap items-center justify-center gap-3 text-xs text-zinc-400 font-medium">
                        <span class="px-2 py-1 rounded bg-white/5 border border-white/5 flex items-center gap-1">🔒 Secure Payment</span>
                        <span class="px-2 py-1 rounded bg-white/5 border border-white/5 flex items-center gap-1">⚡ High Performance</span>
                        <span class="px-2 py-1 rounded bg-white/5 border border-white/5 flex items-center gap-1">🤖 AI Orchestrator</span>
                        <span class="px-2 py-1 rounded bg-white/5 border border-white/5 flex items-center gap-1">☁️ Cloud Infrastructure</span>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
