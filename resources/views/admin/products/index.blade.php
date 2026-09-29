<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление товарами</title>
    <!-- Подключаем наш локальный красивый стиль -->
    <link href="{{ asset('admin.css') }}" rel="stylesheet">
    <style>
        /* Небольшие стили для красивой таблицы */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            background: #fff;
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        .admin-table th, .admin-table td {
            padding: 1rem;
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
        .admin-table tr:last-child td { border-bottom: none; }
        .badge-stock {
            background-color: #e2e8f0;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.85rem;
        }
        /* Стили для кнопок действий */
        .actions-cell {
            display: flex;
            gap: 0.5rem;
        }
        .btn-edit {
            background-color: #3b82f6;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 0.25rem;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .btn-edit:hover { background-color: #2563eb; }
        .btn-delete {
            background-color: #ef4444;
            color: white;
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 0.85rem;
        }
        .btn-delete:hover { background-color: #dc2626; }
        .btn-create {
            background-color: #10b981;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-create:hover { background-color: #059669; }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
            border: 1px solid #a7f3d0;
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
                <li><a class="nav-link active" href="{{ route('admin.products.index') }}">📦 Товары</a></li>
                <li><a class="nav-link" href="{{ route('admin.orders.index') }}">🛒 Заказы</a></li>
                <li><a class="nav-link" href="{{ route('admin.users.index') }}">👥 Пользователи</a></li>
            </ul>
            <hr>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">🚪 Выйти</button>
            </form>
        </div>

        <!-- Основной контент страницы -->
        <div class="main-content">
            <!-- ВЫВОД УВЕДОМЛЕНИЙ ОБ УСПЕХЕ -->
            @if(session('success'))
                <div class="alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <div>
                    <h1>📦 Управление товарами</h1>
                    <p>Список всех товаров, размещенных на витрине вашего магазина.</p>
                </div>
                <!-- КНОПКА СОЗДАНИЯ ТОВАРA -->
                <a href="{{ route('admin.products.create') }}" class="btn-create">➕ Добавить товар</a>
            </div>

            <!-- Таблица со списком товаров из базы данных -->
            <table class="admin-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название товара</th>
                    <th>Цена</th>
                    <th>Остаток на складе</th>
                    <th>Действия</th> <!-- Добавили заголовок для кнопок -->
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>#{{ $product->id }}</td>
                        <td style="font-weight: 600; color: #0f172a;">{{ $product->title ?? $product->name }}</td>
                        <td>{{ number_format((float)$product->price, 2, '.', ' ') }} руб.</td>
                        <td><span class="badge-stock">{{ $product->stock ?? 0 }} шт.</span></td>
                        <!-- КОЛОНКА С КНОПКАМИ УПРАВЛЕНИЯ -->
                        <td>
                            <div class="actions-cell">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn-edit">Изменить</a>

                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Вы действительно хотите удалить этот товар?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">Удалить</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            Товары в базе данных пока не созданы.
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
