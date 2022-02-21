<?php

namespace App\Console\Commands;

use App\Nexus2\Models\User;
use Illuminate\Console\Command;

class ViewUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nexus2:viewUser {userdir}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View a Nexus2 User';

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
        $userdir = $this->argument('userdir');

        try {
            $user = new User($userdir);
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
            die();
        }

        $this->comment("User");
        $this->info("Username:\t" . $user->username());
        $this->info("UserID:\t\t" . $user->userId());
        $this->info("realName:\t" . $user->realName());
        $this->info("popName:\t" . $user->popName());
        $this->info("rights:\t\t" . $user->rights());
        $this->info("noOfEdits:\t" . $user->noOfEdits());
        $this->info("totalTimeOn:\t" . $user->totalTimeOn());
        $this->info("noOfTimesOn:\t" . $user->noOfTimesOn());
        $this->info("password:\t" . $user->password());
        $this->info("dept:\t\t" . $user->dept());
        $this->info("faculty:\t" . $user->faculty());
        $this->info("created:\t" . $user->created());
        $this->info("lastOn:\t\t" . $user->lastOn());
        $this->info("historyFile:\t" . $user->historyFile());
        $this->info("BBSNo:\t\t" . $user->BBSNo());
        $this->info("flags:\t\t" . $user->flags());

        $this->newLine();
        $this->comment("Info");
        $this->info($user->info());

        $this->newLine();
        $this->comment("Comments");
        foreach ($user->comments() as $comment) {
            $this->info("{$comment['username']} - {$comment['body']}");
        }
    }
}
