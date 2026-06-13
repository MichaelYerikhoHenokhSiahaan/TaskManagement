<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.22em] text-blue-600">Project Feature</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-800">
                    {{ __('Projects') }}
                </h2>
            </div>

            <a
                href="{{ route('projects.create') }}"
                class="inline-flex items-center rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 shadow-sm transition hover:bg-blue-100"
            >
                + Add Project
            </a>
        </div>
    </x-slot>

    <div class="bg-[linear-gradient(180deg,#f8fbff_0%,#eef5ff_100%)] py-10">
        <div
            x-data="{
                search: '',
                matches(value) {
                    if (!this.search.trim()) return true;
                    return value.includes(this.search.toLowerCase());
                }
            }"
            class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8"
        >
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_16px_40px_rgba(46,95,189,0.08)]">
                    <p class="text-sm font-medium text-slate-500">Total Projects</p>
                    <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $projects->count() }}</p>
                </div>

                <div class="rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_16px_40px_rgba(46,95,189,0.08)]">
                    <p class="text-sm font-medium text-slate-500">Completed Projects</p>
                    <p class="mt-3 text-3xl font-semibold text-emerald-600">
                        {{ $projects->filter(fn ($project) => $project->tasks->isNotEmpty() && $project->tasks->every(fn ($task) => $task->is_completed))->count() }}
                    </p>
                </div>

                <div class="rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_16px_40px_rgba(46,95,189,0.08)]">
                    <p class="text-sm font-medium text-slate-500">Pending Projects</p>
                    <p class="mt-3 text-3xl font-semibold text-rose-500">
                        {{ $projects->filter(fn ($project) => $project->tasks->isEmpty() || $project->tasks->contains(fn ($task) => ! $task->is_completed))->count() }}
                    </p>
                </div>
            </div>

            <div class="rounded-3xl border border-white/80 bg-white/90 p-5 shadow-[0_16px_40px_rgba(46,95,189,0.08)]">
                <label for="project_search" class="mb-2 block text-sm font-medium text-slate-700">
                    Search all columns
                </label>
                <input
                    id="project_search"
                    x-model="search"
                    type="text"
                    placeholder="Search by project name, description, tasks, completion, owner, or action..."
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100"
                />
            </div>

            <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/95 shadow-[0_20px_60px_rgba(46,95,189,0.10)]">
                <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
                    <h3 class="text-lg font-semibold text-slate-900">Project Table</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Open a project to manage its details and tasks on a separate page.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-0">
                        <thead class="bg-slate-50/80">
                            <tr class="text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                                <th class="border-b border-r border-slate-200 px-6 py-4 sm:px-8">Project Name</th>
                                <th class="border-b border-r border-slate-200 px-6 py-4">Description</th>
                                <th class="border-b border-r border-slate-200 px-6 py-4">Tasks</th>
                                <th class="border-b border-r border-slate-200 px-6 py-4">Completion</th>
                                <th class="border-b border-r border-slate-200 px-6 py-4">Owner</th>
                                <th class="border-b border-slate-200 px-6 py-4 text-right sm:px-8">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projects as $project)
                                @php
                                    $isComplete = $project->tasks->isNotEmpty() && $project->tasks->every(fn ($task) => $task->is_completed);
                                    $completedTaskCount = $project->tasks->where('is_completed', true)->count();
                                    $actionLabel = auth()->user()->isAdmin() && auth()->id() !== $project->user_id ? 'view tasks' : 'manage';
                                    $searchText = strtolower(
                                        implode(' ', [
                                            $project->name,
                                            $project->description,
                                            "{$completedTaskCount}/{$project->tasks->count()} tasks",
                                            $isComplete ? 'completed' : 'not complete',
                                            $project->user?->name ?? '',
                                            $project->user?->email ?? '',
                                            $actionLabel,
                                        ])
                                    );
                                @endphp
                                <tr class="bg-white" x-show="matches({{ Illuminate\Support\Js::from($searchText) }})">
                                    <td class="border-b border-r border-slate-100 px-6 py-5 align-top sm:px-8">
                                        <div class="text-base font-semibold text-slate-900">{{ $project->name }}</div>
                                    </td>
                                    <td class="border-b border-r border-slate-100 px-6 py-5 align-top">
                                        <p class="max-w-2xl text-sm leading-7 text-slate-600">{{ $project->description }}</p>
                                    </td>
                                    <td class="border-b border-r border-slate-100 px-6 py-5 align-top">
                                        <div class="text-sm font-semibold text-slate-800">
                                            {{ $completedTaskCount }}/{{ $project->tasks->count() }} tasks complete
                                        </div>
                                    </td>
                                    <td class="border-b border-r border-slate-100 px-6 py-5 align-top">
                                        @if ($isComplete)
                                            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">
                                                <span class="text-base leading-none">✓</span>
                                                Completed
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-sm font-semibold text-rose-700">
                                                <span class="text-base leading-none">✗</span>
                                                Not Complete
                                            </span>
                                        @endif
                                    </td>
                                    <td class="border-b border-r border-slate-100 px-6 py-5 align-top">
                                        <div class="text-sm font-semibold text-slate-800">{{ $project->user?->name ?? '-' }}</div>
                                        <div class="mt-1 text-xs text-slate-500">{{ $project->user?->email ?? '' }}</div>
                                    </td>
                                    <td class="border-b border-slate-100 px-6 py-5 align-top sm:px-8">
                                        <div class="flex flex-wrap justify-end gap-2">
                                            <a
                                                href="{{ route('projects.edit', $project) }}"
                                                class="rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 transition hover:bg-blue-100"
                                            >
                                                @if (auth()->user()->isAdmin() && auth()->id() !== $project->user_id)
                                                    View Tasks
                                                @else
                                                    Manage
                                                @endif
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-slate-500">
                                        No projects found. Create your first project.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
