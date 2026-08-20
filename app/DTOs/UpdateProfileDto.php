<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\UpdateProfileRequest;
use Spatie\LaravelData\Data;

class UpdateProfileDto extends Data
{
    public function __construct(
        public ?string $first_name,
        public ?string $last_name,
        public ?string $email,
        public ?string $phone,
        public ?string $address,
    ) {
    }

    public static function fromRequest(UpdateProfileRequest $request): self
    {
        // Метод from() в Spatie Data автоматически возьмет валидированные данные
        return self::from($request->validated());
    }
}
