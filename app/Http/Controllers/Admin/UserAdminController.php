<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Services\Auth\UserService;

class UserAdminController
{
    public function index()
    {
        // Берем всех пользователей из базы данных, сначала новых
        $users = User::orderByDesc('id')->get();

        // Передаем их во вьюху, которую вы создали на прошлом шаге
        return view('admin.users.index', compact('users'));
    }

    public function store(UserStoreRequest $request, UserService $service)
    {
        $service->createFromAdmin($request->validated());

        return redirect()->route('admin.users.index');
    }
}
