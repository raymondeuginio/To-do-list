<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = [
            ['title' => 'Design homepage layout', 'notes' => 'Finalize UI structure for the main dashboard.', 'due_date' => '2025-10-17', 'priority' => 'high', 'is_done' => false],
            ['title' => 'Write project README', 'notes' => 'Include setup, screenshots, and documentation steps.', 'due_date' => '2025-10-18', 'priority' => 'medium', 'is_done' => false],
            ['title' => 'Fix login validation bug', 'notes' => 'Error message not showing when password is incorrect.', 'due_date' => '2025-10-18', 'priority' => 'high', 'is_done' => true],
            ['title' => 'Prepare presentation slides', 'notes' => 'Highlight main features and progress timeline.', 'due_date' => '2025-10-19', 'priority' => 'medium', 'is_done' => false],
            ['title' => 'Database backup & cleanup', 'notes' => 'Delete test data and export SQL dump.', 'due_date' => '2025-10-19', 'priority' => 'low', 'is_done' => false],
            ['title' => 'Review pull requests', 'notes' => 'Check code style and logic for the new API endpoints.', 'due_date' => '2025-10-20', 'priority' => 'medium', 'is_done' => true],
            ['title' => 'Update user profile page', 'notes' => 'Add profile picture upload feature.', 'due_date' => '2025-10-21', 'priority' => 'low', 'is_done' => false],
            ['title' => 'Optimize database queries', 'notes' => 'Reduce load time by indexing frequently queried columns.', 'due_date' => '2025-10-21', 'priority' => 'high', 'is_done' => false],
            ['title' => 'Team meeting: sprint review', 'notes' => 'Discuss completed milestones and next sprint goals.', 'due_date' => '2025-10-22', 'priority' => 'medium', 'is_done' => false],
            ['title' => 'UI polish & spacing adjustments', 'notes' => 'Adjust margins and improve readability.', 'due_date' => '2025-10-22', 'priority' => 'low', 'is_done' => true],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
