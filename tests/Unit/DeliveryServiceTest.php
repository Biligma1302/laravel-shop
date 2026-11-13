<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\DeliveryService;
use PHPUnit\Framework\TestCase;

class DeliveryServiceTest extends TestCase
{
    public function test_delivery_costs_500_for_small_order(): void
    {
        $service = new DeliveryService();

        $result = $service->calculate(3000);

        $this->assertSame(500, $result);
    }

    public function test_delivery_is_free_from_5000(): void
    {
        $service = new DeliveryService();

        $result = $service->calculate(5000);

        $this->assertSame(0, $result);
    }
}
