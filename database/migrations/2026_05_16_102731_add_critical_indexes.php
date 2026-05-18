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
        // Covers latestPostByTime (ofMany max time) and getMostRecentPostTimeAttribute:
        // WHERE topic_id = ? ORDER BY time DESC
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['topic_id', 'time'], 'posts_topic_id_time_index');
        });

        // Covers recentTopics raw SQL (GROUP BY topic_id / MAX(id) DESC)
        // and routeToPost pagination count (WHERE topic_id = ? AND id >= ?)
        Schema::table('posts', function (Blueprint $table) {
            $table->index(['topic_id', 'id'], 'posts_topic_id_id_index');
        });

        // Covers the most frequent ViewHelper query pattern appearing 4+ times:
        // WHERE topic_id = ? AND user_id = ?
        // The unique constraint also enforces one view record per user-topic pair.
        Schema::table('views', function (Blueprint $table) {
            $table->unique(['user_id', 'topic_id'], 'views_user_id_topic_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('views', function (Blueprint $table) {
            $table->dropUnique('views_user_id_topic_id_unique');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_topic_id_id_index');
            $table->dropIndex('posts_topic_id_time_index');
        });
    }
};
