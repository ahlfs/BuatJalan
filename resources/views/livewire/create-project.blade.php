<div x-data="{
    step: @entangle('step'),
    description: @entangle('description'),
    techOption: @entangle('techOption'),
    frontend: @entangle('frontend'),
    backend: @entangle('backend'),
    database: @entangle('database'),
    deployment: @entangle('deployment'),
    q1: @entangle('q1'),
    q2: @entangle('q2'),
    q3: @entangle('q3'),
    q4: @entangle('q4'),
    q5: @entangle('q5'),
    generating: @entangle('generating'),
    genStep: @entangle('genStep'),
    
    secondsRemaining: 30,
    timerInterval: null,
    
    get answeredCount() {
        let count = 0;
        if (this.q1 && this.q1.trim().length > 0) count++;
        if (this.q2 && this.q2.length > 0) count++;
        if (this.q3 && this.q3.length > 0) count++;
        if (this.q4 && this.q4.length > 0) count++;
        if (this.q5 && this.q5.length > 0) count++;
        return count;
    },
    
    toggleTag(target, tag) {
        if (!this[target]) {
            this[target] = [];
        }
        if (this[target].includes(tag)) {
            this[target] = this[target].filter(t => t !== tag);
        } else {
            this[target].push(tag);
        }
    },
    
    startGeneration() {
        this.generating = true;
        this.genStep = 0;
        this.secondsRemaining = 30;
        
        let self = this;
        this.timerInterval = setInterval(() => {
            if (self.secondsRemaining > 1) {
                self.secondsRemaining--;
            }
        }, 1000);
        
        setTimeout(() => { self.genStep = 1; }, 800);
        setTimeout(() => { self.genStep = 2; }, 1600);
        setTimeout(() => { self.genStep = 3; }, 2400);
        setTimeout(() => { self.genStep = 4; }, 3200);
        setTimeout(() => {
            self.$wire.generateProjectBackend().then((result) => {
                clearInterval(self.timerInterval);
                if (result && result.success && result.slug) {
                    window.location.href = '/dashboard/projects/' + result.slug;
                } else {
                    self.generating = false;
                    self.genStep = 0;
                    alert(result && result.error ? result.error : 'Gagal menghasilkan proyek. Pastikan API Key di konfigurasi .env sudah benar.');
                }
            });
        }, 4000);
    }
}" class="max-w-4xl mx-auto py-2">

    {{-- GENERATING LOADER SCREEN --}}
    <div x-show="generating" class="fixed inset-0 bg-zinc-950 z-50 flex flex-col justify-center items-center p-6" style="display: none;">
        <div class="relative flex flex-col items-center max-w-md w-full text-center">
            {{-- Glowing orb --}}
            <div class="absolute -top-24 h-72 w-72 rounded-full bg-primary/10 blur-3xl pointer-events-none"></div>
            
            {{-- Spinner --}}
            <svg class="animate-spin h-12 w-12 text-primary mb-8" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>

            <h2 class="text-2xl font-bold text-white mb-2">Menghasilkan PRD & Roadmap</h2>
            <p class="text-zinc-400 text-sm mb-2">AI sedang merancang spesifikasi kebutuhan dan rute belajar terbaik untuk aplikasi Anda.</p>
            <div class="mb-8 text-xs font-semibold text-primary flex items-center gap-1.5 justify-center">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span>
                </span>
                Estimasi selesai: ~<span x-text="secondsRemaining">30</span> detik
            </div>

            {{-- Status Steps --}}
            <div class="w-full space-y-4 text-left border border-white/5 bg-zinc-900/40 backdrop-blur rounded-xl p-5">
                <div class="flex items-center gap-3">
                    <span class="text-xs transition-colors" :class="genStep >= 1 ? 'text-primary' : 'text-zinc-500'">
                        <span x-show="genStep >= 1">✓</span>
                        <span x-show="genStep < 1" class="inline-block w-2.5 h-2.5 rounded-full bg-zinc-700 animate-pulse"></span>
                    </span>
                    <span class="text-sm font-medium transition-colors" :class="genStep >= 1 ? 'text-white' : 'text-zinc-500'">Menganalisis ide aplikasi</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs transition-colors" :class="genStep >= 2 ? 'text-primary' : 'text-zinc-500'">
                        <span x-show="genStep >= 2">✓</span>
                        <span x-show="genStep < 2" class="inline-block w-2.5 h-2.5 rounded-full bg-zinc-700" :class="genStep === 1 ? 'animate-pulse bg-primary' : ''"></span>
                    </span>
                    <span class="text-sm font-medium transition-colors" :class="genStep >= 2 ? 'text-white' : 'text-zinc-500'">Merumuskan target & fitur PRD</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs transition-colors" :class="genStep >= 3 ? 'text-primary' : 'text-zinc-500'">
                        <span x-show="genStep >= 3">✓</span>
                        <span x-show="genStep < 3" class="inline-block w-2.5 h-2.5 rounded-full bg-zinc-700" :class="genStep === 2 ? 'animate-pulse bg-primary' : ''"></span>
                    </span>
                    <span class="text-sm font-medium transition-colors" :class="genStep >= 3 ? 'text-white' : 'text-zinc-500'">Memetakan tech stack & arsitektur</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs transition-colors" :class="genStep >= 4 ? 'text-primary' : 'text-zinc-500'">
                        <span x-show="genStep >= 4">✓</span>
                        <span x-show="genStep < 4" class="inline-block w-2.5 h-2.5 rounded-full bg-zinc-700" :class="genStep === 3 ? 'animate-pulse bg-primary' : ''"></span>
                    </span>
                    <span class="text-sm font-medium transition-colors" :class="genStep >= 4 ? 'text-white' : 'text-zinc-500'">Menyiapkan repositori & roadmap belajar</span>
                </div>
            </div>
        </div>
    </div>

    {{-- WIZARD BOX CONTAINER --}}
    <div class="relative bg-zinc-900/40 border border-white/10 rounded-2xl p-6 md:p-10 backdrop-blur-md overflow-hidden">
        {{-- Top Header Logo & Capsule indicators --}}
        <div class="flex items-center justify-between mb-8 pb-6 border-b border-white/5">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('assets/icon-images/buatjalan-icon.png') }}" class="w-6 h-6 rounded" alt="BuatJalan Icon">
                <span class="text-base font-bold text-white tracking-wide">
                    buat<span class="text-primary">jalan</span>
                </span>
            </div>
            
            {{-- Horizontal capsules indicator --}}
            <div class="flex items-center gap-1.5">
                <span class="w-8 h-1.5 rounded-full transition-all duration-300" :class="step >= 1 ? 'bg-primary' : 'bg-zinc-700'"></span>
                <span class="w-8 h-1.5 rounded-full transition-all duration-300" :class="step >= 2 ? 'bg-primary' : 'bg-zinc-700'"></span>
                <span class="w-8 h-1.5 rounded-full transition-all duration-300" :class="step >= 3 ? 'bg-primary' : 'bg-zinc-700'"></span>
            </div>
        </div>

        {{-- STEP 1: EXPLAIN APP --}}
        <div x-show="step === 1" x-transition class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight sm:text-3xl">Jelaskan aplikasi yang ingin dibuat</h1>
                <p class="text-zinc-400 text-sm mt-1">Gambarkan ide aplikasi Anda secara bebas. AI akan membantu merapikannya menjadi modul terstruktur.</p>
            </div>

            <div class="space-y-2">
                <textarea 
                    x-model="description"
                    rows="8"
                    placeholder="Contoh: Saya ingin membuat aplikasi pencatat keuangan harian otomatis dengan integrasi AI. Pengguna bisa mengunggah struk belanja dan AI akan otomatis mengkategorikan transaksi serta memberikan analisis budget bulanan..."
                    class="w-full bg-zinc-950 border border-white/10 rounded-xl p-4 text-white text-sm placeholder:text-zinc-600 focus:outline-none focus:border-primary/80 focus:ring-1 focus:ring-primary/80 transition-all leading-relaxed"
                ></textarea>
            </div>

            <div class="flex justify-end pt-4">
                <button 
                    @click="step = 2"
                    :disabled="!description || description.trim().length < 10"
                    class="bg-primary text-primary-foreground px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-[0.98] cursor-pointer"
                >
                    Lanjut
                </button>
            </div>
        </div>

        {{-- STEP 2: TECH PREFERENCE --}}
        <div x-show="step === 2" x-transition class="space-y-6" style="display: none;">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight sm:text-3xl">Preferensi teknologi</h1>
                <p class="text-zinc-400 text-sm mt-1">Udah punya pilihan tech stack, atau mau AI yang tentuin?</p>
            </div>

            {{-- Preference Choice Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Card: Biarkan AI pilih --}}
                <div 
                    @click="techOption = 'ai'"
                    class="p-5 rounded-xl border cursor-pointer transition-all select-none relative"
                    :class="techOption === 'ai' ? 'border-primary bg-primary/2' : 'border-white/10 bg-zinc-950 hover:bg-white/5'"
                >
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary">AI</div>
                        <span class="text-sm font-semibold text-white">Biarkan AI pilih</span>
                    </div>
                    <p class="text-zinc-400 text-xs leading-relaxed">AI rekomendasiin stack yang paling cocok buat project kamu</p>
                </div>

                {{-- Card: Pilih sendiri --}}
                <div 
                    @click="techOption = 'manual'"
                    class="p-5 rounded-xl border cursor-pointer transition-all select-none relative"
                    :class="techOption === 'manual' ? 'border-primary bg-primary/2' : 'border-white/10 bg-zinc-950 hover:bg-white/5'"
                >
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary">⚙</div>
                        <span class="text-sm font-semibold text-white">Pilih sendiri</span>
                    </div>
                    <p class="text-zinc-400 text-xs leading-relaxed">Kamu tentuin teknologi yang mau dipakai</p>
                </div>
            </div>

            {{-- Expandable Dropdowns for Manual Selection --}}
            <div 
                x-show="techOption === 'manual'" 
                x-transition
                class="space-y-4 pt-4 border-t border-white/5"
            >
                <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Pilih teknologi untuk setiap layer</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Dropdown: Frontend --}}
                    <div class="bg-zinc-950 border border-white/10 rounded-xl p-4 flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-zinc-300">
                            <span class="text-sm">🗄</span>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white">Frontend</span>
                                <span class="text-[10px] text-zinc-500">UI & tampilan user</span>
                            </div>
                        </div>
                        <select x-model="frontend" class="w-full bg-zinc-900 border border-white/10 rounded-lg p-2 text-xs text-white focus:outline-none focus:border-primary">
                            <option value="">Pilih framework...</option>
                            <option value="React.js">React.js</option>
                            <option value="Vue.js">Vue.js</option>
                            <option value="Svelte">Svelte</option>
                            <option value="Next.js">Next.js</option>
                            <option value="Astro">Astro</option>
                        </select>
                    </div>

                    {{-- Dropdown: Backend --}}
                    <div class="bg-zinc-950 border border-white/10 rounded-xl p-4 flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-zinc-300">
                            <span class="text-sm">☁</span>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white">Backend</span>
                                <span class="text-[10px] text-zinc-500">Logic & API server</span>
                            </div>
                        </div>
                        <select x-model="backend" class="w-full bg-zinc-900 border border-white/10 rounded-lg p-2 text-xs text-white focus:outline-none focus:border-primary">
                            <option value="">Pilih backend...</option>
                            <option value="Laravel">Laravel</option>
                            <option value="Node.js">Node.js (Express)</option>
                            <option value="Go">Go (Golang)</option>
                            <option value="Django">Django (Python)</option>
                            <option value="FastAPI">FastAPI</option>
                        </select>
                    </div>

                    {{-- Dropdown: Database --}}
                    <div class="bg-zinc-950 border border-white/10 rounded-xl p-4 flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-zinc-300">
                            <span class="text-sm">📁</span>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white">Database</span>
                                <span class="text-[10px] text-zinc-500">Penyimpanan data</span>
                            </div>
                        </div>
                        <select x-model="database" class="w-full bg-zinc-900 border border-white/10 rounded-lg p-2 text-xs text-white focus:outline-none focus:border-primary">
                            <option value="">Pilih database...</option>
                            <option value="PostgreSQL">PostgreSQL</option>
                            <option value="MySQL">MySQL</option>
                            <option value="SQLite">SQLite</option>
                            <option value="MongoDB">MongoDB</option>
                            <option value="Redis">Redis</option>
                        </select>
                    </div>

                    {{-- Dropdown: Deployment --}}
                    <div class="bg-zinc-950 border border-white/10 rounded-xl p-4 flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-zinc-300">
                            <span class="text-sm">🚀</span>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white">Deployment</span>
                                <span class="text-[10px] text-zinc-500">Hosting & infra</span>
                            </div>
                        </div>
                        <select x-model="deployment" class="w-full bg-zinc-900 border border-white/10 rounded-lg p-2 text-xs text-white focus:outline-none focus:border-primary">
                            <option value="">Pilih platform...</option>
                            <option value="Vercel">Vercel</option>
                            <option value="Railway">Railway</option>
                            <option value="Heroku">Heroku</option>
                            <option value="AWS">AWS</option>
                            <option value="DigitalOcean">DigitalOcean</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex items-center justify-between pt-6 border-t border-white/5">
                <button 
                    @click="step = 1"
                    class="text-zinc-400 hover:text-white text-sm font-semibold transition-colors cursor-pointer"
                >
                    Kembali
                </button>
                <button 
                    @click="step = 3"
                    class="bg-primary text-primary-foreground px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all active:scale-[0.98] cursor-pointer"
                >
                    Lanjut
                </button>
            </div>
        </div>

        {{-- STEP 3: REFINEMENT QUESTIONS --}}
        <div x-show="step === 3" x-transition class="space-y-6" style="display: none;">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight sm:text-3xl">Beberapa pertanyaan</h1>
                    <p class="text-zinc-400 text-sm mt-1">Biar PRD-nya lebih akurat. Jawab semua pertanyaan di bawah.</p>
                </div>
                <span class="text-xs font-bold text-zinc-500 bg-white/5 border border-white/10 px-2.5 py-1 rounded-full" x-text="answeredCount + '/5'">0/5</span>
            </div>

            <div class="space-y-6 divide-y divide-white/5">
                {{-- Question 1 --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-white">1. Ceritakan seseorang yang butuh aplikasi ini. Sekarang mereka ngapain buat ngatasi masalahnya?</label>
                        <button @click="q1 = ''" class="text-xs text-zinc-500 hover:text-white transition-colors cursor-pointer">Lewati</button>
                    </div>
                    <textarea 
                        x-model="q1"
                        rows="2"
                        placeholder="Ketik jawaban..."
                        class="w-full bg-zinc-950 border border-white/10 rounded-xl p-3 text-white text-xs placeholder:text-zinc-700 focus:outline-none focus:border-primary transition-colors"
                    ></textarea>
                </div>

                {{-- Question 2 --}}
                <div class="space-y-3 pt-6">
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-white">2. Seberapa besar skala aplikasi ini</label>
                        <button @click="q2 = []" class="text-xs text-zinc-500 hover:text-white transition-colors cursor-pointer">Lewati</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="tag in ['Skala Kecil (MVP / Portofolio)', 'Skala Menengah (SaaS Bisnis)', 'Skala Besar (Enterprise)', 'Eksperimen / Just for Fun']">
                            <button 
                                @click="toggleTag('q2', tag)"
                                type="button"
                                class="px-3.5 py-1.5 rounded-full border text-xs font-medium transition-all cursor-pointer"
                                :class="q2 && q2.includes(tag) ? 'bg-primary/20 border-primary text-white' : 'bg-zinc-950 border-white/10 text-zinc-400 hover:text-white hover:border-white/20'"
                                x-text="tag"
                            ></button>
                        </template>
                        <button type="button" class="px-3.5 py-1.5 rounded-full border border-white/10 border-dashed bg-transparent text-zinc-500 text-xs font-medium cursor-pointer">+ Lainnya</button>
                    </div>
                </div>

                {{-- Question 3 --}}
                <div class="space-y-3 pt-6">
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-white">3. Biaya yang siap anda keluarkan</label>
                        <button @click="q3 = []" class="text-xs text-zinc-500 hover:text-white transition-colors cursor-pointer">Lewati</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="tag in ['Hanya Free Tier (Rp 0)', 'Budget Hemat (< Rp 200rb/bln)', 'Budget Menengah (Rp 200rb - 1jt/bln)', 'Budget Enterprise (> Rp 1jt/bln)']">
                            <button 
                                @click="toggleTag('q3', tag)"
                                type="button"
                                class="px-3.5 py-1.5 rounded-full border text-xs font-medium transition-all cursor-pointer"
                                :class="q3 && q3.includes(tag) ? 'bg-primary/20 border-primary text-white' : 'bg-zinc-950 border-white/10 text-zinc-400 hover:text-white hover:border-white/20'"
                                x-text="tag"
                            ></button>
                        </template>
                        <button type="button" class="px-3.5 py-1.5 rounded-full border border-white/10 border-dashed bg-transparent text-zinc-500 text-xs font-medium cursor-pointer">+ Lainnya</button>
                    </div>
                </div>

                {{-- Question 4 --}}
                <div class="space-y-3 pt-6">
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-white">4. Siapa target pengguna utama aplikasi ini</label>
                        <button @click="q4 = []" class="text-xs text-zinc-500 hover:text-white transition-colors cursor-pointer">Lewati</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="tag in ['UMKM / Toko Lokal', 'Developer / Tim Teknis', 'Karyawan Internal Kantor', 'Publik / Umum']">
                            <button 
                                @click="toggleTag('q4', tag)"
                                type="button"
                                class="px-3.5 py-1.5 rounded-full border text-xs font-medium transition-all cursor-pointer"
                                :class="q4 && q4.includes(tag) ? 'bg-primary/20 border-primary text-white' : 'bg-zinc-950 border-white/10 text-zinc-400 hover:text-white hover:border-white/20'"
                                x-text="tag"
                            ></button>
                        </template>
                        <button type="button" class="px-3.5 py-1.5 rounded-full border border-white/10 border-dashed bg-transparent text-zinc-500 text-xs font-medium cursor-pointer">+ Lainnya</button>
                    </div>
                </div>

                {{-- Question 5 --}}
                <div class="space-y-3 pt-6">
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-medium text-white">5. Bagaimana tingkat pemahaman coding Anda</label>
                        <button @click="q5 = []" class="text-xs text-zinc-500 hover:text-white transition-colors cursor-pointer">Lewati</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="tag in ['Vibecoder (Tanpa ngoding)', 'Pemula (Bisa copy-paste)', 'Menengah (Mengerti logika)', 'Profesional (Terbiasa deploy)']">
                            <button 
                                @click="toggleTag('q5', tag)"
                                type="button"
                                class="px-3.5 py-1.5 rounded-full border text-xs font-medium transition-all cursor-pointer"
                                :class="q5 && q5.includes(tag) ? 'bg-primary/20 border-primary text-white' : 'bg-zinc-950 border-white/10 text-zinc-400 hover:text-white hover:border-white/20'"
                                x-text="tag"
                            ></button>
                        </template>
                        <button type="button" class="px-3.5 py-1.5 rounded-full border border-white/10 border-dashed bg-transparent text-zinc-500 text-xs font-medium cursor-pointer">+ Lainnya</button>
                    </div>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex items-center justify-between pt-6 border-t border-white/5">
                <button 
                    @click="step = 2"
                    class="text-zinc-400 hover:text-white text-sm font-semibold transition-colors cursor-pointer"
                >
                    Kembali
                </button>
                <button 
                    @click="startGeneration()"
                    class="bg-primary text-primary-foreground px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all active:scale-[0.98] cursor-pointer"
                >
                    Generate PRD
                </button>
            </div>
        </div>
    </div>
</div>
