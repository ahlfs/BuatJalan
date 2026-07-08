<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Workspace;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EnsureHasWorkspace
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user has any workspaces they belong to
            if ($user->workspaces()->count() === 0) {
                // Create a default workspace
                $workspace = Workspace::create([
                    'owner_id' => $user->id,
                    'name' => 'Personal Workspace',
                    'slug' => 'personal-workspace-' . strtolower(Str::random(4)),
                    'tokens_balance' => 20, // default starting tokens
                ]);

                // Attach as owner in pivot table
                $user->workspaces()->attach($workspace->id, ['role' => 'owner']);

                // Associate user's existing projects (with null workspace_id) to this default workspace
                Project::where('user_id', $user->id)
                       ->whereNull('workspace_id')
                       ->update(['workspace_id' => $workspace->id]);

                // Set current workspace ID for user
                $user->update([
                    'current_workspace_id' => $workspace->id
                ]);
            } elseif (!$user->current_workspace_id) {
                // Fallback: set active to first workspace they belong to
                $firstWorkspace = $user->workspaces()->first();
                if ($firstWorkspace) {
                    $user->update([
                        'current_workspace_id' => $firstWorkspace->id
                    ]);
                }
            }
        }

        return $next($request);
    }
}
