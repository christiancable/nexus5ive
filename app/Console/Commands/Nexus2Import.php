<?php

namespace App\Console\Commands;

use App\Events\TreeCacheBecameDirty;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Section;
use App\Models\Topic;
use App\Models\User;
use App\Nexus2\Importer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Nexus2Import extends Command
{
    protected $signature = 'nexus2:import
                            {--dry-run : Show what would be imported without making changes}
                            {--path= : Base path to Nexus 2 BBS data (default: untracked/ucl_info/BBS)}
                            {--section= : Import legacy menus under this existing section ID}';

    protected $description = 'Import legacy Nexus 2 data into Nexus5ive';

    public function handle(): int
    {
        $bbsDir = $this->option('path') ?? base_path('untracked/ucl_info/BBS');
        $dryRun = (bool) $this->option('dry-run');
        $parentSectionId = $this->option('section') ? (int) $this->option('section') : null;

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

        $importer = new Importer($this, $bbsDir, $dryRun, $parentSectionId);

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
            $this->line($e->getTraceAsString());

            return self::FAILURE;
        } finally {
            // Re-enable model events
            foreach ($models as $model) {
                $model::setEventDispatcher(app('events'));
            }
        }

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
