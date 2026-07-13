<div x-data="{ 
    tab: 'techstack', 
    prdModalOpen: false, 
    editModalOpen: false,
    updatingProject: false,
    updateStep: 0,
    showInsufficientModal: false,
    currentTokens: {{ auth()->user()->currentWorkspace->tokens_balance ?? 0 }},
    
    projectTitle: @js($project->title),
    projectDesc: @js($project->description),
    editInfoModalOpen: false,
    
    secondsRemaining: 120,
    timerInterval: null,
    
    showErrorModal: false,
    errorMessage: '',
    
    openEditModal() {
        if (this.currentTokens < 10) {
            this.showInsufficientModal = true;
        } else {
            this.editModalOpen = true;
        }
    },
    
    saveInfo() {
        let self = this;
        this.editInfoModalOpen = false;
        self.projectTitle = self.$wire.editTitleInput;
        self.projectDesc = self.$wire.editDescInput;
        this.$wire.saveInfo();
    },
    
    startUpdating() {
        if (this.currentTokens < 10) {
            this.showInsufficientModal = true;
            return;
        }
        this.updatingProject = true;
        this.updateStep = 0;
        this.secondsRemaining = 120;
        
        let self = this;
        this.timerInterval = setInterval(() => {
            if (self.secondsRemaining !== 1) {
                self.secondsRemaining--;
            }
        }, 1000);
        
        setTimeout(() => { self.updateStep = 1; }, 1000);
        setTimeout(() => { self.updateStep = 2; }, 3000);
        setTimeout(() => { self.updateStep = 3; }, 6000);
        setTimeout(() => {
            self.$wire.updateProjectBackend(self.$wire.editRequest).then((result) => {
                clearInterval(self.timerInterval);
                self.updatingProject = false;
                self.updateStep = 0;
                if (result && result.success) {
                    self.editModalOpen = false;
                    self.projectTitle = result.title;
                    self.projectDesc = result.description;
                } else {
                    self.errorMessage = result && result.error ? result.error : 'Gagal memperbarui proyek.';
                    self.showErrorModal = true;
                }
            });
        }, 2000);
    }
}" class="flex flex-col gap-6 font-sans">
    
    {{-- Project Header Info --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-white/10">
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-white sm:text-3xl" x-text="projectTitle"></h1>
                <button @click="editInfoModalOpen = true; $wire.editTitleInput = projectTitle; $wire.editDescInput = projectDesc;" class="p-1 rounded text-zinc-500 hover:text-white hover:bg-white/5 transition-colors cursor-pointer" title="Edit Judul & Deskripsi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                </button>
            </div>
            <p class="text-sm text-zinc-400 mt-1" x-text="projectDesc"></p>
        </div>
        
        <div class="flex items-center gap-3">
            <button @click="openEditModal()" class="border border-white/10 hover:border-white/20 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors cursor-pointer flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                <span>Ubah Project</span>
                <span class="px-1.5 py-0.5 rounded bg-white/10 text-emerald-400 text-[10px] font-bold flex items-center gap-1 border border-emerald-500/20">
                    <img src="{{ asset('assets/icon-images/emerald-icon.png') }}" class="w-3.5 h-3.5 object-contain shrink-0" alt="Emerald"> 10
                </span>
            </button>
            <button @click="prdModalOpen = true" class="bg-primary text-primary-foreground px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-colors cursor-pointer flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Lihat PRD
            </button>
        </div>
    </div>

    {{-- Tabs Navigation Bar --}}
    <div class="flex border-b border-white/5 gap-1 select-none overflow-x-auto whitespace-nowrap scrollbar-none w-full">
        <button 
            @click="tab = 'techstack'"
            class="px-5 py-3 text-sm font-semibold border-b-2 transition-all cursor-pointer shrink-0"
            :class="tab === 'techstack' ? 'border-primary text-primary font-bold' : 'border-transparent text-zinc-400 hover:text-white'"
        >
            Tech Stack
        </button>
        <button 
            @click="tab = 'roadmap'"
            class="px-5 py-3 text-sm font-semibold border-b-2 transition-all cursor-pointer shrink-0"
            :class="tab === 'roadmap' ? 'border-primary text-primary font-bold' : 'border-transparent text-zinc-400 hover:text-white'"
        >
            Roadmap
        </button>
        <button 
            @click="tab = 'schema'"
            class="px-5 py-3 text-sm font-semibold border-b-2 transition-all cursor-pointer shrink-0"
            :class="tab === 'schema' ? 'border-primary text-primary font-bold' : 'border-transparent text-zinc-400 hover:text-white'"
        >
            Skema Database
        </button>
        <button 
            @click="tab = 'costs'"
            class="px-5 py-3 text-sm font-semibold border-b-2 transition-all cursor-pointer shrink-0"
            :class="tab === 'costs' ? 'border-primary text-primary font-bold' : 'border-transparent text-zinc-400 hover:text-white'"
        >
            Biaya
        </button>
        <button 
            @click="tab = 'export'"
            class="px-5 py-3 text-sm font-semibold border-b-2 transition-all cursor-pointer shrink-0"
            :class="tab === 'export' ? 'border-primary text-primary font-bold' : 'border-transparent text-zinc-400 hover:text-white'"
        >
            Export Context
        </button>
    </div>

    {{-- TAB CONTENT PANELS --}}
    <div>
        {{-- TAB 1: TECH STACK --}}
        <div x-show="tab === 'techstack'" x-transition class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($project->techStacks as $tech)
                    <div class="bg-zinc-900 border border-white/10 rounded-xl p-5 flex gap-4 hover:border-primary/30 transition-all">
                        <div class="w-12 h-12 shrink-0 rounded-lg bg-white/5 border border-white/5 flex items-center justify-center overflow-hidden">
                            @if($tech->logo_url)
                                <img src="{{ $tech->logo_url }}" class="w-8 h-8 object-contain" alt="{{ $tech->name }}">
                            @else
                                <span class="text-xl">{{ $tech->icon }}</span>
                            @endif
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase tracking-wider text-zinc-500 font-bold">{{ $tech->layer }}</span>
                            <h3 class="text-base font-semibold text-white">{{ $tech->name }}</h3>
                            <p class="text-xs text-zinc-400 leading-relaxed">{{ $tech->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- TAB 2: ROADMAP (VERTICAL STEPS TIMELINE) --}}
        <div x-show="tab === 'roadmap'" x-transition style="display: none;" class="relative pl-6 md:pl-10">
            
            {{-- Continuous vertical line --}}
            <div class="absolute top-6 bottom-6 left-[22px] md:left-[38px] w-0.5 bg-zinc-800"></div>

            <div class="space-y-8">
                @foreach($project->roadmaps as $index => $step)
                    <div class="relative flex gap-6 md:gap-10 items-start">
                        
                        {{-- Step bubble node --}}
                        <div class="absolute left-[-22px] md:left-[-38px] w-8 h-8 rounded-full border bg-zinc-950 flex items-center justify-center text-xs font-bold z-10 transition-all"
                             :class="{{ $index }} === 0 ? 'border-primary text-primary ring-4 ring-primary/10' : 'border-zinc-800 text-zinc-500'">
                            {{ $index + 1 }}
                        </div>

                        {{-- Card --}}
                        <div class="flex-1 bg-zinc-900 border border-white/10 rounded-xl p-5 md:p-6 space-y-3 hover:border-primary/20 transition-all">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg">{{ $step->icon }}</span>
                                    <h3 class="text-base font-bold text-white">{{ $step->title }}</h3>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 bg-zinc-800 border border-white/5 rounded text-zinc-400">
                                    {{ $step->time }}
                                </span>
                            </div>
                            <p class="text-xs text-zinc-400 leading-relaxed">{{ $step->description }}</p>

                            @if($index < $project->roadmaps->count() - 1)
                                {{-- Mini visual arrow down inside card to assist flow understanding --}}
                                <div class="flex items-center gap-1.5 text-[10px] font-semibold text-zinc-500 pt-1">
                                    <span>Langkah berikutnya</span>
                                    <svg class="w-3.5 h-3.5 animate-bounce text-primary mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

        {{-- TAB: DATABASE SCHEMA (STRUKTUR DATABASE) --}}
        <div x-show="tab === 'schema'" x-transition style="display: none;" class="space-y-6">
            @foreach($project->dbSchemas as $table)
                <div class="bg-zinc-900 border border-white/10 rounded-xl p-5 md:p-6 space-y-4">
                    <div>
                        <h3 class="text-base font-bold text-white flex items-center gap-2">
                            <span class="text-primary">📂</span> {{ $table->table_name }}
                        </h3>
                        <p class="text-xs text-zinc-400 mt-1">{{ $table->table_desc }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-white/5 text-zinc-500 font-semibold">
                                    <th class="pb-3 pr-4">Nama Kolom</th>
                                    <th class="pb-3 px-4">Tipe Data</th>
                                    <th class="pb-3 px-4">Nullable</th>
                                    <th class="pb-3 pl-4">Deskripsi Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-zinc-300">
                                @foreach($table->columns as $col)
                                    <tr class="hover:bg-white/2 transition-colors">
                                        <td class="py-3 pr-4 font-mono font-bold text-white">{{ $col->name }}</td>
                                        <td class="py-3 px-4 font-mono text-primary">{{ $col->type }}</td>
                                        <td class="py-3 px-4 text-zinc-400">{{ $col->nullable }}</td>
                                        <td class="py-3 pl-4 text-zinc-400 leading-relaxed">{{ $col->desc }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- TAB 3: COSTS (BIAYA) --}}
        <div x-show="tab === 'costs'" x-transition style="display: none;" class="space-y-6">
            
            {{-- First Deployment Summary --}}
            <div class="bg-primary/5 border border-primary/20 rounded-xl p-6 flex flex-col md:flex-row items-center justify-between gap-6 backdrop-blur">
                <div class="space-y-1 text-center md:text-left">
                    <h2 class="text-lg font-bold text-white">Estimasi Biaya First Deployment</h2>
                    <p class="text-xs text-zinc-400">Total modal awal yang diperlukan untuk meluncurkan aplikasi versi pertama Anda.</p>
                </div>
                <div class="text-center md:text-right">
                    @if($project->first_deployment_cost > 0)
                        <div class="text-3xl font-extrabold text-primary">Rp {{ number_format($project->first_deployment_cost, 0, ',', '.') }}</div>
                        <p class="text-[10px] text-zinc-500 mt-1">Sum dari domain + server bulan pertama</p>
                    @else
                        <div class="text-3xl font-extrabold text-primary">Gratis / Rp 0</div>
                        <p class="text-[10px] text-zinc-500 mt-1">Menggunakan hosting & DB gratis tier</p>
                    @endif
                </div>
            </div>

            {{-- Cost Breakdown Tables --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Table: One-time Cost --}}
                <div class="bg-zinc-900 border border-white/10 rounded-xl p-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('assets/svg/money-icon.svg') }}" alt="Money Icon" class="w-6 h-6 object-contain">
                        <h3 class="text-sm font-bold text-white">Biaya Sekali (One-Time)</h3>
                    </div>
                    <div class="divide-y divide-white/5">
                        @forelse($project->costs->where('type', 'one_time') as $item)
                            <div class="flex justify-between py-3 text-xs">
                                <span class="text-zinc-400">{{ $item->name }}</span>
                                <span class="font-semibold text-white">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <div class="text-zinc-500 text-xs py-2 italic">Tidak ada biaya awal tambahan.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Table: Recurring Cost --}}
                <div class="bg-zinc-900 border border-white/10 rounded-xl p-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('assets/svg/up-icon.svg') }}" alt="Recycle Icon" class="w-6 h-6 object-contain">
                        <h3 class="text-sm font-bold text-white">Biaya Berkelanjutan (Bulanan)</h3>
                    </div>
                    <div class="divide-y divide-white/5">
                        @forelse($project->costs->where('type', 'recurring') as $item)
                            <div class="flex justify-between py-3 text-xs">
                                <span class="text-zinc-400">{{ $item->name }}</span>
                                <span class="font-semibold text-white">
                                    @if($item->price > 0)
                                        Rp {{ number_format($item->price, 0, ',', '.') }} / bln
                                    @else
                                        Gratis (Free Tier)
                                    @endif
                                </span>
                            </div>
                        @empty
                            <div class="text-zinc-500 text-xs py-2 italic">Tidak ada biaya bulanan wajib.</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- TAB 5: EXPORT CONTEXT --}}
        <div x-show="tab === 'export'" x-transition style="display: none;" class="space-y-6">
            
            {{-- Hidden context source container --}}
            <div x-ref="aiContextSource" class="hidden"># AI CODING CONTEXT SPECIFICATION: {{ $project->title }}
