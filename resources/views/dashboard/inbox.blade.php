@extends('layouts.dashboard', ['activeId' => $activeId])

@section('title', $title)
@section('page_title', $title)

@section('content')
    <div 
        x-data="{ 
            activeMessage: null,
            searchQuery: '',
            messages: [
                {
                    id: 1,
                    type: 'system',
                    sender: 'System Update',
                    avatar: '⚙️',
                    avatarBg: 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                    subject: 'Sistem Update: Model Gemini Flash 3 Kini Lebih Cepat ⚡',
                    date: 'Hari ini',
                    unread: true,
                    content: 'Halo Developer & Vibecoder!\n\nKami baru saja merilis pembaruan server pada engine kecerdasan buatan BuatJalan. Kami kini menggunakan model Gemini Flash 3 versi stabil terbaru.\n\nKeuntungan update ini:\n- Waktu generate PRD & Arsitektur Database 2x lebih cepat.\n- Peningkatan kualitas referensi relasi tabel database.\n- Konsumsi koin tetap hemat (10 Kredit per Generate).\n\nSilakan coba membuat project baru dan rasakan peningkatannya. Jika Anda memiliki saran pengembangan, jangan ragu untuk memberikan masukan kepada kami melalui menu Bantuan.\n\nSelamat berkreasi!\nTim Developer BuatJalan'
                },
                {
                    id: 2,
                    type: 'billing',
                    sender: 'Billing Platform',
                    avatar: '💳',
                    avatarBg: 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                    subject: 'Top Up Kredit Berhasil - Popular Pack Ditambahkan 🌟',
                    date: 'Kemarin',
                    unread: true,
                    content: 'Terima kasih atas pembelian Anda!\n\nPembayaran Anda via Midtrans QRIS telah berhasil diverifikasi oleh sistem kami secara otomatis.\n\nDetail Pembelian:\n- Paket: Popular Pack\n- Jumlah Kredit: +150 Kredit\n- Total Biaya: Rp 49.000 (Lunas)\n- Invoice ID: INV-20260705-1940-A\n- Waktu Transaksi: 05 Juli 2026, 19:40 WIB\n\nSaldo kredit workspace Anda telah berhasil diperbarui. Periksa halaman Pricing untuk melihat detail kuota Anda. Terima kasih telah mendukung keberlangsungan platform ini!'
                },
                {
                    id: 3,
                    type: 'invitation',
                    sender: 'Workspace Invitation',
                    avatar: '🤝',
                    avatarBg: 'bg-purple-500/10 text-purple-400 border border-purple-500/20',
                    subject: 'Undangan Kolaborasi: Bergabung ke Workspace \'Airlangga Dev\' 🚀',
                    date: '3 hari yang lalu',
                    unread: true,
                    content: 'Ahlul Firdaus (ahlulffirdaus@gmail.com) mengundang Anda untuk berkolaborasi di workspace miliknya: \'Airlangga Dev\'.\n\nKeuntungan Kolaborasi:\n- Akses bersama untuk mengelola proyek aktif.\n- Penggunaan koin kredit workspace terpusat.\n- Berbagi rancangan PRD, skema database, dan roadmap belajar.\n\nSilakan tentukan keputusan Anda dengan menekan tombol Terima atau Tolak di bawah.',
                    workspace_name: 'Airlangga Dev',
                    invitation_id: 42,
                    status: 'pending'
                }
            ],
            
            get filteredMessages() {
                if (this.searchQuery.trim() === '') {
                    return this.messages;
                }
                let query = this.searchQuery.toLowerCase();
                return this.messages.filter(m => 
                    m.subject.toLowerCase().includes(query) || 
                    m.sender.toLowerCase().includes(query) || 
                    m.content.toLowerCase().includes(query)
                );
            },
            
            openMessage(msg) {
                this.activeMessage = msg;
                msg.unread = false;
            },

            respondInvitation(msg, response) {
                msg.status = response;
                if (response === 'accepted') {
                    msg.content = 'Anda telah MENERIMA undangan kolaborasi ini.\n\nSelamat! Sekarang Anda resmi menjadi anggota dari Workspace \'Airlangga Dev\'. Anda dapat mengakses seluruh proyek yang terdaftar di bawah workspace ini melalui menu navigasi samping.';
                    msg.subject = '✓ Undangan Kolaborasi Diterima: Workspace \'Airlangga Dev\'';
                } else {
                    msg.content = 'Anda telah MENOLAK undangan kolaborasi ini.\n\nUndangan ini telah diarsipkan dan tidak lagi berlaku.';
                    msg.subject = '✕ Undangan Kolaborasi Ditolak: Workspace \'Airlangga Dev\'';
                }
            }
        }"
        class="flex flex-col gap-6 font-sans"
    >
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">Inbox</h1>
                <p class="text-sm text-zinc-400 mt-1">Pemberitahuan penting, tips, dan riwayat tagihan akun Anda.</p>
            </div>
            
            {{-- Search Bar --}}
            <div class="relative w-full sm:w-72">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input 
                    type="text" 
                    x-model="searchQuery" 
                    placeholder="Cari pesan..." 
                    class="w-full bg-zinc-900 border border-white/10 rounded-lg pl-9 pr-4 py-2 text-xs text-white placeholder:text-zinc-500 focus:outline-none focus:border-primary transition-colors"
                />
            </div>
        </div>

        {{-- INBOX LIST CARD --}}
        <div class="border border-white/10 bg-zinc-900/40 rounded-xl overflow-hidden backdrop-blur-md">
            
            {{-- List Container --}}
            <div class="divide-y divide-white/5">
                
                {{-- Empty State --}}
                <div x-show="filteredMessages.length === 0" class="p-12 text-center flex flex-col items-center justify-center" style="display: none;">
                    <svg class="w-10 h-10 text-zinc-600 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"></path></svg>
                    <p class="text-sm font-semibold text-white">Tidak ada pesan ditemukan</p>
                    <p class="text-xs text-zinc-500 mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                </div>

                {{-- Message Item Template --}}
                <template x-for="msg in filteredMessages" :key="msg.id">
                    <div 
                        @click="openMessage(msg)"
                        class="p-4 flex items-center justify-between gap-4 hover:bg-white/5 cursor-pointer transition-all select-none relative group"
                        :class="msg.unread ? 'bg-primary/5 hover:bg-primary/10' : ''"
                    >
                        {{-- Left Side: Sender Avatar & Info --}}
                        <div class="flex items-center gap-3.5 min-w-0">
                            {{-- Avatar icon indicator --}}
                            <div 
                                class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 select-none"
                                :class="msg.avatarBg"
                                x-text="msg.avatar"
                            ></div>
                            
                            {{-- Subject and Sender --}}
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold" :class="msg.unread ? 'text-primary' : 'text-zinc-400'" x-text="msg.sender"></span>
                                    <template x-if="msg.unread">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                                    </template>
                                </div>
                                <span class="text-sm font-semibold text-white mt-0.5 truncate pr-8" x-text="msg.subject"></span>
                                <span class="text-xs text-zinc-400 mt-0.5 line-clamp-1" x-text="msg.content"></span>
                            </div>
                        </div>

                        {{-- Right Side: Date & Arrow --}}
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs text-zinc-500 font-medium" x-text="msg.date"></span>
                            <svg class="w-4 h-4 text-zinc-600 group-hover:text-white transition-all transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </div>
                </template>

            </div>
        </div>

        {{-- DETAIL MESSAGE MODAL DIALOG --}}
        <div 
            x-show="activeMessage !== null" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            x-transition
            style="display: none;"
        >
            {{-- Click away --}}
            <div class="absolute inset-0" @click="activeMessage = null"></div>
            
            {{-- Modal Card --}}
            <div 
                x-show="activeMessage !== null"
                class="relative bg-zinc-900 border border-white/10 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl z-10 animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[85vh]"
            >
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-6 border-b border-white/5 bg-zinc-900/50">
                    <div class="flex items-center gap-3">
                        <div 
                            class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs select-none shrink-0"
                            :class="activeMessage ? activeMessage.avatarBg : ''"
                            x-text="activeMessage ? activeMessage.avatar : ''"
                        ></div>
                        <div>
                            <div class="text-xs text-zinc-500">
                                Dari: <span class="font-semibold text-white" x-text="activeMessage ? activeMessage.sender : ''"></span>
                            </div>
                            <div class="text-[10px] text-zinc-500 mt-0.5" x-text="activeMessage ? activeMessage.date : ''"></div>
                        </div>
                    </div>
                    
                    <button @click="activeMessage = null" class="p-1.5 rounded-md text-zinc-500 hover:bg-white/5 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 overflow-y-auto custom-scrollbar flex-1 space-y-4">
                    <h2 class="text-base font-bold text-white" x-text="activeMessage ? activeMessage.subject : ''"></h2>
                    
                    {{-- Invitation Info Card (if invitation) --}}
                    <template x-if="activeMessage && activeMessage.type === 'invitation'">
                        <div class="bg-zinc-950/60 border border-white/5 rounded-xl p-4 space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-zinc-500 font-bold uppercase tracking-wider">Status Undangan</span>
                                <span 
                                    class="px-2 py-0.5 rounded-full font-semibold uppercase tracking-wider text-[9px]"
                                    :class="{
                                        'bg-amber-500/10 text-amber-400 border border-amber-500/20': activeMessage.status === 'pending',
                                        'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20': activeMessage.status === 'accepted',
                                        'bg-rose-500/10 text-rose-400 border border-rose-500/20': activeMessage.status === 'declined'
                                    }"
                                    x-text="activeMessage.status === 'pending' ? 'Menunggu Keputusan' : (activeMessage.status === 'accepted' ? 'Diterima' : 'Ditolak')"
                                ></span>
                            </div>
                            
                            <div class="h-px bg-white/5"></div>
                            
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-lg font-bold text-purple-400">
                                    🏢
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-white" x-text="activeMessage.workspace_name"></div>
                                    <div class="text-[10px] text-zinc-500">Workspace Kolaborasi</div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <p class="text-xs text-zinc-300 whitespace-pre-line leading-relaxed" x-text="activeMessage ? activeMessage.content : ''"></p>
                    
                    {{-- Action buttons for pending invitation --}}
                    <template x-if="activeMessage && activeMessage.type === 'invitation' && activeMessage.status === 'pending'">
                        <div class="flex items-center gap-3 pt-2">
                            <button 
                                @click="respondInvitation(activeMessage, 'accepted')" 
                                class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-2 rounded-lg text-xs cursor-pointer transition-colors flex items-center justify-center gap-1.5"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"></path></svg>
                                <span>Terima Undangan</span>
                            </button>
                            <button 
                                @click="respondInvitation(activeMessage, 'declined')" 
                                class="flex-1 bg-zinc-800 hover:bg-zinc-700 hover:text-rose-400 text-zinc-300 font-semibold py-2 rounded-lg text-xs cursor-pointer border border-white/5 transition-colors flex items-center justify-center gap-1.5"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                <span>Tolak Undangan</span>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end p-4 border-t border-white/5 bg-zinc-900/50">
                    <button 
                        @click="activeMessage = null"
                        class="bg-primary text-primary-foreground hover:bg-primary/90 px-5 py-2 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                    >
                        Tutup Pesan
                    </button>
                </div>
            </div>
        </div>

    </div>
@endsection
