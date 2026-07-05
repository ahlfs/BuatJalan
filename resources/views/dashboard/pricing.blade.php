@extends('layouts.dashboard')

@section('title', 'Pricing')
@section('page_title', 'Token & Pricing')

@section('content')
<div class="max-w-6xl mx-auto py-4 space-y-10 font-sans">
    
    {{-- Header Intro --}}
    <div class="text-center max-w-2xl mx-auto space-y-3">
        <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Pilih Paket Kredit Anda</h1>
        <p class="text-sm text-zinc-400">Dapatkan akses instan ke AI Architect kami. Saldo kredit yang Anda beli aktif selamanya tanpa batas waktu kedaluwarsa.</p>
        
        {{-- Credit Info badges --}}
        <div class="inline-flex flex-wrap items-center justify-center gap-3 pt-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 border border-primary/20 text-primary">
                ⚡ 10 Kredit = 1x Generate Project Baru
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/5 border border-white/10 text-zinc-300">
                🔧 10 Kredit = 1x Edit / Ubah Project
            </span>
        </div>
    </div>

    {{-- Pricing Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pt-4">
        
        {{-- CARD 1: STARTER --}}
        <div class="relative bg-zinc-900/40 border border-white/10 hover:border-white/20 rounded-2xl p-6 flex flex-col justify-between transition-all duration-300 group hover:-translate-y-1 shadow-lg">
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-white group-hover:text-primary transition-colors">Starter Pack</h3>
                    <p class="text-xs text-zinc-400 mt-1 leading-relaxed">Cocok untuk pemula yang ingin mencoba merancang ide proyek pertama mereka.</p>
                </div>
                
                {{-- Price --}}
                <div class="flex items-baseline gap-1 text-white">
                    <span class="text-3xl font-extrabold">Rp 19.000</span>
                    <span class="text-xs text-zinc-500 font-medium">/ sekali beli</span>
                </div>

                {{-- Credit Quantity --}}
                <div class="bg-zinc-950/60 rounded-xl p-4 border border-white/5 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Kuota Kredit</div>
                        <div class="text-xl font-black text-white mt-0.5">50 Kredit</div>
                    </div>
                    <span class="text-2xl">🌱</span>
                </div>

                <hr class="border-white/5">

                {{-- Features List --}}
                <ul class="space-y-3 text-xs text-zinc-400">
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Kapasitas hingga 5x Generate proyek</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Sisa kredit aktif selamanya (no expired)</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Model Cepat Gemini Flash 3</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Rincian Tech Stack & Rute Roadmap</span>
                    </li>
                </ul>
            </div>
            
            <button class="w-full mt-8 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors cursor-pointer border border-white/5">
                Beli Starter
            </button>
        </div>

        {{-- CARD 2: POPULAR (RECOMMENDED) --}}
        <div class="relative bg-zinc-900 border-2 border-primary rounded-2xl pt-8 pb-6 px-6 flex flex-col justify-between transition-all duration-300 group hover:-translate-y-1 shadow-2xl scale-105">
            {{-- Badge --}}
            <span class="absolute bg-primary text-primary-foreground text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider z-20" style="top: -14px; left: 50%; transform: translate(-50%, 0);">Terpopuler</span>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-white group-hover:text-primary transition-colors">Popular Pack</h3>
                    <p class="text-xs text-zinc-400 mt-1 leading-relaxed">Paket terbaik bagi mahasiswa dan developer pemula untuk eksplorasi lebih jauh.</p>
                </div>
                
                {{-- Price --}}
                <div class="flex items-baseline gap-1 text-white">
                    <span class="text-3xl font-extrabold text-primary">Rp 49.000</span>
                    <span class="text-xs text-zinc-500 font-medium">/ sekali beli</span>
                </div>

                {{-- Credit Quantity --}}
                <div class="bg-primary/5 rounded-xl p-4 border border-primary/20 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] text-primary/70 font-bold uppercase tracking-wider">Kuota Kredit</div>
                        <div class="text-xl font-black text-primary mt-0.5">150 Kredit</div>
                    </div>
                    <span class="text-2xl">🔥</span>
                </div>

                <hr class="border-white/5">

                {{-- Features List --}}
                <ul class="space-y-3 text-xs text-zinc-300">
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Kapasitas hingga 15x Generate proyek</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Sisa kredit aktif selamanya (no expired)</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Rancangan Tabel & Kolom Database</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Antrean Generate Prioritas Tinggi</span>
                    </li>
                </ul>
            </div>
            
            <button class="w-full mt-8 bg-primary text-primary-foreground hover:bg-primary/95 font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors cursor-pointer shadow-lg shadow-primary/20">
                Beli Terpopuler
            </button>
        </div>

        {{-- CARD 3: DEVELOPER --}}
        <div class="relative bg-zinc-900/40 border border-white/10 hover:border-white/20 rounded-2xl p-6 flex flex-col justify-between transition-all duration-300 group hover:-translate-y-1 shadow-lg">
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-white group-hover:text-primary transition-colors">Developer Pack</h3>
                    <p class="text-xs text-zinc-400 mt-1 leading-relaxed">Sangat cocok untuk freelancer, agensi, dan 'vibecoder' dengan intensitas tinggi.</p>
                </div>
                
                {{-- Price --}}
                <div class="flex items-baseline gap-1 text-white">
                    <span class="text-3xl font-extrabold">Rp 99.000</span>
                    <span class="text-xs text-zinc-500 font-medium">/ sekali beli</span>
                </div>

                {{-- Credit Quantity --}}
                <div class="bg-zinc-950/60 rounded-xl p-4 border border-white/5 flex items-center justify-between">
                    <div>
                        <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Kuota Kredit</div>
                        <div class="text-xl font-black text-white mt-0.5">500 Kredit</div>
                    </div>
                    <span class="text-2xl">🚀</span>
                </div>

                <hr class="border-white/5">

                {{-- Features List --}}
                <ul class="space-y-3 text-xs text-zinc-400">
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Kapasitas hingga 50x Generate proyek</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Sisa kredit aktif selamanya (no expired)</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Ekspor PRD & Readme Tanpa Batas</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Akses Model AI Premium Lainnya</span>
                    </li>
                </ul>
            </div>
            
            <button class="w-full mt-8 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors cursor-pointer border border-white/5">
                Beli Developer
            </button>
        </div>

    </div>

    {{-- Safe Guarantee Banner --}}
    <div class="mt-12 bg-zinc-900/20 border border-white/5 rounded-2xl p-5 flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left justify-between">
        <div class="space-y-1">
            <h4 class="text-sm font-bold text-white flex items-center gap-1.5 justify-center sm:justify-start">
                🔒 Pembayaran Aman & Instan
            </h4>
            <p class="text-xs text-zinc-400 leading-relaxed">Mendukung pembayaran langsung menggunakan QRIS (GoPay, OVO, Dana, LinkAja) atau transfer Virtual Account Bank Indonesia.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <span class="text-[10px] font-semibold tracking-wider text-zinc-500 uppercase">Powered by Midtrans</span>
        </div>
    </div>

</div>
@endsection
