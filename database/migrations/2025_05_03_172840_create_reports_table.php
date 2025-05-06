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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation to Post or Chat - which will be 'reportable'
            $table->string('reportable_type');
            $table->unsignedBigInteger('reportable_id');

            // Reporter details
            $table->unsignedBigInteger('reporter_id')->nullable(); // NULL if anonymous

            // Report reason & additional details
            $table->string('reason');
            $table->text('details')->nullable();

            // json of the content being reported at the time of the report
            $table->json('reported_content_snapshot')->nullable();

            // Status and moderation review
            $table->string('status')->default('new');

            $table->unsignedBigInteger('moderator_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index(['reportable_id', 'reportable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
