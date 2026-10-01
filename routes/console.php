<?php

declare(strict_types=1);

use App\Jobs\GenerateSalesReportsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new GenerateSalesReportsJob())
    ->everyTenMinutes()
    ->withoutOverlapping(2);
