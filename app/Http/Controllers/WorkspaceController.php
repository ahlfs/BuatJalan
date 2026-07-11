<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\WorkspaceTransaction;
use App\Models\Inbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    /**
     * Switch the active workspace for the authenticated user.
     */
    public function switchWorkspace($id)
    {
        $user = Auth::user();
        
        // Find workspace and verify user belongs to it
        $workspace = $user->workspaces()->findOrFail($id);
        
        $user->update([
            'current_workspace_id' => $workspace->id
        ]);

        return redirect()->back()->with('success', "Berhasil beralih ke Workspace: {$workspace->name}");
    }

    /**
     * Create a new workspace.
     */
    public function createWorkspace(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        // Create new workspace with 0 default tokens
        $workspace = Workspace::create([
            'owner_id' => $user->id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . strtolower(Str::random(4)),
            'tokens_balance' => 0,
        ]);

        // Attach as owner
        $user->workspaces()->attach($workspace->id, ['role' => 'owner']);

        // Set as current active workspace
        $user->update([
            'current_workspace_id' => $workspace->id
        ]);

        return redirect()->back()->with('success', "Workspace '{$workspace->name}' berhasil dibuat!");
    }

    /**
     * Simulate buying tokens / credit packages.
     */
    public function buyTokens(Request $request)
    {
        $request->validate([
            'package' => 'required|string|in:starter,popular,developer',
        ]);

        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        if (!$workspace) {
            return redirect()->back()->with('error', 'Silakan buat atau pilih workspace terlebih dahulu.');
        }

        // Package specs
        $packages = [
            'starter' => ['credits' => 50, 'price' => 'Rp 19.000', 'name' => 'Starter Pack'],
            'popular' => ['credits' => 150, 'price' => 'Rp 49.000', 'name' => 'Popular Pack'],
            'developer' => ['credits' => 500, 'price' => 'Rp 99.000', 'name' => 'Developer Pack'],
        ];

        $selected = $packages[$request->package];

        // 1. Add tokens to workspace balance
        $workspace->increment('tokens_balance', $selected['credits']);

        // 2. Record transaction
        WorkspaceTransaction::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'type' => 'top_up',
            'amount' => $selected['credits'],
            'description' => "Top Up: {$selected['name']} (+{$selected['credits']} Token)",
        ]);

        // 3. Create inbox notification message
        Inbox::create([
            'user_id' => $user->id,
            'type' => 'billing',
            'sender' => 'Billing Platform',
            'avatar' => '💳',
            'avatar_bg' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
            'subject' => "Top Up Token Berhasil - {$selected['name']} Ditambahkan 🌟",
            'content' => "Terima kasih atas pembelian Anda!\n\nPembayaran Anda via Midtrans QRIS telah berhasil diverifikasi oleh sistem kami secara otomatis.\n\nDetail Pembelian:\n- Paket: {$selected['name']}\n- Jumlah Token: +{$selected['credits']} Token\n- Total Biaya: {$selected['price']} (Lunas)\n- Invoice ID: INV-" . now()->format('YmdHis') . "-A\n- Waktu Transaksi: " . now()->format('d M Y, H:i') . " WIB\n\nSaldo token workspace '{$workspace->name}' Anda telah berhasil diperbarui. Periksa halaman Pricing untuk melihat detail kuota Anda. Terima kasih telah mendukung keberlangsungan platform ini!",
            'unread' => true,
        ]);

        return redirect()->back()->with('success', "Pembelian {$selected['name']} berhasil! +{$selected['credits']} Token telah ditambahkan ke workspace {$workspace->name}.");
    }

    /**
     * Respond to workspace invitation.
     */
    public function respondInvitation($id, Request $request)
    {
        $request->validate([
            'response' => 'required|string|in:accepted,rejected',
        ]);

        $user = Auth::user();
        $inbox = $user->inboxes()->where('type', 'invitation')->findOrFail($id);
        
        $workspace = $inbox->workspace;

        if (!$workspace) {
            return redirect()->back()->with('error', 'Workspace tujuan undangan tidak ditemukan.');
        }

        $inbox->update([
            'invitation_status' => $request->response,
            'unread' => false,
        ]);

        if ($request->response === 'accepted') {
            // Join workspace as member if not already joined
            if (!$user->workspaces()->where('workspace_id', $workspace->id)->exists()) {
                $user->workspaces()->attach($workspace->id, ['role' => 'member']);
            }

            // Set as current workspace
            $user->update([
                'current_workspace_id' => $workspace->id
            ]);

            // Update inbox content to reflect acceptance
            $inbox->update([
                'subject' => "✓ Undangan Kolaborasi Diterima: Workspace '{$workspace->name}'",
                'content' => "Anda telah MENERIMA undangan kolaborasi ini.\n\nSelamat! Sekarang Anda resmi menjadi anggota dari Workspace '{$workspace->name}'. Anda dapat mengakses seluruh proyek yang terdaftar di bawah workspace ini melalui menu navigasi samping.",
            ]);

            return redirect()->back()->with('success', "Berhasil menerima undangan dan bergabung ke Workspace: {$workspace->name}!");
        } else {
            // Update inbox content to reflect rejection
            $inbox->update([
                'subject' => "✕ Undangan Kolaborasi Ditolak: Workspace '{$workspace->name}'",
                'content' => "Anda telah MENOLAK undangan kolaborasi ini.\n\nUndangan ini telah diarsipkan dan tidak lagi berlaku.",
            ]);

            return redirect()->back()->with('success', "Undangan kolaborasi ke Workspace: {$workspace->name} ditolak.");
        }
    }

    /**
     * Rename the current workspace.
     */
    public function renameWorkspace(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        if (!$workspace) {
            return redirect()->back()->with('error', 'Workspace aktif tidak ditemukan.');
        }

        // Verify user is owner
        if ($workspace->owner_id !== $user->id) {
            return redirect()->back()->with('error', 'Hanya pemilik (owner) workspace yang dapat mengubah namanya.');
        }

        $oldName = $workspace->name;
        $workspace->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . strtolower(Str::random(4)),
        ]);

        return redirect()->back()->with('success', "Workspace '{$oldName}' berhasil diubah namanya menjadi '{$workspace->name}'!");
    }

    /**
     * Delete the current workspace.
     */
    public function deleteWorkspace(Request $request)
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        if (!$workspace) {
            return redirect()->back()->with('error', 'Workspace aktif tidak ditemukan.');
        }

        // Verify user is owner
        if ($workspace->owner_id !== $user->id) {
            return redirect()->back()->with('error', 'Hanya pemilik (owner) workspace yang dapat menghapusnya.');
        }

        // Delete the workspace
        $workspace->delete();

        // Switch to remaining workspace or null
        $nextWorkspace = $user->workspaces()->first();
        if ($nextWorkspace) {
            $user->update([
                'current_workspace_id' => $nextWorkspace->id
            ]);
        } else {
            $user->update([
                'current_workspace_id' => null
            ]);
        }

        return redirect('/dashboard')->with('success', "Workspace '{$workspace->name}' berhasil dihapus.");
    }

    /**
     * Invite a new member to the current workspace via email.
     */
    public function inviteMember(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        if (!$workspace) {
            return redirect()->back()->with('error', 'Workspace aktif tidak ditemukan.');
        }

        // Verify user is owner
        if ($workspace->owner_id !== $user->id) {
            return redirect()->back()->with('error', 'Hanya pemilik (owner) workspace yang dapat mengundang anggota baru.');
        }

        $invitedUser = \App\Models\User::where('email', $request->email)->first();

        if (!$invitedUser) {
            return redirect()->back()->with('error', "Pengguna dengan email '{$request->email}' tidak ditemukan di platform kami.");
        }

        // Check if already a member
        if ($invitedUser->workspaces()->where('workspace_id', $workspace->id)->exists()) {
            return redirect()->back()->with('error', "Pengguna '{$invitedUser->name}' sudah bergabung di dalam workspace ini.");
        }

        // Check pending invitations
        $pendingInvitation = Inbox::where('user_id', $invitedUser->id)
                                  ->where('type', 'invitation')
                                  ->where('workspace_id', $workspace->id)
                                  ->where('invitation_status', 'pending')
                                  ->exists();
        
        if ($pendingInvitation) {
            return redirect()->back()->with('error', "Undangan kolaborasi untuk '{$request->email}' sudah dikirim dan sedang menunggu keputusan.");
        }

        // Create invitation inbox entry
        Inbox::create([
            'user_id' => $invitedUser->id,
            'type' => 'invitation',
            'sender' => $user->name,
            'avatar' => $user->avatar ?: strtoupper(substr($user->name, 0, 1)),
            'avatar_bg' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            'subject' => "{$user->name} mengundang Anda ke Workspace {$workspace->name}",
            'content' => "{$user->name} ({$user->email}) mengundang Anda untuk berkolaborasi di workspace miliknya: '{$workspace->name}'.\n\nSetelah bergabung, Anda akan dapat mengakses proyek-proyek bersama dan menggunakan token di workspace ini secara terpusat.",
            'unread' => true,
            'workspace_id' => $workspace->id,
            'invitation_status' => 'pending',
        ]);

        return redirect()->back()->with('success', "Undangan kolaborasi berhasil dikirimkan ke email '{$request->email}'!");
    }

    /**
     * Cancel/revoke a pending workspace invitation.
     */
    public function cancelInvitation($id)
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        if (!$workspace) {
            return redirect()->back()->with('error', 'Workspace aktif tidak ditemukan.');
        }

        // Verify user is owner
        if ($workspace->owner_id !== $user->id) {
            return redirect()->back()->with('error', 'Hanya pemilik (owner) workspace yang dapat membatalkan undangan.');
        }

        // Find the invitation in Inbox
        $invitation = Inbox::where('id', $id)
            ->where('workspace_id', $workspace->id)
            ->where('type', 'invitation')
            ->where('invitation_status', 'pending')
            ->first();

        if (!$invitation) {
            return redirect()->back()->with('error', 'Undangan tidak ditemukan atau sudah diproses.');
        }

        // Delete the invitation
        $invitation->delete();

        return redirect()->back()->with('success', 'Undangan kolaborasi berhasil dibatalkan.');
    }

    /**
     * Leave the current workspace (for members only).
     */
    public function leaveWorkspace(Request $request)
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        if (!$workspace) {
            return redirect()->back()->with('error', 'Workspace aktif tidak ditemukan.');
        }

        // Owners cannot leave
        if ($workspace->owner_id === $user->id) {
            return redirect()->back()->with('error', 'Pemilik workspace tidak dapat keluar dari workspace miliknya sendiri.');
        }

        // Detach member
        $user->workspaces()->detach($workspace->id);

        // Switch to remaining workspace or null
        $nextWorkspace = $user->workspaces()->first();
        if ($nextWorkspace) {
            $user->update([
                'current_workspace_id' => $nextWorkspace->id
            ]);
        } else {
            $user->update([
                'current_workspace_id' => null
            ]);
        }

        return redirect('/dashboard')->with('success', "Anda telah keluar dari Workspace '{$workspace->name}'.");
    }

    /**
     * Kick a member from the workspace (owner only).
     */
    public function kickMember(Request $request, $id)
    {
        $user = Auth::user();
        $workspace = $user->currentWorkspace;

        if (!$workspace) {
            return redirect()->back()->with('error', 'Workspace aktif tidak ditemukan.');
        }

        // Verify logged in user is owner
        if ($workspace->owner_id !== $user->id) {
            return redirect()->back()->with('error', 'Hanya pemilik (owner) workspace yang dapat mengeluarkan anggota.');
        }

        // Owner cannot kick themselves
        if ((int)$id === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengeluarkan diri sendiri.');
        }

        // Detach member
        $workspace->members()->detach($id);

        // Update current_workspace_id for kicked user if they were in this workspace
        $kickedUser = \App\Models\User::find($id);
        if ($kickedUser && $kickedUser->current_workspace_id === $workspace->id) {
            $nextWorkspace = $kickedUser->workspaces()->first();
            $kickedUser->update([
                'current_workspace_id' => $nextWorkspace ? $nextWorkspace->id : null
            ]);
        }

        return redirect()->back()->with('success', 'Anggota berhasil dikeluarkan dari workspace.');
    }
}
