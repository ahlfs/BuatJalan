<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inboxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type');
            $table->string('sender');
            $table->string('avatar');
            $table->string('avatar_bg');
            $table->string('subject');
            $table->text('content');
            $table->boolean('unread')->default(true);
            $table->foreignId('workspace_id')->nullable()->constrained('workspaces')->onDelete('set null');
            $table->string('invitation_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inboxes');
    }
};
