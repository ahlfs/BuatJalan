@extends('layouts.dashboard', ['activeId' => 'home'])

@section('title', 'Beranda')
@section('page_title', 'Beranda')

@section('content')
    <div class="flex flex-col gap-6">
        {{-- Top Welcome Bar --}}
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">Selamat Datang di BuatJalan 👋</h1>
                <p class="text-sm text-zinc-400 mt-1">Kelola ide aplikasi, tech stack, dan AI roadmap project Anda dalam satu tempat.</p>
            </div>
            <a href="/dashboard/new" class="bg-white text-zinc-950 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-zinc-200 transition-colors cursor-pointer flex items-center gap-2">
                <span>+</span> Buat Project Baru
            </a>
        </div>
        
        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Card 1 --}}
            <div class="relative overflow-hidden rounded-xl border border-white/10 bg-zinc-900 p-6 flex flex-col justify-between h-36">
                <div class="flex items-center justify-between text-zinc-400">
                    <span class="text-sm font-medium">Roadmap Aktif</span>
                    <img src="{{ asset('assets/icon-images/cube-icon.png') }}" class="w-5 h-5 shrink-0" alt="Cube Roadmap">
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-bold text-white">{{ count($projects) }} Project</div>
                    <p class="text-xs text-zinc-500 mt-1">Total proyek yang terdaftar</p>
                </div>
            </div>

            {{-- Card 2 --}}
            <div class="relative overflow-hidden rounded-xl border border-white/10 bg-zinc-900 p-6 flex flex-col justify-between h-36">
                <div class="flex items-center justify-between text-zinc-400">
                    <span class="text-sm font-medium">Emerald Token</span>
                    <img src="{{ asset('assets/icon-images/emerald-icon.png') }}" class="w-5 h-5 shrink-0" alt="Emerald Token">
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-bold text-white">18 / 20</div>
                    <p class="text-xs text-zinc-500 mt-1">Kuota terpakai bulan ini</p>
                </div>
            </div>

            {{-- Card 3 --}}
            <div class="relative overflow-hidden rounded-xl border border-white/10 bg-zinc-900 p-6 flex flex-col justify-between h-36">
                <div class="flex items-center justify-between text-zinc-400">
                    <span class="text-sm font-medium">PRD Ter-generate</span>
                    <img src="{{ asset('assets/icon-images/paper-icon.png') }}" class="w-5 h-5 shrink-0" alt="Doc PRD">
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
                    <h2 class="text-lg font-bold text-white">Project Aktif Anda</h2>
                    <p class="text-xs text-zinc-400 mt-1">Daftar Project yang sedang Anda kembangkan roadmapnya.</p>
                </div>
                <span class="text-xs text-zinc-400">Terakhir diperbarui hari ini</span>
            </div>
            
            <div class="h-px bg-white/10 mb-6"></div>
            
            <div class="flex flex-col gap-4">
                @forelse($projects as $project)
                    <div class="flex items-center justify-between p-4 bg-zinc-950 rounded-lg border border-white/5 hover:border-white/10 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center font-bold text-primary shrink-0 overflow-hidden">
                                @if($project->logo_url)
                                    <img src="{{ $project->logo_url }}" class="w-6 h-6 object-contain" alt="{{ $project->title }}">
                                @else
                                    <span class="text-sm font-extrabold text-primary">{{ strtoupper(substr($project->title, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-white">{{ $project->title }}</h3>
                                <p class="text-xs text-zinc-500 mt-0.5">
                                    Tech Stack: 
                                    @if($project->techStacks->count() > 0)
                                        {{ $project->techStacks->pluck('name')->join(' + ') }}
                                    @else
                                        Belum ditentukan
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="flex flex-col items-end">
                                <span class="text-xs font-semibold text-green-500">PRD Ready</span>
                                <span class="text-[10px] text-zinc-500 mt-0.5">Roadmap {{ $project->roadmaps->count() }} Langkah</span>
                            </div>
                            <a href="/dashboard/projects/{{ $project->slug }}" class="px-3 py-1.5 rounded bg-white/5 hover:bg-white/10 text-xs text-white border border-white/10 cursor-pointer">Buka</a>
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
    </div>
@endsection
