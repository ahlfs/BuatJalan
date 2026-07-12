<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\TechStack;
use App\Models\Roadmap;
use App\Models\DbSchema;
use App\Models\DbColumn;
use App\Models\ProjectCost;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\AIService;
use Illuminate\Support\Facades\DB;

class ProjectDetail extends Component
{
    public Project $project;
    
    // Form fields
    public string $editTitleInput = '';
    public string $editDescInput = '';
    public string $editRequest = '';
    
    // Status flag
    public bool $successMessage = false;

    /**
     * Initialize component state.
     *
     * @param \App\Models\Project $project
     */
    public function mount(Project $project)
    {
        $this->project = $project;
        $this->editTitleInput = $project->title;
        $this->editDescInput = $project->description;
    }

    /**
     * Save the title and description updates to the database.
     */
    public function saveInfo()
    {
        $this->validate([
            'editTitleInput' => 'required|string|max:255',
            'editDescInput' => 'required|string|max:1000',
        ]);

        $this->project->update([
            'title' => $this->editTitleInput,
            'description' => $this->editDescInput,
        ]);

        $this->successMessage = true;
        
        // Dispatch browser event for Alpine notifications if needed
        $this->dispatch('info-saved', [
            'title' => $this->editTitleInput,
            'description' => $this->editDescInput
        ]);

        // Dispatch dynamic sidebar refresh event
        $this->dispatch('refresh-sidebar');
    }

