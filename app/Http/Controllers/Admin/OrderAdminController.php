<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\OrderStoreAdminRequest;
use App\Models\Order;
use App\Services\Admin\AdminOrderService;
use App\Services\OrderService;

class OrderAdminController
{
    protected OrderService $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        $orders = Order::orderByDesc('created_at')->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        return view('admin.orders.create');
    }

    public function store(OrderStoreAdminRequest $request, AdminOrderService $service)
    {
        $service->create($request->validated());

        return redirect()->route('admin.orders.index');
    }

    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    public function update(OrderstoreAdminRequest $request, Order $order, AdminOrderService $service)
    {
        $service->update($order, $request->validated());

        return redirect()->route('admin.orders.index');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index');
    }
}
