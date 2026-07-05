<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialiteController;

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

// Dashboard Routes
Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/new', function () {
        return view('dashboard.new');
    })->name('dashboard.new');

    Route::get('/', function () {
        $projects = \App\Models\Project::with('techStacks')->latest()->get();
        return view('dashboard.home', compact('projects'));
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
        $projects = \App\Models\Project::with(['techStacks', 'roadmaps', 'dbSchemas'])->latest()->get();
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
        ])->where('slug', $slug)->firstOrFail();

        return view('dashboard.project-show-wrapper', [
            'project' => $project,
        ]);
    });
});
