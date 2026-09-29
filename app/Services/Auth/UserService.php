<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\DTOs\RegisterDto;
use App\DTOs\UpdateProfileDto;
use App\Jobs\SendRegistrationVerificationJob;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function register(RegisterDto $dto): User
    {
        $user = new User();
        $user->first_name = $dto->first_name;
        $user->last_name = $dto->last_name;
        $user->email = $dto->email;
        $user->password = Hash::make($dto->password);
        $user->save();

        SendRegistrationVerificationJob::dispatch($user->id);

        return $user;
    }

    public function updateProfile(UpdateProfileDto $dto): void //метод для обновления пользователя:
    {
        $user = Auth::user();
        $user->fill($dto->toArray());
        $user->save();
    }

    public function updatePassword(
        User $user,
        string $currentPassword,
        string $newPassword
    ): void {
        if (!Hash::check($currentPassword, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'Invalid current password']);
        }

        $user->password = Hash::make($newPassword);
        $user->save();
    }

    public function createFromAdmin(array $data): User
    {
        return User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'status'     => $data['status'],
            'role_id'    => $data['role_id'],
            'password'   => bcrypt(Str::random(10)),
        ]);
    }

    public function resetPassword(User $user, string $password): void
    {
        $user->update([
            'password' => bcrypt($password)
        ]);
    }
}
