<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminUserId = User::query()->where('role', 'admin')->value('id')
            ?? User::query()->orderBy('id')->value('id');

        if (! $adminUserId) {
            return;
        }

        $projects = [
            [
                'name' => 'Website Redesign',
                'description' => 'Refresh the task management website interface with a cleaner login flow and updated branding.',
                'tasks' => [
                    [
                        'name' => 'Review login layout',
                        'description' => 'Check the updated login page structure and brand placement.',
                        'is_completed' => true,
                    ],
                    [
                        'name' => 'Approve new logo usage',
                        'description' => 'Confirm the uploaded logo is used consistently across login and dashboard views.',
                        'is_completed' => true,
                    ],
                    [
                        'name' => 'Finalize responsive spacing',
                        'description' => 'Ensure the main auth screen spacing looks correct on tablet and mobile screens.',
                        'is_completed' => true,
                    ],
                ],
            ],
            [
                'name' => 'Mobile App Planning',
                'description' => 'Prepare wireframes and feature priorities for the first mobile version of the task management system.',
                'tasks' => [
                    [
                        'name' => 'Draft wireframe ideas',
                        'description' => 'Create early low-fidelity wireframes for dashboard and task detail screens.',
                        'is_completed' => true,
                    ],
                    [
                        'name' => 'Prioritize MVP features',
                        'description' => 'List the first mobile features that should be included in the MVP release.',
                        'is_completed' => false,
                    ],
                    [
                        'name' => 'Review notification flow',
                        'description' => 'Plan how reminders and task alerts should appear inside the mobile app.',
                        'is_completed' => false,
                    ],
                ],
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::updateOrCreate(
                ['name' => $projectData['name']],
                [
                    'user_id' => $adminUserId,
                    'description' => $projectData['description'],
                ]
            );

            $project->tasks()->delete();
            $project->tasks()->createMany($projectData['tasks']);
        }
    }
}
