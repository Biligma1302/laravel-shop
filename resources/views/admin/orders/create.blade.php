<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание заказа</title>
    <link href="{{ asset('admin.css') }}" rel="stylesheet">
</head>
<body>

<div class="form-container">
    <h1 class="form-title">Создание нового заказа</h1>

    <form action="{{ route('admin.orders.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="user_id">ID Пользователя</label>
            <input type="number" name="user_id" id="user_id" required value="{{ old('user_id') }}">
        </div>

        <!-- Статус заказа -->
        <div class="form-group">
            <label for="status">Статус заказа</label>
            <select name="status" id="status" required>
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Ожидает оплаты</option>
                <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>В обработке</option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Выполнен</option>
                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Отменен</option>
            </select>
        </div>

        <!-- Секция добавления одного товара -->
        <div class="items-section">
            <div class="items-title">Первый товар в заказе</div>

            <div class="item-row">
                <div class="item-field">
                    <label>ID Товара</label>
                    <input type="number" name="items[0][product_id]" required value="{{ old('items.0.product_id') }}">
                </div>
                <div class="item-field item-field-sm">
                    <label>Кол-во</label>
                    <input type="number" name="items[0][quantity]" min="1" required value="{{ old('items.0.quantity', 1) }}">
                </div>
                <div class="item-field">
                    <label>Цена за ед.</label>
                    <input type="number" name="items[0][price]" step="0.01" required value="{{ old('items.0.price') }}">
                </div>
            </div>
        </div>

        <!-- Действия -->
        <div class="form-actions">
            <a href="{{ route('admin.orders.index') }}" class="btn-back">Назад</a>
            <button type="submit" class="btn-submit">Создать заказ</button>
        </div>
    </form>
</div>

<style>
    .form-container {
        max-width: 600px;
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
        font-size: 24px;
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

    .form-container input[type="text"],
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

    .items-section {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 2px dashed #e2e8f0;
        margin-bottom: 24px;
    }

    .items-title {
        font-size: 18px;
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 16px;
    }

    .item-row {
        display: flex;
        gap: 12px;
        background: #f8fafc;
        padding: 16px;
        border-radius: 6px;
    }

    .item-field {
        flex: 1;
    }

    .item-field-sm {
        flex: 0 0 90px;
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

</body>
</html>
