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
        Schema::create('db_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('db_schema_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->string('nullable');
            $table->text('desc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_columns');
    }
};
