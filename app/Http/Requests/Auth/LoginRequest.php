<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $loginType = $this->input('login_type', 'employee');

        if ($loginType === 'admin') {
            return [
                'login_type' => ['required', 'in:employee,admin'],
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ];
        }

        return [
            'login_type' => ['required', 'in:employee,admin'],
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginType = $this->input('login_type', 'employee');

        $credentials = match ($loginType) {
            'admin' => [
                'email' => $this->input('email'),
                'password' => $this->input('password'),
            ],
            default => [
                'phone' => $this->input('phone'),
                'password' => $this->input('password'),
                'role' => 'employee',
            ],
        };

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            $field = $loginType === 'admin' ? 'email' : 'phone';
            throw ValidationException::withMessages([
                $field => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        $loginType = $this->input('login_type', 'employee');
        $identifier = $loginType === 'admin' ? $this->string('email') : $this->string('phone');
        return Str::transliterate(Str::lower($identifier) . '|' . $this->ip());
    }
}
