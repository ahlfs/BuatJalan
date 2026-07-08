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
            return ['error' => 'Saldo koin Anda tidak mencukupi untuk mengubah proyek (Dibutuhkan 10 Kredit).'];
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

        // 2. Build prompt
        $prompt = "You are a friendly Senior Product Manager and Developer Coach AI.
Your target users are absolute coding beginners and 'vibecoders' (people who use AI code assistants, copy-paste snippets, and need simple step-by-step guidance).

TASK:
Update the existing software project based on the user's change request.

USER'S CHANGE REQUEST:
\"{$requestText}\"

{$currentState}

TONE & LANG:
- Language MUST be Bahasa Indonesia.
- Use friendly, easy-to-understand, and encouraging language.
- Avoid academic, overly complex database or infrastructure jargon. Explain terms clearly (e.g., explain what PK/FK means in simple words if showing columns).
- Provide a clear, chronological, step-by-step roadmap.
- Keep the output concise, practical, and highly focused. Avoid extremely wordy or repetitive explanations. The entire response must be under 2,500 tokens so it generates quickly without timing out.

CRITICAL ALIGNMENT & UPDATING RULES:
1. Generate the updated version of the title, description, and PRD matching the requested changes.
2. The updated database schemas (`db_schemas`) and columns (`columns`) must match the tables, roles, and business logic described in the updated PRD 100%. Ensure database columns are adapted if the tech stack/database type changes.
3. The updated step-by-step roadmap (`roadmaps`) must follow the exact features, role requirements, and development phases listed in the updated PRD, starting from database design, building core backend features, writing frontend UI, testing, and deployment.
4. The updated roadmap (`roadmaps`) MUST NOT contain general high-level phases (like 'Desain', 'Pengembangan', 'Testing'). It must be a concrete, step-by-step programming checklist. Every step description must specify exactly what database fields to setup, what files/controllers to create, or what commands to run (in friendly Bahasa Indonesia).

You MUST return a JSON object with the following exact structure. Do not wrap the JSON inside markdown code blocks like ```json or anything else. Just return raw JSON.

JSON Structure:
{
  \"title\": \"Updated name of the project\",
  \"description\": \"A simple, friendly description in Bahasa Indonesia explaining what this app is and what it does for beginners.\",
  \"prd_markdown\": \"# Product Requirements Document (PRD)\\n# [Project Title]\\n\\n## 1. Ringkasan Produk\\n[Brief product summary]\\n\\n## 2. Masalah yang Diselesaikan\\n[Table containing: # | Masalah | Solusi]\\n\\n## 3. Target Pengguna\\n- [Describe roles like Admin, Mahasiswa, etc.]\\n\\n## 4. Fitur Utama & Requirements\\n- [List features by user role/module, including points/ratings if applicable]\\n\\n## 5. Alur Bisnis Utama\\n- [Step-by-step description of the main user journey]\\n\\n## 6. Fitur AI (Jika Ada)\\n- [Details of Vertex AI / Gemini integration if relevant]\\n\\n## 7. Non-Functional Requirements\\n- Bahasa UI: Bahasa Indonesia\\n- Responsive: Yes\\n- Security & Performance targets\",
  \"first_deployment_cost\": 500000,
  \"tech_stacks\": [
    {
      \"layer\": \"Frontend / Backend / Database / Deployment / etc.\",
      \"name\": \"React.js / Laravel / Postgres / etc.\",
      \"icon\": \"Single emoji representing this layer (e.g. 🌐, 📁, ☁, 🚀)\",
      \"description\": \"Simple explanation of what this tech does in this project (Bahasa Indonesia).\"
    }
  ],
  \"roadmaps\": [
    {
      \"title\": \"Step/Phase Title (e.g. Setup Database)\",
      \"time\": \"Estimated timeframe (e.g. Hari 1-2)\",
      \"icon\": \"Single emoji (e.g. ⚙️, 💻, 💳, 🚀)\",
      \"description\": \"Friendly, clear step-by-step instructions on what to do (Bahasa Indonesia).\"
    }
  ],
  \"db_schemas\": [
    {
      \"table_name\": \"Name of SQL table (e.g. users, profiles, user_skills)\",
      \"table_desc\": \"Simple explanation of what this table stores (Bahasa Indonesia)\",
      \"columns\": [
        {
          \"name\": \"column_name (e.g. id, user_id, status)\",
          \"type\": \"Data type (e.g. BigInt (PK), VarChar(255), Timestamp)\",
          \"nullable\": \"Yes / No\",
          \"desc\": \"Simple explanation of this column (Bahasa Indonesia)\"
        }
      ]
    }
  ],
  \"costs\": [
    {
      \"type\": \"one_time / recurring\",
      \"name\": \"Name of the item (e.g. Domain .com)\",
      \"price\": 150000
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
                'description' => "Mengubah Proyek: {$data['title']} (-10 Kredit)",
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
