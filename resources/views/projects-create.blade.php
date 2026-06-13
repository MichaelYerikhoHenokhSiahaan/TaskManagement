<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.22em] text-blue-600">Project Feature</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-800">{{ __('Create Project') }}</h2>
            </div>

            <a
                href="{{ route('projects.index') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
            >
                Back to Projects
            </a>
        </div>
    </x-slot>

    <div class="bg-[linear-gradient(180deg,#f8fbff_0%,#eef5ff_100%)] py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-[2rem] border border-white/70 bg-white/95 p-6 shadow-[0_20px_60px_rgba(46,95,189,0.10)] sm:p-8">
                <h3 class="text-lg font-semibold text-slate-900">Add New Project</h3>
                <p class="mt-1 text-sm text-slate-500">
                    Create a project first, then manage its tasks on the project detail page.
                </p>

                @if ($errors->any())
                    <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('projects.store') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="project_name" class="mb-2 block text-sm font-medium text-slate-700">Project Name</label>
                        <input
                            id="project_name"
                            name="project_name"
                            type="text"
                            value="{{ old('project_name') }}"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100"
                            placeholder="Enter project name"
                            required
                        />
                    </div>

                    <div>
                        <label for="project_description" class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                        <textarea
                            id="project_description"
                            name="project_description"
                            rows="5"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100"
                            placeholder="Enter project description"
                            required
                        >{{ old('project_description') }}</textarea>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        Save Project
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
