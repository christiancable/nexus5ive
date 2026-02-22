<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class NexusSetPassword extends Command
{
    protected $signature = 'nexus:setpassword {username : The username of the account}';

    protected $description = 'Set the password for a user account';

    public function handle(): int
    {
        $username = $this->argument('username');

        $user = User::where('username', $username)->first();

        if (! $user) {
            $this->error("User '{$username}' not found.");

            return self::FAILURE;
        }

        $password = $this->secret('New password');

        if (! $password) {
            $this->error('Password cannot be empty.');

            return self::FAILURE;
        }

        $confirm = $this->secret('Confirm password');

        if ($password !== $confirm) {
            $this->error('Passwords do not match.');

            return self::FAILURE;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("Password updated for {$user->username}.");

        return self::SUCCESS;
    }
}
