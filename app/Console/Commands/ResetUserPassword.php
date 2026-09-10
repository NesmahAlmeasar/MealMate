<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    protected $signature = 'user:reset-password {email} {password}';

    protected $description = 'Reset password for a specific user';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found!");

            return 1;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("Password reset successfully for user: {$user->Fname} {$user->Lname} ({$user->email})");
        $this->info("New password: {$password}");

        return 0;
    }
}
