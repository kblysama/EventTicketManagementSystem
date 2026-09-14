<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): JsonResponse|RedirectResponse
    {
        $user = User::query()->create([
            ...$request->safe()->only(['name', 'email', 'password']),
            'role' => Role::Attendee,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $request->expectsJson() || $request->ajax()
            ? ajax_redirect(route('events.index'), 'Hesabın hazır.')
            : redirect()->route('events.index');
    }
}
