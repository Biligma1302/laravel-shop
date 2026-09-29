<?php

declare(strict_types=1);

namespace App\DTOs\Admin;

use Spatie\LaravelData\Data;

class RoleDto extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
    ) {
    }
}
