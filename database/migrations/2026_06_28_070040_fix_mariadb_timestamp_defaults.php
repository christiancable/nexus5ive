<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Apply timestamp default fixes for MariaDB.
     *
     * The two earlier migrations (fix_timestamp_defaults and add_supporting_indexes)
     * guarded their ALTER TABLE statements with `DB::getDriverName() === 'mysql'`, which
     * returns false when Laravel is configured with DB_CONNECTION=mariadb. Those
     * statements were therefore skipped on MariaDB. This migration runs them for any
     * database whose driver is 'mariadb'.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mariadb') {
            return;
        }

        // From fix_timestamp_defaults — sections, topics, posts, users, comments, views
        DB::statement('ALTER TABLE `sections`
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `topics`
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `posts`
            MODIFY `time` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `users`
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `comments`
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');

        DB::statement('ALTER TABLE `views`
            MODIFY `latest_view_date` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');

        // From add_supporting_indexes — activities
        DB::statement('ALTER TABLE `activities`
            MODIFY `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL,
            MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');
    }

    public function down(): void
    {
        // No need to reverse — the old defaults were invalid
    }
};
