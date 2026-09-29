<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админка</title>
    <!-- Наш локальный красивый стиль -->
    <link href="{{ asset('admin.css') }}" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- Боковое меню (Sidebar) -->
        <div class="sidebar-panel">
            <a href="{{ url('/products') }}" target="_blank" style="color: #ffffff; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                🛍️ Магазин
            </a>
            <hr>
            <ul class="nav-menu">
                <li>
                    <a class="nav-link active" href="{{ route('admin.dashboard') }}">📊 Главная</a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('admin.products.index') }}">📦 Товары</a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('admin.orders.index') }}">🛒 Заказы</a>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('admin.users.index') }}">👥 Пользователи</a>
                </li>
            </ul>
            <hr>
            <!-- Форма выхода -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">🚪 Выйти</button>
            </form>
        </div>

        <!-- Основной контент панели -->
        <div class="main-content">
            <h1>Панель управления</h1>
            <p>Добро пожаловать обратно! Вот актуальные данные вашего проекта на сегодня.</p>

            <!-- Сетка красивых карточек -->
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="card-title">Всего товаров</span>
                    <h3 class="card-value">{{ $productsCount }}.</h3>
                </div>
                <div class="stat-card">
                    <span class="card-title">Новых заказов</span>
                    <h3 class="card-value">{{$ordersCount}}</h3>
                </div>
                <div class="stat-card">
                    <span class="card-title">Пользователи</span>
                    <h3 class="card-value">{{$usersCount}}</h3>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
