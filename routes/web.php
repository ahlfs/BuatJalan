<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Middleware\EnsureHasWorkspace;

Route::get('/', function () {
    return view('landing-page');
});

Route::get('/login', function () {
    return view('login');
})->middleware('guest')->name('login');

// Socialite OAuth Routes
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'handleProviderCallback']);
Route::post('/logout', [SocialiteController::class, 'logout'])->name('logout');

// Public Project Share Routes
Route::get('/shared/project/{slug}', function ($slug) {
    $project = \App\Models\Project::with([
        'techStacks',
        'roadmaps',
        'dbSchemas.columns',
        'costs'
    ])
    ->where('slug', $slug)
    ->firstOrFail();

    // Construct the raw Markdown text
    $markdown = "# SPESIFIKASI PROYEK: " . $project->title . "\n\n";
    $markdown .= "## DESKRIPSI\n" . ($project->description ?? 'Tidak ada deskripsi.') . "\n\n";

    if ($project->prd_markdown) {
        $markdown .= "## PRODUCT REQUIREMENT DOCUMENT (PRD)\n" . $project->prd_markdown . "\n\n";
    }

    if ($project->techStacks->isNotEmpty()) {
        $markdown .= "## TECH STACK\n";
        foreach ($project->techStacks as $tech) {
            $markdown .= "* **" . $tech->layer . "**: " . $tech->name . " - " . $tech->description . "\n";
        }
        $markdown .= "\n";
    }

    if ($project->dbSchemas->isNotEmpty()) {
        $markdown .= "## SKEMA DATABASE (DATABASE SCHEMA)\n";
        foreach ($project->dbSchemas as $schema) {
            $markdown .= "### Tabel: " . $schema->table_name . " (" . ($schema->table_desc ?? 'Tidak ada deskripsi.') . ")\n";
            $markdown .= "| Kolom | Tipe Data | Nullable | Keterangan |\n";
            $markdown .= "| :--- | :--- | :--- | :--- |\n";
            foreach ($schema->columns as $col) {
                $markdown .= "| " . $col->name . " | " . $col->type . " | " . ($col->nullable ? 'Ya' : 'Tidak') . " | " . ($col->desc ?? '-') . " |\n";
            }
            $markdown .= "\n";
        }
    }

    if ($project->roadmaps->isNotEmpty()) {
        $markdown .= "## ROADMAP PENGEMBANGAN (DEVELOPMENT ROADMAP)\n";
        foreach ($project->roadmaps as $index => $step) {
            $markdown .= "### Langkah " . ($index + 1) . ": " . $step->title . " (" . $step->time . ")\n";
            $markdown .= $step->description . "\n\n";
        }
    }

    return response($markdown)
        ->header('Content-Type', 'text/plain; charset=UTF-8')
        ->header('Cache-Control', 'no-cache, must-revalidate');
})->name('projects.share');

Route::prefix('dashboard')->middleware(['auth', EnsureHasWorkspace::class])->group(function () {
    Route::put('/profile/update', function (Illuminate\Http\Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        auth()->user()->update($data);

        return redirect()->back()->with('success', 'Nama profil Anda berhasil diperbarui!');
    })->name('profile.update');

    Route::get('/new', function () {
        return view('dashboard.new');
    })->name('dashboard.new');

    Route::get('/', function () {
        $user = auth()->user();
        $workspaces = $user->workspaces;
        $workspaceIds = $workspaces->pluck('id');

        $projects = \App\Models\Project::whereIn('workspace_id', $workspaceIds)
            ->with(['techStacks', 'workspace'])
            ->latest()
            ->get();

        return view('dashboard.home', compact('projects', 'workspaces'));
    })->name('dashboard.home');

    // Dedicated Inbox route
    Route::get('inbox', function () {
        return view('dashboard.inbox', [
            'activeId' => 'inbox',
            'title' => 'Inbox'
        ]);
    });

    // Dedicated Pricing route
    Route::get('pricing', function () {
        return view('dashboard.pricing', [
            'activeId' => 'pricing',
            'title' => 'Pricing'
        ]);
    });

    // Dedicated Analytics route
    Route::get('analytics', function () {
        $projects = auth()->user()->currentWorkspace
            ? auth()->user()->currentWorkspace->projects()->with(['techStacks', 'roadmaps', 'dbSchemas'])->latest()->get()
            : collect();
        return view('dashboard.analytics', [
            'activeId' => 'analytics',
            'title' => 'Analytics',
            'projects' => $projects
        ]);
    });

    // Generic dashboard pages mapping
    $pages = [
        'settings' => ['settings', 'Settings'],
    ];

    foreach ($pages as $uri => $data) {
        Route::get($uri, function () use ($data) {
            return view('dashboard.generic', [
                'activeId' => $data[0],
                'title' => $data[1]
            ]);
        });
    }

    // Dedicated project detail route
    Route::get('projects/{slug}', function ($slug) {
        $project = \App\Models\Project::with([
            'techStacks',
            'roadmaps',
            'dbSchemas.columns',
            'costs'
        ])
        ->where('slug', $slug)
        ->whereIn('workspace_id', auth()->user()->workspaces->pluck('id'))
        ->firstOrFail();

        return view('dashboard.project-show-wrapper', [
            'project' => $project,
        ]);
    });

    Route::post('inbox/read/{id}', function ($id) {
        auth()->user()->inboxes()->findOrFail($id)->update(['unread' => false]);
        return response()->json(['success' => true]);
    });

    // Workspace Action Routes
    Route::post('workspaces/switch/{id}', [WorkspaceController::class, 'switchWorkspace'])->name('workspaces.switch');
    Route::post('workspaces/create', [WorkspaceController::class, 'createWorkspace'])->name('workspaces.create');
    Route::post('workspaces/rename', [WorkspaceController::class, 'renameWorkspace'])->name('workspaces.rename');
    Route::post('workspaces/delete', [WorkspaceController::class, 'deleteWorkspace'])->name('workspaces.delete');
    Route::post('workspaces/invite', [WorkspaceController::class, 'inviteMember'])->name('workspaces.invite');
    Route::post('workspaces/invite/{id}/cancel', [WorkspaceController::class, 'cancelInvitation'])->name('workspaces.invite.cancel');
    Route::post('workspaces/leave', [WorkspaceController::class, 'leaveWorkspace'])->name('workspaces.leave');
    Route::post('workspaces/kick/{id}', [WorkspaceController::class, 'kickMember'])->name('workspaces.kick');
    Route::post('pricing/buy', [WorkspaceController::class, 'buyTokens'])->name('pricing.buy');
    Route::post('inbox/invitation/{id}/respond', [WorkspaceController::class, 'respondInvitation'])->name('inbox.invitation.respond');
});
