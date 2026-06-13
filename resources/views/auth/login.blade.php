<x-guest-layout>
    <form method="POST" action="{{ route('login') }}" class="panel">
        @csrf

        <h2 style="margin: 0 0 10px; font-size: 2rem; letter-spacing: -0.04em;">Login</h2>

        <x-auth-session-status class="status" :status="session('status')" />

        <div class="field">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="name@company.com" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="error" />
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Enter your password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="error" />
        </div>

        <label class="checkbox" for="remember_me">
            <input id="remember_me" type="checkbox" name="remember">
            <span>Remember me</span>
        </label>

        <button class="primary" type="submit">Sign in</button>
    </form>
</x-guest-layout>
