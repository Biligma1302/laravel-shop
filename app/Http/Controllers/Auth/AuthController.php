<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\RegisterDto;
use App\DTOs\UpdateProfileDto;

use App\Http\Controllers\Controller;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;

use App\Services\Auth\UserService;

use Illuminate\Http\RedirectResponse;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    )
    {}

    public function showRegistrationForm(): Factory|View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $dto = RegisterDto::fromRequest($request);
        $user = $this->userService->register($dto);

        return redirect()
            ->route('login.form')
            ->with('status', 'Регистрация прошла успешно');
    }

    public function showLoginForm(): Factory|View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            return redirect()->intended('profile');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        return redirect()->route('login.form');
    }

    public function showProfile(): Factory|View
    {
        $user = Auth::user();

        return view('auth.profile', compact('user'));
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $dto = UpdateProfileDto::fromRequest($request);
        $this->userService->updateProfile($dto);

        return redirect()->route('profile.form');
    }

    public function showChangePasswordForm(): Factory|View
    {
        return view('auth.change-password');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $user = Auth::user();

        $this->userService->updatePassword(
            $user,
            $validatedData['current_password'],
            $validatedData['new_password']
            );

        return redirect()
            ->route('profile.form')
            ->with('status', 'Password successfully changed!');
    }
}
