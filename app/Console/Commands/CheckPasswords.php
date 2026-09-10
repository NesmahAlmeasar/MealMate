<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckPasswords extends Command
{
    protected $signature = 'users:check-passwords';

    protected $description = 'Check and display password hash status for users';

    public function handle()
    {
        $users = User::select('user_id', 'Fname', 'email', 'password')->limit(5)->get();

        $this->info('Checking users passwords:');
        $this->newLine();

        foreach ($users as $user) {
            $isHashed = str_starts_with($user->password, '$2y$');
            $status = $isHashed ? '✓ Hashed' : '✗ NOT Hashed';
            $preview = substr($user->password, 0, 20).'...';

            $this->line("ID: {$user->user_id} | Email: {$user->email}");
            $this->line("  Status: {$status}");
            $this->line("  Preview: {$preview}");
            $this->newLine();
        }

        return 0;
    }
}
