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
        if (empty(trim($requestText))) {
            return ['error' => 'Permintaan perubahan tidak boleh kosong.'];
        }

        @set_time_limit(180);
        $provider = config('services.ai.provider', 'iyh');
        $providerConfig = config("services.ai.providers.{$provider}");
        
        if (empty($providerConfig) || empty($providerConfig['key'])) {
            Log::error("API key is not configured for provider: {$provider} in .env file.");
            return [
                'error' => "API Key untuk provider '" . strtoupper($provider) . "' belum diisi di berkas .env Anda."
            ];
        }

        $apiKey = $providerConfig['key'];
        $model = $providerConfig['model'];

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
        $rawJson = '';
        try {
            if ($provider === 'gemini') {
                $response = Http::timeout(120)->post(
                    "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey,
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'responseMimeType' => 'application/json'
                        ]
                    ]
                );

                if ($response->successful()) {
                    $body = $response->json();
                    $rawJson = $body['candidates'][0]['content']['parts'][0]['text'] ?? '';
                } else {
                    Log::error('Gemini API call failed: ' . $response->body());
                    return ['error' => 'API request failed: ' . $response->reason()];
                }
            } elseif (in_array($provider, ['openai', 'xai', 'deepseek', 'openrouter', 'iyh'])) {
                $endpoints = [
                    'openai' => 'https://api.openai.com/v1/chat/completions',
                    'xai' => 'https://api.x.ai/v1/chat/completions',
                    'deepseek' => 'https://api.deepseek.com/chat/completions',
                    'openrouter' => 'https://openrouter.ai/api/v1/chat/completions',
                    'iyh' => 'https://api.iyh.app/v1/chat/completions',
                ];

                $request = Http::timeout(120)->withToken($apiKey);
                
                if ($provider === 'openrouter') {
                    $request->withHeaders([
                        'HTTP-Referer' => config('app.url', 'http://localhost:8000'),
                        'X-Title' => config('app.name', 'BuatJalan'),
                    ]);
                }

                $response = $request->post($endpoints[$provider], [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'response_format' => ['type' => 'json_object']
                ]);

                if ($response->successful()) {
                    $body = $response->json();
                    $rawJson = $body['choices'][0]['message']['content'] ?? '';
                } else {
                    Log::error(strtoupper($provider) . ' API call failed: ' . $response->body());
                    return ['error' => 'API request failed: ' . $response->reason()];
                }
            } elseif ($provider === 'anthropic') {
                $response = Http::timeout(120)
                    ->withHeaders([
                        'x-api-key' => $apiKey,
                        'anthropic-version' => '2023-06-01',
                        'content-type' => 'application/json',
                    ])
                    ->post('https://api.anthropic.com/v1/messages', [
                        'model' => $model,
                        'max_tokens' => 4000,
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt]
                        ]
                    ]);

                if ($response->successful()) {
                    $body = $response->json();
                    $rawJson = $body['content'][0]['text'] ?? '';
                } else {
                    Log::error('Anthropic API call failed: ' . $response->body());
                    return ['error' => 'API request failed: ' . $response->reason()];
                }
            } else {
                return ['error' => "Provider AI '" . strtoupper($provider) . "' tidak didukung."];
            }

            // Clean up code block ticks
            $rawJson = trim($rawJson);
            if (Str::startsWith($rawJson, '```json')) {
                $rawJson = Str::after($rawJson, '```json');
            }
            if (Str::endsWith($rawJson, '```')) {
                $rawJson = Str::beforeLast($rawJson, '```');
            }
            $rawJson = trim($rawJson);

            $data = json_decode($rawJson, true);

            if (json_last_error() !== JSON_ERROR_NONE || empty($data['title'])) {
                Log::error("Failed to parse update JSON response from {$provider}. Raw response: " . $rawJson);
                return ['error' => 'Gagal melakukan parsing JSON dari model AI. Silakan coba lagi.'];
            }

            // Append change log to PRD
            if (!empty($data['prd_markdown'])) {
                $data['prd_markdown'] .= "\n\n## 📝 AI Change Request History\n- **Request**: \"" . e($requestText) . "\"\n- **Status**: Completed at " . now()->format('Y-m-d H:i:s');
            }

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
