@extends('layouts.dashboard', ['activeId' => $activeId])

@section('title', $title)
@section('page_title', $title)

@section('content')
    <div class="flex flex-col gap-6">
        {{-- Page Header --}}
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">{{ $title }}</h1>
            <p class="text-sm text-zinc-400 mt-1">Kelola panel {{ $title }} untuk workspace aplikasi Anda.</p>
        </div>
        
        {{-- Generic Coming Soon Panel --}}
        <div class="relative overflow-hidden rounded-xl border border-white/10 bg-zinc-900 p-8 flex flex-col justify-center items-center h-96">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
            
            <svg class="w-12 h-12 text-zinc-600 mb-4 animate-pulse" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <h2 class="text-lg font-semibold text-white">Modul {{ $title }}</h2>
            <p class="text-sm text-zinc-400 mt-2 text-center max-w-md">Fitur ini sedang disiapkan oleh AI BuatJalan. Akses penuh ke generator PRD, roadmap roadmap interaktif, dan tech stack recommendations akan segera aktif.</p>
        </div>
    </div>
@endsection
