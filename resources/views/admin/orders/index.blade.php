<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление заказами</title>
    <link href="{{ asset('admin.css') }}" rel="stylesheet">
    <style>
        /* Стили для шапки и кнопки Создать */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .btn-create {
            display: inline-flex;
            align-items: center;
            padding: 10px 18px;
            background-color: #10b981;
            color: #ffffff;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .btn-create:hover {
            background-color: #059669;
        }

        /* Стили для таблицы */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            background: #fff;
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        .admin-table th, .admin-table td {
            padding: 1rem 1.25rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        .admin-table th {
            background-color: #f8fafc;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .badge-status {
            background-color: #fef3c7;
            color: #d97706;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }

        /* Стили для кнопок Действия */
        .actions-cell {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .btn-action-edit {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            background-color: #2563eb;
            color: #ffffff;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .btn-action-edit:hover {
            background-color: #1d4ed8;
        }
        .btn-action-delete {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            background-color: #ef4444;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-action-delete:hover {
            background-color: #dc2626;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- Боковое меню -->
        <div class="sidebar-panel">
            <h4>🛍️ Магазин</h4>
            <hr>
            <ul class="nav-menu">
                <li><a class="nav-link" href="{{ route('admin.dashboard') }}">📊 Главная</a></li>
                <li><a class="nav-link" href="{{ route('admin.products.index') }}">📦 Товары</a></li>
                <li><a class="nav-link active" href="{{ route('admin.orders.index') }}">🛒 Заказы</a></li>
                <li><a class="nav-link" href="{{ route('admin.users.index') }}">👥 Пользователи</a></li>
            </ul>
        </div>

        <!-- Контент страницы -->
        <div class="main-content">
            <div class="page-header">
                <div>
                    <h1>🛒 Управление заказами</h1>
                    <p style="margin: 4px 0 0 0;">Список всех покупок, совершенных в вашем магазине.</p>
                </div>
                <!-- Кнопка под метод create() -->
                <a href="{{ route('admin.orders.create') }}" class="btn-create">➕ Создать заказ</a>
            </div>

            <table class="admin-table">
                <thead>
                <tr>
                    <th>ID Заказа</th>
                    <th>Адрес доставки</th>
                    <th>Метод оплаты</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th style="width: 180px;">Действия</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->shipping_address }}</td>
                        <td>{{ $order->payment_method }}</td>
                        <td style="font-weight: 600;">{{ number_format((float)$order->total, 2, '.', ' ') }} ₽</td>
                        <td><span class="badge-status">{{ $order->status }}</span></td>
                        <td>
                            <div class="actions-cell">
                                <!-- Кнопка под метод edit() -->
                                <a href="{{ route('admin.orders.edit', $order) }}" class="btn-action-edit">✏️ Редактировать</a>

                                <!-- Форма под метод destroy() -->
                                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить этот заказ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-delete">🗑️ Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            Заказов в базе данных пока нет.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>
