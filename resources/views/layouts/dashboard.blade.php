<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-zinc-950 scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Dashboard') - BuatJalan</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Livewire Styles (Loads Alpine.js dynamically in Livewire v3) -->
        @livewireStyles

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body x-data="{ 
              sidebarOpen: window.innerWidth >= 768, 
              activeWorkspace: 'Acme Corp', 
              settingsModalOpen: false, 
              activeSettingsTab: 'general', 
              helpModalOpen: false,
              checkSize() {
                  this.sidebarOpen = window.innerWidth >= 768;
              }
          }" 
          x-init="checkSize()"
          @resize.window="checkSize()"
          class="bg-zinc-950 text-white min-h-screen font-sans antialiased overflow-hidden flex items-center justify-center">
        
        <div class="relative w-full h-screen bg-zinc-950 flex overflow-hidden">
            
            {{-- Mobile Sidebar Backdrop Overlay --}}
            <div 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false" 
                class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 md:hidden animate-in fade-in duration-200"
                style="display: none;"
            ></div>

            {{-- SIDEBAR COMPONENT --}}
            <aside 
                x-show="sidebarOpen"
                x-transition:enter="transition-all duration-300 ease-in-out"
                x-transition:enter-start="w-0 opacity-0"
                x-transition:enter-end="w-[260px] opacity-100"
                x-transition:leave="transition-all duration-300 ease-in-out"
                x-transition:leave-start="w-[260px] opacity-100"
                x-transition:leave-end="w-0 opacity-0"
                class="fixed md:relative inset-y-0 left-0 h-full w-[260px] shrink-0 bg-zinc-900 border-r border-white/10 p-3 flex flex-col z-40 md:z-30"
            >
                {{-- Sidebar Header --}}
                <div class="flex items-center justify-between mb-4">
                    {{-- Workspace Switcher --}}
                    <div class="relative flex-1" x-data="{ open: false }">
                        <div @click="open = !open" class="flex items-center justify-between px-2 py-2 rounded-lg hover:bg-white/5 cursor-pointer transition-colors select-none group">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('assets/icon-images/buatjalan-icon.png') }}" class="w-8 h-8 rounded-[6px] object-cover shadow-sm bg-zinc-850 shrink-0" alt="BuatJalan">
                                <div class="flex flex-col overflow-hidden">
                                    <span class="text-[13px] font-medium leading-none mb-1 text-white truncate max-w-[120px]" x-text="activeWorkspace">Acme Corp</span>
                                    <span class="text-[11px] text-zinc-400 leading-none">Pro Plan</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-zinc-500 group-hover:text-white transition-colors shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>

                        <div x-show="open" @click.away="open = false" class="absolute top-[44px] left-0 w-full bg-zinc-900 border border-white/10 rounded-lg shadow-2xl z-50 py-1 flex flex-col gap-0.5">
                            <template x-for="ws in ['Acme Corp', 'Personal Workspace', 'Client Sandbox']">
                                <div @click="activeWorkspace = ws; open = false" 
                                     class="px-3 py-2 mx-1 text-[13px] rounded-md cursor-pointer transition-colors"
                                     :class="activeWorkspace === ws ? 'bg-white/10 text-white font-medium' : 'text-zinc-300 hover:bg-white/5'">
                                    <span x-text="ws"></span>
                                </div>
                            </template>
                            <div class="h-px bg-white/10 my-1 mx-2"></div>
                            <div class="px-3 py-2 mx-1 text-[13px] text-zinc-400 hover:bg-white/5 rounded-md cursor-pointer flex items-center gap-2 transition-colors">
                                <span class="text-[16px] leading-none mb-0.5">+</span> Create Workspace
                            </div>
                        </div>
                    </div>

                    {{-- Close Sidebar Button (Mobile only) --}}
                    <button @click="sidebarOpen = false" class="md:hidden p-1.5 rounded-md text-zinc-400 hover:bg-white/5 hover:text-white transition-colors shrink-0 ml-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                {{-- Navigation Items --}}
                <div class="flex-1 overflow-y-auto scrollbar-none flex flex-col gap-4 mt-2">
                    
                    {{-- Group 1: General --}}
                    <div class="flex flex-col gap-0.5">
                        {{-- Item: Home --}}
                        <a href="/dashboard" wire:navigate class="group flex items-center justify-between px-2.5 py-[7px] rounded-[6px] cursor-pointer transition-all duration-200 select-none {{ ($activeId ?? '') === 'home' ? 'bg-white/10 text-white font-medium' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-[16px] h-[16px] {{ ($activeId ?? '') === 'home' ? 'text-white' : 'text-zinc-400 group-hover:text-white' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                                <span class="text-[13px] tracking-wide">Home</span>
                            </div>
                        </a>

                        {{-- Item: Inbox --}}
                        <a href="/dashboard/inbox" wire:navigate class="group flex items-center justify-between px-2.5 py-[7px] rounded-[6px] cursor-pointer transition-all duration-200 select-none {{ ($activeId ?? '') === 'inbox' ? 'bg-white/10 text-white font-medium' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-[16px] h-[16px] {{ ($activeId ?? '') === 'inbox' ? 'text-white' : 'text-zinc-400 group-hover:text-white' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                                <span class="text-[13px] tracking-wide">Inbox</span>
                            </div>
                            <span class="flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-medium rounded-full bg-white/10 text-white">3</span>
                        </a>
                    </div>

                    {{-- Group 2: Workspace --}}
                    <div class="flex flex-col gap-0.5">
                        <span class="px-2.5 mb-1 text-[11px] font-semibold tracking-wider text-zinc-500 uppercase">Workspace</span>
                        
                        {{-- Accordion Item: Projects --}}
                        <div x-data="{ open: {{ request()->is('dashboard/projects/*') ? 'true' : 'false' }} }" class="flex flex-col w-full">
                            <div @click="open = !open" class="group flex items-center justify-between px-2.5 py-[7px] rounded-[6px] cursor-pointer transition-all duration-200 select-none text-zinc-400 hover:bg-white/5 hover:text-white">
                                <div class="flex items-center gap-2.5">
                                    <svg class="w-[16px] h-[16px] text-zinc-400 group-hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                    <span class="text-[13px] tracking-wide">Projects</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-90' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                            <div x-show="open" class="flex flex-col gap-0.5 mt-0.5 relative pl-4">
                                <div class="absolute top-0 bottom-0 left-5 border-l border-white/10"></div>
                                @foreach(\App\Models\Project::with('techStacks')->get() as $p)
                                    <a href="/dashboard/projects/{{ $p->slug }}" wire:navigate class="group flex items-center gap-2.5 px-2.5 py-[7px] rounded-[6px] cursor-pointer transition-all duration-200 {{ request()->is('dashboard/projects/' . $p->slug) ? 'bg-white/10 text-white font-medium' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">
                                        @if($p->logo_url)
                                            <img src="{{ $p->logo_url }}" class="w-4 h-4 shrink-0 object-contain filter group-hover:brightness-125 transition-all" alt="{{ $p->title }}">
                                        @else
                                            <img src="{{ asset('assets/icon-images/cube-icon.png') }}" class="w-4 h-4 shrink-0" alt="Cube Roadmap"> 
                                        @endif
                                        <span class="text-[13px] truncate">{{ $p->title }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Item: Analytics --}}
                        <a href="/dashboard/analytics" wire:navigate class="group flex items-center justify-between px-2.5 py-[7px] rounded-[6px] cursor-pointer transition-all duration-200 select-none {{ ($activeId ?? '') === 'analytics' ? 'bg-white/10 text-white font-medium' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-[16px] h-[16px] {{ ($activeId ?? '') === 'analytics' ? 'text-white' : 'text-zinc-400 group-hover:text-white' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                                <span class="text-[13px] tracking-wide">Analytics</span>
                            </div>
                        </a>

                        {{-- Item: Pricing --}}
                        <a href="/dashboard/pricing" wire:navigate class="group flex items-center justify-between px-2.5 py-[7px] rounded-[6px] cursor-pointer transition-all duration-200 select-none {{ ($activeId ?? '') === 'pricing' ? 'bg-white/10 text-white font-medium' : 'text-zinc-400 hover:bg-white/5 hover:text-white' }}">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-[16px] h-[16px] {{ ($activeId ?? '') === 'pricing' ? 'text-white' : 'text-zinc-400 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                                <span class="text-[13px] tracking-wide">Pricing</span>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- Bottom Items --}}
                <div class="mt-auto pt-4 border-t border-white/10 flex flex-col gap-0.5">
                    {{-- Item: Settings --}}
                    <button @click="settingsModalOpen = true" class="w-full group flex items-center justify-between px-2.5 py-[7px] rounded-[6px] cursor-pointer transition-all duration-200 select-none text-zinc-400 hover:bg-white/5 hover:text-white">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-[16px] h-[16px] text-zinc-400 group-hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            <span class="text-[13px] tracking-wide">Settings</span>
                        </div>
                        <kbd class="hidden group-hover:inline-flex items-center justify-center h-5 px-1.5 text-[10px] font-medium font-mono text-zinc-400 bg-zinc-800 border border-white/10 rounded-[4px]">⌘,</kbd>
                    </button>

                    {{-- Item: Log out --}}
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="group flex items-center justify-between px-2.5 py-[7px] rounded-[6px] cursor-pointer transition-all duration-200 select-none text-zinc-400 hover:bg-white/5 hover:text-white">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-[16px] h-[16px] text-zinc-400 group-hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            <span class="text-[13px] tracking-wide">Log out</span>
                        </div>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </aside>

            {{-- MAIN CONTENT WRAPPER --}}
            <div class="flex-1 bg-zinc-950 flex flex-col min-w-0 transition-all duration-300">
                
                {{-- Header / Navbar --}}
                <header class="h-14 border-b border-white/10 flex items-center px-4 justify-between bg-zinc-900/30 shrink-0 z-20">
                    <div class="flex items-center gap-3">
                        {{-- Collapse Sidebar Trigger --}}
                        <button 
                            @click="sidebarOpen = !sidebarOpen"
                            class="p-1.5 rounded-md text-zinc-400 hover:bg-white/5 hover:text-white transition-colors"
                        >
                            <svg x-show="sidebarOpen" class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><path d="M14 9l-3 3 3 3"></path></svg>
                            <svg x-show="!sidebarOpen" class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><path d="M11 15l3-3-3-3"></path></svg>
                        </button>
                        {{-- Breadcrumbs --}}
                        <div class="flex items-center gap-2 text-sm text-zinc-400">
                            @if(request()->is('dashboard/projects/*'))
                                <span class="truncate" x-text="activeWorkspace">Acme Corp</span>
                                <span>/</span>
                                <span class="text-zinc-400">Projects</span>
                                <span>/</span>
                                <span class="font-medium text-white truncate">@yield('page_title', 'Dashboard')</span>
                            @elseif(in_array($activeId ?? '', ['home', 'inbox']))
                                <span class="font-medium text-white truncate">@yield('page_title', 'Dashboard')</span>
                            @else
                                <span class="truncate" x-text="activeWorkspace">Acme Corp</span>
                                <span>/</span>
                                <span class="font-medium text-white truncate">@yield('page_title', 'Dashboard')</span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- User Profile Display --}}
                    @auth
                        <div class="flex items-center gap-4 {{ request()->is('dashboard/projects/*') ? 'hidden md:flex' : '' }}">
                            {{-- Help Trigger Button --}}
                            <button 
                                @click="helpModalOpen = true" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-zinc-400 hover:text-white hover:bg-white/5 border border-white/10 transition-all cursor-pointer select-none"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                <span>Help</span>
                            </button>

                            <div class="h-4 w-px bg-white/10 hidden sm:block"></div>

                            <div class="hidden sm:flex sm:flex-col sm:text-right">
                                <span class="text-[12px] font-semibold text-white leading-tight">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] text-zinc-400 leading-tight">{{ auth()->user()->email }}</span>
                            </div>
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" class="w-8 h-8 rounded-full border border-white/10 object-cover" alt="{{ auth()->user()->name }}">
                            @else
                                <div class="w-8 h-8 rounded-full border border-white/10 bg-white/5 flex items-center justify-center text-xs font-semibold text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    @endauth
                </header>

                {{-- Dashboard View Body --}}
                <div class="flex-1 p-6 md:p-8 overflow-y-auto scrollbar-none z-10">
                    @yield('content')
                </div>
        </div>

        {{-- GLOBAL SETTINGS MODAL DIALOG --}}
        <div 
            x-show="settingsModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            x-transition
            style="display: none;"
        >
            {{-- Click away --}}
            <div class="absolute inset-0" @click="settingsModalOpen = false"></div>
            
            {{-- Modal Card --}}
            <div class="relative bg-zinc-900 border border-white/10 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl z-10 animate-in fade-in zoom-in-95 duration-150 flex flex-col h-[500px]">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-6 border-b border-white/5 bg-zinc-900/50">
                    <div>
                        <h2 class="text-lg font-bold text-white">Pengaturan Workspace</h2>
                        <p class="text-xs text-zinc-400 mt-1">Konfigurasi preferensi general, API key, dan sistem langganan.</p>
                    </div>
                    <button @click="settingsModalOpen = false" class="p-1.5 rounded-md text-zinc-500 hover:bg-white/5 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                {{-- Modal Body: Split view with tabs on left and contents on right --}}
                <div class="flex flex-1 overflow-hidden">
                    
                    {{-- Left Tab Panel --}}
                    <div class="w-48 border-r border-white/5 bg-zinc-950/40 p-4 space-y-1">
                        <button 
                            @click="activeSettingsTab = 'general'"
                            class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer"
                            :class="activeSettingsTab === 'general' ? 'bg-primary/10 text-primary' : 'text-zinc-400 hover:bg-white/5 hover:text-white'"
                        >
                            General (Umum)
                        </button>
                        <button 
                            @click="activeSettingsTab = 'api'"
                            class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer"
                            :class="activeSettingsTab === 'api' ? 'bg-primary/10 text-primary' : 'text-zinc-400 hover:bg-white/5 hover:text-white'"
                        >
                            API & Integrasi
                        </button>
                        <button 
                            @click="activeSettingsTab = 'billing'"
                            class="w-full text-left px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer"
                            :class="activeSettingsTab === 'billing' ? 'bg-primary/10 text-primary' : 'text-zinc-400 hover:bg-white/5 hover:text-white'"
                        >
                            Token & Billing
                        </button>
                    </div>

                    {{-- Right Content Panel --}}
                    <div class="flex-1 p-6 overflow-y-auto custom-scrollbar space-y-6">
                        
                        {{-- TAB 1: GENERAL --}}
                        <div x-show="activeSettingsTab === 'general'" class="space-y-4" x-transition>
                            {{-- Workspace Name --}}
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Nama Workspace</label>
                                <input 
                                    type="text" 
                                    x-model="activeWorkspace"
                                    class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs focus:outline-none focus:border-primary transition-all"
                                />
                            </div>

                            {{-- Language selection --}}
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Bahasa Default</label>
                                <select class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs focus:outline-none focus:border-primary transition-all">
                                    <option value="id" selected>Bahasa Indonesia</option>
                                    <option value="en">English (US)</option>
                                </select>
                            </div>

                            {{-- Theme Toggle --}}
                            <div class="flex items-center justify-between p-3 bg-zinc-950/40 border border-white/5 rounded-xl">
                                <div>
                                    <div class="text-xs font-semibold text-white">Gunakan Dark Mode</div>
                                    <div class="text-[10px] text-zinc-500 mt-0.5">Selalu menggunakan tema gelap premium.</div>
                                </div>
                                <div class="w-9 h-5 bg-primary/20 border border-primary/40 rounded-full p-0.5 cursor-not-allowed">
                                    <div class="w-3.5 h-3.5 bg-primary rounded-full translate-x-4.5 transition-transform"></div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: API KEY & INTEGRATION --}}
                        <div x-show="activeSettingsTab === 'api'" class="space-y-4" x-transition style="display: none;">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Gemini API Key</label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        value="gemini-api-key-dummy-val-12345"
                                        disabled
                                        class="w-full bg-zinc-950/60 border border-white/5 rounded-xl p-3 text-zinc-500 text-xs select-none"
                                    />
                                    <span class="absolute right-3 top-3 text-[10px] text-primary bg-primary/10 border border-primary/20 px-1.5 py-0.5 rounded">Connected</span>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">OpenAI API Key (Optional)</label>
                                <input 
                                    type="password" 
                                    placeholder="Ketik OpenAI API Key..."
                                    class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs focus:outline-none focus:border-primary transition-all"
                                />
                            </div>

                            {{-- GitHub Integration status --}}
                            <div class="flex items-center justify-between p-3 bg-zinc-950/40 border border-white/5 rounded-xl mt-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.167 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.464-1.11-1.464-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.137 20.162 22 16.418 22 12c0-5.523-4.477-10-10-10z"></path></svg>
                                    <div>
                                        <div class="text-xs font-semibold text-white">GitHub Integration</div>
                                        <div class="text-[10px] text-zinc-500 mt-0.5">Telah terhubung sebagai @vibecoder-creator</div>
                                    </div>
                                </div>
                                <span class="text-[10px] text-emerald-400 font-semibold bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full">Aktif</span>
                            </div>
                        </div>

                        {{-- TAB 3: TOKEN & BILLING --}}
                        <div x-show="activeSettingsTab === 'billing'" class="space-y-4" x-transition style="display: none;">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 bg-zinc-950/40 border border-white/5 rounded-xl">
                                    <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Paket Saat Ini</div>
                                    <div class="text-lg font-bold text-white mt-1">Pro Plan</div>
                                    <div class="text-xs text-zinc-400 mt-0.5">Rp 99.000 / bulan</div>
                                </div>
                                <div class="p-4 bg-zinc-950/40 border border-white/5 rounded-xl flex items-center justify-between">
                                    <div>
                                        <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Sisa Quota</div>
                                        <div class="text-lg font-bold text-white mt-1">20 Emerald</div>
                                    </div>
                                    <img src="{{ asset('assets/svg/emerald-icon.svg') }}" class="w-6 h-6" alt="Emerald Token">
                                </div>
                            </div>

                            {{-- Payment History Table --}}
                            <div class="space-y-2 mt-4">
                                <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Riwayat Tagihan</label>
                                <div class="border border-white/5 bg-zinc-950/20 rounded-xl overflow-hidden text-xs">
                                    <div class="grid grid-cols-3 bg-zinc-950/60 p-2.5 font-semibold text-zinc-400 border-b border-white/5">
                                        <span>Tanggal</span>
                                        <span>Paket</span>
                                        <span class="text-right">Nominal</span>
                                    </div>
                                    <div class="grid grid-cols-3 p-2.5 border-b border-white/5 text-white">
                                        <span>28 Jun 2026</span>
                                        <span class="text-zinc-400">Pro Plan (Monthly)</span>
                                        <span class="text-right font-medium">Rp 99.000</span>
                                    </div>
                                    <div class="grid grid-cols-3 p-2.5 text-white">
                                        <span>28 Mei 2026</span>
                                        <span class="text-zinc-400">Pro Plan (Monthly)</span>
                                        <span class="text-right font-medium">Rp 99.000</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end p-4 border-t border-white/5 bg-zinc-900/50">
                    <button 
                        @click="settingsModalOpen = false"
                        class="bg-primary text-primary-foreground hover:bg-primary/90 px-5 py-2 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                    >
                        Selesai & Simpan
                    </button>
                </div>

            </div>
        </div>

        {{-- GLOBAL HELP MODAL DIALOG --}}
        <div 
            x-show="helpModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            x-transition
            style="display: none;"
        >
            {{-- Click away --}}
            <div class="absolute inset-0" @click="helpModalOpen = false"></div>
            
            {{-- Modal Card --}}
            <div class="relative bg-zinc-900 border border-white/10 rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl z-10 animate-in fade-in zoom-in-95 duration-150 flex flex-col h-[520px]">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-6 border-b border-white/5 bg-zinc-900/50">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">💡</span>
                        <div>
                            <h2 class="text-lg font-bold text-white">Pusat Bantuan BuatJalan</h2>
                            <p class="text-xs text-zinc-400 mt-1">Panduan praktis, FAQ, dan cara mengintegrasikan AI Architect.</p>
                        </div>
                    </div>
                    <button @click="helpModalOpen = false" class="p-1.5 rounded-md text-zinc-500 hover:bg-white/5 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                {{-- Modal Body (Scrollable Contents) --}}
                <div class="flex-1 p-6 overflow-y-auto custom-scrollbar space-y-6 text-xs text-zinc-300 leading-relaxed">
                    
                    {{-- Section 1: Cara Kerja Kredit --}}
                    <div class="space-y-2">
                        <h3 class="font-bold text-white text-sm flex items-center gap-1.5">
                            ⚡ Sistem Kredit BuatJalan
                        </h3>
                        <p>Setiap tindakan di platform ini dihitung berdasarkan koin kredit yang ada pada workspace Anda:</p>
                        <ul class="list-disc pl-5 space-y-1 mt-1 text-zinc-400">
                            <li><strong>Membuat Proyek Baru</strong>: Mengonsumsi <strong>10 Kredit</strong>.</li>
                            <li><strong>Mengubah / Edit Proyek</strong>: Mengonsumsi <strong>10 Kredit</strong>.</li>
                            <li><strong>Unduh & Salin Context</strong>: Gratis <strong>(0 Kredit)</strong>.</li>
                        </ul>
                    </div>

                    <hr class="border-white/5">

                    {{-- Section 2: Integrasi AI Coding --}}
                    <div class="space-y-2">
                        <h3 class="font-bold text-white text-sm flex items-center gap-1.5">
                            🤖 Integrasi dengan Cursor / VS Code
                        </h3>
                        <p>Dapatkan alur kerja koding 10x lebih cepat dengan cara berikut:</p>
                        <ol class="list-decimal pl-5 space-y-1.5 text-zinc-400">
                            <li>Masuk ke tab <strong>Export Context</strong> di detail proyek Anda.</li>
                            <li>Klik <strong>Export as File</strong> untuk mengunduh berkas Markdown context.</li>
                            <li>Simpan file tersebut di folder root proyek kodingan Anda.</li>
                            <li>Ketik prompt Anda di Cursor atau Github Copilot dengan memanggil file tersebut (contoh: <code>"Buat model dan seeder baru sesuai skema di @[file-context].md"</code>).</li>
                        </ol>
                    </div>

                    <hr class="border-white/5">

                    {{-- Section 3: FAQ --}}
                    <div class="space-y-4">
                        <h3 class="font-bold text-white text-sm">
                            🙋 FAQ (Tanya Jawab Sering Diajukan)
                        </h3>
                        
                        <div class="space-y-1">
                            <h4 class="font-semibold text-white">Apakah saldo kredit yang dibeli memiliki masa kedaluwarsa?</h4>
                            <p class="text-zinc-400">Tidak. Saldo kredit yang Anda beli aktif selamanya selama akun Anda terdaftar di platform kami.</p>
                        </div>
                        
                        <div class="space-y-1">
                            <h4 class="font-semibold text-white">Bagaimana cara merevisi / memperbaiki proyek?</h4>
                            <p class="text-zinc-400">Klik tombol <strong>Ubah Project</strong> di halaman detail proyek. Ketik instruksi spesifik kepada AI (misal: "tambahkan kolom 'telepon' pada tabel users dan sesuaikan roadmap-nya"), AI akan otomatis merancang ulang semuanya secara instan.</p>
                        </div>

                        <div class="space-y-1">
                            <h4 class="font-semibold text-white">Mengapa halaman proyek saya kosong?</h4>
                            <p class="text-zinc-400">Pastikan koneksi internet Anda stabil. Jika terindikasi ada masalah timeout dari model AI saat proses regenerasi, riwayat detail proyek Anda tetap aman tersimpan di database.</p>
                        </div>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end p-4 border-t border-white/5 bg-zinc-900/50">
                    <button 
                        @click="helpModalOpen = false"
                        class="bg-zinc-800 hover:bg-zinc-700 border border-white/5 text-white px-5 py-2 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                    >
                        Tutup Bantuan
                    </button>
                </div>

            </div>
        </div>

        @livewireScripts
    </body>
</html>
