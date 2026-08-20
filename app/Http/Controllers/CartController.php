<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\SessionCartService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(
        private SessionCartService $sessionCartService,
    ) {
    }

    /**
     * Просмотр корзины.
     */
    public function index(SessionCartService $cart): Factory|View
    {
        $defaultAddress = (object)[
            'city' => 'г. Москва',
            'street' => 'ул. Ленина',
            'house' => '15',
            'apartment' => '42'
        ];
        $defaultAddress = null;
        if (Auth::check()) {
            $user = Auth::user();
            $defaultAddress = (object)[
                'city' => $user->city,
                'street' => $user->street,
                'house' => $user->house,
                'apartment' => $user->apartment,
            ];
        }

        return view('cart.index', [
            'items' => $this->sessionCartService->getItems(),
            'totalQuantity' => $this->sessionCartService->getTotalQuantity(),
            'totalPrice' => $this->sessionCartService->getTotalPrice(),
            'defaultAddress' => $defaultAddress,
        ]);
    }


    public function store(Product $product, Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->sessionCartService->add($product, (int)($data['quantity'] ?? 1));

        return $this->respond($request);
    }

    /**
     * Изменить количество товара.
     */
    public function update(Product $product, Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $this->sessionCartService->setQuantity($product, (int)$data['quantity']);

        return $this->respond($request);
    }

    /**
     * Удалить товар из корзины.
     */
    public function destroy(Product $product, Request $request): JsonResponse|RedirectResponse
    {
        $this->sessionCartService->remove($product);

        return $this->respond($request);
    }

    /**
     * Полностью очистить корзину.
     */
    public function clear(Request $request): JsonResponse|RedirectResponse
    {
        $this->sessionCartService->clear();

        return $this->respond($request);
    }

    /**
     * Единый ответ: JSON для AJAX или редирект для обычных запросов.
     */
    private function respond(Request $request): JsonResponse|RedirectResponse
    {
        $cartCount = $this->sessionCartService->getTotalQuantity();

        if ($request->expectsJson()) {
            return response()->json([
                'cartCount' => $cartCount,
                'html' => view('cart._content', [
                    'items' => $this->sessionCartService->getItems(),
                    'totalQuantity' => $cartCount,
                    'totalPrice' => $this->sessionCartService->getTotalPrice(),
                ])->render(),
            ]);
        }

        return redirect()
            ->back()
            ->with('cartCount', $cartCount);
    }
}
