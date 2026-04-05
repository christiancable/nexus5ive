<?php

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class NexusCreateGuest extends Command
{
    protected $signature = 'nexus:create-guest
                            {username : Username for the guest account}';

    protected $description = 'Create a guest account (read-only, no write access)';

    public function handle(): int
    {
        $username = $this->argument('username');

        if (User::where('username', $username)->exists()) {
            $this->error("A user with username '{$username}' already exists.");

            return self::FAILURE;
        }

        $password = $this->secret("Password for '{$username}'");

        if (! $password) {
            $this->error('Password cannot be empty.');

            return self::FAILURE;
        }

        try {
            $guest = new User;
            $guest->username = $username;
            $guest->name = $username;
            $guest->email = strtolower($username).'@nexus.local';
            $guest->password = Hash::make($password);
            $guest->email_verified_at = now();
            $guest->is_guest = true;
            $guest->theme_id = 1;
            $guest->save();
        } catch (Exception $e) {
            $this->error('Failed to create guest account: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info("Guest account created (username: {$guest->username}).");

        return self::SUCCESS;
    }
}
