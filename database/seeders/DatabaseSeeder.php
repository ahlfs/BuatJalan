<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create/get test user
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // 2. Create another user to own the external workspace
        $admin = User::updateOrCreate(
            ['email' => 'admin@airlangga.dev'],
            [
                'name' => 'Airlangga Dev Admin',
                'password' => bcrypt('password'),
            ]
        );

        // 3. Create active workspace for test user
        $workspace = \App\Models\Workspace::updateOrCreate(
            ['slug' => 'personal-workspace'],
            [
                'owner_id' => $user->id,
                'name' => 'Personal Workspace',
                'tokens_balance' => 20,
            ]
        );

        // Attach owner pivot
        if (!$user->workspaces()->where('workspace_id', $workspace->id)->exists()) {
            $user->workspaces()->attach($workspace->id, ['role' => 'owner']);
        }

        // Set active workspace
        $user->update([
            'current_workspace_id' => $workspace->id
        ]);

        // 4. Create external workspace for invitation testing
        $externalWs = \App\Models\Workspace::updateOrCreate(
            ['slug' => 'airlangga-dev'],
            [
                'owner_id' => $admin->id,
                'name' => 'Airlangga Dev',
                'tokens_balance' => 150,
            ]
        );
        
        if (!$admin->workspaces()->where('workspace_id', $externalWs->id)->exists()) {
            $admin->workspaces()->attach($externalWs->id, ['role' => 'owner']);
        }

        // Seed Projects
        $this->call(ProjectSeeder::class);

        // Associate all projects to the personal workspace and user
        \App\Models\Project::whereNull('workspace_id')->update([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id
        ]);

        // 5. Seed Inbox Messages
        // Truncate inboxes to avoid duplicates
        \App\Models\Inbox::truncate();

        // System Inbox
        \App\Models\Inbox::create([
            'user_id' => $user->id,
            'type' => 'system',
            'sender' => 'System Administrator',
            'avatar' => '🤖',
            'avatar_bg' => 'bg-zinc-800 text-zinc-400 border border-white/10',
            'subject' => 'Selamat Datang di BuatJalan! 🚀',
            'content' => "Halo!\n\nSelamat datang di BuatJalan, platform AI pembuat arsitektur web tercepat.\n\nKami telah menambahkan saldo awal gratis sebesar 20 Emerald Token ke akun Anda. Anda dapat menggunakannya untuk membuat 2 spesifikasi kebutuhan produk (PRD) baru secara gratis.\n\nJika Anda membutuhkan bantuan, buka menu Help di kanan atas dasbor Anda. Selamat berkarya!",
            'unread' => true,
        ]);

        // Invitation Inbox
        \App\Models\Inbox::create([
            'user_id' => $user->id,
            'type' => 'invitation',
            'sender' => 'Airlangga Dev',
            'avatar' => '🤝',
            'avatar_bg' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
            'subject' => 'Undangan Kolaborasi: Bergabung ke Workspace Airlangga Dev',
            'content' => "Airlangga Dev mengundang Anda untuk berkolaborasi dalam workspace proyek mereka.\n\nDengan bergabung, Anda dapat melihat, membuat, dan memodifikasi proyek bersama anggota tim lainnya secara real-time.\n\nApakah Anda bersedia bergabung dengan Workspace ini?",
            'unread' => true,
            'workspace_id' => $externalWs->id,
            'invitation_status' => 'pending',
        ]);

        // Billing Top-Up Inbox
        \App\Models\Inbox::create([
            'user_id' => $user->id,
            'type' => 'billing',
            'sender' => 'Payment Gateway',
            'avatar' => '💳',
            'avatar_bg' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
            'subject' => 'Top Up Berhasil - Starter Pack +50 Kredit',
            'content' => "Pembayaran Anda via Midtrans QRIS telah berhasil diverifikasi oleh sistem kami secara otomatis.\n\nDetail Pembelian:\n- Paket: Starter Pack\n- Jumlah Kredit: +50 Kredit\n- Total Biaya: Rp 19.000 (Lunas)\n- Invoice ID: INV-20260705001-A\n- Waktu Transaksi: 05 Jul 2026, 14:30 WIB\n\nSaldo kredit workspace Anda telah berhasil diperbarui.",
            'unread' => false,
        ]);
    }
}
