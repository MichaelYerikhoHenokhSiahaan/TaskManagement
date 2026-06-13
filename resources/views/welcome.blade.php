<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600;outfit:500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-950 text-white antialiased">
        <div class="relative isolate min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.20),_transparent_25%),radial-gradient(circle_at_80%_20%,_rgba(168,85,247,0.20),_transparent_25%),linear-gradient(135deg,_#020617,_#0f172a_45%,_#111827)]"></div>
            <div class="pointer-events-none absolute -left-16 -top-16 h-72 w-72 rounded-full bg-cyan-400/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-16 -right-16 h-80 w-80 rounded-full bg-fuchsia-500/10 blur-3xl"></div>

            <div class="relative mx-auto flex min-h-screen w-full max-w-6xl items-center px-6 py-10 sm:px-10">
                <div class="grid w-full grid-cols-1 items-center gap-10 lg:grid-cols-2">
                    <div class="max-w-2xl text-center lg:text-left">
                        <p class="text-sm font-medium uppercase tracking-[0.28em] text-cyan-300">Task Management Platform</p>
                        <h1 class="mt-4 font-['Outfit'] text-4xl font-bold leading-tight text-white sm:text-5xl">
                            Laravel project with login page and MySQL database integration
                        </h1>
                        <p class="mt-6 text-base leading-7 text-slate-300">
                            This starter is configured for the `Task_Management` database and includes authentication pages,
                            protected routes, and a clean foundation for a task management system.
                        </p>
                    </div>

                    <div class="w-full max-w-sm rounded-3xl border border-white/10 bg-white/10 p-8 shadow-2xl backdrop-blur-xl">
                        <div class="space-y-6">
                            <div>
                                <p class="text-sm font-medium text-white">Project is ready</p>
                                <p class="mt-2 text-sm leading-6 text-slate-400">Use the authentication flow below to continue.</p>
                            </div>

                            <div class="grid gap-4">
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-4 text-center text-sm font-medium text-white transition hover:bg-cyan-300/10 hover:text-cyan-200">
                                        Open Login Page
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
