@extends('layouts.dashboard', ['activeId' => $activeId])

@section('title', 'Workspace Analytics')
@section('page_title', 'Workspace Analytics')

@section('content')
<div class="max-w-6xl mx-auto py-2 space-y-8 font-sans">
    
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-white/5 pb-5">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">Workspace Analytics</h1>
            <p class="text-xs text-zinc-400 mt-1">Lacak ringkasan pembuatan modul AI, data arsitektur, dan log aktivitas real-time.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium bg-emerald-500/10 border border-emerald-500/20 text-emerald-400">
                ● Live Update
            </span>
        </div>
    </div>

    {{-- Metrics Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Card 1: Total Proyek --}}
        <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-4 flex items-center justify-between shadow-md hover:border-white/10 transition-colors">
            <div class="space-y-1">
                <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Total Proyek</span>
                <div class="text-2xl font-extrabold text-white">{{ count($projects) }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-lg shadow-inner">
                <img src="{{ asset('assets/svg/folder-icon.svg') }}" class="w-10 h-auto" alt="Folder">
            </div>
        </div>

        {{-- Card 2: Tech Stacks --}}
        <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-4 flex items-center justify-between shadow-md hover:border-white/10 transition-colors">
            <div class="space-y-1">
                <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Tech Stack Pilihan</span>
                <div class="text-2xl font-extrabold text-white">{{ $projects->sum(fn($p) => $p->techStacks->count()) }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-lg shadow-inner">
                <img src="{{ asset('assets/svg/bulb-icon.svg') }}" class="w-10 h-auto" alt="Terminal">
            </div>
        </div>

        {{-- Card 3: Roadmap Tasks --}}
        <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-4 flex items-center justify-between shadow-md hover:border-white/10 transition-colors">
            <div class="space-y-1">
                <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Modul Roadmap</span>
                <div class="text-2xl font-extrabold text-white">{{ $projects->sum(fn($p) => $p->roadmaps->count()) }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-lg shadow-inner">
                <img src="{{ asset('assets/svg/compass-icon.svg') }}" class="w-10 h-auto" alt="Roadmap">
            </div>
        </div>

        {{-- Card 4: Database Schemas --}}
        <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-4 flex items-center justify-between shadow-md hover:border-white/10 transition-colors">
            <div class="space-y-1">
                <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Tabel Database</span>
                <div class="text-2xl font-extrabold text-white">{{ $projects->sum(fn($p) => $p->dbSchemas->count()) }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-lg shadow-inner">
                <img src="{{ asset('assets/svg/database-icon.svg') }}" class="w-10 h-auto" alt="Database">
            </div>
        </div>

    </div>

    {{-- Main Analytics Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- LEFT SIDE: Log Aktivitas Pembuatan (Col-span 2) --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    📋 Log Aktivitas Pembuatan
                </h3>
                <span class="text-[10px] text-zinc-500 font-medium">Diurutkan berdasarkan terbaru</span>
            </div>

            @php
                $transactions = auth()->user()->currentWorkspace
                    ? auth()->user()->currentWorkspace->transactions()->with('user')->latest()->get()
                    : collect();
            @endphp

            @if($transactions->count() === 0)
                {{-- Empty State --}}
                <div class="border border-dashed border-white/10 rounded-2xl p-12 text-center flex flex-col items-center justify-center space-y-4 bg-zinc-900/10">
                    <span class="text-4xl">📭</span>
                    <h4 class="text-sm font-bold text-white">Log Aktivitas Kosong</h4>
                    <p class="text-xs text-zinc-500 max-w-sm">Anda belum memiliki transaksi koin atau aktivitas generator di workspace ini.</p>
                    <a href="/dashboard/new" class="inline-flex items-center justify-center bg-primary text-primary-foreground hover:bg-primary/90 font-semibold px-4 py-2 rounded-xl text-xs transition-colors">
                        + Buat Proyek Baru
                    </a>
                </div>
            @else
                <div class="max-h-[520px] overflow-y-auto pr-2 custom-scrollbar">
                    {{-- Timeline Log Stream --}}
                    <div class="relative pl-6 space-y-6 before:absolute before:top-2 before:bottom-2 before:left-[11px] before:w-0.5 before:bg-white/10">
                        
                        @foreach($transactions as $trans)
                            @php
                                $color = 'emerald';
                                $badge = 'Create';
                                $actionText = 'membuat';
                                $creditsText = '-' . abs($trans->amount) . ' Token';
                                $creditsColor = 'text-emerald-400';

                                if ($trans->type === 'modification') {
                                    $color = 'amber';
                                    $badge = 'Edit';
                                    $actionText = 'mengubah';
                                    $creditsColor = 'text-amber-400';
                                } elseif ($trans->type === 'top_up') {
                                    $color = 'blue';
                                    $badge = 'Top Up';
                                    $actionText = 'melakukan top up untuk';
                                    $creditsText = '+' . $trans->amount . ' Token';
                                    $creditsColor = 'text-blue-400';
                                }
                            @endphp
                            <div class="relative group">
                                {{-- Timeline Marker --}}
                                <div class="absolute left-[-21px] top-1.5 w-[12px] h-[12px] rounded-full border-4 border-zinc-950 group-hover:scale-125 transition-transform bg-{{ $color }}-500"></div>
                                
                                <div class="bg-zinc-900/30 border border-white/5 hover:border-white/10 p-4 rounded-2xl space-y-1.5 transition-colors">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-xs text-zinc-300 leading-relaxed">
                                                <span class="text-white font-semibold">{{ $trans->user->name ?? 'User' }}</span> 
                                                {{ $actionText }} 
                                                <span class="text-white font-semibold">"{{ $trans->description }}"</span>.
                                            </p>
                                            <p class="text-[10px] text-zinc-500 mt-1">
                                                Mutasi Token: <span class="font-bold {{ $creditsColor }}">{{ $creditsText }}</span>
                                            </p>
                                        </div>
                                        <span class="text-[9px] font-bold uppercase tracking-wider text-zinc-400 bg-white/5 border border-white/10 px-2 py-0.5 rounded-full shrink-0">
                                            {{ $badge }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] text-zinc-500 pt-0.5">
                                        <span>Workspace: {{ auth()->user()->currentWorkspace->name ?? '' }}</span>
                                        <span>{{ $trans->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            @endif
        </div>

        {{-- RIGHT SIDE: Technology Radar & Summary (Col-span 1) --}}
        <div class="space-y-6">
            
            {{-- Technology Radar Widget --}}
            <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-5 space-y-4">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">
                    📊 Radar Teknologi Workspace
                </h3>
                <p class="text-[11px] text-zinc-400 leading-relaxed">Persentase distribusi teknologi paling dominan yang direkomendasikan AI untuk proyek-proyek Anda.</p>
                
                @php
                    $knownKeywords = [
                        'laravel' => 'Laravel',
                        'react' => 'React',
                        'next' => 'Next.js',
                        'astro' => 'Astro',
                        'node' => 'Node.js',
                        'express' => 'Express',
                        'postgres' => 'PostgreSQL',
                        'mysql' => 'MySQL',
                        'mongo' => 'MongoDB',
                        'python' => 'Python',
                        'go' => 'Go',
                        'vue' => 'Vue.js',
                        'flutter' => 'Flutter',
                        'docker' => 'Docker',
                        'tailwind' => 'Tailwind CSS',
                        'kotlin' => 'Kotlin',
                        'typescript' => 'TypeScript',
                        'javascript' => 'JavaScript',
                        'java' => 'Java',
                        'spring' => 'Spring Boot',
                        'swift' => 'Swift',
                        'firebase' => 'Firebase',
                        'redis' => 'Redis',
                        'php' => 'PHP',
                        'ruby' => 'Ruby',
                        'supabase' => 'Supabase',
                        'vercel' => 'Vercel',
                        'railway' => 'Railway',
                        'aws' => 'AWS',
                        'stripe' => 'Stripe',
                        'kubernetes' => 'Kubernetes',
                        'nginx' => 'Nginx',
                        'django' => 'Django',
                        'flask' => 'Flask',
                        'angular' => 'Angular',
                        'svelte' => 'Svelte',
                        'figma' => 'Figma',
                    ];

                    $allTech = [];
                    foreach($projects as $p) {
                        foreach($p->techStacks as $ts) {
                            $nameLower = strtolower($ts->name);
                            $matched = false;
                            
                            foreach ($knownKeywords as $key => $displayName) {
                                if (str_contains($nameLower, $key)) {
                                    $allTech[] = $displayName;
                                    $matched = true;
                                }
                            }
                            
                            if (!$matched) {
                                $allTech[] = ucwords($ts->name);
                            }
                        }
                    }
                    
                    $techCounts = array_count_values($allTech);
                    arsort($techCounts);
                    $totalTechCount = count($allTech) ?: 1;
                    $topTech = array_slice($techCounts, 0, 5, true);
                @endphp

                @if(count($topTech) === 0)
                    <div class="text-center py-6 text-xs text-zinc-500">
                        Belum ada teknologi terdaftar
                    </div>
                @else
                    <div class="space-y-3 pt-2">
                        @foreach($topTech as $name => $count)
                            @php
                                $percent = round(($count / $totalTechCount) * 100);
                            @endphp
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-zinc-300 font-medium">{{ $name }}</span>
                                    <span class="text-zinc-500">{{ $percent }}%</span>
                                </div>
                                <div class="w-full bg-zinc-950 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-primary h-1.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Workspace Health Widget --}}
            <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-5 space-y-3">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">
                    📈 Efisiensi Pembangunan
                </h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-xs">
                        <span class="text-zinc-400">Jam Kerja Dihemat:</span>
                        <span class="text-emerald-400 font-bold">{{ count($projects) * 30 }} Jam</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-zinc-400">Rata-rata Modul / Proyek:</span>
                        <span class="text-white font-medium">{{ count($projects) ? round($projects->sum(fn($p) => $p->roadmaps->count()) / count($projects), 1) : 0 }} Tugas</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
