<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\TechStack;
use App\Models\Roadmap;
use App\Models\DbSchema;
use App\Models\DbColumn;
use App\Models\ProjectCost;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Services\AIService;

class CreateProject extends Component
{
    // Form wizard states
    public int $step = 1;
    public string $description = '';
    public string $techOption = 'ai';
    
    // Manual tech selections
    public string $frontend = '';
    public string $backend = '';
    public string $database = '';
    public string $deployment = '';
    
    // Refinement questions
    public string $q1 = '';
    public array $q2 = [];
    public array $q3 = [];
    public array $q4 = [];
    public array $q5 = [];
    
    // Loading overlay
    public bool $generating = false;
    public int $genStep = 0;

    /**
     * Call Gemini API to generate the project specs and save to database.
     *
     * @return array|null
     */
    public function generateProjectBackend()
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace;

        if (!$workspace) {
            return ['error' => 'Silakan pilih atau buat workspace terlebih dahulu.'];
        }

        if ($workspace->tokens_balance < 10) {
            return ['error' => 'Saldo koin Anda tidak mencukupi untuk membuat proyek baru (Dibutuhkan 10 Token).'];
        }

        @set_time_limit(180);
        // API key loading and configurations are handled within AIService

        // 1. Prepare tech preference text
        $techPreference = "";
        if ($this->techOption === 'manual') {
            $techPreference = "The user has pre-selected these technologies:\n" .
                "- Frontend: " . ($this->frontend ?: 'AI choice') . "\n" .
                "- Backend: " . ($this->backend ?: 'AI choice') . "\n" .
                "- Database: " . ($this->database ?: 'AI choice') . "\n" .
                "- Deployment: " . ($this->deployment ?: 'AI choice') . "\n" .
                "Please respect these selections and build the tech stack, roadmap, and DB schema around them.";
        } else {
            $techPreference = "Suggest the most suitable tech stack for this project idea. Make it modern, popular, yet very beginner-friendly (e.g. React.js, Tailwind CSS, Laravel, MySQL, SQLite, Vercel, Railway).";
        }

        // 2. Prepare refinement questions details
        $refinementInfo = "Refinement details from user:\n" .
            "- Target problem / flow description: " . ($this->q1 ?: 'Not specified') . "\n" .
            "- Scale: " . implode(', ', $this->q2) . "\n" .
            "- Budget constraints: " . implode(', ', $this->q3) . "\n" .
            "- Target users: " . implode(', ', $this->q4) . "\n" .
            "- Coding skill level: " . implode(', ', $this->q5);

        // 3. Detect empty or vague input
        $descTrimmed = trim($this->description);
        $inputGuard = "";
        if (mb_strlen($descTrimmed) < 10) {
            $inputGuard = "PERINGATAN INPUT: Deskripsi pengguna sangat singkat/kosong (\"{$descTrimmed}\"). Kamu WAJIB tetap menghasilkan output JSON valid. Interpretasikan input secara kreatif — jika hanya berupa satu kata (misal 'toko', 'sekolah', 'chat'), kembangkan menjadi aplikasi lengkap yang logis berdasarkan kata kunci tersebut. Jika benar-benar kosong atau tidak bermakna, buatkan proyek contoh berupa 'Aplikasi Manajemen Tugas Sederhana' sebagai fallback.\n\n";
        }

