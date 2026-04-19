<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nexus2_imports', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('legacy_key');
            $table->unsignedBigInteger('model_id');
            $table->timestamps();
            $table->unique(['type', 'legacy_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nexus2_imports');
    }
};
