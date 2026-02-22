<?php

namespace App\Console\Commands;

use App\Events\TreeCacheBecameDirty;
use App\Models\Comment;
use App\Models\Mention;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use App\Nexus2\Importer;
use App\Nexus2\Nexus2Import as Nexus2ImportModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Nexus2Import extends Command
{
    protected $signature = 'nexus2:import
                            {--dry-run : Show what would be imported without making changes}
                            {--path= : Base path to Nexus 2 BBS data (default: untracked/ucl_info/BBS)}
                            {--section= : Import legacy menus under this existing section ID}
                            {--merge-existing-users : Map legacy nicks to existing accounts instead of creating _legacy duplicates}
                            {--priv=100 : Only import menu items with a read privilege level at or below this value}';

    protected $description = 'Import legacy Nexus 2 data into Nexus5ive';

    public function handle(): int
    {
        $bbsDir = $this->option('path') ?? base_path('untracked/ucl_info/BBS');
        $dryRun = (bool) $this->option('dry-run');
        $parentSectionId = $this->option('section') ? (int) $this->option('section') : null;
        $mergeExistingUsers = (bool) $this->option('merge-existing-users');
        $privLevel = (int) $this->option('priv');

        if (! $this->validateDataPath($bbsDir)) {
            return self::FAILURE;
        }

        if ($parentSectionId && ! Section::find($parentSectionId)) {
            $this->error("Section {$parentSectionId} not found");

            return self::FAILURE;
        }

        if ($dryRun) {
            $this->info('DRY RUN — no changes will be made');
            $this->line('');
        }

        if ($parentSectionId) {
            $section = Section::find($parentSectionId);
            $this->info("Importing under section: {$section->title} (ID {$parentSectionId})");
        }

        $importer = new Importer($this, $bbsDir, $dryRun, $parentSectionId, $mergeExistingUsers, $privLevel);

        if ($dryRun) {
            $importer->importAll();
            $this->line('');
            $this->info('Dry run complete.');

            return self::SUCCESS;
        }

        // Disable model events during import to prevent cache invalidation spam
        $models = [User::class, Section::class, Topic::class, Post::class, Comment::class];
        foreach ($models as $model) {
            $model::unsetEventDispatcher();
        }

        try {
            DB::transaction(function () use ($importer) {
                $importer->importAll();
            });
        } catch (\Exception $e) {
            $this->error("Import failed: {$e->getMessage()}");
            if ($importer->getCurrentFile()) {
                $this->error("While processing: {$importer->getCurrentFile()}");
            }
            $this->line($e->getTraceAsString());

            return self::FAILURE;
        } finally {
            // Re-enable model events
            foreach ($models as $model) {
                $model::setEventDispatcher(app('events'));
            }
        }

        // Clear notifications for imported users
        $this->info('Clearing notifications for imported users...');
        $importedUserIds = Nexus2ImportModel::where('type', 'user')->pluck('model_id');
        Comment::whereIn('user_id', $importedUserIds)->update(['read' => true]);
        Mention::whereIn('user_id', $importedUserIds)->delete();

        // Fix post counts: if a user was imported with totalPosts=0 but actually
        // authored posts during the import, set their count from the posts table.
        $this->info('Fixing post counts for imported users...');
        DB::statement('
            UPDATE users
            SET totalPosts = (
                SELECT COUNT(*) FROM posts WHERE posts.user_id = users.id AND posts.deleted_at IS NULL
            )
            WHERE users.id IN (
                SELECT model_id FROM nexus2_imports WHERE type = \'user\'
            )
            AND users.totalPosts = 0
        ');

        // Fire cache rebuild events once
        $this->info('Rebuilding caches...');
        event(new TreeCacheBecameDirty);

        $sectionIds = Section::pluck('id');
        foreach ($sectionIds as $sectionId) {
            Section::forgetMostRecentPostAttribute($sectionId);
        }

        $this->line('');
        $this->info('Import complete.');

        return self::SUCCESS;
    }

    private function validateDataPath(string $bbsDir): bool
    {
        $required = [
            'USR' => $bbsDir.'/USR',
            'SECTIONS' => $bbsDir.'/SECTIONS',
            'ONSTUFF/NEXUS.INI' => $bbsDir.'/ONSTUFF/NEXUS.INI',
        ];

        $valid = true;
        foreach ($required as $label => $path) {
            if (! file_exists($path)) {
                $this->error("Required path not found: {$label} ({$path})");
                $valid = false;
            }
        }

        return $valid;
    }
}
