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
              activeWorkspace: '{{ auth()->user()->currentWorkspace->name ?? 'Personal Workspace' }}', 
              settingsModalOpen: false, 
              createWorkspaceModalOpen: false,
              manageWorkspaceModalOpen: false,
              deleteWorkspaceModalOpen: false,
              deleteConfirmText: '',
              activeSettingsTab: 'general', 
              activeManageTab: 'tokens',
              apiProvider: 'buatjalan',
              testingApi: false,
              apiTestResult: '',
              testApi() {
                  this.testingApi = true;
                  this.apiTestResult = '';
                  setTimeout(() => {
                      this.testingApi = false;
                      this.apiTestResult = 'success';
                      setTimeout(() => { this.apiTestResult = ''; }, 3000);
                  }, 1200);
              },
              helpModalOpen: false,
              cancelInviteModalOpen: false,
              cancelInviteId: null,
              cancelInviteName: '',
              darkMode: true,
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
                        @php
                            $workspaceOwner = auth()->user()->currentWorkspace?->owner;
                            $ownerName = $workspaceOwner?->name ?? 'Pengguna';
                        @endphp
                        <div @click="open = !open" class="flex items-center justify-between px-2 py-2 rounded-lg hover:bg-white/5 cursor-pointer transition-colors select-none group">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('assets/icon-images/buatjalan-icon.png') }}" class="w-8 h-8 rounded-[6px] object-cover shadow-sm bg-zinc-850 shrink-0" alt="BuatJalan">
                                <div class="flex flex-col overflow-hidden">
                                    <span class="text-[13px] font-medium leading-none mb-1 text-white truncate max-w-[120px]">{{ auth()->user()->currentWorkspace->name ?? 'Select Workspace' }}</span>
                                    <span class="text-[10px] text-zinc-400 leading-none truncate max-w-[120px]">{{ $ownerName }}</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-zinc-500 group-hover:text-white transition-colors shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>

                        <div x-show="open" @click.away="open = false" class="absolute top-[44px] left-0 w-full bg-zinc-900 border border-white/10 rounded-lg shadow-2xl z-50 py-1 flex flex-col gap-0.5">
                            @foreach(auth()->user()->workspaces as $ws)
                                <form action="{{ route('workspaces.switch', $ws->id) }}" method="POST" class="m-0" id="switch-ws-form-{{ $ws->id }}">
                                    @csrf
                                    <button type="submit" 
                                         class="w-full text-left px-3 py-2 text-[13px] rounded-md cursor-pointer transition-colors {{ auth()->user()->current_workspace_id === $ws->id ? 'bg-white/10 text-white font-medium' : 'text-zinc-300 hover:bg-white/5' }}">
                                        <span>{{ $ws->name }}</span>
                                    </button>
                                </form>
                            @endforeach
                            <div class="h-px bg-white/10 my-1 mx-2"></div>
                            <div @click="createWorkspaceModalOpen = true; open = false;" class="px-3 py-2 mx-1 text-[13px] text-zinc-400 hover:bg-white/5 rounded-md cursor-pointer flex items-center gap-2 transition-colors">
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
                            @php
                                $unreadCount = auth()->user()->inboxes()->where('unread', true)->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-medium rounded-full bg-white/10 text-white">{{ $unreadCount }}</span>
                            @endif
                        </a>
                    </div>

                    {{-- Group 2: Workspace --}}
                    <div class="flex flex-col gap-0.5">
                        <span class="px-2.5 mb-1 text-[11px] font-semibold tracking-wider text-zinc-500 uppercase">Workspace</span>
                        
                        {{-- Item: Manage Workspace --}}
                        <div @click="manageWorkspaceModalOpen = true" class="group flex items-center justify-between px-2.5 py-[7px] rounded-[6px] cursor-pointer transition-all duration-200 select-none text-zinc-400 hover:bg-white/5 hover:text-white">
                            <div class="flex items-center gap-2.5">
                                <svg class="w-[16px] h-[16px] text-zinc-400 group-hover:text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                <span class="text-[13px] tracking-wide">Manage Workspace</span>
                            </div>
                        </div>

                        {{-- Accordion Item: Projects --}}
                        <div x-data="{ open: {{ request()->is('dashboard/projects/*') ? 'true' : 'false' }} }" class="flex flex-col w-full">
                            <div @click="open = !open" class="group flex items-center justify-between px-2.5 py-[7px] rounded-[6px] cursor-pointer transition-all duration-200 select-none text-zinc-400 hover:bg-white/5 hover:text-white">
                                <div class="flex items-center gap-2.5">
                                    <svg class="w-[16px] h-[16px] text-zinc-400 group-hover:text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                                    <span class="text-[13px] tracking-wide">Projects</span>
                                </div>
                                <svg class="w-3.5 h-3.5 text-zinc-500 transition-transform duration-200" :class="open ? 'rotate-90' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </div>
                            <div x-show="open">
                                <livewire:sidebar-projects :current-slug="request()->segment(3)" />
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
                        <div class="flex items-center gap-1.5 text-xs sm:text-sm text-zinc-400 min-w-0">
                            @if(request()->is('dashboard/projects/*'))
                                <span class="max-w-[70px] sm:max-w-[150px] md:max-w-[240px] inline-block truncate align-bottom" x-text="activeWorkspace">Acme Corp</span>
                                <span class="shrink-0">/</span>
                                <span class="text-zinc-400 shrink-0 hidden xs:inline-block">Projects</span>
                                <span class="shrink-0 hidden xs:inline-block">/</span>
                                <span class="font-medium text-white max-w-[80px] sm:max-w-[180px] md:max-w-[240px] inline-block truncate align-bottom">@yield('page_title', 'Dashboard')</span>
                            @elseif(in_array($activeId ?? '', ['home', 'inbox']))
                                <span class="font-medium text-white">@yield('page_title', 'Dashboard')</span>
                            @else
                                <span class="max-w-[80px] sm:max-w-[150px] md:max-w-[240px] inline-block truncate align-bottom" x-text="activeWorkspace">Acme Corp</span>
                                <span class="shrink-0">/</span>
                                <span class="font-medium text-white">@yield('page_title', 'Dashboard')</span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- User Profile Display --}}
                    @auth
                        <div class="flex items-center gap-4 {{ in_array($activeId ?? '', ['home', 'inbox']) ? 'flex' : 'hidden md:flex' }}">
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
                <div class="flex-1 p-6 md:p-8 overflow-y-auto scrollbar-none">
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
                <div class="flex flex-col md:flex-row flex-1 overflow-hidden">
                    
                    {{-- Left Tab Panel --}}
                    <div class="w-full md:w-48 border-b md:border-b-0 md:border-r border-white/5 bg-zinc-950/40 p-3 md:p-4 flex md:flex-col gap-2 md:gap-0 md:space-y-1 overflow-x-auto md:overflow-x-visible whitespace-nowrap md:whitespace-normal scrollbar-none shrink-0">
                        <button 
                            @click="activeSettingsTab = 'general'"
                            class="text-left px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer shrink-0 md:w-full"
                            :class="activeSettingsTab === 'general' ? 'bg-primary/10 text-primary' : 'text-zinc-400 hover:bg-white/5 hover:text-white'"
                        >
                            General (Umum)
                        </button>
                        <button 
                            @click="activeSettingsTab = 'api'"
                            class="text-left px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer shrink-0 md:w-full"
                            :class="activeSettingsTab === 'api' ? 'bg-primary/10 text-primary' : 'text-zinc-400 hover:bg-white/5 hover:text-white'"
                        >
                            API & Integrasi
                        </button>

                    </div>

                    {{-- Right Content Panel --}}
                    <div class="flex-1 p-6 overflow-y-auto custom-scrollbar space-y-6">
                        
                        {{-- TAB 1: GENERAL --}}
                        <form id="profile-update-form" action="{{ route('profile.update') }}" method="POST" x-show="activeSettingsTab === 'general'" class="space-y-4 m-0" x-transition>
                            @csrf
                            @method('PUT')

                            {{-- Edit Profile Name --}}
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Nama Profil</label>
                                <input 
                                    type="text" 
                                    name="name"
                                    value="{{ auth()->user()->name }}"
                                    required
                                    placeholder="Nama Anda..."
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
                                <button 
                                    type="button"
                                    @click="darkMode = !darkMode"
                                    class="w-9 h-5 rounded-full p-0.5 transition-colors cursor-pointer focus:outline-none flex items-center"
                                    :class="darkMode ? 'bg-primary/20 border border-primary/40' : 'bg-zinc-800 border border-white/10'"
                                >
                                    <div 
                                        class="w-3.5 h-3.5 bg-primary rounded-full transition-transform duration-200"
                                        :class="darkMode ? 'translate-x-4.5' : 'translate-x-0'"
                                    ></div>
                                </button>
                            </div>
                        </form>

                        {{-- TAB 2: API KEY & INTEGRATION --}}
                        <div x-show="activeSettingsTab === 'api'" class="space-y-4" x-transition style="display: none;">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">AI API Provider</label>
                                <select 
                                    x-model="apiProvider" 
                                    class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs focus:outline-none focus:border-primary transition-all cursor-pointer"
                                >
                                    <option value="buatjalan">BuatJalan AI (Default - Recommended)</option>
                                    <option value="gemini">Custom Gemini API Key</option>
                                    <option value="openai">Custom OpenAI API Key</option>
                                    <option value="9router">Custom 9router API Key</option>
                                </select>
                                <p class="text-[10px] text-zinc-400 leading-relaxed mt-1">
                                    Pilih sumber infrastruktur AI yang ingin digunakan untuk memproses pembuatan dan analisis proyek. <span class="text-emerald-400 font-semibold">(Catatan: Jika Anda tidak menggunakan BuatJalan AI, akun Anda tidak akan dikenakan biaya/charge Token workspace).</span>
                                </p>
                            </div>

                            {{-- Option 1: BuatJalan AI (Default) --}}
                            <div x-show="apiProvider === 'buatjalan'" class="p-4 bg-zinc-950/40 border border-white/5 rounded-xl space-y-3 animate-in fade-in slide-in-from-top-1 duration-200" style="display: none;">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-xs font-semibold text-white">BuatJalan AI Engine Aktif</span>
                                </div>
                                <p class="text-[11px] text-zinc-400 leading-relaxed">
                                    Menggunakan model LLM teroptimasi bawaan platform BuatJalan. Bebas biaya langganan tambahan dan langsung memotong saldo Token dari workspace aktif Anda saat ini.
                                </p>
                                <div class="pt-1 flex items-center gap-3">
                                    <button 
                                        @click="testApi()"
                                        :disabled="testingApi"
                                        class="bg-zinc-850 hover:bg-zinc-800 disabled:opacity-50 text-white font-semibold py-1.5 px-3 rounded-lg text-[10px] transition-colors cursor-pointer border border-white/5 select-none flex items-center gap-1.5"
                                    >
                                        <svg x-show="testingApi" class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span x-text="testingApi ? 'Menguji...' : 'Uji Koneksi API'"></span>
                                    </button>
                                    <span x-show="apiTestResult === 'success'" class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 animate-in fade-in zoom-in duration-200" style="display: none;">
                                        ✅ Koneksi Berhasil & Responsif!
                                    </span>
                                </div>
                            </div>

                            {{-- Option 2: Gemini API Key (Custom) --}}
                            <div x-show="apiProvider === 'gemini'" class="space-y-3 animate-in fade-in slide-in-from-top-1 duration-200" style="display: none;">
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Custom Gemini API Key</label>
                                    <div class="relative">
                                        <input 
                                            type="password" 
                                            placeholder="Masukkan Gemini API Key Anda..."
                                            value="gemini-api-key-dummy-val-12345"
                                            class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs focus:outline-none focus:border-primary transition-all"
                                        />
                                        <span class="absolute right-3 top-3 text-[10px] text-primary bg-primary/10 border border-primary/20 px-1.5 py-0.5 rounded">Custom Connected</span>
                                    </div>
                                </div>
                                <p class="text-[11px] text-zinc-400 leading-relaxed">
                                    Gunakan kuota API Key Google Gemini Anda sendiri. Seluruh request AI pembuatan modul proyek akan dialihkan ke API Key Anda secara gratis tanpa memotong Token workspace.
                                </p>
                                <div class="pt-1 flex items-center gap-3">
                                    <button 
                                        @click="testApi()"
                                        :disabled="testingApi"
                                        class="bg-zinc-850 hover:bg-zinc-800 disabled:opacity-50 text-white font-semibold py-1.5 px-3 rounded-lg text-[10px] transition-colors cursor-pointer border border-white/5 select-none flex items-center gap-1.5"
                                    >
                                        <svg x-show="testingApi" class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span x-text="testingApi ? 'Menguji...' : 'Uji Koneksi API'"></span>
                                    </button>
                                    <span x-show="apiTestResult === 'success'" class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 animate-in fade-in zoom-in duration-200" style="display: none;">
                                        ✅ Koneksi API Key Gemini Aktif!
                                    </span>
                                </div>
                            </div>

                            {{-- Option 3: OpenAI API Key (Custom) --}}
                            <div x-show="apiProvider === 'openai'" class="space-y-3 animate-in fade-in slide-in-from-top-1 duration-200" style="display: none;">
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Custom OpenAI API Key</label>
                                    <input 
                                        type="password" 
                                        placeholder="Masukkan OpenAI API Key (sk-...)..."
                                        class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs focus:outline-none focus:border-primary transition-all"
                                    />
                                </div>
                                <p class="text-[11px] text-zinc-400 leading-relaxed">
                                    Hubungkan API Key OpenAI kustom Anda untuk memproses kebutuhan PRD menggunakan model GPT-4o / GPT-3.5 secara langsung.
                                </p>
                                <div class="pt-1 flex items-center gap-3">
                                    <button 
                                        @click="testApi()"
                                        :disabled="testingApi"
                                        class="bg-zinc-850 hover:bg-zinc-800 disabled:opacity-50 text-white font-semibold py-1.5 px-3 rounded-lg text-[10px] transition-colors cursor-pointer border border-white/5 select-none flex items-center gap-1.5"
                                    >
                                        <svg x-show="testingApi" class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span x-text="testingApi ? 'Menguji...' : 'Uji Koneksi API'"></span>
                                    </button>
                                    <span x-show="apiTestResult === 'success'" class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 animate-in fade-in zoom-in duration-200" style="display: none;">
                                        ✅ Koneksi API Key OpenAI Sukses!
                                    </span>
                                </div>
                            </div>

                            {{-- Option 4: 9router API Key (Custom) --}}
                            <div x-show="apiProvider === '9router'" class="space-y-3 animate-in fade-in slide-in-from-top-1 duration-200" style="display: none;">
                                <div class="space-y-3">
                                    <div class="space-y-1.5">
                                        <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Custom 9router API Key</label>
                                        <input 
                                            type="password" 
                                            placeholder="Masukkan 9router API Key..."
                                            class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs focus:outline-none focus:border-primary transition-all"
                                        />
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">9router Base URL (Optional)</label>
                                        <input 
                                            type="text" 
                                            placeholder="https://api.9router.com/v1"
                                            class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs focus:outline-none focus:border-primary transition-all"
                                        />
                                    </div>
                                </div>
                                <p class="text-[11px] text-zinc-400 leading-relaxed">
                                    Gunakan kuota routing LLM 9router kustom Anda. Request AI pembuatan modul proyek akan dikirimkan langsung ke 9router tanpa memotong Token workspace.
                                </p>
                                <div class="pt-1 flex items-center gap-3">
                                    <button 
                                        @click="testApi()"
                                        :disabled="testingApi"
                                        class="bg-zinc-850 hover:bg-zinc-800 disabled:opacity-50 text-white font-semibold py-1.5 px-3 rounded-lg text-[10px] transition-colors cursor-pointer border border-white/5 select-none flex items-center gap-1.5"
                                    >
                                        <svg x-show="testingApi" class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span x-text="testingApi ? 'Menguji...' : 'Uji Koneksi API'"></span>
                                    </button>
                                    <span x-show="apiTestResult === 'success'" class="text-[10px] text-emerald-400 font-semibold flex items-center gap-1 animate-in fade-in zoom-in duration-200" style="display: none;">
                                        ✅ Koneksi 9router Berhasil Terhubung!
                                    </span>
                                </div>
                            </div>

                            {{-- Google Integration status --}}
                            <div class="flex items-center justify-between p-3 bg-zinc-950/40 border border-white/5 rounded-xl mt-2">
                                <div class="flex items-center gap-2.5">
                                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                    </svg>
                                    <div>
                                        <div class="text-xs font-semibold text-white">Google Integration</div>
                                        @if(auth()->user()->google_id)
                                            <div class="text-[10px] text-zinc-500 mt-0.5">Telah terhubung ke akun Google Anda.</div>
                                        @else
                                            <div class="text-[10px] text-zinc-500 mt-0.5">Belum terhubung.</div>
                                        @endif
                                    </div>
                                </div>
                                @if(auth()->user()->google_id)
                                    <span class="text-[10px] text-emerald-400 font-semibold bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full select-none">Aktif</span>
                                @else
                                    <a href="/auth/google/redirect" class="text-[10px] text-primary hover:text-primary/80 font-bold bg-primary/10 border border-primary/20 hover:bg-primary/20 px-3 py-1 rounded-lg transition-colors cursor-pointer select-none">Hubungkan</a>
                                @endif
                            </div>

                            {{-- GitHub Integration status --}}
                            <div class="flex items-center justify-between p-3 bg-zinc-950/40 border border-white/5 rounded-xl mt-2">
                                <div class="flex items-center gap-2.5">
                                    <svg class="w-5 h-5 text-white shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.167 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.464-1.11-1.464-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.137 20.162 22 16.418 22 12c0-5.523-4.477-10-10-10z"></path>
                                    </svg>
                                    <div>
                                        <div class="text-xs font-semibold text-white">GitHub Integration</div>
                                        @if(auth()->user()->github_id)
                                            <div class="text-[10px] text-zinc-500 mt-0.5">Telah terhubung ke akun GitHub Anda.</div>
                                        @else
                                            <div class="text-[10px] text-zinc-500 mt-0.5">Belum terhubung.</div>
                                        @endif
                                    </div>
                                </div>
                                @if(auth()->user()->github_id)
                                    <span class="text-[10px] text-emerald-400 font-semibold bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full select-none">Aktif</span>
                                @else
                                    <a href="/auth/github/redirect" class="text-[10px] text-primary hover:text-primary/80 font-bold bg-primary/10 border border-primary/20 hover:bg-primary/20 px-3 py-1 rounded-lg transition-colors cursor-pointer select-none">Hubungkan</a>
                                @endif
                            </div>
                        </div>



                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end p-4 border-t border-white/5 bg-zinc-900/50 gap-2">
                    <button 
                        type="button"
                        @click="settingsModalOpen = false"
                        class="bg-zinc-800 hover:bg-zinc-700 text-white border border-white/5 px-4 py-2 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        form="profile-update-form"
                        class="bg-primary text-primary-foreground hover:bg-primary/90 px-5 py-2 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                    >
                        Simpan
                    </button>
                </div>

            </div>
        </div>

        {{-- GLOBAL CREATE WORKSPACE MODAL DIALOG --}}
        <div 
            x-show="createWorkspaceModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            x-transition
            style="display: none;"
        >
            {{-- Click away --}}
            <div class="absolute inset-0" @click="createWorkspaceModalOpen = false"></div>
            
            {{-- Modal Card --}}
            <div class="relative bg-zinc-900 border border-white/10 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl z-10 animate-in fade-in zoom-in-95 duration-150 flex flex-col">
                <form action="{{ route('workspaces.create') }}" method="POST" class="m-0">
                    @csrf
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between p-6 border-b border-white/5 bg-zinc-900/50">
                        <div>
                            <h2 class="text-lg font-bold text-white">Buat Workspace Baru</h2>
                            <p class="text-xs text-zinc-400 mt-1">Buat workspace baru untuk memisahkan proyek dan kolaborasi Anda.</p>
                        </div>
                        <button type="button" @click="createWorkspaceModalOpen = false" class="p-1.5 rounded-md text-zinc-500 hover:bg-white/5 hover:text-white transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Nama Workspace</label>
                            <input 
                                type="text" 
                                name="name"
                                required
                                placeholder="Nama workspace baru..."
                                class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs focus:outline-none focus:border-primary transition-all"
                            />
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex justify-end p-4 border-t border-white/5 bg-zinc-900/50 gap-2">
                        <button 
                            type="button"
                            @click="createWorkspaceModalOpen = false"
                            class="bg-zinc-800 hover:bg-zinc-700 text-white px-4 py-2 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="bg-primary text-primary-foreground hover:bg-primary/90 px-5 py-2 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                        >
                            Buat Workspace
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @php
            $currentWorkspace = auth()->user()->currentWorkspace;
            $members = $currentWorkspace ? $currentWorkspace->members : collect();
            $isOwner = $currentWorkspace && $currentWorkspace->owner_id === auth()->id();
            $pendingInvites = $currentWorkspace 
                ? \App\Models\Inbox::where('workspace_id', $currentWorkspace->id)
                    ->where('type', 'invitation')
                    ->where('invitation_status', 'pending')
                    ->with('user')
                    ->get()
                : collect();
        @endphp

        {{-- GLOBAL MANAGE WORKSPACE MODAL DIALOG --}}
        <div 
            x-show="manageWorkspaceModalOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            x-transition
            style="display: none;"
        >
            {{-- Click away --}}
            <div class="absolute inset-0" @click="manageWorkspaceModalOpen = false"></div>
            
            {{-- Modal Card --}}
            <div class="relative bg-zinc-900 border border-white/10 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl z-10 animate-in fade-in zoom-in-95 duration-150 flex flex-col h-[520px]">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-6 border-b border-white/5 bg-zinc-900/50">
                    <div>
                        <h2 class="text-lg font-bold text-white">Kelola Workspace: {{ $currentWorkspace->name ?? '' }}</h2>
                        <p class="text-xs text-zinc-400 mt-1">Kelola kolaborasi anggota, informasi token saldo, dan pengaturan administrasi.</p>
                    </div>
                    <button @click="manageWorkspaceModalOpen = false" class="p-1.5 rounded-md text-zinc-500 hover:bg-white/5 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>

                {{-- Modal Body: Split View --}}
                <div class="flex flex-col md:flex-row flex-1 overflow-hidden">
                    
                    {{-- Left Tab Panel --}}
                    <div class="w-full md:w-48 border-b md:border-b-0 md:border-r border-white/5 bg-zinc-950/40 p-3 md:p-4 flex md:flex-col gap-2 md:gap-0 md:space-y-1 overflow-x-auto md:overflow-x-visible whitespace-nowrap md:whitespace-normal scrollbar-none shrink-0">
                        <button 
                            @click="activeManageTab = 'tokens'"
                            class="text-left px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer flex items-center gap-1.5 shrink-0 md:w-full"
                            :class="activeManageTab === 'tokens' ? 'bg-primary/10 text-primary' : 'text-zinc-400 hover:bg-white/5 hover:text-white'"
                        >
                            <img src="{{ asset('assets/icon-images/emerald-icon.png') }}" class="w-3.5 h-3.5 object-contain shrink-0" alt="Emerald">
                            <span>Saldo Token</span>
                        </button>
                        <button 
                            @click="activeManageTab = 'members'"
                            class="text-left px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer shrink-0 md:w-full"
                            :class="activeManageTab === 'members' ? 'bg-primary/10 text-primary' : 'text-zinc-400 hover:bg-white/5 hover:text-white'"
                        >
                            👥 Anggota ({{ count($members) }})
                        </button>
                        @if($isOwner)
                            <button 
                                @click="activeManageTab = 'invite'"
                                class="text-left px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer shrink-0 md:w-full"
                                :class="activeManageTab === 'invite' ? 'bg-primary/10 text-primary' : 'text-zinc-400 hover:bg-white/5 hover:text-white'"
                            >
                                📩 Undang Kolaborator
                            </button>
                            <button 
                                @click="activeManageTab = 'rename'"
                                class="text-left px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer shrink-0 md:w-full"
                                :class="activeManageTab === 'rename' ? 'bg-primary/10 text-primary' : 'text-zinc-400 hover:bg-white/5 hover:text-white'"
                            >
                                📝 Ubah Nama
                            </button>
                            <button 
                                @click="activeManageTab = 'danger'"
                                class="text-left px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer shrink-0 md:w-full"
                                :class="activeManageTab === 'danger' ? 'bg-red-500/10 text-red-400' : 'text-zinc-400 hover:bg-white/5 hover:text-white'"
                            >
                                ⚠️ Hapus Workspace
                            </button>
                        @else
                            <button 
                                @click="activeManageTab = 'leave'"
                                class="text-left px-3 py-2 rounded-lg text-xs font-semibold transition-all cursor-pointer shrink-0 md:w-full"
                                :class="activeManageTab === 'leave' ? 'bg-red-500/10 text-red-400' : 'text-zinc-400 hover:bg-white/5 hover:text-white'"
                            >
                                🚪 Keluar Workspace
                            </button>
                        @endif
                    </div>

                    {{-- Right Content Panel --}}
                    <div class="flex-1 p-6 overflow-y-auto custom-scrollbar space-y-6">
                        
                        {{-- TAB 1: TOKENS --}}
                        <div x-show="activeManageTab === 'tokens'" class="space-y-4" x-transition>
                            <div class="p-4 bg-zinc-950/40 border border-white/5 rounded-xl flex items-center justify-between">
                                <div>
                                    <div class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Saldo Token Saat Ini</div>
                                    <div class="text-2xl font-black text-white mt-1">{{ $currentWorkspace->tokens_balance ?? 0 }} Emerald</div>
                                    <div class="text-[10px] text-zinc-400 mt-1">Digunakan bersama oleh seluruh anggota untuk merancang modul proyek.</div>
                                </div>
                                <img src="{{ asset('assets/icon-images/emerald-icon.png') }}" class="w-8 h-8 object-contain shrink-0" alt="Emerald">
                            </div>
                            
                            <div class="space-y-2">
                                <h4 class="text-xs font-bold text-white uppercase tracking-wider">💡 Ketentuan Biaya Koin:</h4>
                                <div class="grid grid-cols-2 gap-3 text-xs">
                                    <div class="p-3 bg-zinc-950/20 border border-white/5 rounded-xl">
                                        <div class="font-bold text-white">Generate Baru</div>
                                        <div class="text-[10px] text-zinc-500 mt-0.5">10 Token per proyek</div>
                                    </div>
                                    <div class="p-3 bg-zinc-950/20 border border-white/5 rounded-xl">
                                        <div class="font-bold text-white">Edit Proyek</div>
                                        <div class="text-[10px] text-zinc-500 mt-0.5">10 Token per request</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: MEMBERS --}}
                        <div x-show="activeManageTab === 'members'" class="space-y-4" x-transition style="display: none;">
                            <div class="space-y-2">
                                <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Daftar Anggota Aktif</label>
                                <div class="border border-white/5 bg-zinc-950/20 rounded-xl overflow-hidden divide-y divide-white/5">
                                    @forelse($members as $member)
                                        <div class="flex items-center justify-between p-3">
                                            <div class="flex items-center gap-3">
                                                @if($member->avatar)
                                                    <img src="{{ $member->avatar }}" class="w-7 h-7 rounded-full object-cover border border-white/10" alt="{{ $member->name }}">
                                                @else
                                                    <div class="w-7 h-7 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-[10px] font-bold text-white">
                                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-semibold text-white leading-none mb-0.5">{{ $member->name }}</span>
                                                    <span class="text-[10px] text-zinc-500 leading-none">{{ $member->email }}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full border 
                                                    {{ $member->pivot->role === 'owner' ? 'bg-primary/10 text-primary border-primary/20' : 'bg-zinc-800 text-zinc-400 border-white/5' }}">
                                                    {{ $member->pivot->role }}
                                                </span>
                                                @if($isOwner && $member->id !== auth()->id())
                                                    <form action="{{ route('workspaces.kick', $member->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin mengeluarkan {{ $member->name }} dari workspace ini?');">
                                                        @csrf
                                                        <button type="submit" class="px-2 py-1 rounded bg-red-500/10 hover:bg-red-500/20 text-red-400 text-[9px] font-bold border border-red-500/20 hover:border-red-500/30 transition-colors cursor-pointer select-none">
                                                            Keluarkan
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 text-center text-zinc-500 text-xs">Belum ada anggota terdaftar.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        @if($isOwner)
                            {{-- TAB 3: INVITE --}}
                            <div x-show="activeManageTab === 'invite'" class="space-y-4" x-transition style="display: none;">
                                <form action="{{ route('workspaces.invite') }}" method="POST" class="m-0 space-y-4">
                                    @csrf
                                    <div class="space-y-1.5">
                                        <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Email Pengguna Tujuan</label>
                                        <input 
                                            type="email" 
                                            name="email"
                                            required
                                            placeholder="Ketik email kolaborator yang terdaftar..."
                                            class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs focus:outline-none focus:border-primary transition-all"
                                        />
                                    </div>
                                    <button type="submit" class="bg-primary text-primary-foreground hover:bg-primary/90 px-4 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors w-full">
                                        Kirim Undangan Kolaborasi
                                    </button>
                                </form>

                                <div class="space-y-2 pt-4 border-t border-white/5">
                                    <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Undangan Tertunda (Pending)</label>
                                    <div class="border border-white/5 bg-zinc-950/20 rounded-xl overflow-hidden divide-y divide-white/5">
                                        @forelse($pendingInvites as $invite)
                                            @php
                                                $inviteUser = $invite->user;
                                                $inviteName = $inviteUser?->name ?? 'Pengguna';
                                                $inviteEmail = $inviteUser?->email ?? '';
                                                $inviteAvatar = $inviteUser?->avatar ?? null;
                                            @endphp
                                            <div class="flex items-center justify-between p-3">
                                                <div class="flex items-center gap-3">
                                                    @if($inviteAvatar)
                                                        <img src="{{ $inviteAvatar }}" class="w-7 h-7 rounded-full object-cover border border-white/10" alt="{{ $inviteName }}">
                                                    @else
                                                        <div class="w-7 h-7 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-[10px] font-bold text-white">
                                                            {{ strtoupper(substr($inviteName, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-semibold text-white leading-none mb-0.5">{{ $inviteName }}</span>
                                                        <span class="text-[10px] text-zinc-500 leading-none">{{ $inviteEmail }}</span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full border border-amber-500/20 bg-amber-500/10 text-amber-400">
                                                        Pending
                                                    </span>
                                                    <button 
                                                        type="button" 
                                                        @click="cancelInviteModalOpen = true; cancelInviteId = {{ $invite->id }}; cancelInviteName = '{{ $inviteName }}'"
                                                        class="px-2 py-1 rounded bg-red-500/10 hover:bg-red-500/20 text-red-400 text-[9px] font-bold border border-red-500/20 hover:border-red-500/30 transition-colors cursor-pointer select-none"
                                                    >
                                                        Batalkan
                                                    </button>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="p-4 text-center text-zinc-500 text-xs">Tidak ada undangan pending.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            {{-- TAB 4: RENAME --}}
                            <div x-show="activeManageTab === 'rename'" class="space-y-4" x-transition style="display: none;">
                                <form action="{{ route('workspaces.rename') }}" method="POST" class="m-0 space-y-4">
                                    @csrf
                                    <div class="space-y-1.5">
                                        <label class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Nama Workspace Baru</label>
                                        <input 
                                            type="text" 
                                            name="name"
                                            required
                                            value="{{ $currentWorkspace->name ?? '' }}"
                                            class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs focus:outline-none focus:border-primary transition-all"
                                        />
                                    </div>
                                    <button type="submit" class="bg-primary text-primary-foreground hover:bg-primary/90 px-4 py-2 rounded-xl text-xs font-semibold cursor-pointer transition-colors w-full">
                                        Simpan Perubahan Nama
                                    </button>
                                </form>
                            </div>

                            {{-- TAB 5: DANGER --}}
                            <div x-show="activeManageTab === 'danger'" class="space-y-4" x-transition style="display: none;">
                                <div class="bg-red-500/10 border border-red-500/20 p-4 rounded-xl space-y-3">
                                    <h4 class="text-xs font-bold text-red-400">Peringatan Penghapusan Permanen</h4>
                                    <p class="text-[10px] text-zinc-400 leading-relaxed">Menghapus workspace akan menghapus seluruh data proyek, database schema, tech stack, dan riwayat transaksi di dalamnya secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                                    
                                    <div class="pt-2">
                                        <button 
                                            @click="deleteWorkspaceModalOpen = true; deleteConfirmText = '';"
                                            class="bg-red-600 hover:bg-red-500 text-white px-4 py-2.5 rounded-xl text-xs font-bold w-full transition-colors cursor-pointer text-center select-none"
                                        >
                                            Ya, Hapus Workspace Ini
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- TAB 6: LEAVE WORKSPACE (For members) --}}
                            <div x-show="activeManageTab === 'leave'" class="space-y-4" x-transition style="display: none;">
                                <div class="bg-red-500/10 border border-red-500/20 p-4 rounded-xl space-y-3">
                                    <h4 class="text-xs font-bold text-red-400">Peringatan Keluar dari Workspace</h4>
                                    <p class="text-[10px] text-zinc-400 leading-relaxed">Anda akan kehilangan akses ke seluruh proyek dan kolaborasi yang terdaftar di bawah workspace '{{ $currentWorkspace->name ?? '' }}'. Anda perlu diundang kembali oleh pemilik jika ingin bergabung lagi di masa mendatang.</p>
                                    
                                    <form action="{{ route('workspaces.leave') }}" method="POST" class="m-0 pt-2" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari workspace ini?');">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-500 text-white px-4 py-2.5 rounded-xl text-xs font-bold w-full transition-colors cursor-pointer text-center">
                                            Ya, Keluar dari Workspace
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex justify-end p-4 border-t border-white/5 bg-zinc-900/50">
                    <button 
                        @click="manageWorkspaceModalOpen = false"
                        class="bg-primary text-primary-foreground hover:bg-primary/90 px-5 py-2 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                    >
                        Tutup
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
                    
                    {{-- Section 1: Cara Kerja Token --}}
                    <div class="space-y-2">
                        <h3 class="font-bold text-white text-sm flex items-center gap-1.5">
                            ⚡ Sistem Token BuatJalan
                        </h3>
                        <p>Setiap tindakan di platform ini dihitung berdasarkan koin token yang ada pada workspace Anda:</p>
                        <ul class="list-disc pl-5 space-y-1 mt-1 text-zinc-400">
                            <li><strong>Membuat Proyek Baru</strong>: Mengonsumsi <strong>10 Token</strong>.</li>
                            <li><strong>Mengubah / Edit Proyek</strong>: Mengonsumsi <strong>10 Token</strong>.</li>
                            <li><strong>Unduh & Salin Context</strong>: Gratis <strong>(0 Token)</strong>.</li>
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
                            <h4 class="font-semibold text-white">Apakah saldo token yang dibeli memiliki masa kedaluwarsa?</h4>
                            <p class="text-zinc-400">Tidak. Saldo token yang Anda beli aktif selamanya selama akun Anda terdaftar di platform kami.</p>
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

        {{-- DELETE WORKSPACE CONFIRMATION SUB-MODAL --}}
        <div 
            x-show="deleteWorkspaceModalOpen" 
            class="fixed inset-0 z-100 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            x-transition
            style="display: none;"
        >
            <div class="absolute inset-0" @click="deleteWorkspaceModalOpen = false"></div>
            <div class="relative bg-zinc-900 border border-red-500/20 rounded-2xl w-full max-w-sm p-6 shadow-2xl z-10 animate-in fade-in scale-in duration-200">
                <div class="flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full bg-red-500/10 border border-red-500/20 flex items-center justify-center text-xl text-red-400 mb-4 animate-bounce">
                        ⚠️
                    </div>
                    
                    <h3 class="text-base font-bold text-white mb-2">Konfirmasi Hapus Workspace</h3>
                    <p class="text-xs text-zinc-400 mb-4 leading-relaxed">
                        Tindakan ini tidak dapat dibatalkan. Seluruh data proyek, database schema, dan token di workspace ini akan hilang selamanya.
                    </p>
                    <p class="text-xs text-red-400 font-bold mb-4">
                        Ketik <span class="bg-red-500/10 border border-red-500/20 px-1.5 py-0.5 rounded text-[11px] font-mono select-none">CONFIRM</span> di bawah untuk mengonfirmasi:
                    </p>
                    
                    <input 
                        type="text" 
                        x-model="deleteConfirmText"
                        placeholder="Ketik CONFIRM..." 
                        class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-center text-white text-xs font-mono placeholder:text-zinc-700 focus:outline-none focus:border-red-500 transition-all mb-6"
                    />
                    
                    <div class="flex gap-3 w-full">
                        <button 
                            @click="deleteWorkspaceModalOpen = false; deleteConfirmText = ''"
                            class="flex-1 border border-white/10 hover:border-white/20 text-zinc-300 hover:text-white px-4 py-2.5 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                        >
                            Batal
                        </button>
                        <form action="{{ route('workspaces.delete') }}" method="POST" class="flex-1 m-0">
                            @csrf
                            <button 
                                type="submit" 
                                :disabled="deleteConfirmText !== 'CONFIRM'"
                                class="w-full bg-red-600 hover:bg-red-500 disabled:bg-red-950/40 disabled:text-zinc-600 disabled:border-red-950/20 text-white border border-red-500/20 px-4 py-2.5 rounded-lg text-xs font-bold cursor-pointer transition-colors disabled:cursor-not-allowed"
                            >
                                Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- CANCEL INVITATION CONFIRMATION SUB-MODAL --}}
        <div 
            x-show="cancelInviteModalOpen" 
            class="fixed inset-0 z-100 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            x-transition
            style="display: none;"
        >
            <div class="absolute inset-0" @click="cancelInviteModalOpen = false; cancelInviteId = null; cancelInviteName = ''"></div>
            <div class="relative bg-zinc-900 border border-red-500/20 rounded-2xl w-full max-w-sm p-6 shadow-2xl z-10 animate-in fade-in scale-in duration-200">
                <div class="flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full bg-red-500/10 border border-red-500/20 flex items-center justify-center text-xl text-red-400 mb-4">
                        ⚠️
                    </div>
                    
                    <h3 class="text-base font-bold text-white mb-2">Batalkan Undangan</h3>
                    <p class="text-xs text-zinc-400 mb-6 leading-relaxed">
                        Apakah Anda yakin ingin membatalkan undangan kolaborasi untuk <strong class="text-white" x-text="cancelInviteName"></strong>? Penerima tidak akan dapat bergabung ke workspace ini menggunakan undangan tersebut.
                    </p>
                    
                    <div class="flex gap-3 w-full">
                        <button 
                            @click="cancelInviteModalOpen = false; cancelInviteId = null; cancelInviteName = ''"
                            class="flex-1 border border-white/10 hover:border-white/20 text-zinc-300 hover:text-white px-4 py-2.5 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                        >
                            Kembali
                        </button>
                        <form :action="'/dashboard/workspaces/invite/' + cancelInviteId + '/cancel'" method="POST" class="flex-1 m-0">
                            @csrf
                            <button 
                                type="submit" 
                                class="w-full bg-red-600 hover:bg-red-500 text-white border border-red-500/20 px-4 py-2.5 rounded-lg text-xs font-bold cursor-pointer transition-colors"
                            >
                                Ya, Batalkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Session Flash Toast Alerts (Responsive & Premium) --}}
        @if(session('success') || session('error'))
            <div 
                x-data="{ show: true }" 
                x-show="show" 
                x-init="setTimeout(() => show = false, {{ session('success') ? 4000 : 5000 }})"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-2"
                x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed bottom-4 left-4 right-4 sm:left-auto sm:right-5 sm:bottom-5 sm:max-w-sm z-110 bg-zinc-950/95 backdrop-blur-md border rounded-xl shadow-2xl p-3 flex items-center justify-between gap-3 animate-in fade-in slide-in-from-bottom-4 duration-300 {{ session('success') ? 'border-emerald-500/30' : 'border-rose-500/30' }}"
            >
                <div class="flex items-center gap-2.5 min-w-0">
                    @if(session('success'))
                        <div class="w-5 h-5 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs font-bold shrink-0">
                            ✓
                        </div>
                        <div class="text-[11px] font-semibold text-white truncate">
                            {{ session('success') }}
                        </div>
                    @else
                        <div class="w-5 h-5 rounded-full bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 text-xs font-bold shrink-0">
                            ✕
                        </div>
                        <div class="text-[11px] font-semibold text-white truncate">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
                <button @click="show = false" class="text-zinc-500 hover:text-white transition-colors p-1 cursor-pointer shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        @endif

        @livewireScripts
    </body>
</html>
