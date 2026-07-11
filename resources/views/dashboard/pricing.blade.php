@extends('layouts.dashboard')

@section('title', 'Pricing')
@section('page_title', 'Token & Pricing')

@section('content')
<div class="max-w-6xl mx-auto py-4 space-y-10 font-sans" x-data="{
    showConfirmModal: false,
    showQrisModal: false,
    selectedPackage: '',
    selectedPrice: '',
    selectedCredits: '',
    step: 'loading', // 'loading' -> 'qris' -> 'verifying' -> 'success'
    currentForm: null,
    
    confirmPurchase(packageKey, price, credits, formElement) {
        this.selectedPackage = packageKey;
        this.selectedPrice = price;
        this.selectedCredits = credits;
        this.currentForm = formElement;
        this.showConfirmModal = true;
    },
    
    startPurchaseAfterConfirm() {
        this.showConfirmModal = false;
        this.showQrisModal = true;
        this.step = 'loading';
        
        // Step 1: Loading/Creating Invoice (1 sec)
        setTimeout(() => {
            this.step = 'qris';
            
            // Step 2: Show QRIS and simulate user scanning & paying (3.5 secs)
            setTimeout(() => {
                this.step = 'verifying';
                
                // Step 3: Verifying payment (1.5 secs)
                setTimeout(() => {
                    this.step = 'success';
                    
                    // Step 4: Submit to backend to record tokens (1.2 secs)
                    setTimeout(() => {
                        this.currentForm.submit();
                    }, 1200);
                }, 1500);
            }, 3500);
        }, 1000);
    }
}">


    
    {{-- Header Intro --}}
    <div class="text-center max-w-2xl mx-auto space-y-3">
        <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Pilih Paket Token Anda</h1>
        <p class="text-sm text-zinc-400">Dapatkan akses instan ke AI Architect kami. Saldo token yang Anda beli aktif selamanya tanpa batas waktu kedaluwarsa.</p>
        
        {{-- Credit Info badges --}}
        <div class="inline-flex flex-wrap items-center justify-center gap-3 pt-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-primary/10 border border-primary/20 text-primary">
                ⚡ 10 Token = 1x Generate Project Baru
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/5 border border-white/10 text-zinc-300">
                🔧 10 Token = 1x Edit / Ubah Project
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
                        <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Kuota Token</div>
                        <div class="text-xl font-black text-white mt-0.5">50 Token</div>
                    </div>
                    <span class="text-2xl"><img src="{{ asset('assets/icon-images/emerald-icon.png') }}" class="w-10 h-auto" alt="High Performance"></span>
                </div>

                <hr class="border-white/5">

                {{-- Features List --}}
                <ul class="space-y-3 text-xs text-zinc-400">
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Setara dengan 5x Generate proyek baru</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Atau setara dengan 5x Edit/Ubah proyek</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Token aktif selamanya (Tanpa Kedaluwarsa)</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Bisa digunakan oleh seluruh anggota workspace</span>
                    </li>
                </ul>
            </div>
            
            <form action="{{ route('pricing.buy') }}" method="POST" class="m-0" @submit.prevent="confirmPurchase('starter', 'Rp 19.000', 50, $el)">
                @csrf
                <input type="hidden" name="package" value="starter">
                <button type="submit" class="w-full mt-8 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors cursor-pointer border border-white/5">
                    Beli Starter
                </button>
            </form>
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
                        <div class="text-[10px] text-primary/70 font-bold uppercase tracking-wider">Kuota Token</div>
                        <div class="text-xl font-black text-primary mt-0.5">150 Token</div>
                    </div>
                    <span class="text-2xl"><img src="{{ asset('assets/icon-images/emerald-icon.png') }}" class="w-10 h-auto" alt="High Performance"></span>
                </div>

                <hr class="border-white/5">

                {{-- Features List --}}
                <ul class="space-y-3 text-xs text-zinc-300">
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Setara dengan 15x Generate proyek baru</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Atau setara dengan 15x Edit/Ubah proyek</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Token aktif selamanya (Tanpa Kedaluwarsa)</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Bisa digunakan oleh seluruh anggota workspace</span>
                    </li>
                </ul>
            </div>
            
            <form action="{{ route('pricing.buy') }}" method="POST" class="m-0" @submit.prevent="confirmPurchase('popular', 'Rp 49.000', 150, $el)">
                @csrf
                <input type="hidden" name="package" value="popular">
                <button type="submit" class="w-full mt-8 bg-primary text-primary-foreground hover:bg-primary/95 font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors cursor-pointer shadow-lg shadow-primary/20">
                    Beli Terpopuler
                </button>
            </form>
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
                        <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Kuota Token</div>
                        <div class="text-xl font-black text-white mt-0.5">500 Token</div>
                    </div>
                    <span class="text-2xl"><img src="{{ asset('assets/icon-images/emerald-icon.png') }}" class="w-10 h-auto" alt="High Performance"></span>
                </div>

                <hr class="border-white/5">

                {{-- Features List --}}
                <ul class="space-y-3 text-xs text-zinc-400">
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Setara dengan 50x Generate proyek baru</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Atau setara dengan 50x Edit/Ubah proyek</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Token aktif selamanya (Tanpa Kedaluwarsa)</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Bisa digunakan oleh seluruh anggota workspace</span>
                    </li>
                </ul>
            </div>
            
            <form action="{{ route('pricing.buy') }}" method="POST" class="m-0" @submit.prevent="confirmPurchase('developer', 'Rp 99.000', 500, $el)">
                @csrf
                <input type="hidden" name="package" value="developer">
                <button type="submit" class="w-full mt-8 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors cursor-pointer border border-white/5">
                    Beli Developer
                </button>
            </form>
        </div>

    </div>

    {{-- Safe Guarantee Banner --}}
    <div class="mt-12 bg-zinc-900/20 border border-white/5 rounded-2xl p-5 flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left justify-between">
        <div class="space-y-1">
            <h4 class="text-sm font-bold text-white flex items-center gap-2 justify-center sm:justify-start">
                <img src="{{ asset('assets/svg/secure-icon.svg') }}" class="w-5 h-5 object-contain shrink-0" alt="Shield">
                <span>Pembayaran Aman & Instan</span>
            </h4>
            <p class="text-xs text-zinc-400 leading-relaxed">Mendukung pembayaran langsung menggunakan QRIS (GoPay, OVO, Dana, LinkAja) atau transfer Virtual Account Bank Indonesia.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <span class="text-[10px] font-semibold tracking-wider text-zinc-500 uppercase">Powered by DOKU</span>
        </div>
    </div>

    {{-- CONFIRMATION MODAL --}}
    <div 
        x-show="showConfirmModal" 
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        x-transition
        style="display: none;"
    >
        <div class="absolute inset-0" @click="showConfirmModal = false"></div>
        <div class="relative bg-zinc-900 border border-white/10 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl z-10 animate-in fade-in zoom-in-95 duration-200">
            
            {{-- Header --}}
            <div class="p-6 border-b border-white/5 bg-zinc-900/50 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                        <img src="{{ asset('assets/icon-images/emerald-icon.png') }}" class="w-4 h-4 object-contain" alt="Emerald">
                    </div>
                    <h3 class="text-sm font-bold text-white">Konfirmasi Pembelian</h3>
                </div>
                <button @click="showConfirmModal = false" class="p-1.5 rounded-md text-zinc-500 hover:bg-white/5 hover:text-white transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6 space-y-4">
                <p class="text-xs text-zinc-400 leading-relaxed">
                    Token yang dibeli akan langsung ditambahkan secara permanen ke dalam **Workspace Aktif** Anda saat ini:
                </p>

                <div class="p-3 bg-zinc-950 border border-white/5 rounded-xl flex items-center gap-3">
                    <img src="{{ asset('assets/icon-images/emerald-icon.png') }}" class="w-5 h-5 object-contain shrink-0" alt="Emerald">
                    <div class="min-w-0">
                        <div class="text-[9px] text-zinc-500 font-bold uppercase tracking-wider">Workspace Aktif</div>
                        <div class="text-xs font-bold text-white truncate" x-text="activeWorkspace"></div>
                    </div>
                </div>

                <div class="text-xs text-zinc-400">
                    Detail Pembelian:
                    <ul class="mt-1.5 space-y-1 text-zinc-300 pl-4 list-disc">
                        <li>Paket: <span class="font-semibold text-white" x-text="selectedPackage.charAt(0).toUpperCase() + selectedPackage.slice(1) + ' Pack'"></span></li>
                        <li>Harga: <span class="font-semibold text-white" x-text="selectedPrice"></span></li>
                        <li>Jumlah: <span class="font-semibold text-emerald-400" x-text="selectedCredits + ' Emerald Token'"></span></li>
                    </ul>
                </div>
            </div>

            {{-- Footer --}}
            <div class="p-6 border-t border-white/5 bg-zinc-900/30 flex items-center justify-end gap-2.5">
                <button 
                    @click="showConfirmModal = false"
                    class="bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-2 px-4 rounded-xl text-xs transition-colors cursor-pointer border border-white/5 select-none"
                >
                    Batal
                </button>
                <button 
                    @click="startPurchaseAfterConfirm()"
                    class="bg-primary hover:bg-primary/95 text-primary-foreground font-semibold py-2 px-4 rounded-xl text-xs transition-colors cursor-pointer shadow-lg shadow-primary/20 select-none"
                >
                    Ya, Lanjutkan
                </button>
            </div>

        </div>
    </div>

    {{-- QRIS CHECKOUT MODAL --}}
    <div 
        x-show="showQrisModal" 
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        x-transition
        style="display: none;"
    >
        <div class="absolute inset-0" @click="showQrisModal = false"></div>
        <div class="relative bg-zinc-900 border border-white/10 rounded-2xl w-full max-w-[340px] overflow-hidden shadow-2xl z-10 animate-in fade-in zoom-in-95 duration-200">
            
            {{-- Loading State --}}
            <div x-show="step === 'loading'" class="p-8 text-center space-y-6 flex flex-col items-center">
                <svg class="animate-spin h-10 w-10 text-primary" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div class="space-y-2">
                    <h3 class="text-sm font-bold text-white">Menyiapkan Metode QRIS</h3>
                    <p class="text-xs text-zinc-400">Menghubungkan ke secure payment gateway...</p>
                </div>
            </div>

            {{-- Main QRIS Interface (Shown during 'qris', 'verifying', 'success') --}}
            <div x-show="step !== 'loading'" class="flex flex-col">
                {{-- Header --}}
                <div class="p-5 border-b border-white/5 bg-zinc-900/50 flex items-center justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-white">Pembayaran QRIS</h3>
                        <p class="text-[9px] text-zinc-400 mt-0.5" x-text="'Paket: ' + selectedPackage.toUpperCase() + ' (' + selectedCredits + ' Token)'"></p>
                    </div>
                    <span class="text-xs font-mono font-black text-white" x-text="selectedPrice"></span>
                </div>
                
                {{-- Body --}}
                <div class="p-6 flex flex-col items-center justify-center space-y-5">
                    {{-- QR Code Container with checkmark overlay --}}
                    <div class="relative bg-white rounded-2xl shadow-xl overflow-hidden w-full flex items-center justify-center">
                        <img src="{{ asset('assets/background-images/qris-test.png') }}" 
                             class="w-full h-auto transition-all duration-500"
                             :class="step === 'verifying' || step === 'success' ? 'opacity-20 blur-[1px]' : ''"
                             alt="QRIS Code">
                        
                        {{-- Bouncing Checkmark Overlay --}}
                        <div x-show="step === 'verifying' || step === 'success'" 
                             class="absolute inset-0 flex flex-col items-center justify-center bg-emerald-500/5 backdrop-blur-[1px] animate-in fade-in zoom-in duration-300"
                             style="display: none;">
                             <div class="w-20 h-20 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30 animate-[bounce_0.5s_ease-out]">
                                 <svg class="w-12 h-12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                     <polyline points="20 6 9 17 4 12"></polyline>
                                 </svg>
                             </div>
                             <span class="text-sm text-emerald-600 font-bold mt-3 uppercase tracking-wider">Lunas</span>
                        </div>
                    </div>

                    <div class="text-center space-y-2 w-full">
                        {{-- Status Indicators --}}
                        <div x-show="step === 'qris'" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 border border-amber-500/20 text-amber-400 animate-pulse">
                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                            Menunggu Pembayaran...
                        </div>

                        {{-- Download QRIS Button (only shown during 'qris' step) --}}
                        <div x-show="step === 'qris'" class="pt-1 px-4">
                            <a href="{{ asset('assets/background-images/qris-test.png') }}" 
                               download="BuatJalan-QRIS.png"
                               class="inline-flex items-center justify-center gap-1.5 bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-2 px-4 rounded-xl text-[11px] transition-colors cursor-pointer border border-white/5 select-none w-full"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                <span>Unduh Kode QRIS</span>
                            </a>
                        </div>
                        
                        <div x-show="step === 'verifying'" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-blue-500/10 border border-blue-500/20 text-blue-400 animate-pulse" style="display: none;">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                            Memverifikasi Transaksi...
                        </div>

                        <div x-show="step === 'success'" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 border border-emerald-500/20 text-emerald-400" style="display: none;">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                            Pembayaran Berhasil!
                        </div>

                        <p class="text-[10px] text-zinc-500 leading-relaxed max-w-xs mx-auto" x-show="step === 'qris'">
                            Pindai kode QRIS di atas untuk menyelesaikan pembelian.
                        </p>
                        
                        <p class="text-[10px] text-zinc-500 leading-relaxed max-w-xs mx-auto" x-show="step === 'verifying'" style="display: none;">
                            Mendapatkan data pelunasan dari server merchant...
                        </p>

                        <p class="text-[10px] text-emerald-400/80 font-medium leading-relaxed max-w-xs mx-auto" x-show="step === 'success'" style="display: none;" x-text="'+' + selectedCredits + ' Token sedang ditambahkan ke workspace Anda...'">
                        </p>
                    </div>
                </div>
            </div>/div>

        </div>
    </div>

</div>
@endsection
