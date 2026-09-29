<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $productsCount = Product::count();
        $ordersCount = Order::count();
        $usersCount = User::count();

        return view('admin.dashboard', compact('productsCount', 'ordersCount', 'usersCount'));
    }
}
