<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailySalesReport;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\SalesReportService;

class DashboardController extends Controller
{
    public function index(SalesReportService $salesReportService)
    {
        $productsCount = Product::count();
        $ordersCount = Order::count();
        $usersCount = User::count();

        $report = $salesReportService->getDashboardReport();

        $lastReport = DailySalesReport::latest()->first();

        $pendingOrdersCount = $report['pending_orders_count'] ?? 0;

        return view('admin.dashboard', compact('productsCount', 'ordersCount', 'usersCount', 'report', 'lastReport', 'pendingOrdersCount', ));
    }
}
