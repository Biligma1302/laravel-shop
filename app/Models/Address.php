<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    // Разрешаем массовое заполнение этих полей
    protected $fillable = [
        'street',
        'is_default',
        'user_id',
    ];

    // Указываем обратную связь с пользователем
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
