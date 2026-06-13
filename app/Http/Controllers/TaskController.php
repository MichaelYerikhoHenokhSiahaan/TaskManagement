<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse|JsonResponse
    {
        $this->authorizeProjectManagement($request, $project);

        $validated = $request->validate([
            'task_name' => ['required', 'string', 'max:255'],
            'task_description' => ['required', 'string'],
            'task_completed' => ['nullable', 'boolean'],
        ]);

        $task = $project->tasks()->create([
            'name' => $validated['task_name'],
            'description' => $validated['task_description'],
            'is_completed' => (bool) ($validated['task_completed'] ?? false),
        ]);

        if ($request->expectsJson()) {
            return response()->json(
                $this->buildPayload($request->user(), $project->id, $task->id, 'Task created successfully.')
            );
        }

        return redirect()
            ->route('projects.edit', $project)
            ->with('success', 'Task created successfully.');
    }

    public function update(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $this->authorizeProjectManagement($request, $task->project);

        $validated = $request->validate([
            'task_name' => ['required', 'string', 'max:255'],
            'task_description' => ['required', 'string'],
            'task_completed' => ['nullable', 'boolean'],
        ]);

        $task->update([
            'name' => $validated['task_name'],
            'description' => $validated['task_description'],
            'is_completed' => (bool) ($validated['task_completed'] ?? false),
        ]);

        if ($request->expectsJson()) {
            return response()->json(
                $this->buildPayload($request->user(), $task->project_id, $task->id, 'Task updated successfully.')
            );
        }

        return redirect()
            ->route('projects.edit', $task->project_id)
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $this->authorizeProjectManagement($request, $task->project);

        $projectId = $task->project_id;
        $task->delete();

        if ($request->expectsJson()) {
            return response()->json(
                $this->buildPayload($request->user(), $projectId, null, 'Task removed.')
            );
        }

        return redirect()
            ->route('projects.edit', $projectId)
            ->with('success', 'Task removed.');
    }

    public function toggle(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $this->authorizeProjectManagement($request, $task->project);

        $task->update([
            'is_completed' => ! $task->is_completed,
        ]);

        if ($request->expectsJson()) {
            return response()->json(
                $this->buildPayload($request->user(), $task->project_id, $task->id, 'Task status updated successfully.')
            );
        }

        return redirect()
            ->route('projects.edit', $task->project_id)
            ->with('success', 'Task status updated successfully.');
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
            ->with(['tasks' => fn ($query) => $query->orderBy('id')])
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

        if ($project->user_id !== $request->user()->id) {
            abort(403, 'You can only view tasks for this project.');
        }
    }

    private function serializeProjects(Collection $projects): array
    {
        return $projects->map(function (Project $project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
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