    /**
     * Persist the project edit request and apply simulation changes to database.
     *
     * @param string $requestText
     */
    public function updateProjectBackend(string $requestText)
    {
        $user = auth()->user();
        $workspace = $user->currentWorkspace;

        if (!$workspace) {
            return ['error' => 'Silakan pilih atau buat workspace terlebih dahulu.'];
        }

        if ($workspace->tokens_balance < 10) {
            return ['error' => 'Saldo koin Anda tidak mencukupi untuk mengubah proyek (Dibutuhkan 10 Token).'];
        }

        if (empty(trim($requestText))) {
            return ['error' => 'Permintaan perubahan tidak boleh kosong.'];
        }

        @set_time_limit(180);
        // API key loading and configurations are handled within AIService

        // 1. Gather current state of the project
        $currentState = "PROYEK SEBELUMNYA:\n";
        $currentState .= "- Judul: " . $this->project->title . "\n";
        $currentState .= "- Deskripsi: " . $this->project->description . "\n";
        $currentState .= "- PRD: \n" . $this->project->prd_markdown . "\n\n";
        
        $currentState .= "TECH STACK SEBELUMNYA:\n";
        foreach ($this->project->techStacks as $tech) {
            $currentState .= "- Layer: {$tech->layer}, Nama: {$tech->name}, Deskripsi: {$tech->description}\n";
        }
        
        $currentState .= "\nROADMAP SEBELUMNYA:\n";
        foreach ($this->project->roadmaps as $step) {
            $currentState .= "- Waktu: {$step->time}, Judul: {$step->title}, Deskripsi: {$step->description}\n";
        }
        
        $currentState .= "\nDATABASE SCHEMA SEBELUMNYA:\n";
        foreach ($this->project->dbSchemas as $schema) {
            $currentState .= "- Tabel: {$schema->table_name} ({$schema->table_desc})\n";
            foreach ($schema->columns as $col) {
                $currentState .= "  * Kolom: {$col->name}, Tipe: {$col->type}, Nullable: {$col->nullable}, Deskripsi: {$col->desc}\n";
            }
        }

        // 2. Detect vague or unclear change request
        $requestTrimmed = trim($requestText);
        $inputGuard = "";
        if (mb_strlen($requestTrimmed) < 10) {
            $inputGuard = "PERINGATAN INPUT: Permintaan perubahan pengguna sangat singkat (\"{$requestTrimmed}\"). Interpretasikan secara cerdas berdasarkan konteks proyek yang ada — jika permintaan ambigu, lakukan perbaikan/optimasi umum pada arsitektur, database schema, dan roadmap proyek yang sudah ada. Jangan pernah menghasilkan output kosong.\n\n";
        }

        // 3. Build prompt — Professional IT Project Manager (Update Mode)
        $prompt = "PERAN: Kamu adalah seorang IT Project Manager profesional berpengalaman 10+ tahun yang sedang melakukan iterasi perubahan pada proyek software existing. Kamu memiliki keahlian mendalam dalam arsitektur sistem, perencanaan teknis, estimasi biaya infrastruktur, dan perancangan database relasional.

INSTRUKSI INTI:
- Hasilkan versi TERBARU dari seluruh spesifikasi proyek berdasarkan permintaan perubahan pengguna.
- Bahasa output WAJIB Bahasa Indonesia, namun istilah teknis tetap dalam bahasa Inggris.
- JANGAN bertele-tele. Langsung ke inti. Target output maksimal 2500 token.
- Output HARUS berupa raw JSON valid tanpa pembungkus markdown.
- Pertahankan elemen yang tidak diminta untuk diubah, namun sesuaikan jika ada dampak domino dari perubahan yang diminta.

{$inputGuard}PERMINTAAN PERUBAHAN DARI PENGGUNA:
\"{$requestTrimmed}\"

{$currentState}

ATURAN KUALITAS PEMBARUAN:
1. PRD (`prd_markdown`) harus diperbarui untuk mencerminkan perubahan yang diminta — tambahkan, ubah, atau hapus fitur/modul sesuai instruksi. Pertahankan format PRD profesional: ringkasan eksekutif, tabel masalah vs solusi, definisi peran, daftar fitur per modul, alur bisnis, integrasi AI jika relevan, dan non-functional requirements.
2. Database schema (`db_schemas`) WAJIB 100% konsisten dengan PRD yang sudah diperbarui. Jika ada fitur baru, tambahkan tabel/kolom yang relevan. Jika ada fitur yang dihapus, hapus tabel terkait. Sertakan id (PK), foreign keys, timestamps, dan soft-delete jika sesuai.
3. Roadmap (`roadmaps`) harus diperbarui menjadi checklist pemrograman konkret bertahap — BUKAN fase generik. Setiap langkah harus menyebutkan file, migration, controller/model, atau command yang spesifik. Gunakan estimasi waktu 'Hari X-Y'.
4. Tech stack (`tech_stacks`) harus disesuaikan jika perubahan memerlukan teknologi baru atau penggantian teknologi. Minimal 4 layer.
5. Biaya (`costs`) harus realistis untuk pasar Indonesia. `first_deployment_cost` = total one_time + 1 bulan recurring.

STRUKTUR JSON YANG HARUS DIKEMBALIKAN:
{
  \"title\": \"Nama proyek yang diperbarui\",
  \"description\": \"Deskripsi singkat 1-2 kalimat yang diperbarui (Bahasa Indonesia).\",
  \"prd_markdown\": \"# PRD — [Judul Proyek]\\n\\n## 1. Ringkasan Eksekutif\\n...\\n\\n## 2. Masalah & Solusi\\n| # | Masalah | Solusi |\\n|---|---------|-------|\\n| 1 | ... | ... |\\n\\n## 3. Target Pengguna & Peran\\n...\\n\\n## 4. Fitur Utama per Modul\\n...\\n\\n## 5. Alur Bisnis Utama\\n...\\n\\n## 6. Integrasi AI (Jika Ada)\\n...\\n\\n## 7. Non-Functional Requirements\\n...\",
  \"first_deployment_cost\": 0,
  \"tech_stacks\": [
    {
      \"layer\": \"Frontend\",
      \"name\": \"React.js\",
      \"icon\": \"🌐\",
      \"description\": \"Penjelasan singkat (Bahasa Indonesia).\"
    }
  ],
  \"roadmaps\": [
    {
      \"title\": \"Judul langkah konkret\",
      \"time\": \"Hari 1-2\",
      \"icon\": \"⚙️\",
      \"description\": \"Instruksi spesifik dan teknis (Bahasa Indonesia).\"
    }
  ],
  \"db_schemas\": [
    {
      \"table_name\": \"users\",
      \"table_desc\": \"Penjelasan singkat (Bahasa Indonesia)\",
      \"columns\": [
        {
          \"name\": \"id\",
          \"type\": \"BigInt (PK, Auto Increment)\",
          \"nullable\": \"No\",
          \"desc\": \"Penjelasan singkat (Bahasa Indonesia)\"
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

        // 3. Make API Request
        try {
            $aiResult = AIService::call($prompt);
            if (isset($aiResult['error'])) {
                return ['error' => $aiResult['error']];
            }
            $data = $aiResult['data'];

            // Append change log to PRD
            if (!empty($data['prd_markdown'])) {
                $data['prd_markdown'] .= "\n\n## 📝 AI Change Request History\n- **Request**: \"" . e($requestText) . "\"\n- **Status**: Completed at " . now()->format('Y-m-d H:i:s');
            }

            // Deduct tokens and log transaction
            $workspace->decrement('tokens_balance', 10);
            
            \App\Models\WorkspaceTransaction::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'type' => 'modification',
                'amount' => -10,
                'description' => "Mengubah Proyek: {$data['title']} (-10 Token)",
            ]);

            // 4. Save updates via transaction
            DB::transaction(function () use ($data) {
                // Update main project row
                $this->project->update([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'prd_markdown' => $data['prd_markdown'],
                    'first_deployment_cost' => $data['first_deployment_cost'] ?? 0,
                ]);

                // Clear old relationships
                $this->project->techStacks()->delete();
                $this->project->roadmaps()->delete();
                $this->project->dbSchemas()->delete();
                $this->project->costs()->delete();

                // Save Tech Stacks
                if (!empty($data['tech_stacks'])) {
                    foreach ($data['tech_stacks'] as $tech) {
                        TechStack::create([
                            'project_id' => $this->project->id,
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
                            'project_id' => $this->project->id,
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
                            'project_id' => $this->project->id,
                            'table_name' => $table['table_name'] ?? '',
                            'table_desc' => $table['table_desc'] ?? '',
                        ]);

                        if (!empty($table['columns'])) {
                            foreach ($table['columns'] as $col) {
                                DbColumn::create([
                                    'db_schema_id' => $schema->id,
                                    'name' => $col['name'] ?? '',
                                    'type' => $col['type'] ?? '',
                                    'nullable' => $col['nullable'] ?? 'No',
                                    'desc' => $col['desc'] ?? '',
                                ]);
                            }
                        }
                    }
                }

                // Save Project Costs
                if (!empty($data['costs'])) {
                    foreach ($data['costs'] as $item) {
                        ProjectCost::create([
                            'project_id' => $this->project->id,
                            'type' => $item['type'] ?? 'one_time',
                            'name' => $item['name'] ?? '',
                            'price' => $item['price'] ?? 0,
                        ]);
                    }
                }
            });

            // Reload relationships to refresh UI
            $this->project->load(['techStacks', 'roadmaps', 'dbSchemas.columns', 'costs']);

            // Reset properties
            $this->editRequest = '';
            $this->editTitleInput = $this->project->title;
            $this->editDescInput = $this->project->description;

            // Dispatch dynamic sidebar refresh event
            $this->dispatch('refresh-sidebar');

            return [
                'success' => true,
                'title' => $this->project->title,
                'description' => $this->project->description
            ];

        } catch (\Exception $e) {
            Log::error('Error updating project via AI: ' . $e->getMessage());
            return ['error' => 'System exception: ' . $e->getMessage()];
        }
    }

    /**
     * Render the Livewire component.
     */
    public function render()
    {
        return view('livewire.project-detail');
    }
}
