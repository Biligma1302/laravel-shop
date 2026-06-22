<?php

declare(strict_types=1);

namespace App\DTOs;

namespace App\DTOs;

use App\Http\Requests\UpdateProfileRequest;
use Spatie\LaravelData\Data;

class UpdateProfileDto extends Data
{
    public function __construct(
        public string $first_name,
        public string $last_name,
        public string $email,
    ) {
    }

    public static function fromRequest(UpdateProfileRequest $request): self
    {
        return new self(
            $request->validated('first_name'),
            $request->validated('last_name'),
            $request->validated('email'),
        );
    }
}
