<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\KanbanSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create superuser (developer)
        User::firstOrCreate(
            ['email' => 'developer@kjpp.id'],
            [
                'name' => 'Developer',
                'password' => bcrypt('developer123'),
                'role' => User::ROLE_SUPERUSER,
                'is_active' => true,
            ]
        );

        // Create admin users
        User::firstOrCreate(
            ['email' => 'admin@kjpp.id'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('admin123'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'supervisor@kjpp.id'],
            [
                'name' => 'Supervisor',
                'password' => bcrypt('supervisor123'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );

        // Create regular users (appraisers)
        $users = [
            ['name' => 'Andi Pratama', 'email' => 'andi@kjpp.id'],
            ['name' => 'Budi Santoso', 'email' => 'budi@kjpp.id'],
            ['name' => 'Citra Dewi', 'email' => 'citra@kjpp.id'],
            ['name' => 'Dian Permata', 'email' => 'dian@kjpp.id'],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => bcrypt('password123'),
                    'role' => User::ROLE_USER,
                    'is_active' => true,
                ]
            );
        }

        // Run Kanban Seeder (clients, projects, assets, documents, notes)
        $this->call([
            KanbanSeeder::class,
        ]);

        $this->command->info('✅ Users seeded:');
        $this->command->info('   - Superuser: developer@kjpp.id / developer123');
        $this->command->info('   - Admin: admin@kjpp.id / admin123');
        $this->command->info('   - Admin: supervisor@kjpp.id / supervisor123');
        $this->command->info('   - Users: andi/budi/citra/dian@kjpp.id / password123');
    }
}
