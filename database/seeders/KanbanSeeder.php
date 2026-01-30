<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Board;
use App\Models\Column;
use App\Models\Card;
use Illuminate\Database\Seeder;

class KanbanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users
        $user1 = User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password123'),
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'jane@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => bcrypt('password123'),
            ]
        );

        // Create a sample board
        $board = Board::create([
            'name' => 'Website Redesign Project',
            'description' => 'Kanban board untuk project redesign website perusahaan',
            'created_by' => $user1->id,
        ]);

        // Create default columns
        $columns = [];
        $columnNames = ['To Do', 'In Progress', 'Done'];
        foreach ($columnNames as $index => $columnName) {
            $columns[$columnName] = Column::create([
                'board_id' => $board->id,
                'name' => $columnName,
                'order' => $index,
            ]);
        }

        // Create sample cards
        $card1 = Card::create([
            'column_id' => $columns['To Do']->id,
            'title' => 'Design landing page mockups',
            'description' => 'Create high-fidelity mockups for the new landing page',
            'priority' => 'high',
            'order' => 0,
            'due_date' => now()->addDays(7),
        ]);
        $card1->assignedUsers()->attach($user1);

        $card2 = Card::create([
            'column_id' => $columns['To Do']->id,
            'title' => 'Setup project repository',
            'description' => 'Initialize Git repo and setup CI/CD pipeline',
            'priority' => 'medium',
            'order' => 1,
        ]);
        $card2->assignedUsers()->attach($user2);

        $card3 = Card::create([
            'column_id' => $columns['In Progress']->id,
            'title' => 'Develop header component',
            'description' => 'Build responsive header with navigation menu',
            'priority' => 'high',
            'order' => 0,
            'due_date' => now()->addDays(3),
        ]);
        $card3->assignedUsers()->attach($user1);
        $card3->assignedUsers()->attach($user2);

        $card4 = Card::create([
            'column_id' => $columns['In Progress']->id,
            'title' => 'Create color palette',
            'description' => 'Define brand colors and typography',
            'priority' => 'medium',
            'order' => 1,
        ]);
        $card4->assignedUsers()->attach($user1);

        $card5 = Card::create([
            'column_id' => $columns['Done']->id,
            'title' => 'Kick-off meeting',
            'description' => 'Initial project kickoff meeting with stakeholders',
            'priority' => 'low',
            'order' => 0,
        ]);
        $card5->assignedUsers()->attach($user1);
        $card5->assignedUsers()->attach($user2);
    }
}
