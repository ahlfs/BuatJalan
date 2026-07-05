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

        // 3. Craft prompt
        $prompt = "You are a friendly Senior Product Manager and Developer Coach AI.
Your target users are absolute coding beginners and 'vibecoders' (people who use AI code assistants, copy-paste snippets, and need simple step-by-step guidance).

TASK:
Generate a complete software project spec, tech stack, roadmap, database schema, and cost estimate.

TONE & LANG:
- Language MUST be Bahasa Indonesia.
- Use friendly, easy-to-understand, and encouraging language.
- Avoid academic, overly complex database or infrastructure jargon. Explain terms clearly (e.g., explain what PK/FK means in simple words if showing columns).
- Provide a clear, chronological, step-by-step roadmap.
- Keep the output concise, practical, and highly focused. Avoid extremely wordy or repetitive explanations. The entire response must be under 2,500 tokens so it generates quickly without timing out.

CRITICAL ALIGNMENT RULES:
1. The generated database schemas (`db_schemas`) and columns (`columns`) must match the tables, roles, and business logic described in the generated PRD 100%. (e.g. if the PRD has point rewards, verifications, or specific status flags, ensure tables like `points`, `verifications`, or fields like `status` are created).
2. The generated step-by-step roadmap (`roadmaps`) must follow the exact features, role requirements, and development phases listed in the generated PRD, starting from database design, building core backend features, writing frontend UI, testing, and deployment.
3. The generated roadmap (`roadmaps`) MUST NOT contain general high-level phases (like 'Desain', 'Pengembangan', 'Testing'). It must be a concrete, step-by-step programming checklist (e.g., Step 1: Setup Project & DB Config, Step 2: Create DB Migrations & Eloquent Models, Step 3: Implement Authentication, Step 4: Develop Core CRUD Controllers, Step 5: Integrate Gemini API, Step 6: Build UI & Charts, Step 7: Write Tests & Deploy). Every step description must specify exactly what database fields to setup, what files/controllers to create, or what commands to run (in friendly Bahasa Indonesia).

PROJECT IDEA:
\"{$this->description}\"

{$techPreference}

{$refinementInfo}

You MUST return a JSON object with the following exact structure. Do not wrap the JSON inside markdown code blocks like ```json or anything else. Just return raw JSON.

JSON Structure:
{
  \"title\": \"Name of the project (e.g. TalentHub — University Talent Hub)\",
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

        // 4. API Request
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

            // Clean up code block ticks if AI accidentally wraps it
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
                Log::error("Failed to parse JSON response from {$provider}. Raw response: " . $rawJson);
                return ['error' => 'Gagal melakukan parsing JSON dari model AI. Silakan coba lagi.'];
            }

            // 5. Save to database under current user
            $slug = Str::slug($data['title']) . '-' . Str::random(4);
            
            $project = Project::create([
                'user_id' => auth()->id(),
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
