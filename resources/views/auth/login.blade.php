<x-guest-layout>
    <div class="auth-card">
        <div class="auth-card-header">
            <h2>Welcome Back</h2>
            <p>Log in to your Expense Splitter account</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="form-group">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="error-message" />
            </div>

            <!-- Password -->
            <div class="form-group">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="error-message" />
            </div>

            <!-- Remember Me -->
            <div class="remember-me">
                <label for="remember_me" class="remember-label">
                    <input id="remember_me" type="checkbox" name="remember" class="remember-checkbox">
                    <span>{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="form-actions">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-link">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
                <x-primary-button class="btn">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
            
            <div class="register-link">
                <span>Not having an account?</span> 
                <a href="{{ route('register') }}" class="auth-link">
                    {{ __('Register Now') }}
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
