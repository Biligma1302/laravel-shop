<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями</title>
    <!-- Наш локальный премиальный стиль -->
    <link href="{{ asset('admin.css') }}" rel="stylesheet">
    <style>
        /* Стиль для красивой таблицы пользователей */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            background: #fff;
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }
        .admin-table th, .admin-table td {
            padding: 1rem 1.25rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        .admin-table th {
            background-color: #f8fafc;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .admin-table tr:last-child td { border-bottom: none; }
        .admin-table tr:hover td { background-color: #f8fafc; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- Боковое меню (Sidebar) -->
        <div class="sidebar-panel">
            <h4>🛍️ Магазин</h4>
            <hr>
            <ul class="nav-menu">
                <li><a class="nav-link" href="{{ route('admin.dashboard') }}">📊 Главная</a></li>
                <li><a class="nav-link" href="{{ route('admin.products.index') }}">📦 Товары</a></li>
                <li><a class="nav-link" href="{{ route('admin.orders.index') }}">🛒 Заказы</a></li>
                <li><a class="nav-link active" href="{{ route('admin.users.index') }}">👥 Пользователи</a></li>
            </ul>
            <hr>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">🚪 Выйти</button>
            </form>
        </div>

        <!-- Основной контент страницы -->
        <div class="main-content">
            <h1>👥 Управление пользователями</h1>
            <p>Список всех зарегистрированных клиентов вашего интернет-магазина.</p>

            <!-- Таблица пользователей -->
            <table class="admin-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя пользователя</th>
                    <th>Email</th>
                    <th>Телефон</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>#{{ $user->id }}</td>
                        <!-- Используем ваш метод-аксессор getFullNameAttribute() -->
                        <td style="font-weight: 600; color: #0f172a;">{{ $user->full_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'Не указан' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            Зарегистрированных пользователей пока нет.
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
