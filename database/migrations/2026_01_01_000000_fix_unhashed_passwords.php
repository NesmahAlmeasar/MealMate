<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all users
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            // Check if password is already hashed (bcrypt hashes start with $2y$)
            if (! str_starts_with($user->password, '$2y$')) {
                // Password is not hashed, hash it
                DB::table('users')
                    ->where('user_id', $user->user_id)
                    ->update([
                        'password' => Hash::make($user->password),
                    ]);

                echo "Fixed password for user ID: {$user->user_id} ({$user->email})\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse password hashing
    }
};
