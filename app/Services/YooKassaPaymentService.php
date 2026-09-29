<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\PaymentReceipt;
use Illuminate\Support\Str;
use YooKassa\Client;

class YooKassaPaymentService
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

    public function createPaymentForOrder(Order $order): OrderPayment
    {
        $idempotenceKey = (string)Str::uuid();

        $payload = [
            'amount' => [
                'value' => number_format((float)$order->total, 2, '.', ''),
                'currency' => 'RUB',
            ],
            'capture' => true,
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => route('payments.yookassa.return', ['order_id' => $order->id]),
            ],
            'description' => 'Оплата заказа #' . $order->id,
            'metadata' => [
                'order_id' => $order->id,
            ],
        ];
        $shopId = (int)config('services.yookassa.shop_id');
        $this->client->setAuth($shopId, config('services.yookassa.secret_key'));

        $response = $this->client->createPayment($payload, $idempotenceKey);

        return OrderPayment::create([
            'order_id' => $order->id,
            'provider' => 'yookassa',
            'status' => $response->getStatus(),
            'amount' => $order->total,
            'currency' => 'RUB',
            'external_payment_id' => $response->getId(),
            'idempotence_key' => $idempotenceKey,
            'confirmation_url' => $response->getConfirmation()?->getConfirmationUrl(),
            'response_payload' => json_encode($response->jsonSerialize()),
        ]);
    }


    public function handleWebhook(array $payload, OrderService $orderService): void
    {
        $id = $payload['object']['id'];
        $status = $payload['event'];

        $payment = OrderPayment::where('external_payment_id', $id)->first();
        if (!$payment) {
            return;
        }
        if ($status === 'payment.succeeded') {
            $payment->update(['status' => 'succeeded']);

            // Передаем связанный с платежом заказ в службу заказов
            $orderService->markAsPaid($payment->order);
        }
    }


    public function synchronizePayment(string $externalPaymentId): OrderPayment
    {
        $this->client->setAuth((int)config('services.yookassa.shop_id'), config('services.yookassa.secret_key'));

        $response = $this->client->getPaymentInfo($externalPaymentId);
        $payment = OrderPayment::where('external_payment_id', $externalPaymentId)->firstOrFail();

        $payment->update([
            'status' => $response->getStatus(),
            'response_payload' => $response->jsonSerialize(),
        ]);

        return $payment;
    }

    public function createReceipt(OrderPayment $payment): PaymentReceipt
    {
        return PaymentReceipt::create([
            'order_payment_id' => $payment->id,
            'external_receipt_id' => null,
            'type' => 'payment',
            'status' => 'pending',
            'send_to_customer' => $payment->order->user->email ?? null,


            'request_payload' => json_encode([]),
            'response_payload' => json_encode([]),
        ]);
    }
}
