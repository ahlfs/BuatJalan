@extends('layouts.dashboard', ['activeId' => $activeId])

@section('title', $title)
@section('page_title', $title)

@section('content')
    @php
        $dbMessages = auth()->user()->inboxes()->orderBy('created_at', 'desc')->get()->map(function($msg) {
            return [
                'id' => $msg->id,
                'type' => $msg->type,
                'sender' => $msg->sender,
                'avatar' => $msg->avatar,
                'avatarBg' => $msg->avatar_bg,
                'subject' => $msg->subject,
                'date' => $msg->created_at->diffForHumans(),
                'unread' => $msg->unread,
                'content' => $msg->content,
                'workspace_name' => $msg->workspace ? $msg->workspace->name : null,
                'invitation_id' => $msg->id,
                'status' => $msg->invitation_status ?? 'pending'
            ];
        });
    @endphp

    <div 
        x-data='{ 
            activeMessage: null,
            searchQuery: "",
            messages: @json($dbMessages),
            
            get filteredMessages() {
                if (this.searchQuery.trim() === "") {
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
                if (msg.unread) {
                    msg.unread = false;
                    fetch("/dashboard/inbox/read/" + msg.id, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json"
                        }
                    });
                }
            },

            respondInvitation(msg, response) {
                let form = document.getElementById("respond-invitation-form");
                form.action = "/dashboard/inbox/invitation/" + msg.invitation_id + "/respond";
                
                let mappedResponse = response === "declined" ? "rejected" : "accepted";
                document.getElementById("invitation-response-value").value = mappedResponse;
                
                form.submit();
            }
        }'
        class="flex flex-col gap-6 font-sans"
    >
        {{-- Hidden Form for Invitation Response --}}
        <form id="respond-invitation-form" method="POST" action="" class="hidden">
            @csrf
            <input type="hidden" name="response" id="invitation-response-value" value="">
        </form>

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
                            <div class="shrink-0 select-none">
                                <template x-if="msg.avatar && (msg.avatar.startsWith('http') || msg.avatar.startsWith('/') || msg.avatar.includes('.') || msg.avatar.startsWith('data:'))">
                                    <img :src="msg.avatar" class="w-9 h-9 rounded-full object-cover border border-white/10" alt="Avatar">
                                </template>
                                <template x-if="!msg.avatar || (!msg.avatar.startsWith('http') && !msg.avatar.startsWith('/') && !msg.avatar.includes('.') && !msg.avatar.startsWith('data:'))">
                                    <div 
                                        class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs"
                                        :class="msg.avatarBg"
                                        x-text="msg.avatar || '🤝'"
                                    ></div>
                                </template>
                            </div>
                            
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
                        <div class="shrink-0 select-none">
                            <template x-if="activeMessage && activeMessage.avatar && (activeMessage.avatar.startsWith('http') || activeMessage.avatar.startsWith('/') || activeMessage.avatar.includes('.') || activeMessage.avatar.startsWith('data:'))">
                                <img :src="activeMessage.avatar" class="w-9 h-9 rounded-full object-cover border border-white/10" alt="Avatar">
                            </template>
                            <template x-if="activeMessage && (!activeMessage.avatar || (!activeMessage.avatar.startsWith('http') && !activeMessage.avatar.startsWith('/') && !activeMessage.avatar.includes('.') && !activeMessage.avatar.startsWith('data:')))">
                                <div 
                                    class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs"
                                    :class="activeMessage.avatarBg"
                                    x-text="activeMessage.avatar || '🤝'"
                                ></div>
                            </template>
                        </div>
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
