<?php

namespace App\Console\Commands;

use App\Nexus2\ArticleParser;
use App\Nexus2\MnuParser;
use App\Nexus2\NxText;
use App\Nexus2\UdbParser;
use Illuminate\Console\Command;
use RuntimeException;

class Nexus2Preview extends Command
{
    protected $signature = 'nexus2:preview
                            {type : The file type to preview (udb, mnu, article)}
                            {path : Path to the file}
                            {--info : Also display the user\'s INFO.TXT (udb only)}
                            {--comments : Also display the user\'s COMMENTS.TXT (udb only)}
                            {--plain : Strip highlight markup instead of colouring (useful for piping to files)}
                            {--priv=100 : Privilege level for menu visibility filtering (0-255, default 100)}';

    protected $description = 'Preview parsed data from legacy Nexus 2 files';

    public function handle(): int
    {
        $type = $this->argument('type');
        $path = $this->argument('path');

        if (! in_array($type, ['udb', 'mnu', 'article'])) {
            $this->error("Unsupported file type: {$type}. Supported types: udb, mnu, article");

            return self::FAILURE;
        }

        if (! file_exists($path) || ! is_readable($path)) {
            $this->error("File not found or not readable: {$path}");

            return self::FAILURE;
        }

        try {
            return match ($type) {
                'udb' => $this->previewUdb($path),
                'mnu' => $this->previewMnu($path),
                'article' => $this->previewArticle($path),
            };
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    private function previewUdb(string $path): int
    {
        $parser = new UdbParser($path);
        $data = $parser->parse();

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

    private function previewMnu(string $path): int
    {
        $privLevel = (int) $this->option('priv');
        $parser = new MnuParser($path);
        $data = $parser->parse($privLevel);

        $this->info("Nexus 2 Menu: {$path} (priv level: {$privLevel})");

        if ($data['header']) {
            $this->line('Header: '.$this->highlights($data['header']));
        }

        if (! empty($data['owners'])) {
            $this->line('Owners: '.implode(', ', $data['owners']));
        }

        if (! empty($data['directives'])) {
            $this->line('');
            $this->info('Directives');
            $this->table(
                ['Command', 'Args'],
                array_map(fn ($d) => [$d['command'], $d['args']], $data['directives'])
            );
        }

        $this->line('');
        $this->info('Items');

        $rows = [];
        foreach ($data['items'] as $item) {
            $row = [
                $item['type'],
                $item['read'],
            ];

            if (isset($item['write'])) {
                $row[] = $item['write'];
            } else {
                $row[] = '';
            }

            $row[] = $item['key'] ?? '';
            $row[] = $item['file'] ?? '';
            $row[] = $item['flags'] ?? '';
            $row[] = $this->highlights($item['info']);

            $rows[] = $row;
        }

        $this->table(
            ['Type', 'Read', 'Write', 'Key', 'File', 'Flags', 'Info'],
            $rows
        );

        return self::SUCCESS;
    }

    private function previewArticle(string $path): int
    {
        $parser = new ArticleParser($path);
        $data = $parser->parse();

        $this->info("Nexus 2 Article: {$path}");
        $this->line(count($data['posts']).' post(s)');

        if ($data['preamble'] !== '') {
            $this->line('');
            $this->info('Preamble');
            $this->line($this->highlights($data['preamble']));
        }

        foreach ($data['posts'] as $i => $post) {
            $this->line('');
            $this->info(str_repeat('=', 70));

            $num = $i + 1;
            $this->line("Post #{$num}  {$post['timestamp']}");

            $from = $post['nick'] ?? 'Unknown';
            if ($post['popname']) {
                $from .= '  ('.$this->highlights($post['popname']).')';
            }
            $this->line("From: {$from}");

            if ($post['subject']) {
                $this->line('Subject: '.$this->highlights($post['subject']));
            }

            if ($post['body'] !== '') {
                $this->line('');
                $this->line($this->highlights($post['body']));
            }
        }

        return self::SUCCESS;
    }

    private function highlights(string $text): string
    {
        return $this->option('plain')
            ? NxText::stripHighlights($text)
            : NxText::toConsole($text);
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
        $this->line($this->highlights($content));
    }
}
