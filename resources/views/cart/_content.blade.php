@if(empty($items))
    <div class="alert alert-info mb-0">
        Корзина пуста. <a href="/products">Перейти в каталог</a>
    </div>
@else
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Товар</th>
                <th>Цена</th>
                <th style="width: 220px;" class="text-center">Количество</th>
                <th>Итого</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $productId => $item)
                @php($product = $item['product'])
                <tr>
                    <td>
                        <div class="fw-bold">{{ $product->name }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <form method="POST"
                                  action="{{ route('cart.items.update', $product) }}"
                                  data-ajax-cart="1"
                                  data-cart-action="set">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] - 1 }}">
                                <button type="submit" class="btn btn-outline-secondary btn-sm" @disabled($item['quantity'] <= 1)>
                                    −
                                </button>
                            </form>

                            <span class="mx-2">{{ $item['quantity'] }}</span>

                            <form method="POST"
                                  action="{{ route('cart.items.update', $product) }}"
                                  data-ajax-cart="1"
                                  data-cart-action="update">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                <button type="submit" class="btn btn-outline-secondary btn-sm" @disabled($item['quantity'] >= $product->stock)>
                                    +
                                </button>
                            </form>
                        </div>
                    </td>
                    <td>
                        {{ number_format($product->price * $item['quantity'], 0, ',', ' ') }} ₽
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end align-items-center mt-3 p-3 bg-light rounded">
        <span class="fs-5 me-3">Общая стоимость:</span>
        <span class="fs-4 fw-bold text-primary">
            {{ number_format(collect($items)->sum(fn($i) => $i['product']->price * $i['quantity']), 0, ',', ' ') }} ₽
        </span>
    </div>

    <form method="POST" action="{{ route('orders.store') }}" id="store-order-form">
        @csrf

        <div class="mb-4 mt-4">
            <h5 class="h6 font-weight-bold mb-2">Адрес доставки</h5>

            @if(!empty(auth()->user()->address))
                <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted d-block mb-1">Товар будет доставлен по адресу:</small>
                        <strong>
                            {{ auth()->user()->address }}
                        </strong>
                    </div>
                    <a href="/profile" class="btn btn-sm btn-outline-primary">
                        Изменить
                    </a>
                </div>
            @else
                <div class="alert alert-warning d-flex justify-content-between align-items-center mb-0">
                    <span>⚠️ В вашем профиле не указан основной адрес доставки.</span>
                    <a href="/profile" class="btn btn-sm btn-danger">
                        Добавить адрес
                    </a>
                </div>
            @endif
        </div>

        <div class="mb-4">
            <h3 class="h6 mb-2">Способ оплаты</h3>

            <div class="form-check">
                <input class="form-check-input"
                       type="radio"
                       name="payment_method"
                       id="payment-cash"
                       value="cash"
                    @checked(old('payment_method', 'cash') === 'cash')>
                <label class="form-check-label" for="payment-cash">
                    Наличными при получении
                </label>
            </div>

            <div class="form-check mt-1">
                <input class="form-check-input"
                       type="radio"
                       name="payment_method"
                       id="payment-card"
                       value="card"
                    @checked(old('payment_method') === 'card')>
                <label class="form-check-label" for="payment-card">
                    Картой при получении
                </label>
            </div>
        </div>

        <button type="button" onclick="document.getElementById('store-order-form').submit();" class="btn btn-primary mt-3">
            Оформить заказ
        </button>
    </form>
@endif
