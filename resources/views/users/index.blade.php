<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.22em] text-blue-600">Admin Feature</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-800">{{ __('Users Management') }}</h2>
            </div>

            <a
                href="{{ route('users.create') }}"
                class="inline-flex items-center rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 shadow-sm transition hover:bg-blue-100"
            >
                + Create User
            </a>
        </div>
    </x-slot>

    <div class="bg-[linear-gradient(180deg,#f8fbff_0%,#eef5ff_100%)] py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/95 shadow-[0_20px_60px_rgba(46,95,189,0.10)]">
                <div class="border-b border-slate-100 px-6 py-5 sm:px-8">
                    <h3 class="text-lg font-semibold text-slate-900">User Table</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        This page is admin-only. Regular users cannot create new users.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-0">
                        <thead class="bg-slate-50/80">
                            <tr class="text-left text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                                <th class="border-b border-r border-slate-200 px-6 py-4 sm:px-8">Name</th>
                                <th class="border-b border-r border-slate-200 px-6 py-4">Email</th>
                                <th class="border-b border-r border-slate-200 px-6 py-4">Role</th>
                                <th class="border-b border-slate-200 px-6 py-4">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="bg-white">
                                    <td class="border-b border-r border-slate-100 px-6 py-5 sm:px-8">
                                        <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                    </td>
                                    <td class="border-b border-r border-slate-100 px-6 py-5 text-sm text-slate-600">
                                        {{ $user->email }}
                                    </td>
                                    <td class="border-b border-r border-slate-100 px-6 py-5">
                                        @if ($user->role === 'admin')
                                            <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-700">
                                                Admin
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-slate-700">
                                                User
                                            </span>
                                        @endif
                                    </td>
                                    <td class="border-b border-slate-100 px-6 py-5 text-sm text-slate-600">
                                        {{ $user->created_at?->format('Y-m-d H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
