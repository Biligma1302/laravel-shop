<?php

declare(strict_types=1);

namespace App\Services;

class DeliveryService
{
    public function calculate(int $orderTotal): int
    {
        if ($orderTotal >= 5000) {
            return 0;
        }

        return 500;
    }
}
