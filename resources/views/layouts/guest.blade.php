<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <style>
            :root {
                --bg: #f7faff;
                --surface: rgba(255, 255, 255, 0.92);
                --surface-strong: #ffffff;
                --text: #11243d;
                --muted: #66768c;
                --line: rgba(17, 36, 61, 0.1);
                --blue: #2064f5;
                --blue-deep: #0d48c8;
                --blue-soft: rgba(32, 100, 245, 0.1);
                --danger: #dc2626;
                --success-bg: rgba(22, 163, 74, 0.12);
                --success-text: #166534;
                --shadow: 0 24px 60px rgba(21, 54, 120, 0.12);
                --radius: 28px;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: "Segoe UI Variable", "Trebuchet MS", system-ui, sans-serif;
                color: var(--text);
                background:
                    radial-gradient(circle at top right, rgba(32, 100, 245, 0.18), transparent 28%),
                    linear-gradient(135deg, #ffffff 0%, #f4f8ff 100%);
                display: grid;
                place-items: center;
                padding: 28px;
            }

            .page {
                width: min(1100px, 100%);
                min-height: 720px;
                display: grid;
                grid-template-columns: 1.15fr 0.85fr;
                background: linear-gradient(90deg, rgba(255,255,255,0.98) 0 80%, rgba(228,238,255,0.9) 80% 100%);
                border: 1px solid rgba(255, 255, 255, 0.9);
                border-radius: 36px;
                overflow: hidden;
                box-shadow: var(--shadow);
                position: relative;
                isolation: isolate;
            }

            .page::before {
                content: "";
                position: absolute;
                inset: 24px 24px auto auto;
                width: 280px;
                height: 280px;
                background: radial-gradient(circle, rgba(32, 100, 245, 0.22), transparent 70%);
                filter: blur(10px);
                z-index: -1;
            }

            .left,
            .right {
                position: relative;
                padding: 56px;
            }

            .brand {
                display: inline-flex;
                align-items: center;
                gap: 14px;
                color: inherit;
                text-decoration: none;
            }

            .brand-mark {
                width: 52px;
                height: 52px;
                padding: 10px;
                border-radius: 18px;
                color: white;
                background: linear-gradient(135deg, var(--blue), var(--blue-deep));
                box-shadow: 0 16px 34px rgba(32, 100, 245, 0.22);
            }

            .brand-copy strong {
                display: block;
                font-size: 1rem;
                letter-spacing: 0.01em;
            }

            .brand-copy span {
                display: block;
                margin-top: 4px;
                color: var(--muted);
                font-size: 0.92rem;
            }

            .hero-title {
                margin: 24px 0 18px;
                max-width: 10ch;
                font-family: Georgia, "Times New Roman", serif;
                font-size: clamp(2.9rem, 5vw, 5.4rem);
                line-height: 0.98;
                letter-spacing: -0.05em;
                font-weight: 600;
            }

            .lead {
                max-width: 34rem;
                margin: 0;
                color: var(--muted);
                font-size: 1.05rem;
                line-height: 1.8;
            }

            .right {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .panel {
                width: min(420px, 100%);
                background: var(--surface);
                border: 1px solid rgba(255, 255, 255, 0.7);
                border-radius: var(--radius);
                padding: 34px;
                backdrop-filter: blur(18px);
                box-shadow: 0 20px 40px rgba(22, 50, 110, 0.1);
            }

            .status {
                margin-bottom: 16px;
                padding: 14px 16px;
                border-radius: 16px;
                background: var(--success-bg);
                color: var(--success-text);
                font-size: 0.94rem;
            }

            .field {
                margin-bottom: 16px;
            }

            label {
                display: block;
                margin-bottom: 10px;
                font-size: 0.92rem;
                font-weight: 600;
            }

            input[type="email"],
            input[type="password"] {
                width: 100%;
                border: 1px solid rgba(17, 36, 61, 0.12);
                background: var(--surface-strong);
                border-radius: 16px;
                padding: 16px 18px;
                font: inherit;
                color: var(--text);
                outline: none;
                transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
            }

            input[type="email"]:focus,
            input[type="password"]:focus {
                border-color: rgba(32, 100, 245, 0.55);
                box-shadow: 0 0 0 4px rgba(32, 100, 245, 0.12);
                transform: translateY(-1px);
            }

            .checkbox {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                margin: 8px 0 22px;
                color: var(--muted);
                font-size: 0.92rem;
            }

            .checkbox input {
                width: 18px;
                height: 18px;
                accent-color: var(--blue);
            }

            .error {
                margin-top: 8px;
                color: var(--danger);
                font-size: 0.88rem;
            }

            .primary {
                width: 100%;
                border: 0;
                border-radius: 18px;
                padding: 16px 18px;
                background: linear-gradient(135deg, var(--blue), var(--blue-deep));
                color: white;
                font: inherit;
                font-weight: 700;
                cursor: pointer;
                box-shadow: 0 16px 30px rgba(32, 100, 245, 0.26);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 20px 34px rgba(32, 100, 245, 0.3);
            }

            @media (max-width: 900px) {
                .page {
                    grid-template-columns: 1fr;
                }

                .left {
                    padding-bottom: 8px;
                }

                .hero-title {
                    margin-top: 44px;
                    max-width: 12ch;
                }
            }

            @media (max-width: 560px) {
                body,
                .left,
                .right {
                    padding: 18px;
                }

                .panel {
                    padding: 24px;
                }
            }
        </style>
    </head>
    <body>
        <main class="page">
            <section class="left">
                <a href="{{ route('login') }}" class="brand">
                    <img
                        src="{{ asset('images/task-management-logo.png') }}"
                        alt="{{ config('app.name', 'Task Management') }}"
                        class="brand-mark"
                    />
                    <div class="brand-copy">
                        <strong>Task Management</strong>
                        <span>Secure access portal</span>
                    </div>
                </a>

                <h1 class="hero-title">Task Management Application</h1>
                <p class="lead">
                    This app is used to manage your tasks with a simple and focused login experience.
                </p>
            </section>

            <section class="right">
                {{ $slot }}
            </section>
        </main>
    </body>
</html>
