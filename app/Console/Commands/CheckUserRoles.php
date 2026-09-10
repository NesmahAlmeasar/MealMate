<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckUserRoles extends Command
{
    protected $signature = 'user:check-roles {email}';

    protected $description = 'Check roles for a specific user';

    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email '{$email}' not found!");

            return 1;
        }

        $this->info("User: {$user->Fname} {$user->Lname} ({$user->email})");
        $this->info("User ID: {$user->user_id}");
        $this->newLine();

        $roles = DB::table('user_roles')
            ->join('roles', 'user_roles.role_id', '=', 'roles.role_id')
            ->where('user_roles.user_id', $user->user_id)
            ->select('roles.role_id', 'roles.name')
            ->get();

        if ($roles->isEmpty()) {
            $this->warn('This user has NO roles assigned!');
        } else {
            $this->info('Roles:');
            foreach ($roles as $role) {
                $this->line("  - ID: {$role->role_id} | Name: {$role->name}");
            }
        }

        $this->newLine();
        $this->info('All available roles in database:');
        $allRoles = DB::table('roles')->select('role_id', 'name')->get();
        foreach ($allRoles as $role) {
            $this->line("  - ID: {$role->role_id} | Name: {$role->name}");
        }

        return 0;
    }
}
