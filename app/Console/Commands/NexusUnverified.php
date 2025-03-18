<?php

/**
 *  Command to remove unverified user accounts which have remained
 *  unverified for at least {--age} days
 *
 *  nexus:unverified {--confirm} {--age=30}
 */

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class NexusUnverified extends Command
{
    /**
     * Age an unverified account should reached to be
     * considered for removal.
     *
     * @var int
     */
    protected $age = 30;

    /**
     * List of unverified users to be
     * considered for removal.
     *
     * @var Collection
     */
    protected $unverifiedUsers = null;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus:unverified
                            {--confirm : do not prompt for confirmation}
                            {--age= : unverified users should be at least this many days old}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List and remove unverified users';

    /**
     * generate list of unverified users
     *
     * @return collection;
     */
    private function getUnverifiedUsers()
    {
        return User::unverified()
            ->whereDate('created_at', '<', Carbon::now()->subDays($this->age)->toDateTimeString())
            ->get();
    }

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('age') && is_numeric($this->option('age'))) {
            $this->age = (int) $this->option('age');
        }

        $this->unverifiedUsers = $this->getUnverifiedUsers();

        $this->line('Remove Unverified Users');
        $this->line('=======================');
        foreach ($this->unverifiedUsers as $user) {
            $this->info("* {$user->username}");
        }

        $unverifiedUsersCount = count($this->unverifiedUsers);
        if (! $this->option('confirm')) {
            $confirm = $this->ask("Remove these unverified $unverifiedUsersCount users? (yes/no)");

            if ($confirm != 'yes') {
                $this->info('Not removing any users');

                return;
            }
        }

        $deletedUserCount = 0;
        foreach ($this->unverifiedUsers as $user) {
            $this->comment("* removing {$user->username}");
            $deletedUserCount++;
            $user->forceDelete();
        }

        $this->info("Removed $deletedUserCount unverified users");
    }
}
