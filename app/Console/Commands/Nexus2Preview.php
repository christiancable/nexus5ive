<?php

namespace App\Console\Commands;

use App\Nexus2\UdbParser;
use Illuminate\Console\Command;
use RuntimeException;

class Nexus2Preview extends Command
{
    protected $signature = 'nexus2:preview
                            {type : The file type to preview (udb)}
                            {path : Path to the file}
                            {--info : Also display the user\'s INFO.TXT}
                            {--comments : Also display the user\'s COMMENTS.TXT}';

    protected $description = 'Preview parsed data from legacy Nexus 2 files';

    public function handle(): int
    {
        $type = $this->argument('type');
        $path = $this->argument('path');

        if ($type !== 'udb') {
            $this->error("Unsupported file type: {$type}. Supported types: udb");

            return self::FAILURE;
        }

        if (! file_exists($path) || ! is_readable($path)) {
            $this->error("File not found or not readable: {$path}");

            return self::FAILURE;
        }

        try {
            $parser = new UdbParser($path);
            $data = $parser->parse();
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Nexus 2 UDB: {$path}");
        $this->line('');

        $this->table(
            ['Field', 'Value'],
            [
                ['Nick', $data['Nick']],
                ['User ID', $data['UserID']],
                ['Real Name', $data['RealName']],
                ['Pop Name', $data['PopName']],
                ['Rights', "{$data['RightsLabel']} ({$data['Rights']})"],
                ['Total Edits', number_format($data['TotalEdits'])],
                ['Time On (mins)', number_format($data['TimeOn'])],
                ['Times On', number_format($data['TimesOn'])],
                ['Password', $data['Password']],
                ['Dept', $data['Dept']],
                ['Faculty', $data['Faculty']],
                ['Created', $data['Created']],
                ['Last On', $data['LastOn']],
                ['History File', $data['HistoryFile']],
                ['BBS No (User No)', $data['UserNo']],
                ['Max Logins', $data['MaxLogins']],
            ]
        );

        $this->line('');
        $this->info('Flags');

        $this->table(
            ['Flag', 'Value'],
            [
                ['Sex', $data['Sex']],
                ['Hide Level', $data['Hide']],
                ['Time Mode', $data['TimeMode']],
                ['Chat', $data['Chat']],
                ['Message', $data['Message']],
                ['Comment', $data['CommentFlag']],
                ['Mail', $data['Mail']],
                ['Validated', $data['Validated']],
                ['See All', $data['SeeAll']],
            ]
        );

        if (! empty($data['PrivLabels'])) {
            $this->line('');
            $this->info('Privileges: '.implode(', ', $data['PrivLabels']));
        }

        if (! empty($data['BanLabels'])) {
            $this->line('');
            $this->comment('Bans: '.implode(', ', $data['BanLabels']));
        }

        if (! empty($data['BannedLabels'])) {
            $this->comment('Banned From: '.implode(', ', $data['BannedLabels']));
        }

        $dir = dirname($path);

        if ($this->option('info')) {
            $this->displayTextFile($dir.'/INFO.TXT', 'Info');
        }

        if ($this->option('comments')) {
            $this->displayTextFile($dir.'/COMMENTS.TXT', 'Comments', reverse: true);
        }

        return self::SUCCESS;
    }

    private function displayTextFile(string $path, string $label, bool $reverse = false): void
    {
        $this->line('');

        if (! file_exists($path)) {
            $this->comment("{$label}: not found ({$path})");

            return;
        }

        $content = trim(file_get_contents($path));

        if ($content === '') {
            $this->comment("{$label}: empty");

            return;
        }

        if ($reverse) {
            $lines = array_reverse(explode("\n", $content));
            $content = implode("\n", $lines);
        }

        $this->info($label);
        $this->line($content);
    }
}
