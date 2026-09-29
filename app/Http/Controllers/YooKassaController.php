<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentReceipt;
use App\Services\YooKassaPaymentService;
use Illuminate\Http\Request;
use YooKassa\Client;

class YooKassaController extends Controller
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth(
            (int)config('services.yookassa.shop_id'),
            config('services.yookassa.secret_key')
        );
    }

    /**
     * 1. Метод return() — когда пользователь вернулся на сайт
     */
    public function return(Request $request, YooKassaPaymentService $yooKassaPaymentService)
    {
        $orderId = $request->input('order_id');
        $order = Order::findOrFail($orderId);
        $payment = $order->lastPayment;

        if ($payment) {
            $payment = $yooKassaPaymentService->synchronizePayment($payment->external_payment_id);

            if ($payment->status === 'succeeded') {
                $order->update(['status' => Order::STATUS_PAID]);

                $receiptExists = PaymentReceipt::where('order_payment_id', $payment->id)->exists();
                if (!$receiptExists) {
                    $yooKassaPaymentService->createReceipt($payment);
                }

                return redirect()->route('orders.index')->with('success', 'Заказ успешно оплачен!');
            }
        }

        return redirect()->route('orders.index')->with(
            'success',
            'Вы вернулись в магазин. Статус оплаты проверяется...'
        );
    }


    public function webhook(Request $request, YooKassaPaymentService $yooKassaPaymentService)
    {
        $paymentId = $request->input('object.id');

        if (!$paymentId) {
            return response('No ID', 400);
        }

        $payment = $yooKassaPaymentService->synchronizePayment($paymentId);

        if ($payment->status === 'succeeded') {
            $payment->order->update(['status' => Order::STATUS_PAID]);

            $receiptExists = PaymentReceipt::where('order_payment_id', $payment->id)->exists();
            if (!$receiptExists) {
                $yooKassaPaymentService->createReceipt($payment);
            }
        }
        return response('OK', 200);
    }
}
