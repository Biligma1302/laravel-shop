<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\DTOs\Admin\RoleDto;
use App\Models\Role;
use Exception;

class RoleService
{
    public function create(RoleDto $dto): Role
    {
        return Role::create([
            'name' => $dto->name,
            'slug' => $dto->slug,
        ]);
    }

    public function update(Role $role, RoleDto $dto): bool
    {
        return $role->update([
            'name' => $dto->name,
            'slug' => $dto->slug,
        ]);
    }

    public function delete(Role $role): bool
    {
        if (in_array($role->slug, ['admin', 'manager', 'user'])) {
            throw new Exception('Нельзя удалить системную роль');
        }

        return $role->delete();
    }
}
