<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.22em] text-blue-600">Project Feature</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-800">{{ __('Manage Project') }}</h2>
            </div>

            <a
                href="{{ route('projects.index') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
            >
                Back to Projects
            </a>
        </div>
    </x-slot>

    <div
        x-data="projectsCrud({
            projects: {{ Illuminate\Support\Js::from($projectsData) }},
            selectedProjectId: {{ Illuminate\Support\Js::from($selectedProjectId) }},
            selectedTaskId: {{ Illuminate\Support\Js::from($selectedTaskId) }},
            routes: {
                projectsIndex: {{ Illuminate\Support\Js::from(route('projects.index')) }},
                projectStore: {{ Illuminate\Support\Js::from(route('projects.store')) }},
                projectBase: {{ Illuminate\Support\Js::from(url('/projects')) }},
                taskBase: {{ Illuminate\Support\Js::from(url('/tasks')) }}
            },
            canManageProject: {{ Illuminate\Support\Js::from($canManageProject) }},
            redirectAfterProjectDelete: true
        })"
        x-init="init()"
        class="bg-[linear-gradient(180deg,#f8fbff_0%,#eef5ff_100%)] py-10"
    >
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div
                x-show="successMessage"
                x-transition
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700"
            >
                <span x-text="successMessage"></span>
            </div>

            <div
                x-show="errorMessage"
                x-transition
                class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-700"
            >
                <span x-text="errorMessage"></span>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_16px_40px_rgba(46,95,189,0.08)]">
                    <p class="text-sm font-medium text-slate-500">Selected Project</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-900" x-text="selectedProject?.name"></p>
                </div>

                <div class="rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_16px_40px_rgba(46,95,189,0.08)]">
                    <p class="text-sm font-medium text-slate-500">Completed Tasks</p>
                    <p class="mt-3 text-3xl font-semibold text-emerald-600" x-text="selectedProject ? completedTaskCount(selectedProject) : 0"></p>
                </div>

                <div class="rounded-3xl border border-white/80 bg-white/90 p-6 shadow-[0_16px_40px_rgba(46,95,189,0.08)]">
                    <p class="text-sm font-medium text-slate-500">Project Status</p>
                    <div class="mt-3">
                        <template x-if="selectedProject && isProjectComplete(selectedProject)">
                            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700">
                                <span class="text-base leading-none">✓</span>
                                Completed
                            </span>
                        </template>
                        <template x-if="selectedProject && !isProjectComplete(selectedProject)">
                            <span class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-700">
                                <span class="text-base leading-none">✗</span>
                                Not Complete
                            </span>
                        </template>
                    </div>
                </div>
            </div>

            <div class="grid gap-8 xl:grid-cols-[0.95fr_1.05fr]">
                <div class="rounded-[2rem] border border-white/70 bg-white/95 p-6 shadow-[0_20px_60px_rgba(46,95,189,0.10)] sm:p-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Edit Project</h3>
                            <p class="mt-1 text-sm text-slate-500" x-show="canManageProject">
                                Update project details here.
                            </p>
                            <p class="mt-1 text-sm text-amber-600" x-show="!canManageProject">
                                View-only mode. You can see tasks, but you cannot change this project.
                            </p>
                        </div>

                        <template x-if="canManageProject">
                            <button
                                type="button"
                                @click="openDeleteProjectDialog(selectedProject.id)"
                                class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-100"
                            >
                                Delete Project
                            </button>
                        </template>
                    </div>

                    <form class="mt-6 space-y-5" @submit.prevent="updateProject()">
                        <div>
                            <label for="selected_project_name" class="mb-2 block text-sm font-medium text-slate-700">Project Name</label>
                            <input
                                id="selected_project_name"
                                x-model="projectForm.project_name"
                                type="text"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100"
                                :disabled="!canManageProject"
                                required
                            />
                        </div>

                        <div>
                            <label for="selected_project_description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                            <textarea
                                id="selected_project_description"
                                x-model="projectForm.project_description"
                                rows="5"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100"
                                :disabled="!canManageProject"
                                required
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="isSubmitting || !canManageProject"
                        >
                            Update Project
                        </button>
                    </form>
                </div>

                <div class="rounded-[2rem] border border-white/70 bg-white/95 p-6 shadow-[0_20px_60px_rgba(46,95,189,0.10)] sm:p-8">
                    <h3 class="text-lg font-semibold text-slate-900">
                        <span>Create Task for </span>
                        <span x-text="selectedProject?.name"></span>
                    </h3>
                    <p class="mt-1 text-sm text-slate-500" x-show="canManageProject">
                        Add tasks to this project without leaving the page.
                    </p>
                    <p class="mt-1 text-sm text-amber-600" x-show="!canManageProject">
                        View-only mode for this project's tasks.
                    </p>

                    <form class="mt-6 space-y-5" @submit.prevent="createTask()">
                        <div>
                            <label for="task_name" class="mb-2 block text-sm font-medium text-slate-700">Task Name</label>
                            <input
                                id="task_name"
                                x-model="createTaskForm.task_name"
                                type="text"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100"
                                :disabled="!canManageProject"
                                placeholder="Enter task name"
                                required
                            />
                        </div>

                        <div>
                            <label for="task_description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                            <textarea
                                id="task_description"
                                x-model="createTaskForm.task_description"
                                rows="4"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100"
                                :disabled="!canManageProject"
                                placeholder="Enter task description"
                                required
                            ></textarea>
                        </div>

                        <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                            <input type="checkbox" x-model="createTaskForm.task_completed" :disabled="!canManageProject" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Mark as complete
                        </label>

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="isSubmitting || !canManageProject"
                        >
                            Save Task
                        </button>
                    </form>
                </div>
            </div>

            <div class="rounded-[2rem] border border-white/70 bg-white/95 shadow-[0_20px_60px_rgba(46,95,189,0.10)]">
                <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
                    <h3 class="text-lg font-semibold text-slate-900">Task Table</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Manage all tasks for the selected project here.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-0">
                        <thead class="bg-slate-50/80">
                            <tr class="text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                                <th class="border-b border-r border-slate-200 px-6 py-4 sm:px-8">Task Name</th>
                                <th class="border-b border-r border-slate-200 px-6 py-4">Description</th>
                                <th class="border-b border-r border-slate-200 px-6 py-4">Status</th>
                                <th class="border-b border-slate-200 px-6 py-4 text-right sm:px-8">CRUD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="(selectedProject?.tasks.length ?? 0) === 0">
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-500">
                                        No tasks found for this project yet.
                                    </td>
                                </tr>
                            </template>

                            <template x-for="task in selectedProject?.tasks ?? []" :key="task.id">
                                <tr :class="selectedTaskId === task.id ? 'bg-blue-50/60' : 'bg-white'">
                                    <td class="border-b border-r border-slate-100 px-6 py-5 align-top sm:px-8">
                                        <div class="text-base font-semibold text-slate-900" x-text="task.name"></div>
                                    </td>
                                    <td class="border-b border-r border-slate-100 px-6 py-5 align-top">
                                        <p class="max-w-2xl text-sm leading-7 text-slate-600" x-text="task.description"></p>
                                    </td>
                                    <td class="border-b border-r border-slate-100 px-6 py-5 align-top">
                                        <template x-if="task.completed">
                                            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">
                                                <span class="text-base leading-none">✓</span>
                                                Complete
                                            </span>
                                        </template>
                                        <template x-if="!task.completed">
                                            <span class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-sm font-semibold text-rose-700">
                                                <span class="text-base leading-none">✗</span>
                                                Not Complete
                                            </span>
                                        </template>
                                    </td>
                                    <td class="border-b border-slate-100 px-6 py-5 align-top sm:px-8">
                                        <template x-if="canManageProject">
                                            <div class="flex flex-wrap justify-end gap-2">
                                                <button
                                                    type="button"
                                                    @click="selectTask(task.id)"
                                                    class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-700 transition hover:bg-amber-100"
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="toggleTask(task.id)"
                                                    class="rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-medium text-blue-700 transition hover:bg-blue-100"
                                                >
                                                    Toggle Status
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="openDeleteTaskDialog(task.id)"
                                                    class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-100"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="!canManageProject">
                                            <span class="inline-flex rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-600">
                                                View Only
                                            </span>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <template x-if="selectedTask && canManageProject">
                <div class="rounded-[2rem] border border-white/70 bg-white/95 p-6 shadow-[0_20px_60px_rgba(46,95,189,0.10)] sm:p-8">
                    <h3 class="text-lg font-semibold text-slate-900">Edit Task</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Update the selected task without reloading the page.
                    </p>

                    <form class="mt-6 space-y-5" @submit.prevent="updateTask()">
                        <div class="grid gap-5 lg:grid-cols-2">
                            <div>
                                <label for="edit_task_name" class="mb-2 block text-sm font-medium text-slate-700">Task Name</label>
                                <input
                                    id="edit_task_name"
                                    x-model="taskForm.task_name"
                                    type="text"
                                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100"
                                    required
                                />
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                                <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                                    <input
                                        type="checkbox"
                                        x-model="taskForm.task_completed"
                                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    Mark this task as complete
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="edit_task_description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                            <textarea
                                id="edit_task_description"
                                x-model="taskForm.task_description"
                                rows="4"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100"
                                required
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="isSubmitting"
                        >
                            Update Task
                        </button>
                    </form>
                </div>
            </template>
            <div
                x-show="confirmDialog.open"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-slate-950/45"
                @click="closeConfirmDialog()"
            ></div>

            <div
                x-show="confirmDialog.open"
                x-transition
                class="fixed inset-0 z-50 flex items-center justify-center px-4"
            >
                <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl" @click.stop>
                    <h3 class="text-lg font-semibold text-slate-900" x-text="confirmDialog.title"></h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600" x-text="confirmDialog.message"></p>

                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            @click="closeConfirmDialog()"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            @click="confirmDelete()"
                            class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100"
                            x-text="confirmDialog.confirmLabel"
                        ></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
