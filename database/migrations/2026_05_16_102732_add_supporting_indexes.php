<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Guards on each block make this safe to re-run if a prior deploy partially applied indexes.
        // MySQL DDL is not transactional, so a mid-migration failure leaves some indexes behind.

        // Missing FK index on posts.update_user_id (editor relation)
        if (! Schema::hasIndex('posts', 'posts_update_user_id_index')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->index('update_user_id', 'posts_update_user_id_index');
            });
        }

        // Covers User::views relation and leap sort: WHERE user_id = ? ORDER BY latest_view_date DESC
        if (! Schema::hasIndex('views', 'views_user_id_latest_view_date_index')) {
            Schema::table('views', function (Blueprint $table) {
                $table->index(['user_id', 'latest_view_date'], 'views_user_id_latest_view_date_index');
            });
        }

        // Covers topics ordered by weight within a section (moderator-controlled sections)
        // and sticky + created_at ordering for user-topic sections
        Schema::table('topics', function (Blueprint $table) {
            if (! Schema::hasIndex('topics', 'topics_section_id_weight_index')) {
                $table->index(['section_id', 'weight'], 'topics_section_id_weight_index');
            }
            if (! Schema::hasIndex('topics', 'topics_section_id_sticky_created_at_index')) {
                $table->index(['section_id', 'sticky', 'created_at'], 'topics_section_id_sticky_created_at_index');
            }
        });

        // Covers ReportController::index: status-filtered list ordered by created_at DESC
        // and missing FK indexes on reporter/moderator
        Schema::table('reports', function (Blueprint $table) {
            if (! Schema::hasIndex('reports', 'reports_status_created_at_index')) {
                $table->index(['status', 'created_at'], 'reports_status_created_at_index');
            }
            if (! Schema::hasIndex('reports', 'reports_reporter_id_index')) {
                $table->index('reporter_id', 'reports_reporter_id_index');
            }
            if (! Schema::hasIndex('reports', 'reports_moderator_id_index')) {
                $table->index('moderator_id', 'reports_moderator_id_index');
            }
        });

        // Covers User::chats (WHERE owner_id ORDER BY updated_at DESC)
        // and User::unreadChats (WHERE owner_id AND is_read = false ORDER BY updated_at DESC)
        Schema::table('chats', function (Blueprint $table) {
            if (! Schema::hasIndex('chats', 'chats_owner_id_updated_at_index')) {
                $table->index(['owner_id', 'updated_at'], 'chats_owner_id_updated_at_index');
            }
            if (! Schema::hasIndex('chats', 'chats_owner_id_is_read_updated_at_index')) {
                $table->index(['owner_id', 'is_read', 'updated_at'], 'chats_owner_id_is_read_updated_at_index');
            }
        });

        // Covers User::newCommentCount: WHERE user_id = ? AND read = false
        if (! Schema::hasIndex('comments', 'comments_user_id_read_index')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->index(['user_id', 'read'], 'comments_user_id_read_index');
            });
        }

        // Covers ActivityHelper::recentActivities: WHERE time >= ?
        // Also fixes zero-date defaults on all three timestamp columns in activities — MySQL validates
        // every column default on any ALTER TABLE, so all must be fixed in one statement.
        // (fix_timestamp_defaults missed this table; same pattern used there for all other tables.)
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE `activities`
                MODIFY `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
                MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');
        }

        if (! Schema::hasIndex('activities', 'activities_time_index')) {
            Schema::table('activities', function (Blueprint $table) {
                $table->index('time', 'activities_time_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('activities_time_index');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_user_id_read_index');
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->dropIndex('chats_owner_id_is_read_updated_at_index');
            $table->dropIndex('chats_owner_id_updated_at_index');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('reports_moderator_id_index');
            $table->dropIndex('reports_reporter_id_index');
            $table->dropIndex('reports_status_created_at_index');
        });

        Schema::table('topics', function (Blueprint $table) {
            $table->dropIndex('topics_section_id_sticky_created_at_index');
            $table->dropIndex('topics_section_id_weight_index');
        });

        Schema::table('views', function (Blueprint $table) {
            $table->dropIndex('views_user_id_latest_view_date_index');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_update_user_id_index');
        });
    }
};
