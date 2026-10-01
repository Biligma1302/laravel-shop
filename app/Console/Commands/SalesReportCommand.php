<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\SalesReportService;
use Illuminate\Console\Command;

class SalesReportCommand extends Command
{
    protected $signature = 'reports:sales';
    protected $description = 'Вручную вызывает обновление последних отчетов о продажах';
    public function handle(SalesReportService $salesReportService): int
    {
        $this->info('Запуск обновления отчетов через сервис...');

        $salesReportService->refreshRecentReports();

        $this->info('Сервис успешно выполнил обновление отчетов!');

        return Command::SUCCESS;
    }
}