        // 4. Craft prompt — Professional IT Project Manager
        $prompt = "PERAN: Kamu adalah seorang IT Project Manager profesional berpengalaman 10+ tahun di industri software development Indonesia. Kamu memiliki keahlian mendalam dalam arsitektur sistem, perencanaan teknis, estimasi biaya infrastruktur, dan perancangan database relasional.

INSTRUKSI INTI:
- Hasilkan spesifikasi proyek software yang DETAIL dan PROFESIONAL layaknya dokumen perencanaan teknis perusahaan IT sesungguhnya.
- Bahasa output WAJIB Bahasa Indonesia, namun istilah teknis (nama teknologi, tipe data SQL, HTTP method) tetap dalam bahasa Inggris.
- JANGAN bertele-tele. Langsung ke inti. Setiap kalimat harus membawa informasi baru. Target total output maksimal 2500 token untuk menjaga kecepatan respons.
- Output HARUS berupa raw JSON valid tanpa pembungkus markdown (jangan gunakan \`\`\`json).

{$inputGuard}IDE PROYEK DARI PENGGUNA:
\"{$descTrimmed}\"

{$techPreference}

{$refinementInfo}

ATURAN KUALITAS OUTPUT:
1. PRD (`prd_markdown`) harus menyertakan: ringkasan eksekutif, analisis masalah vs solusi (dalam tabel markdown), definisi peran pengguna, daftar fitur per modul/peran, alur bisnis utama (user journey), integrasi AI jika relevan, dan non-functional requirements (keamanan, performa, responsivitas).
2. Database schema (`db_schemas`) WAJIB 100% konsisten dengan fitur dan peran yang disebutkan di PRD — setiap fitur bisnis harus tercermin dalam tabel dan kolomnya. Sertakan minimal kolom: id (PK), foreign keys yang relevan, timestamps (created_at, updated_at), dan kolom soft-delete jika sesuai.
3. Roadmap (`roadmaps`) harus berupa checklist pemrograman konkret bertahap — BUKAN fase generik ('Desain', 'Development'). Setiap langkah harus menyebutkan secara spesifik: file apa yang dibuat, migration apa yang dijalankan, controller/model apa yang diimplementasi, atau command apa yang dieksekusi. Gunakan estimasi waktu realistis dalam format 'Hari X-Y'.
4. Tech stack (`tech_stacks`) minimal 4 layer: Frontend, Backend, Database, Deployment. Tambahkan layer lain jika relevan (Authentication, Storage, Payment Gateway, AI/ML, dll).
5. Biaya (`costs`) harus realistis untuk pasar Indonesia — pisahkan biaya sekali bayar (domain, SSL) dan biaya berulang (hosting/bulan, database/bulan). Jika bisa gratis (free tier), cantumkan harga 0.
6. `first_deployment_cost` adalah total biaya one_time + 1 bulan pertama recurring.

STRUKTUR JSON YANG HARUS DIKEMBALIKAN:
{
  \"title\": \"Nama proyek yang deskriptif dan profesional\",
  \"description\": \"Deskripsi singkat 1-2 kalimat dalam Bahasa Indonesia tentang apa aplikasi ini dan siapa penggunanya.\",
  \"prd_markdown\": \"# PRD — [Judul Proyek]\\n\\n## 1. Ringkasan Eksekutif\\n...\\n\\n## 2. Masalah & Solusi\\n| # | Masalah | Solusi |\\n|---|---------|-------|\\n| 1 | ... | ... |\\n\\n## 3. Target Pengguna & Peran\\n...\\n\\n## 4. Fitur Utama per Modul\\n...\\n\\n## 5. Alur Bisnis Utama\\n...\\n\\n## 6. Integrasi AI (Jika Ada)\\n...\\n\\n## 7. Non-Functional Requirements\\n- Bahasa UI: Bahasa Indonesia\\n- Responsive: Ya (Mobile-first)\\n- Keamanan: CSRF, XSS protection, hashed passwords\\n- Performa: < 2 detik load time\",
  \"first_deployment_cost\": 0,
  \"tech_stacks\": [
    {
      \"layer\": \"Frontend\",
      \"name\": \"React.js\",
      \"icon\": \"🌐\",
      \"description\": \"Penjelasan singkat peran teknologi ini dalam proyek (Bahasa Indonesia).\"
    }
  ],
  \"roadmaps\": [
    {
      \"title\": \"Judul langkah konkret (misal: Setup Project & Konfigurasi Database)\",
      \"time\": \"Hari 1-2\",
      \"icon\": \"⚙️\",
      \"description\": \"Instruksi langkah demi langkah yang spesifik dan teknis (Bahasa Indonesia).\"
    }
  ],
  \"db_schemas\": [
    {
      \"table_name\": \"users\",
      \"table_desc\": \"Penjelasan singkat fungsi tabel ini (Bahasa Indonesia)\",
      \"columns\": [
        {
          \"name\": \"id\",
          \"type\": \"BigInt (PK, Auto Increment)\",
          \"nullable\": \"No\",
          \"desc\": \"Penjelasan singkat kolom (Bahasa Indonesia)\"
        }
      ]
    }
  ],
  \"costs\": [
    {
      \"type\": \"one_time\",
      \"name\": \"Domain .com\",
      \"price\": 150000
    },
    {
      \"type\": \"recurring\",
      \"name\": \"VPS Hosting / bulan\",
      \"price\": 50000
    }
  ]
}";

        // 4. API Request
        try {
            $aiResult = AIService::call($prompt);
            if (isset($aiResult['error'])) {
                return ['error' => $aiResult['error']];
            }
            $data = $aiResult['data'];

            // Deduct tokens and log transaction
            $workspace->decrement('tokens_balance', 10);
            
            \App\Models\WorkspaceTransaction::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'type' => 'generation',
                'amount' => -10,
                'description' => "Membuat Proyek Baru: {$data['title']} (-10 Token)",
            ]);

            // 5. Save to database under current user
            $slug = Str::slug($data['title']) . '-' . Str::random(4);
            
            $project = Project::create([
                'user_id' => auth()->id(),
                'workspace_id' => $workspace->id,
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'],
                'prd_markdown' => $data['prd_markdown'],
                'first_deployment_cost' => $data['first_deployment_cost'] ?? 0,
            ]);

            // Save Tech Stacks
            if (!empty($data['tech_stacks'])) {
                foreach ($data['tech_stacks'] as $tech) {
                    TechStack::create([
                        'project_id' => $project->id,
                        'layer' => $tech['layer'] ?? '',
                        'name' => $tech['name'] ?? '',
                        'icon' => $tech['icon'] ?? '⚙️',
                        'description' => $tech['description'] ?? '',
                    ]);
                }
            }

            // Save Roadmaps
            if (!empty($data['roadmaps'])) {
                foreach ($data['roadmaps'] as $step) {
                    Roadmap::create([
                        'project_id' => $project->id,
                        'title' => $step['title'] ?? '',
                        'time' => $step['time'] ?? '',
                        'icon' => $step['icon'] ?? '📋',
                        'description' => $step['description'] ?? '',
                    ]);
                }
            }

            // Save Db Schemas & Columns
            if (!empty($data['db_schemas'])) {
                foreach ($data['db_schemas'] as $table) {
                    $schema = DbSchema::create([
                        'project_id' => $project->id,
                        'table_name' => $table['table_name'] ?? '',
                        'table_desc' => $table['table_desc'] ?? '',
                    ]);

                    if (!empty($table['columns'])) {
                        foreach ($table['columns'] as $col) {
                            DbColumn::create([
                                'db_schema_id' => $schema->id,
                                'name' => $col['name'] ?? '',
                                'type' => $col['type'] ?? '',
                                'nullable' => $col['nullable'] ?? 'Yes',
                                'desc' => $col['desc'] ?? '',
                            ]);
                        }
                    }
                }
            }

            // Save Project Costs
            if (!empty($data['costs'])) {
                foreach ($data['costs'] as $cost) {
                    ProjectCost::create([
                        'project_id' => $project->id,
                        'type' => $cost['type'] ?? 'one_time',
                        'name' => $cost['name'] ?? '',
                        'price' => $cost['price'] ?? 0,
                    ]);
                }
            }

            return [
                'success' => true,
                'slug' => $project->slug
            ];

        } catch (\Exception $e) {
            Log::error('Error generating project: ' . $e->getMessage());
            return ['error' => 'System exception: ' . $e->getMessage()];
        }
    }

    /**
     * Render the Livewire component.
     */
    public function render()
    {
        return view('livewire.create-project');
    }
}
