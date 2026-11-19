<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    /**
     * Insert the platform SuperAdmin with raw SQL, as the assignment requires.
     * Login: superadmin@example.com / password
     */
    public function run(): void
    {
        $now = now()->format('Y-m-d H:i:s');
        $password = password_hash('password', PASSWORD_BCRYPT);

        DB::statement(
            'INSERT INTO users (name, email, password, role, company_id, email_verified_at, created_at, updated_at) VALUES (?, ?, ?, ?, NULL, ?, ?, ?)',
            [
                'Super Admin',
                'superadmin@example.com',
                $password,
                User::ROLE_SUPER_ADMIN,
                $now,
                $now,
                $now,
            ]
        );
    }
}