Generated by BuatJalan AI Architect.

## 1. PRODUCT REQUIREMENTS DOCUMENT (PRD)
{{ $project->prd_markdown }}

## 2. RECOMMENDED TECH STACK
@foreach($project->techStacks as $tech)
- **{{ $tech->layer }}**: {{ $tech->name }} (Icon: {{ $tech->icon }})
  Description: {{ $tech->description }}
@endforeach

## 3. RELATIONAL DATABASE SCHEMAS
@foreach($project->dbSchemas as $schema)
### Table Name: `{{ $schema->name }}`
Description: {{ $schema->desc }}
Columns:
| Column Name | Data Type | Nullable | Description |
| :--- | :--- | :--- | :--- |
@foreach($schema->columns as $col)
| `{{ $col->name }}` | {{ $col->type }} | {{ $col->nullable }} | {{ $col->desc }} |
@endforeach

@endforeach

## 4. CONCRETE ROADMAP & TASK LISTS
@foreach($project->roadmaps as $step)
### Step {{ $step->step }}: {{ $step->title }}
- **Description**: {{ $step->desc }}
- **Terminal Command**: `{{ $step->terminal_command }}`
- **Affected File Path**: `{{ $step->file_path }}`

@endforeach
</div>

            {{-- Main Info Header --}}
            <div class="bg-zinc-900 border border-white/10 rounded-xl p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">
                        <img src="{{ asset('assets/svg/robot-icon.svg') }}" alt="Export Icon" class="w-10 h-10 object-contain">
                    </span>
                    <div>
                        <h2 class="text-base font-bold text-white">Export AI Coding Context</h2>
                        <p class="text-xs text-zinc-400 mt-0.5">Umpankan spesifikasi lengkap proyek ini ke AI Coding Assistant Anda secara instan untuk efisiensi koding maksimal.</p>
                    </div>
                </div>
                
                {{-- Quick Instructions --}}
                <div class="bg-zinc-950/50 border border-white/5 rounded-lg p-4 text-xs text-zinc-400 leading-relaxed space-y-2">
                    <span class="font-semibold text-white">💡 Cara Menggunakan Context:</span>
                    <ol class="list-decimal pl-4 space-y-1.5">
                        <li>Gunakan <strong>Export as File</strong> untuk mengunduh dokumen arsitektur proyek.</li>
                        <li>Letakkan file <code>{{ $project->slug }}-ai-context.md</code> di folder root proyek kodingan Anda.</li>
                        <li>Buka proyek di editor seperti Cursor, VS Code, atau Windsurf.</li>
                        <li>Ketik instruksi koding Anda dengan merujuk ke file context tersebut (contoh: <code class="text-primary">"Buat migration dan controller baru berdasarkan specs di {{ $project->slug }}-ai-context.md"</code>).</li>
                    </ol>
                </div>
            </div>

            {{-- Export Options Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Option 1: Export as File --}}
                <div class="bg-zinc-900 border border-white/10 hover:border-white/20 rounded-xl p-6 flex flex-col justify-between space-y-6 transition-all group">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-xl shrink-0 group-hover:bg-emerald-500/20 transition-colors">
                            <img src="{{ asset('assets/svg/file-icon.svg') }}" alt="File Icon" class="w-6 h-6 object-contain">
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-white">Export as File</h3>
                            <p class="text-xs text-zinc-400 leading-relaxed">Unduh seluruh berkas arsitektur proyek (PRD, Tech Stack, Database, & Roadmap) sebagai file Markdown terstruktur.</p>
                        </div>
                    </div>
                    
                    <button 
                        @click="
                            let text = $refs.aiContextSource.innerText;
                            let blob = new Blob([text], {type: 'text/markdown'});
                            let link = document.createElement('a');
                            link.href = URL.createObjectURL(blob);
                            link.download = '{{ $project->slug }}-ai-context.md';
                            link.click();
                        "
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors cursor-pointer flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unduh File Context (.md)
                    </button>
                </div>

                {{-- Option 2: Export as Link --}}
                <div class="bg-zinc-900 border border-white/10 hover:border-white/20 rounded-xl p-6 flex flex-col justify-between space-y-6 transition-all group">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-xl shrink-0 group-hover:bg-blue-500/20 transition-colors">
                            <img src="{{ asset('assets/svg/link-icon.svg') }}" alt="Link Icon" class="w-6 h-6 object-contain">
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-bold text-white">Export as Link (Raw Markdown)</h3>
                            <p class="text-xs text-zinc-400 leading-relaxed">Salin link tautan konteks mentah (Raw Markdown) proyek ini untuk langsung dibaca atau diunduh oleh AI Agent online (seperti ChatGPT, Claude, atau Cursor).</p>
                        </div>
                    </div>
                    
                    <button 
                        @click="
                            navigator.clipboard.writeText('{{ url('/shared/project/' . $project->slug) }}');
                            let original = $el.innerHTML;
                            $el.innerHTML = '<span class=\'flex items-center gap-1.5\'>✓ Link Berhasil Disalin!</span>';
                            $el.classList.remove('bg-blue-600', 'hover:bg-blue-500');
                            $el.classList.add('bg-emerald-600');
                            setTimeout(() => { 
                                $el.innerHTML = original; 
                                $el.classList.add('bg-blue-600', 'hover:bg-blue-500');
                                $el.classList.remove('bg-emerald-600');
                            }, 2000);
                        "
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition-colors cursor-pointer flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        Salin Tautan Context (Raw Link)
                    </button>
                </div>

            </div>

        </div>
    </div>

    {{-- PRD MODAL DIALOG --}}
    <div 
        x-show="prdModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        x-transition
        style="display: none;"
    >
        {{-- Click away --}}
        <div class="absolute inset-0" @click="prdModalOpen = false"></div>
        
        {{-- Modal Card --}}
        <div class="relative bg-zinc-900 border border-white/10 rounded-2xl w-full max-w-3xl h-[80vh] flex flex-col overflow-hidden shadow-2xl z-10 animate-in fade-in zoom-in-95 duration-150">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-white/5">
                <div>
                    <h2 class="text-lg font-bold text-white">Product Requirement Document (PRD)</h2>
                    <p class="text-xs text-zinc-400 mt-1">AI Generated PRD untuk {{ $project->title }}</p>
                </div>
                <button @click="prdModalOpen = false" class="p-1.5 rounded-md text-zinc-500 hover:bg-white/5 hover:text-white transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            {{-- Modal Body (Scrollable Markdown Area) --}}
            <div x-ref="prdText" class="flex-1 p-6 overflow-y-auto font-mono text-xs leading-relaxed text-zinc-300 bg-zinc-950 select-text whitespace-pre-wrap custom-scrollbar" id="prd-content">{{ $project->prd_markdown }}</div>

            {{-- Modal Footer --}}
            <div class="flex flex-col sm:flex-row items-center justify-between p-6 border-t border-white/5 bg-zinc-900/50 gap-4">
                <span class="text-[10px] text-zinc-500">Format: Markdown (README.md)</span>
                <div class="flex gap-3 w-full sm:w-auto">
                    {{-- Copy button --}}
                    <button 
                        @click="
                            navigator.clipboard.writeText($refs.prdText.innerText);
                            let original = $el.innerText;
                            $el.innerText = 'Copied!';
                            setTimeout(() => { $el.innerText = original; }, 2000);
                        "
                        class="flex-1 sm:flex-none border border-white/10 hover:border-white/20 px-4 py-2 rounded-lg text-xs font-semibold text-white cursor-pointer transition-colors"
                    >
                        Copy PRD
                    </button>
                    
                    {{-- Download README.md button --}}
                    <button 
                        @click="
                            let blob = new Blob([$refs.prdText.innerText], {type: 'text/markdown'});
                            let link = document.createElement('a');
                            link.href = URL.createObjectURL(blob);
                            link.download = 'README.md';
                            link.click();
                        "
                        class="flex-1 sm:flex-none bg-primary text-primary-foreground hover:bg-primary/90 px-4 py-2 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                    >
                        Download as README.md
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- UBAH PROJECT MODAL DIALOG --}}
    <div 
        x-show="editModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        x-transition
        style="display: none;"
    >
        {{-- Click away (only when not loading) --}}
        <div class="absolute inset-0" @click="!updatingProject ? editModalOpen = false : null"></div>
        
        {{-- Modal Card --}}
        <div class="relative bg-zinc-900 border border-white/10 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl z-10 animate-in fade-in zoom-in-95 duration-150">
            
            {{-- LOADING OVERLAY inside the card --}}
            <div x-show="updatingProject" class="absolute inset-0 bg-zinc-950/90 z-20 flex flex-col justify-center items-center p-6 text-center" x-transition style="display: none;">
                <svg class="animate-spin h-10 w-10 text-primary mb-6" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <h3 class="text-sm font-bold text-white mb-2">Memproses Pembaruan Proyek...</h3>
                <div class="mb-4 text-xs font-semibold text-primary flex items-center gap-1.5 justify-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary/20 flex items-center justify-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span>
                    </span>
                    Estimasi selesai: ~<span x-text="secondsRemaining">120</span> detik
                </div>
                <div class="w-full max-w-xs space-y-2 text-left bg-zinc-900/60 border border-white/5 rounded-lg p-4">
                    <div class="flex items-center gap-2 text-xs">
                        <span :class="updateStep >= 1 ? 'text-primary' : 'text-zinc-500'">✓</span>
                        <span :class="updateStep >= 1 ? 'text-white font-medium' : 'text-zinc-500'">Menganalisis permintaan perubahan</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span :class="updateStep >= 2 ? 'text-primary' : 'text-zinc-500'">✓</span>
                        <span :class="updateStep >= 2 ? 'text-white font-medium' : 'text-zinc-500'">Meregenerasi detail techstack & roadmap</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span :class="updateStep >= 3 ? 'text-primary' : 'text-zinc-500'">✓</span>
                        <span :class="updateStep >= 3 ? 'text-white font-medium' : 'text-zinc-500'">Menyimpan revisi PRD dokumen</span>
                    </div>
                </div>
            </div>

            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-white/5">
                <div>
                    <h2 class="text-lg font-bold text-white">Ubah Spesifikasi Proyek</h2>
                    <p class="text-xs text-zinc-400 mt-1">AI akan memperbarui roadmap, techstack, dan biaya sesuai instruksi Anda.</p>
                </div>
                <button @click="editModalOpen = false" class="p-1.5 rounded-md text-zinc-500 hover:bg-white/5 hover:text-white transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-4">
                <label class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Apa yang ingin diubah dari project ini?</label>
                <textarea 
                    x-model="$wire.editRequest"
                    rows="4"
                    placeholder="Contoh: Ubah database PostgreSQL menjadi MySQL, atau tambahkan fitur login multi-role pada langkah roadmap..."
                    class="w-full bg-zinc-950 border border-white/10 rounded-xl p-4 text-white text-xs placeholder:text-zinc-700 focus:outline-none focus:border-primary transition-all leading-relaxed custom-scrollbar resize-none"
                ></textarea>
            </div>

            {{-- Modal Footer --}}
            <div class="flex justify-end gap-3 p-6 border-t border-white/5 bg-zinc-900/50">
                <button 
                    @click="editModalOpen = false"
                    class="border border-white/10 hover:border-white/20 px-4 py-2 rounded-lg text-xs font-semibold text-white cursor-pointer transition-colors"
                >
                    Batal
                </button>
                <button 
                    @click="startUpdating()"
                    :disabled="!$wire.editRequest || $wire.editRequest.trim().length < 5"
                    class="bg-primary text-primary-foreground hover:bg-primary/90 px-4 py-2 rounded-lg text-xs font-semibold cursor-pointer transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5"
                >
                    <span>Ubah Sekarang</span>
                    <span class="px-1.5 py-0.5 rounded bg-black/25 text-white text-[10px] font-bold flex items-center gap-1 border border-black/10 select-none">
                        <img src="{{ asset('assets/icon-images/emerald-icon.png') }}" class="w-3.5 h-3.5 object-contain shrink-0" alt="Emerald"> 10
                    </span>
                </button>
            </div>

        </div>
    </div>

    {{-- EDIT TITLE & DESCRIPTION MODAL DIALOG --}}
    <div 
        x-show="editInfoModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        x-transition
        style="display: none;"
    >
        {{-- Click away --}}
        <div class="absolute inset-0" @click="editInfoModalOpen = false"></div>
        
        {{-- Modal Card --}}
        <div class="relative bg-zinc-900 border border-white/10 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl z-10 animate-in fade-in zoom-in-95 duration-150">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between p-6 border-b border-white/5">
                <div>
                    <h2 class="text-lg font-bold text-white">Edit Informasi Proyek</h2>
                    <p class="text-xs text-zinc-400 mt-1">Ubah judul dan deskripsi ringkas proyek Anda secara lokal.</p>
                </div>
                <button @click="editInfoModalOpen = false" class="p-1.5 rounded-md text-zinc-500 hover:bg-white/5 hover:text-white transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-4">
                {{-- Judul Input --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Judul Proyek</label>
                    <input 
                        type="text"
                        x-model="$wire.editTitleInput"
                        placeholder="Ketik judul proyek..."
                        class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs placeholder:text-zinc-700 focus:outline-none focus:border-primary transition-all"
                    >
                </div>

                {{-- Deskripsi Textarea --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Deskripsi Proyek</label>
                    <textarea 
                        x-model="$wire.editDescInput"
                        rows="4"
                        placeholder="Ketik deskripsi ringkas proyek..."
                        class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs placeholder:text-zinc-700 focus:outline-none focus:border-primary transition-all leading-relaxed custom-scrollbar resize-none"
                    ></textarea>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex justify-end gap-3 p-6 border-t border-white/5 bg-zinc-900/50">
                <button 
                    @click="editInfoModalOpen = false"
                    class="border border-white/10 hover:border-white/20 px-4 py-2 rounded-lg text-xs font-semibold text-white cursor-pointer transition-colors"
                >
                    Batal
                </button>
                <button 
                    @click="saveInfo()"
                    :disabled="!$wire.editTitleInput || $wire.editTitleInput.trim().length === 0 || !$wire.editDescInput || $wire.editDescInput.trim().length === 0"
                    class="bg-primary text-primary-foreground hover:bg-primary/90 px-4 py-2 rounded-lg text-xs font-semibold cursor-pointer transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Simpan Perubahan
                </button>
            </div>

        </div>
    </div>

    {{-- INSUFFICIENT TOKENS MODAL --}}
    <div 
        x-show="showInsufficientModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
        x-transition
        style="display: none;"
    >
        <div class="absolute inset-0" @click="showInsufficientModal = false"></div>
        <div class="relative bg-zinc-900 border border-white/10 rounded-2xl w-full max-w-sm p-6 shadow-2xl z-10 animate-in fade-in scale-in duration-200">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-4 animate-pulse">
                    <img src="{{ asset('assets/icon-images/emerald-icon.png') }}" class="w-6 h-6 object-contain" alt="Emerald">
                </div>
                
                <h3 class="text-base font-bold text-white mb-2">Saldo Token Tidak Mencukupi</h3>
                <p class="text-xs text-zinc-400 mb-6 leading-relaxed">
                    Pengubahan proyek ini membutuhkan <strong class="text-white">10 Token</strong>. Saldo token workspace Anda saat ini hanya <strong class="text-emerald-400">{{ auth()->user()->currentWorkspace->tokens_balance ?? 0 }} Token</strong>.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-3 w-full">
                    <button 
                        @click="showInsufficientModal = false"
                        class="flex-1 border border-white/10 hover:border-white/20 text-zinc-300 hover:text-white px-4 py-2 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                    >
                        Tutup
                    </button>
                    <a 
                        href="/dashboard/pricing"
                        class="flex-1 bg-primary text-primary-foreground hover:bg-primary/90 px-4 py-2 rounded-lg text-xs font-semibold text-center cursor-pointer transition-colors flex items-center justify-center gap-1 select-none"
                    >
                        <span>Top Up Token</span> 🚀
                    </a>
                </div>
        </div>
    </div>

    {{-- ERROR NOTIFICATION MODAL --}}
    <div 
        x-show="showErrorModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        x-transition
        style="display: none;"
    >
        <div class="absolute inset-0" @click="showErrorModal = false"></div>
        <div class="relative bg-zinc-900 border border-red-500/20 rounded-2xl w-full max-w-md p-6 shadow-2xl z-10 animate-in fade-in scale-in duration-200">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-red-500/10 border border-red-500/20 flex items-center justify-center mb-4">
                    <span class="text-xl">⚠️</span>
                </div>
                
                <h3 class="text-base font-bold text-white mb-2">Gagal Memproses Proyek</h3>
                <p class="text-xs text-zinc-400 mb-6 leading-relaxed" x-text="errorMessage">
                    Gagal memperbarui proyek. Silakan coba kembali beberapa saat lagi.
                </p>
                
                <button 
                    @click="showErrorModal = false"
                    class="w-full bg-red-600 hover:bg-red-500 text-white px-4 py-2.5 rounded-lg text-xs font-semibold cursor-pointer transition-colors"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
