<div class="form-container">
    <h1 class="form-title">Редактирование заказа №{{ $order->id }}</h1>

    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
        @csrf
        @method('PATCH')

        @foreach($order->items as $index => $item)
            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
            <input type="hidden" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}">
            <input type="hidden" name="items[{{ $index }}][price]" value="{{ $item->price }}">
        @endforeach

        <!-- ID Пользователя -->
        <div class="form-group">
            <label for="user_id">ID Пользователя</label>
            <input type="number" name="user_id" id="user_id" required value="{{ old('user_id', $order->user_id) }}">
        </div>

        <!-- Статус заказа -->
        <div class="form-group">
            <label for="status">Статус заказа</label>
            <select name="status" id="status" required>
                <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Ожидает оплаты</option>
                <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>В обработке</option>
                <option value="completed" {{ old('status', $order->status) == 'completed' ? 'selected' : '' }}>Выполнен</option>
                <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Отменен</option>
            </select>
        </div>

        <div class="form-actions">
            <a href="{{ route('admin.orders.index') }}" class="btn-back">Назад</a>
            <button type="submit" class="btn-submit">Сохранить изменения</button>
        </div>
    </form>
</div>

<style>
    .form-container {
        max-width: 500px;
        margin: 20px auto;
        padding: 30px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .form-title {
        margin-bottom: 24px;
        color: #1a202c;
        font-size: 22px;
        font-weight: 600;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-container label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #475569;
        margin-bottom: 6px;
    }

    .form-container input[type="number"],
    .form-container select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14px;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-container input:focus,
    .form-container select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .btn-submit, .btn-back {
        flex: 1;
        padding: 12px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        box-sizing: border-box;
        transition: background-color 0.2s;
    }

    .btn-submit {
        background-color: #2563eb;
        color: white;
        border: none;
    }

    .btn-submit:hover {
        background-color: #1d4ed8;
    }

    .btn-back {
        background-color: #ffffff;
        color: #64748b;
        border: 1px solid #cbd5e1;
    }

    .btn-back:hover {
        background-color: #f8fafc;
        border-color: #94a3b8;
    }
</style>
