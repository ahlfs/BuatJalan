@extends('layouts.dashboard', ['activeId' => 'home'])

@section('title', 'Beranda')
@section('page_title', 'Beranda')

@section('content')
    <div class="flex flex-col gap-6 font-sans">
        {{-- Top Welcome Bar --}}
        <div class="mb-2">
            <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">Selamat Datang di BuatJalan 👋</h1>
            <p class="text-sm text-zinc-400 mt-1">Kelola seluruh workspace, proyek aplikasi, dan token AI Anda secara global dalam satu dashboard terpadu.</p>
        </div>
        
        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Card 1 --}}
            <div class="relative overflow-hidden rounded-xl border border-white/10 bg-zinc-900 p-6 flex flex-col justify-between min-h-[144px]">
                <div class="flex items-center justify-between text-zinc-400">
                    <span class="text-sm font-medium">Workspace Terdaftar</span>
                    <img src="{{ asset('assets/icon-images/cube-icon.png') }}" class="w-8 h-8 object-contain shrink-0" alt="Workspace">
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-bold text-white">{{ count($workspaces) }} Workspace</div>
                    <p class="text-xs text-zinc-500 mt-1">Milik Anda & kolaborasi tim</p>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="relative overflow-hidden rounded-xl border border-white/10 bg-zinc-900 p-6 flex flex-col justify-between min-h-[144px]">
                <div class="flex items-center justify-between text-zinc-400">
                    <span class="text-sm font-medium">Total Proyek</span>
                    <img src="{{ asset('assets/svg/folder-icon.svg') }}" class="w-8 h-8 object-contain shrink-0" alt="Projects">
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-bold text-white">{{ count($projects) }} Project</div>
                    <p class="text-xs text-zinc-500 mt-1">Di seluruh workspace Anda</p>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="relative overflow-hidden rounded-xl border border-white/10 bg-zinc-900 p-6 flex flex-col justify-between min-h-[144px]">
                <div class="flex items-center justify-between text-zinc-400">
                    <span class="text-sm font-medium">PRD Ter-generate</span>
                    <img src="{{ asset('assets/svg/compass-icon.svg') }}" class="w-8 h-8 object-contain shrink-0" alt="PRD">
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-bold text-white">{{ $projects->filter(fn($p) => !empty($p->prd_markdown))->count() }} Dokumen</div>
                    <p class="text-xs text-zinc-500 mt-1">Semua dokumen PRD berbasis AI</p>
                </div>
            </div>
        </div>

        {{-- Main Projects Table --}}
        <div class="w-full bg-zinc-900 rounded-xl border border-white/10 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white">Seluruh Project Aktif Anda</h2>
                    <p class="text-xs text-zinc-400 mt-1">Daftar proyek dari seluruh workspace yang Anda ikuti.</p>
                </div>
                <span class="text-xs text-zinc-500">Terakhir diperbarui hari ini</span>
            </div>
            
            <div class="h-px bg-white/10 mb-6"></div>
            
            <div class="flex flex-col gap-4">
                @forelse($projects as $project)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-zinc-950 rounded-lg border border-white/5 hover:border-white/10 transition-colors gap-4">
                        <div class="flex items-center gap-4 min-w-0 w-full sm:w-auto">
                            <div class="w-10 h-10 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center font-bold text-primary shrink-0 overflow-hidden">
                                @if($project->logo_url)
                                    <img src="{{ $project->logo_url }}" class="w-6 h-6 object-contain" alt="{{ $project->title }}">
                                @else
                                    <span class="text-sm font-extrabold text-primary">{{ strtoupper(substr($project->title, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-semibold text-white truncate max-w-[140px] sm:max-w-none">{{ $project->title }}</h3>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-primary bg-primary/10 border border-primary/20 px-1.5 py-0.5 rounded truncate max-w-[120px] sm:max-w-none">
                                        {{ $project->workspace->name ?? 'Personal' }}
                                    </span>
                                </div>
                                <p class="text-xs text-zinc-500 mt-1 truncate">
                                    Tech Stack: 
                                    @if($project->techStacks->count() > 0)
                                        {{ $project->techStacks->pluck('name')->join(' + ') }}
                                    @else
                                        Belum ditentukan
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto border-t border-white/5 pt-3 sm:border-0 sm:pt-0">
                            <div class="flex flex-col items-start sm:items-end">
                                <span class="text-xs font-semibold text-green-500">PRD Ready</span>
                                <span class="text-[10px] text-zinc-500 mt-0.5">Roadmap {{ $project->roadmaps->count() }} Langkah</span>
                            </div>
                            <a href="/dashboard/projects/{{ $project->slug }}" class="px-3 py-1.5 rounded bg-white/5 hover:bg-white/10 text-xs text-white border border-white/10 cursor-pointer select-none">Buka</a>
                        </div>
                    </div>
                @empty
                    <div class="border border-dashed border-white/10 rounded-xl p-8 text-center text-zinc-500 text-xs py-12 flex flex-col items-center gap-3">
                        <span class="text-2xl">📁</span>
                        <span>Belum ada project aktif di workspace Anda.</span>
                        <a href="/dashboard/new" class="text-xs font-semibold text-primary hover:underline">+ Buat Proyek Pertama Anda</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Workspace List Widget --}}
        <div class="w-full bg-zinc-900 rounded-xl border border-white/10 shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-white">Daftar Workspace Anda</h2>
                    <p class="text-xs text-zinc-400 mt-1">Beralih antar workspace secara instan untuk mengelola proyek dan token kolaborasi terkait.</p>
                </div>
            </div>
            
            <div class="h-px bg-white/10 mb-6"></div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($workspaces as $ws)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-zinc-950 rounded-lg border {{ auth()->user()->current_workspace_id === $ws->id ? 'border-primary/30 bg-primary/5' : 'border-white/5 hover:border-white/10' }} transition-colors gap-4">
                        <div class="flex items-center gap-3 min-w-0 w-full sm:w-auto">
                            <div class="w-8 h-8 rounded bg-white/5 border border-white/10 flex items-center justify-center text-sm font-bold text-white shrink-0">
                                {{ strtoupper(substr($ws->name, 0, 1)) }}
                            </div>
                            <div class="flex flex-col min-w-0 flex-1">
                                <span class="text-xs font-semibold text-white leading-tight flex items-center gap-1.5 truncate">
                                    {{ $ws->name }}
                                    @if(auth()->user()->current_workspace_id === $ws->id)
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-primary animate-pulse shrink-0" title="Workspace Aktif"></span>
                                    @endif
                                </span>
                                <span class="text-[10px] text-zinc-500 flex items-center gap-1 mt-1 truncate">
                                    <img src="{{ asset('assets/icon-images/emerald-icon.png') }}" class="w-3.5 h-3.5 object-contain shrink-0" alt="Emerald">
                                    <span>{{ $ws->tokens_balance }} Token • 📁 {{ $ws->projects()->count() }} Proyek</span>
                                </span>
                            </div>
                        </div>
                        
                        <form action="{{ route('workspaces.switch', $ws->id) }}" method="POST" class="m-0 w-full sm:w-auto flex justify-end border-t border-white/5 pt-3 sm:border-0 sm:pt-0">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded text-xs transition-colors cursor-pointer select-none w-full sm:w-auto text-center
                                {{ auth()->user()->current_workspace_id === $ws->id ? 'bg-primary/20 text-primary border border-primary/20 font-semibold' : 'bg-white/5 hover:bg-white/10 text-white border border-white/10' }}">
                                {{ auth()->user()->current_workspace_id === $ws->id ? 'Aktif' : 'Beralih' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
