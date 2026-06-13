<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        return view('projects', [
            'projects' => $this->loadProjects($request->user()),
        ]);
    }

    public function create(): View
    {
        return view('projects-create');
    }

    public function edit(Request $request, Project $project): View
    {
        $this->authorizeProjectAccess($request, $project);
        $canManageProject = $this->canManageProject($request->user(), $project);

        $payload = $this->buildPayload(
            $request->user(),
            $project->id,
            $project->tasks()->orderBy('id')->value('id')
        );

        return view('projects-edit', [
            'projectsData' => $payload['projects'],
            'selectedProjectId' => $payload['selectedProjectId'],
            'selectedTaskId' => $payload['selectedTaskId'],
            'canManageProject' => $canManageProject,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'project_description' => ['required', 'string'],
        ]);

        $project = Project::create([
            'user_id' => $request->user()->id,
            'name' => $validated['project_name'],
            'description' => $validated['project_description'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(
                $this->buildPayload($request->user(), $project->id, null, 'Project created successfully.')
            );
        }

        return redirect()
            ->route('projects.edit', $project)
            ->with('success', 'Project created successfully.');
    }

    public function update(Request $request, Project $project): RedirectResponse|JsonResponse
    {
        $this->authorizeProjectManagement($request, $project);

        $validated = $request->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'project_description' => ['required', 'string'],
        ]);

        $project->update([
            'name' => $validated['project_name'],
            'description' => $validated['project_description'],
        ]);

        if ($request->expectsJson()) {
            return response()->json(
                $this->buildPayload($request->user(), $project->id, (int) $request->input('selected_task_id') ?: null, 'Project updated successfully.')
            );
        }

        return redirect()
            ->route('projects.edit', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Request $request, Project $project): RedirectResponse|JsonResponse
    {
        $this->authorizeProjectManagement($request, $project);

        $project->delete();

        $nextProject = $this->loadProjects($request->user())->first();

        if ($request->expectsJson()) {
            return response()->json(
                $this->buildPayload($request->user(), $nextProject?->id, null, 'Project removed.')
            );
        }

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project removed.');
    }

    private function buildPayload(User $actor, ?int $selectedProjectId = null, ?int $selectedTaskId = null, ?string $message = null): array
    {
        $projects = $this->loadProjects($actor);
        $selectedProject = $selectedProjectId ? $projects->firstWhere('id', $selectedProjectId) : $projects->first();
        $selectedTask = $selectedProject && $selectedTaskId
            ? $selectedProject->tasks->firstWhere('id', $selectedTaskId)
            : $selectedProject?->tasks->first();

        return [
            'message' => $message,
            'projects' => $this->serializeProjects($projects),
            'selectedProjectId' => $selectedProject?->id,
            'selectedTaskId' => $selectedTask?->id,
        ];
    }

    private function loadProjects(User $actor): Collection
    {
        return Project::query()
            ->when(! $actor->isAdmin(), fn ($query) => $query->where('user_id', $actor->id))
            ->with([
                'user',
                'tasks' => fn ($query) => $query->orderBy('id'),
            ])
            ->orderBy('id')
            ->get();
    }

    private function authorizeProjectAccess(Request $request, Project $project): void
    {
        $user = $request->user();

        if (! $user->isAdmin() && $project->user_id !== $user->id) {
            abort(403, 'You are not allowed to access this project.');
        }
    }

    private function authorizeProjectManagement(Request $request, Project $project): void
    {
        $this->authorizeProjectAccess($request, $project);

        if (! $this->canManageProject($request->user(), $project)) {
            abort(403, 'You can only view this project.');
        }
    }

    private function canManageProject(User $user, Project $project): bool
    {
        return $project->user_id === $user->id;
    }

    private function serializeProjects(Collection $projects): array
    {
        return $projects->map(function (Project $project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'owner_id' => $project->user_id,
                'owner_name' => $project->user?->name,
                'tasks' => $project->tasks->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'name' => $task->name,
                        'description' => $task->description,
                        'completed' => (bool) $task->is_completed,
                    ];
                })->values()->all(),
            ];
        })->values()->all();
    }
}
